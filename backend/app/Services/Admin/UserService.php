<?php

namespace App\Services\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Exception;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getCompanies(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->getUsersByRole(
            UserRole::EMPLOYER,
            $search,
            $perPage
        );
    }

    public function getJobSeekers(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->getUsersByRole(
            UserRole::JOB_SEEKER,
            $search,
            $perPage
        );
    }

    // DELETE /admin/companies/{id}
    public function deleteCompany(int $id): void
    {
        $company = User::where('id', $id)->where('role', 'employer')->first();

        if (!$company) {
            throw new Exception("Company not found", 404);
        }

        $company->delete(); 
    }

    // DELETE /admin/job-seekers/{id}
    public function deleteJobSeeker(int $id): void
    {
        $jobSeeker = User::where('id', $id)->where('role', 'job_seeker')->first();

        if (!$jobSeeker) {
            throw new Exception("Job Seeker not found", 404);
        }

        $jobSeeker->delete(); 
    }

}
