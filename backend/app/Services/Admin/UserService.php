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

    // Get Companies
    public function getCompanies(
        ?string $search = null,
        ?string $status = null,
        int $perPage = 10
    ): LengthAwarePaginator {

        return $this->userRepository->getUsersByRole(
            UserRole::EMPLOYER,
            $search,
            $status,
            $perPage
        );
    }

    public function getCompanyStats(): array
    {
        return [
            'total' => User::withTrashed()
                ->where('role', UserRole::EMPLOYER->value)
                ->count(),

            'active' => User::where('role', UserRole::EMPLOYER->value)
                ->count(),

            'suspended' => User::onlyTrashed()
                ->where('role', UserRole::EMPLOYER->value)
                ->count(),
        ];
    }

    public function getJobSeekers(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->getUsersByRole(
            UserRole::JOB_SEEKER,
            $search,
            $perPage
        );
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

    public function suspendEmployer(int $id): User
    {
        return $this->userRepository->updateEmployerStatus(
            $id,
            'suspended'
        );
    }

    public function restoreEmployer(int $id): User
    {
        return $this->userRepository->updateEmployerStatus(
            $id,
            'active'
        );
    }
}
