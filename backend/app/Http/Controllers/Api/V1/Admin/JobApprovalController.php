<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\JobApprovalRequest;
use App\Http\Resources\Api\Jobs\JobResource;

use App\Services\Jobs\AdminJobService;
use Illuminate\Http\JsonResponse;
use App\Enums\JobStatus;
use App\Services\Activity\ActivityLogService;

class JobApprovalController extends Controller
{
    protected AdminJobService $jobService;

    public function __construct(
        AdminJobService $jobService,
        protected ActivityLogService $activityLogService
    ) {
        $this->jobService = $jobService;
    }

    /**
     * Display pending jobs.
     */
    public function index(): JsonResponse
    {
        $perPage = request()->integer('per_page', 5);
        $statusValue = request()->query('status', 'all');
        $search = trim(request()->query('search', ''));

        if ($statusValue === 'all') {
            $jobs = $this->jobService->getAllJobsForAdmin(
                $perPage,
                $search ?: null
            );
        } else {
            $status = JobStatus::tryFrom($statusValue);

            if (!$status || !in_array($status, [
                JobStatus::Pending,
                JobStatus::Published,
                JobStatus::Rejected,
            ], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid job status.',
                ], 422);
            }

            $jobs = $this->jobService->getJobsByStatusForAdmin(
                $status,
                $perPage,
                $search ?: null
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Jobs retrieved successfully.',
            'data' => JobResource::collection($jobs)->resolve(),
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
            ],
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
     * Approve or reject a job.
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

        // Log job approval or rejection
        $isApproved = $updatedJob->status === JobStatus::Published;

        $action = $isApproved
            ? 'Approved Job'
            : 'Rejected Job';

        $description = $isApproved
            ? "Approved job: {$updatedJob->title}"
            : "Rejected job: {$updatedJob->title}";

        $this->activityLogService->log(
            $action,
            $description,
            $updatedJob
        );

        return response()->json([
            'success' => true,
            'message' => $isApproved
                ? 'Job approved successfully.'
                : 'Job rejected successfully.',
            'data' => new JobResource($updatedJob),
        ]);
    }
}
