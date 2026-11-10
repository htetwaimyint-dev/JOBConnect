<?php

namespace App\Repositories\Contracts;

use App\Models\JobSeekerProfile;
use App\Models\User;

interface JobSeekerProfileRepositoryInterface
{
    public function findByUser(User $user): ?JobSeekerProfile;

    public function createOrUpdate(
        User $user,
        array $data
    ): JobSeekerProfile;
}
