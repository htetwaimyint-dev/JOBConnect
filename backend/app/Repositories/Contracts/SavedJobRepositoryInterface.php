<?php

namespace App\Repositories\Contracts;

use App\Models\Job;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SavedJobRepositoryInterface
{
    public function save(User $user, Job $job): bool;

    public function getByUser(
        User $user,
        int $perPage = 10
    ): LengthAwarePaginator;

    public function remove(User $user, int $jobId): bool;
}
