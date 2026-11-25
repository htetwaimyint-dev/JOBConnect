<?php

namespace App\Http\Controllers\Api\V1\Interviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Interviews\StoreInterviewRequest;
use App\Http\Requests\Api\Interviews\UpdateInterviewRequest;
use App\Http\Requests\Api\Interviews\UpdateInterviewStatusRequest;
use App\Http\Resources\Api\Applications\ApplicationResource;
use App\Http\Resources\Api\Interviews\InterviewResource;
use App\Models\Company;
use App\Services\Interviews\InterviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function __construct(
        private readonly InterviewService $interviewService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $interviews = $this->interviewService->getInterviews(
            $this->currentCompany($request),
            $request->only(['search', 'status', 'interview_type', 'from', 'to']),
        );

        return response()->json([
            'success' => true,
            'message' => 'Interviews retrieved successfully.',
            'data' => InterviewResource::collection($interviews)->resolve($request),
        ]);
    }

    public function availableApplications(Request $request): JsonResponse
    {
        $applications = $this->interviewService->getAvailableApplications(
            $this->currentCompany($request),
        );

        return response()->json([
            'success' => true,
            'message' => 'Available applications retrieved successfully.',
            'data' => ApplicationResource::collection($applications)->resolve($request),
        ]);
    }

    public function show(Request $request, int $interviewId): JsonResponse
    {
        $interview = $this->interviewService->getInterview(
            $this->currentCompany($request),
            $interviewId,
        );

        return response()->json([
            'success' => true,
            'message' => 'Interview retrieved successfully.',
            'data' => (new InterviewResource($interview))->resolve($request),
        ]);
    }

    public function store(StoreInterviewRequest $request): JsonResponse
    {
        $interview = $this->interviewService->createInterview(
            $this->currentCompany($request),
            $request->validated(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Interview scheduled successfully.',
            'data' => (new InterviewResource($interview))->resolve($request),
        ], 201);
    }

    public function update(UpdateInterviewRequest $request, int $interviewId): JsonResponse
    {
        $interview = $this->interviewService->updateInterview(
            $this->currentCompany($request),
            $interviewId,
            $request->validated(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Interview updated successfully.',
            'data' => (new InterviewResource($interview))->resolve($request),
        ]);
    }

    public function updateStatus(UpdateInterviewStatusRequest $request, int $interviewId): JsonResponse
    {
        $interview = $this->interviewService->updateStatus(
            $this->currentCompany($request),
            $interviewId,
            $request->validated('status'),
        );

        return response()->json([
            'success' => true,
            'message' => 'Interview status updated successfully.',
            'data' => (new InterviewResource($interview))->resolve($request),
        ]);
    }

    private function currentCompany(Request $request): Company
    {
        $company = $request->user()?->company;

        abort_unless($company instanceof Company, 403, 'Your employer account is not linked to a company.');

        return $company;
    }
}
