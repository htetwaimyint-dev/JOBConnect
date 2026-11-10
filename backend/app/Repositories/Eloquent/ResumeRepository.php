<?php

namespace App\Repositories\Eloquent;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\ResumeRepositoryInterface;

class ResumeRepository implements ResumeRepositoryInterface
{
    /**
     * Get all resumes belonging to a job seeker.
     */
    public function getAllByUser(User $user): Collection
    {
        return $user->resumes()
            ->latest()
            ->get();
    }

    /**
     * Find a specific resume belonging to a job seeker.
     */
    public function findByUser(
        User $user,
        int $resumeId
    ): ?Resume {
        return $user->resumes()
            ->find($resumeId);
    }

    /**
     * Find a specific resume by its ID.
     */ public function findById(int $resumeId): ?Resume
    {
        return Resume::find($resumeId);
    }
    /**
     * Create a new resume.
     */
    public function create(
        User $user,
        array $data
    ): Resume {
        return $user->resumes()
            ->create($data);
    }

    /**
     * Update an existing resume.
     */
    public function update(
        Resume $resume,
        array $data
    ): Resume {
        $resume->update($data);

        return $resume->fresh();
    }

    /**
     * Delete an existing resume.
     */
    public function delete(Resume $resume): bool
    {
        return $resume->delete();
    }

    /**
     * Clear the default status of all resumes belonging to a user.
     */
    public function clearDefault(User $user): void
    {
        $user->resumes()
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }
}
