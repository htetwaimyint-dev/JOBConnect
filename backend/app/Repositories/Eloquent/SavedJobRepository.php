<?php

namespace App\Repositories\Eloquent;

use App\Models\Job;
use App\Models\SavedJob;
use App\Models\User;
use App\Repositories\Contracts\SavedJobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SavedJobRepository implements SavedJobRepositoryInterface
{
    public function save(User $user, Job $job): bool
    {
        SavedJob::firstOrCreate([
            'user_id' => $user->id,
            'job_id' => $job->id,
        ]);

        return true;
    }


    public function getByUser(
        User $user,
        int $perPage = 10
    ): LengthAwarePaginator {
        return SavedJob::query()
            ->with(['job.company', 'job.category'])
            ->where('user_id', $user->id)
            ->whereHas('job', function ($query) {
                $query->where('status', 'published');
            })
            ->latest()
            ->paginate($perPage);
    }


    public function remove(User $user, int $jobId): bool
    {
        return SavedJob::query()
            ->where('user_id', $user->id)
            ->where('job_id', $jobId)
            ->delete() > 0;
    }
}
