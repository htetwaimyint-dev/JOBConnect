<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\JobApprovalRequest;
use App\Models\Job;
use Illuminate\Http\JsonResponse;


class JobApprovalController extends Controller
{
    /**
     * Display pending jobs.
     */
    public function index(): JsonResponse
    {
        $jobs = Job::with([
            'company:id,name,logo,city',
            'category:id,name',
        ])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Pending jobs retrieved successfully.',
            'data' => $jobs,
        ]);
    }

    /**
     * Display a specific job.
     */
    public function show(Job $job): JsonResponse
    {
        $job->load([
            'company:id,name,logo,city',
            'category:id,name',
            'approvedBy:id,name,email',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job details retrieved successfully.',
            'data' => $job,
        ]);
    }

    /**
     * Approve or reject a job.
     */
    public function update(
        JobApprovalRequest $request,
        Job $job
    ): JsonResponse {
        $validated = $request->validated();

        $status = $validated['status'];

        $job->update([
            'status' => $status,
            'approved_by' => $status === 'published'
                ? $request->user()->id
                : null,
            'approved_at' => $status === 'published'
                ? now()
                : null,
            'rejection_reason' => $status === 'rejected'
                ? $validated['rejection_reason']
                : null,
        ]);

        $job->refresh()->load([
            'company:id,name,logo,city',
            'category:id,name',
            'approvedBy:id,name,email',
        ]);

        return response()->json([
            'success' => true,
            'message' => $status === 'published'
                ? 'Job approved successfully.'
                : 'Job rejected successfully.',
            'data' => $job,
        ]);
    }
}
