<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Applications\UpdateApplicationStatusRequest;
use App\Http\Resources\Api\Applications\ApplicationResource;
use App\Models\Company;
use App\Models\User;
use App\Services\Applications\ApplicationService;
use App\Services\Employer\CompanyService;
use Illuminate\Http\Request;


class ApplicationController extends Controller
{
    public function __construct(
        protected ApplicationService $applicationService,
        protected CompanyService $companyService
    ) {}

    /**
     * Get applications for a specific company job.
     */
    public function index(Request $request, int $jobId)
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
}
