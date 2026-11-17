<?php

namespace App\Services\Applications;

use App\Models\Application;
use App\Models\Company;
use App\Models\User;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApplicationService
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepository
    ) {}

    /**
     * Get applications belonging to a Job Seeker.
     */
    public function getMyApplications(
        User $jobSeeker,
        int $perPage = 10,
        ?string $status = null,
        ?string $search = null
    ): LengthAwarePaginator {
        return $this->applicationRepository->getByJobSeeker(
            $jobSeeker,
            $perPage,
            $status,
            $search
        );
    }

    /**
     * Get applications belonging to a Company's jobs.
     */
    public function getCompanyApplications(
        Company $company,
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->applicationRepository->getByCompany(
            $company,
            $perPage
        );
    }


    /**
     * Get applications for a specific Company job.
     */
    public function getCompanyJobApplications(
        Company $company,
        int $jobId,
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->applicationRepository->getByCompanyJob(
            $company,
            $jobId,
            $perPage
        );
    }


    /**
     * Create a new application.
     */
    public function submitApplication(array $data): Application
    {
        return $this->applicationRepository->create($data);
    }

    /**
     * Find an application belonging to a Company.
     */
    public function findCompanyApplication(
        Company $company,
        int $applicationId
    ): ?Application {
        return $this->applicationRepository->findForCompany(
            $company,
            $applicationId
        );
    }

    /**
     * Update application status.
     */
    public function updateApplicationStatus(
        Application $application,
        array $data
    ): Application {
        return $this->applicationRepository->updateStatus(
            $application,
            $data
        );
    }
}
