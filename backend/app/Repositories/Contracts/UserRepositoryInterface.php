<?php

namespace App\Repositories\Contracts;

use App\Enums\UserRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getUsersByRole(UserRole $role, ?string $search = null, int $perPage = 10): LengthAwarePaginator;
}
