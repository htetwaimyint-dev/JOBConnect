<?php

namespace App\Http\Controllers\Api\V1\AI;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\User;
use App\Services\Employer\CompanyService;
use App\Services\Jobs\EmployerJobService;
use App\Services\AI\JobMatchingService;
use Illuminate\Http\Request;

class JobMatchingController extends Controller
{
    protected CompanyService $companyService;

    protected EmployerJobService $jobService;

    protected JobMatchingService $jobMatchingService;

    public function __construct(
        CompanyService $companyService,
        EmployerJobService $jobService,
        JobMatchingService $jobMatchingService
    ) {
        $this->companyService = $companyService;
        $this->jobService = $jobService;
        $this->jobMatchingService = $jobMatchingService;
    }

    /**
     * Evaluate all eligible applicants for a specific job.
     */
    public function evaluateApplicants(
        Request $request,
        int $jobId
    ) {
        $employer = $request->user();

        $company = $this->getEmployerCompany($employer);

        $job = $this->jobService->getJob($company, $jobId);

        $result = $this->jobMatchingService->evaluateApplicants(
            $job,
            $employer
        );

        return response()->json([
            'success' => true,
            'message' => 'Applicant evaluation completed.',
            'data' => $result,
        ]);
    }

    /**
     * Evaluate one applicant for a specific application.
     */
    public function evaluateSingleApplicant(
        Request $request,
        int $applicationId
    ) {
        $employer = $request->user();

        $company = $this->getEmployerCompany($employer);

        // Find an application belonging to this employer's company.
        $application = Application::query()
            ->whereHas('job', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->findOrFail($applicationId);

        // Evaluate the selected applicant using AI.
        $result = $this->jobMatchingService->evaluateSingleApplicant(
            $application->job,
            $application,
            $employer
        );

        return response()->json([
            'success' => true,
            'message' => 'Applicant evaluation completed.',
            'data' => $result,
        ]);
    }

    /**
     * Get the company belonging to the employer.
     */
    private function getEmployerCompany(User $user): Company
    {
        $company = $this->companyService->getCompanyByEmployer($user);

        abort_unless(
            $company,
            403,
            'Please create your company profile before using AI screening.'
        );

        return $company;
    }
}
