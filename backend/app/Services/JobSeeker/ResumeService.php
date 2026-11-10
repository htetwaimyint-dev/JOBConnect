<?php

namespace App\Services\JobSeeker;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Contracts\ResumeRepositoryInterface;

class ResumeService
{
    public function __construct(
        private ResumeRepositoryInterface $resumeRepository
    ) {}

    /**
     * Get all resumes belonging to a job seeker.
     */
    public function getAll(User $user)
    {
        return $this->resumeRepository->getAllByUser($user);
    }

    /**
     * Find a resume belonging to a job seeker.
     */
    public function findByUser(
        User $user,
        int $resumeId
    ): ?Resume {
        return $this->resumeRepository->findByUser(
            $user,
            $resumeId
        );
    }

    /**
     * Upload and create a new resume.
     */
    public function create(User $user, array $data): Resume
    {
        $file = $data['file'];

        // Store file in private storage.
        $filePath = $file->store('resumes', 'local');

        unset($data['file']);

        $data['file_path'] = $filePath;
        $data['file_type'] = $file->getClientOriginalExtension();

        try {
            return DB::transaction(function () use ($user, $data) {

                // Ensure only one default resume per user.
                if ($data['is_default'] ?? false) {
                    $this->resumeRepository->clearDefault($user);
                }

                return $this->resumeRepository->create(
                    $user,
                    $data
                );
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($filePath);

            throw $exception;
        }
    }

    /**
     * Update an existing resume.
     */
    public function update(Resume $resume, array $data): Resume
    {
        $newFilePath = null;
        $oldFilePath = $resume->file_path;

        if (isset($data['file'])) {
            $file = $data['file'];

            $newFilePath = $file->store('resumes', 'local');

            unset($data['file']);

            $data['file_path'] = $newFilePath;
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        try {
            $updatedResume = DB::transaction(function () use ($resume, $data) {

                if ($data['is_default'] ?? false) {
                    $this->resumeRepository->clearDefault(
                        $resume->jobSeeker
                    );
                }

                return $this->resumeRepository->update(
                    $resume,
                    $data
                );
            });

            // Delete old file only after successful update.
            if ($newFilePath !== null) {
                Storage::disk('local')->delete($oldFilePath);
            }

            return $updatedResume;
        } catch (\Throwable $exception) {

            // Remove newly uploaded file if update fails.
            if ($newFilePath !== null) {
                Storage::disk('local')->delete($newFilePath);
            }

            throw $exception;
        }
    }

    /**
     * Delete a resume and its file.
     */
    public function delete(Resume $resume): bool
    {
        $filePath = $resume->file_path;

        $deleted = $this->resumeRepository->delete($resume);

        if ($deleted) {
            Storage::disk('local')->delete($filePath);
        }

        return $deleted;
    }
}
