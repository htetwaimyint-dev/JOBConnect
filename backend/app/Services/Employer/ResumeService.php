<?php

namespace App\Services\Employer;

use App\Enums\UserRole;
use App\Models\Resume;
use App\Repositories\Contracts\ResumeRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResumeService
{
    public function __construct(
        private ResumeRepositoryInterface $resumeRepository
    ) {}

    public function download(int $resumeId): string
    {
        $resume = $this->resumeRepository->findById($resumeId);

        if (!$resume) {
            throw new HttpException(404, 'Resume not found.');
        }

        // Only public resumes can be downloaded by employers.
        if ($resume->visibility !== 'public') {
            throw new HttpException(
                403,
                'You are not allowed to download this resume.'
            );
        }

        // Ensure the resume belongs to a Job Seeker.
        if (
            !$resume->jobSeeker ||
            $resume->jobSeeker->role !== UserRole::JOB_SEEKER
        ) {
            throw new HttpException(
                403,
                'This resume is not available.'
            );
        }

        // Ensure the resume file exists.
        if (!Storage::disk('local')->exists($resume->file_path)) {
            throw new HttpException(404, 'Resume file not found.');
        }

        return Storage::disk('local')->path($resume->file_path);
    }
}
