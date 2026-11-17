<?php

namespace App\Services\Jobs;

use App\Models\Job;
use App\Models\User;
use App\Repositories\Contracts\SavedJobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SavedJobService
{
    public function __construct(
        private SavedJobRepositoryInterface $savedJobRepository
    ) {}

    /**
     * Save a published job.
     */
    public function saveJob(User $user, int $jobId): bool
    {
        $job = Job::query()
            ->where('status', 'published')
            ->findOrFail($jobId);

        return $this->savedJobRepository->save($user, $job);
    }

    /**
     * Get jobs saved by the authenticated user.
     */
    public function getMySavedJobs(
        User $user,
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->savedJobRepository->getByUser(
            $user,
            $perPage
        );
    }

    /**
     * Remove a saved job.
     */
    public function removeSavedJob(
        User $user,
        int $jobId
    ): bool {
        return $this->savedJobRepository->remove(
            $user,
            $jobId
        );
    }
}
