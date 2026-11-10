<?php

namespace App\Services\Employer;

use App\Models\Resume;
use App\Repositories\Contracts\ResumeRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

        if ($resume->visibility !== 'public') {
            throw new HttpException(
                403,
                'You are not allowed to download this resume.'
            );
        }

        if (!Storage::disk('local')->exists($resume->file_path)) {
            throw new HttpException(404, 'Resume file not found.');
        }

        return Storage::disk('local')->path($resume->file_path);
    }
}
