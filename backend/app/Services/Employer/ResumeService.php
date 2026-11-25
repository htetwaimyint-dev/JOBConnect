<?php

namespace App\Services\Employer;

use App\Enums\UserRole;
use App\Repositories\Contracts\ResumeRepositoryInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Process\Process;

class ResumeService
{
    public function __construct(
        private ResumeRepositoryInterface $resumeRepository
    ) {}

    public function download(int $resumeId): string
    {
        $resume = $this->getAuthorizedResume($resumeId);

        if (!Storage::disk('local')->exists($resume->file_path)) {
            throw new HttpException(404, 'Resume file not found.');
        }

        return Storage::disk('local')->path($resume->file_path);
    }

    public function preview(int $resumeId): array
    {
        $resume = $this->getAuthorizedResume($resumeId);

        if (!Storage::disk('local')->exists($resume->file_path)) {
            throw new HttpException(404, 'Resume file not found.');
        }

        $sourcePath = Storage::disk('local')->path($resume->file_path);

        $extension = strtolower(
            pathinfo($sourcePath, PATHINFO_EXTENSION)
        );

        // PDF can be previewed directly.
        if ($extension === 'pdf') {
            return [
                'path' => $sourcePath,
                'temporary' => false,
                'directory' => null,
            ];
        }

        // Only DOCX conversion is supported.
        if ($extension !== 'docx') {
            throw new HttpException(
                422,
                'Only PDF and DOCX resumes can be previewed.'
            );
        }

        $workingDirectory = storage_path(
            'app/private/resume-previews/' . Str::uuid()
        );

        File::ensureDirectoryExists($workingDirectory);

        $binary = config(
            'services.libreoffice.binary',
            env('LIBREOFFICE_PATH', 'soffice')
        );

        $profilePath = $workingDirectory . '/lo-profile';

        File::ensureDirectoryExists($profilePath);

        $process = new Process([
            $binary,
            '-env:UserInstallation=file://' . $profilePath,
            '--headless',
            '--convert-to',
            'pdf',
            '--outdir',
            $workingDirectory,
            $sourcePath,
        ]);

        $process->setTimeout(60);
        $process->run();

        $pdfPath = $workingDirectory . '/'
            . pathinfo($sourcePath, PATHINFO_FILENAME)
            . '.pdf';

        if (!$process->isSuccessful() || !File::exists($pdfPath)) {
            File::deleteDirectory($workingDirectory);

            throw new HttpException(
                500,
                'Unable to convert DOCX resume to PDF.'
            );
        }

        return [
            'path' => $pdfPath,
            'temporary' => true,
            'directory' => $workingDirectory,
        ];
    }

    private function getAuthorizedResume(int $resumeId)
    {
        $resume = $this->resumeRepository->findById($resumeId);

        if (!$resume) {
            throw new HttpException(404, 'Resume not found.');
        }

        if ($resume->visibility !== 'public') {
            throw new HttpException(
                403,
                'You are not allowed to access this resume.'
            );
        }

        if (
            !$resume->jobSeeker ||
            $resume->jobSeeker->role !== UserRole::JOB_SEEKER
        ) {
            throw new HttpException(
                403,
                'This resume is not available.'
            );
        }

        return $resume;
    }
}
