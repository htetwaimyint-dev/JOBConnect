<?php

namespace App\Repositories\Eloquent;

use App\Models\JobSeekerProfile;
use App\Models\User;
use App\Repositories\Contracts\JobSeekerProfileRepositoryInterface;

class JobSeekerProfileRepository implements JobSeekerProfileRepositoryInterface
{
    public function findByUser(User $user): ?JobSeekerProfile
    {
        return $user->jobSeekerProfile()->first();
    }

    public function createOrUpdate(
        User $user,
        array $data
    ): JobSeekerProfile {
        return $user->jobSeekerProfile()->updateOrCreate(
            [],
            $data
        );
    }
}
