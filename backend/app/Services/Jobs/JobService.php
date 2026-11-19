<?php

namespace App\Services\Jobs;

use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
class JobService
{
    public function __construct(
        protected CompanyRepositoryInterface $companyRepository,
        protected JobRepositoryInterface $jobRepository
    ) {}

    public function getCompanyJobsForJobseeker(int $companyId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->jobRepository->getPublishedJobsByCompany($companyId, $filters, $perPage);
    }
}
