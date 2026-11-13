<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use App\Models\User;

interface CompanyRepositoryInterface
{
    public function findByEmployer(User $user): ?Company;

    public function createOrUpdate(
        User $user,
        array $data
    ): Company;
}