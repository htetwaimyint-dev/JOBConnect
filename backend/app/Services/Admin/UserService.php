<?php

namespace App\Services;

use App\Enums\UserRole;
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
}
