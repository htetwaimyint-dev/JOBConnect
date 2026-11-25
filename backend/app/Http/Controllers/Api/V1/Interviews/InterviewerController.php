<?php

namespace App\Http\Controllers\Api\V1\Interviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Interviews\StoreInterviewerRequest;
use App\Http\Requests\Api\Interviews\UpdateInterviewerRequest;
use App\Http\Resources\Api\Interviews\InterviewerResource;
use App\Models\Company;
use App\Services\Interviews\InterviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterviewerController extends Controller
{
    public function __construct(
        private readonly InterviewService $interviewService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $interviewers = $this->interviewService->getInterviewers($this->currentCompany($request));

        return response()->json([
            'success' => true,
            'message' => 'Interviewers retrieved successfully.',
            'data' => InterviewerResource::collection($interviewers)->resolve($request),
        ]);
    }

    public function store(StoreInterviewerRequest $request): JsonResponse
    {
        $interviewer = $this->interviewService->createInterviewer(
            $this->currentCompany($request),
            $request->validated(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Interviewer created successfully.',
            'data' => (new InterviewerResource($interviewer))->resolve($request),
        ], 201);
    }

    public function update(UpdateInterviewerRequest $request, int $interviewerId): JsonResponse
    {
        $interviewer = $this->interviewService->updateInterviewer(
            $this->currentCompany($request),
            $interviewerId,
            $request->validated(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Interviewer updated successfully.',
            'data' => (new InterviewerResource($interviewer))->resolve($request),
        ]);
    }

    public function destroy(Request $request, int $interviewerId): JsonResponse
    {
        $this->interviewService->deleteInterviewer($this->currentCompany($request), $interviewerId);

        return response()->json([
            'success' => true,
            'message' => 'Interviewer deleted successfully.',
            'data' => null,
        ]);
    }

    private function currentCompany(Request $request): Company
    {
        $company = $request->user()?->company;

        abort_unless($company instanceof Company, 403, 'Your employer account is not linked to a company.');

        return $company;
    }
}
