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

    public function deleteUser(int $userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            throw new Exception("User not found", 404);
        }   
        return $user->delete();    

    }
}
