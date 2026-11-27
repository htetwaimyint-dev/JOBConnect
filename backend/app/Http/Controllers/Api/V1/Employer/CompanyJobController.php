<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Jobs\PublicJobIndexRequest;
use App\Http\Requests\Api\Jobs\StoreJobRequest;
use App\Http\Requests\Api\Jobs\UpdateJobRequest;
use App\Http\Resources\Api\Jobs\JobResource;
use App\Services\Employer\CompanyService;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Services\Jobs\EmployerJobService;
use App\Enums\JobStatus;
use App\Models\Job;
use App\Services\Jobs\JobQuestionService;

class CompanyJobController extends Controller
{
    protected EmployerJobService $jobService;
    protected CompanyService $companyService;
    protected JobQuestionService $jobQuestionService;

    public function __construct(
        EmployerJobService $jobService,
        CompanyService $companyService,
        JobQuestionService $jobQuestionService
    ) {
        $this->jobService = $jobService;
        $this->companyService = $companyService;
        $this->jobQuestionService = $jobQuestionService;
    }

    public function index(PublicJobIndexRequest $request)
    {
        $company = $this->getEmployerCompany($request->user());


        $perPage = min(
            max((int) $request->query('perpage', 10), 1),
            50
        );

        $job = $this->jobService->getjobs($company, $request->validated(), $perPage);

        return JobResource::collection($job)->additional([
            'success' => true,
            'message' => 'Jobs retrieved successfully.',
        ]);
    }

    public function store(StoreJobRequest $request)
    {
        $company = $this->getEmployerCompany($request->user());
        //admin verify to job post
        abort_unless(
            $company->verification_status === 'verified',
            403,
            'Your company must be approved before posting jobs.'
        );

        $validated = $request->validated();

        $questions = $validated['job_questions'] ?? [];

        unset($validated['job_questions']);

        $job = $this->jobService->createJob(
            $company,
            $validated
        );

        $this->jobQuestionService->sync($job, $questions);

        $job->load('jobQuestion');

        return response()->json([
            'success' => true,
            'message' => $job->status === JobStatus::Draft
                ? 'Job saved as draft successfully.'
                : 'Job submitted for admin approval successfully.',
            'data' => new JobResource($job),
        ], 201);
    }

    public function show(Request $request, int $jobId)
    {
        $company = $this->getEmployerCompany($request->user());

        $job = $this->jobService->getJob($company, $jobId);

        return response()->json([
            'success' => true,
            'message' => 'Job retrieved successfully.',
            'data' => new JobResource($job),
        ]);
    }

    public function update(
        UpdateJobRequest $request,
        int $jobId,
        ActivityLogService $activityLogService
    ) {
        $company = $this->getEmployerCompany($request->user());

        $validated = $request->validated();

        $hasQuestions = array_key_exists('job_questions', $validated);
        $questions = $validated['job_questions'] ?? [];

        unset($validated['job_questions']);

        $job = $this->jobService->updateJob($company, $jobId, $validated);

        if ($hasQuestions) {
            $this->jobQuestionService->sync($job, $questions);
        }

        $activityLogService->log(
            'Updated Job',
            'Updated job: ' . $job->title,
            $job
        );

        $job->load('jobQuestion');

        return response()->json([
            'success' => true,
            'message' => 'Job updated successfully.',
            'data' => new JobResource($job),
        ]);
    }

    //HWM add 
    public function close(Request $request, int $jobId)
    {
        $company = $this->getEmployerCompany($request->user());

        $job = $this->jobService->closeJob($company, $jobId);

        return response()->json([
            'success' => true,
            'message' => 'Job closed successfully.',
            'data' => new JobResource($job),
        ]);
    }

    public function destroy(Request $request, int $jobId)
    {
        $company = $this->getEmployerCompany($request->user());

        $this->jobService->deleteJob($company, $jobId);

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully.',
        ]);
    }

    private function getEmployerCompany(User $user): Company
    {
        $company = $this->companyService->getCompanyByEmployer($user);

        abort_unless(
            $company,
            403,
            'Please create your company profile before posting a job.'
        );

        return $company;
    }
}
