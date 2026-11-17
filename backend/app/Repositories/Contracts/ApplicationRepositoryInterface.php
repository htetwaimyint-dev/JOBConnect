<?php

namespace App\Repositories\Contracts;

use App\Models\Application;
use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ApplicationRepositoryInterface
{
    public function getByJobSeeker(
        User $jobSeeker,
        int $perPage = 10,
        ?string $status = null,
        ?string $search = null
    ): LengthAwarePaginator;

    public function getByCompany(
        Company $company,
        int $perPage = 10
    ): LengthAwarePaginator;


    public function getByCompanyJob(
        Company $company,
        int $jobId,
        int $perPage = 10
    ): LengthAwarePaginator;


    public function createWithAnswers(
        array $applicationData,
        array $answers
    ): Application;

    public function findForCompany(
        Company $company,
        int $applicationId
    ): ?Application;

    public function findForJobSeeker(
        User $jobSeeker,
        int $applicationId
    ): ?Application;

    public function updateStatus(
        Application $application,
        array $data
    ): Application;
}
