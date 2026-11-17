<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface CompanyRepositoryInterface
{
    public function getAll(?string $search): Collection;
    public function findById(int $id): ?Company;
    public function findByEmployer(User $user): ?Company;

    public function createOrUpdate(
        User $user,
        array $data
    ): Company;
}