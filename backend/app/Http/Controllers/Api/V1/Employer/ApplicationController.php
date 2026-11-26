<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Applications\UpdateApplicationStatusRequest;
use App\Http\Requests\Api\Employer\ApplicantStatsRequest;
use App\Http\Resources\Api\Applications\ApplicationResource;
use App\Models\Company;
use App\Models\User;
use App\Services\Applications\ApplicationService;
use App\Services\Employer\CompanyService;
use Illuminate\Http\Request;
use App\Enums\ApplicationStatus;


class ApplicationController extends Controller
{
    public function __construct(
        protected ApplicationService $applicationService,
        protected CompanyService $companyService
    ) {}

    /**
     * Get all applications belonging to the Employer's Company.
     */
    public function index(Request $request)
    {
        $company = $this->getEmployerCompany($request->user());

        $validated = $request->validate([
            'perpage' => ['nullable', 'integer', 'min:1', 'max:50'],
            'status' => [
                'nullable',
                'string',
                \Illuminate\Validation\Rule::in([
                    'pending',
                    'reviewed',
                    'shortlisted',
                    'interview',
                    'accepted',
                    'rejected',
                    'withdrawn',
                ]),
            ],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $perPage = (int) ($validated['perpage'] ?? 10);

        $applications = $this->applicationService
            ->getCompanyApplications(
                $company,
                $perPage,
                $validated['status'] ?? null,
                isset($validated['category_id'])
                    ? (int) $validated['category_id']
                    : null,
                $validated['search'] ?? null
            );

        return ApplicationResource::collection($applications)
            ->additional([
                'success' => true,
                'message' => 'Applications retrieved successfully.',
            ]);
    }


    /**
     * Get applications for a specific company job.
     */
    public function jobIndex(Request $request, int $jobId)
    {
        $company = $this->getEmployerCompany($request->user());

        $perPage = min(
            max((int) $request->query('perpage', 10), 1),
            50
        );

        $applications = $this->applicationService
            ->getCompanyJobApplications($company, $jobId, $perPage);

        return ApplicationResource::collection($applications)
            ->additional([
                'success' => true,
                'message' => 'Applications retrieved successfully.',
            ]);
    }

    /**
     * Get application details for the Employer.
     * Automatically mark pending applications as reviewed.
     */
    public function show(Request $request, int $applicationId)
    {
        $company = $this->getEmployerCompany($request->user());

        $application = $this->applicationService
            ->findCompanyApplication($company, $applicationId);

        abort_unless(
            $application,
            404,
            'Application not found.'
        );

        // Automatically mark as reviewed on first detail view.
        if ($application->status === ApplicationStatus::Pending) {
            $this->applicationService->updateApplicationStatus(
                $application,
                [
                    'new_status' => ApplicationStatus::Reviewed->value,
                    'changed_by_company_id' => $company->id,
                    'note' => 'Automatically marked as reviewed when employer opened application details.',
                ]
            );

            // Reload application with updated status and relationships.
            $application = $this->applicationService
                ->findCompanyApplication($company, $applicationId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Application details retrieved successfully.',
            'data' => new ApplicationResource($application),
        ]);
    }

    /**
     * Update an application's status.
     */
    public function updateStatus(
        UpdateApplicationStatusRequest $request,
        int $applicationId
    ) {
        $company = $this->getEmployerCompany($request->user());

        $application = $this->applicationService
            ->findCompanyApplication($company, $applicationId);

        abort_unless(
            $application,
            404,
            'Application not found.'
        );

        $data = $request->validated();

        $data['changed_by_company_id'] = $company->id;

        $updatedApplication = $this->applicationService
            ->updateApplicationStatus($application, $data);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully.',
            'data' => new ApplicationResource($updatedApplication),
        ]);
    }

    /**
     * Get the authenticated Employer's Company.
     */
    private function getEmployerCompany(User $user): Company
    {
        $company = $this->companyService->getCompanyByEmployer($user);

        abort_unless(
            $company,
            403,
            'Please create your company profile first.'
        );

        return $company;
    }

      // Employer Applicant Statistics -Employer Dashboard
    public function stats(ApplicantStatsRequest $request)
    {
        $stats = $this->applicationService->getApplicantStats(
            $request->user(),
            $request->validated('month'),
            $request->validated('year')
        );

        return response()->json([
            'success' => true,
            'message' => 'Applicant statistics retrieved successfully.',
            'data' => $stats,
        ]);
    }
}
