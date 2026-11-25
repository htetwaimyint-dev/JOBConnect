<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Services\Employer\ResumeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResumeController extends Controller
{
    public function __construct(
        private ResumeService $resumeService
    ) {}

    public function download(int $resume): BinaryFileResponse|JsonResponse
    {
        $filePath = $this->resumeService->download($resume);

        return response()->download($filePath);
    }

    public function preview(int $resume): BinaryFileResponse|JsonResponse
    {
        $preview = $this->resumeService->preview($resume);

        $response = response()->file($preview['path'], [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="resume.pdf"',
        ]);

        if ($preview['temporary']) {
            $response->deleteFileAfterSend(true);

            $directory = $preview['directory'];

            register_shutdown_function(
                static function () use ($directory) {
                    File::deleteDirectory($directory);
                }
            );
        }

        return $response;
    }
}
