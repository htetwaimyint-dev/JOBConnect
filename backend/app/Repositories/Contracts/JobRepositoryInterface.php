<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use App\Models\Job;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface JobRepositoryInterface 
{
    public function getByCompany(
        Company $company, 
        int $perPage = 10
    ): LengthAwarePaginator;

    public function findById(
        Company $company,
        int $jobId
    ): ?Job;

    public function create(
        Company $company,
        array $data
    ): Job;

    public function update(
        Job $job,
        array $data
    ): Job;

    public function delete(Job $job): bool;
}

