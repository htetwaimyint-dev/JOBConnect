<?php

namespace App\Http\Controllers\Api\V1\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Jobs\JobQuestionResource;
use App\Services\Jobs\PublicJobService;
use Illuminate\Http\JsonResponse;

class JobApplicationQuestionController extends Controller
{
    public function __construct(
        private PublicJobService $jobService
    ) {}

    /**
     * Get screening questions when a Job Seeker applies for a job.
     */
    public function index(int $jobId): JsonResponse
    {
        $questions = $this->jobService->getApplicationQuestions($jobId);

        return response()->json([
            'success' => true,
            'message' => 'Application questions retrieved successfully.',
            'data' => JobQuestionResource::collection($questions),
        ]);
    }
}
