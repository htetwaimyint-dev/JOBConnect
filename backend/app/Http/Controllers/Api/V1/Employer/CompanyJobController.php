<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Jobs\StoreJobRequest;
use App\Http\Requests\Api\Jobs\UpdateJobRequest;
use App\Http\Resources\Api\Jobs\JobResource;
use App\Services\Employer\CompanyService;
use App\Services\Jobs\JobService;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;

class CompanyJobController extends Controller
{
    protected JobService $jobService;
    protected CompanyService $companyService;

    public function __construct(
        JobService $jobService,
        CompanyService $companyService
    ) {
        $this->jobService = $jobService;
        $this->companyService = $companyService;
    }

    public function index(Request $request)
    {
        $company = $this->getEmployerCompany($request->user());

        $perPage = min(
            max((int) $request->query('perpage', 10), 1),
            50
        );

        $job = $this->jobService->getjobs($company, $perPage);

        return JobResource::collection($job)->additional([
            'success' => true,
            'message' => 'Jobs retrieved successfully.',
        ]);
    }

    public function store(StoreJobRequest $request)
    {
        $company = $this->getEmployerCompany($request->user());

        $job = $this->jobService->createJob($company, $request->validated());
        return response()->json([
            'success' => true,
            'message' => $job->status === 'draft'
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

    public function update(UpdateJobRequest $request, int $jobId)
    {
        $company = $this->getEmployerCompany($request->user());

        $job = $this->jobService->updateJob($company, $jobId, $request->validated);

        return response()->json([
            'success' => true,
            'message' => 'Job updated successfully.',
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
