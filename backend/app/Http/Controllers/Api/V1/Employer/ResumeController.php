<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Services\Employer\ResumeService;
use Illuminate\Http\JsonResponse;
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
}
