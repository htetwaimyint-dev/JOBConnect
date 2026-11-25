<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
   public function getUsersByRole(
        UserRole $role,
        ?string $search = null,
        ?string $status = null,
        int $perPage = 10
    ): LengthAwarePaginator;
    
    public function updateEmployerStatus(
        int $id,
        string $status
    ): User;

    public function updateJobSeekerStatus(
    int $id,
    string $status
): User;
}
