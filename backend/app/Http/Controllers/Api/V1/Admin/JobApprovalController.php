<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\JobApprovalRequest;
use App\Http\Resources\Api\Jobs\JobResource;
use App\Models\Job;
use App\Services\Jobs\AdminJobService;
use Illuminate\Http\JsonResponse;
use App\Enums\JobStatus;

class JobApprovalController extends Controller
{
    protected AdminJobService $jobService;

    public function __construct(AdminJobService $jobService)
    {
        $this->jobService = $jobService;
    }

    /**
     * Display pending jobs.
     */
    public function index(): JsonResponse
    {
        $jobs = $this->jobService->getPendingJobsForAdmin(10);

        return response()->json([
            'success' => true,
            'message' => 'Pending jobs retrieved successfully.',
            'data' => JobResource::collection($jobs),
        ]);
    }

    /**
     * Display a specific job.
     */
    public function show(int $jobId): JsonResponse
    {
        $job = $this->jobService->getJobForAdmin($jobId);

        return response()->json([
            'success' => true,
            'message' => 'Job details retrieved successfully.',
            'data' => new JobResource($job),
        ]);
    }

    /**
     * Approve or reject a job .
     */
    public function update(
        JobApprovalRequest $request,
        int $jobId
    ): JsonResponse {
        $job = $this->jobService->getJobForAdmin($jobId);

        $updatedJob = $this->jobService->updateJobApproval(
            $job,
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => $updatedJob->status === JobStatus::Published
                ? 'Job approved successfully.'
                : 'Job rejected successfully.',
            'data' => new JobResource($updatedJob),
        ]);
    }
}
