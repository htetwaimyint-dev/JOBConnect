<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Interviews\JobseekerRequestRescheduleRequest;
use App\Http\Resources\Api\Interviews\InterviewResource;
use App\Services\Interviews\InterviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function __construct(
        private readonly InterviewService $interviewService,
    ) {}

    /**
     * Display interviews submitted to the authenticated Job Seeker.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'sometimes', 'string'],
            'interview_type' => ['nullable', 'sometimes', 'string'],
            'search' => ['nullable', 'sometimes', 'string', 'max:255'],
            'from' => ['sometimes', 'nullable', 'date'],
            'to' => ['sometimes', 'nullable', 'date'],
        ]);

        $interviews = $this->interviewService->getJobseekerInterviews(
            $request->user(),
            $validated,
        );

        return response()->json([
            'success' => true,
            'message' => 'Interviews retrieved successfully.',
            'data' => InterviewResource::collection($interviews)->resolve($request),
        ]);
    }

    /**
     * Request a reschedule for an interview belonging to the authenticated Job Seeker.
     */
    public function requestReschedule(
        JobseekerRequestRescheduleRequest $request,
        int $interviewId
    ): JsonResponse {
        $interview = $this->interviewService->requestReschedule(
            $request->user(),
            $interviewId,
            $request->validated(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Reschedule request submitted successfully.',
            'data' => (new InterviewResource($interview))->resolve($request),
        ]);
    }
}
