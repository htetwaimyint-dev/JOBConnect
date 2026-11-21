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
            ->where('role', 'employer')
            ->count(),

        'active' => User::withTrashed()
            ->where('role', 'employer')
            ->where('status', 'active')
            ->count(),

        'suspended' => User::withTrashed()
            ->where('role', 'employer')
            ->where('status', 'suspended')
            ->count(),
    ];
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

    public function getJobSeekers(
    ?string $search = null,
    ?string $status = null,
    int $perPage = 5
): LengthAwarePaginator {
    return $this->userRepository->getUsersByRole(
        UserRole::JOB_SEEKER,
        $search,
        $status,
        $perPage
    );
}

public function getJobSeekerStats(): array
{
    $query = User::withTrashed()
        ->where('role', UserRole::JOB_SEEKER->value);

    return [
        'total' => (clone $query)->count(),

        'active' => (clone $query)
            ->where('status', 'active')
            ->count(),

        'suspended' => (clone $query)
            ->where('status', 'suspended')
            ->count(),
    ];
}

public function suspendJobSeeker(int $id): User
{
    return $this->userRepository->updateJobSeekerStatus(
        $id,
        'suspended'
    );
}

public function restoreJobSeeker(int $id): User
{
    return $this->userRepository->updateJobSeekerStatus(
        $id,
        'active'
    );
}

}
