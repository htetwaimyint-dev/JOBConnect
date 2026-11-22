<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface CompanyRepositoryInterface
{
    //get companies (for jobseeker)
    public function getAll(?string $search): Collection;
    //company details(for jobseeker)
    public function findById(int $id): ?Company;
    //get company's jobs (for jobseeker)
    public function getCompanyWithJobs(int $companyId): ?Company;
    public function findByEmployer(User $user): ?Company;

    public function createOrUpdate(
        User $user,
        array $data
    ): Company;
}