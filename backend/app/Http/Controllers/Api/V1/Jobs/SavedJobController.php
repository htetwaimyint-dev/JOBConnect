<?php

namespace App\Http\Controllers\Api\V1\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Jobs\SavedJobResource;
use App\Services\Jobs\SavedJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedJobController extends Controller
{
    public function __construct(
        private SavedJobService $savedJobService
    ) {}

    /**
     * Get authenticated user's saved jobs.
     */
    public function index(Request $request): JsonResponse
    {
        $savedJobs = $this->savedJobService->getMySavedJobs(
            $request->user(),
            (int) $request->query('per_page', 10)
        );

        return response()->json([
            'success' => true,
            'message' => 'Saved jobs retrieved successfully.',
            'data' => SavedJobResource::collection($savedJobs),
            'meta' => [
                'current_page' => $savedJobs->currentPage(),
                'last_page' => $savedJobs->lastPage(),
                'per_page' => $savedJobs->perPage(),
                'total' => $savedJobs->total(),
            ],
        ]);
    }

    /**
     * Save a job.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
        ]);

        $this->savedJobService->saveJob(
            $request->user(),
            $validated['job_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Job saved successfully.',
        ], 201);
    }

    /**
     * Remove a saved job.
     */
    public function destroy(Request $request, int $jobId): JsonResponse
    {
        $removed = $this->savedJobService->removeSavedJob(
            $request->user(),
            $jobId
        );

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'Saved job not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Saved job removed successfully.',
        ]);
    }
}
