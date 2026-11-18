<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Applications\StoreApplicationRequest;
use App\Http\Resources\Api\Applications\ApplicationResource;
use App\Services\Applications\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Enums\ApplicationStatus;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationService $applicationService
    ) {}

    /**
     * Display applications submitted by the authenticated Job Seeker.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status' => [
                'sometimes',
                'string',
                Rule::in(array_column(ApplicationStatus::cases(), 'value')),
            ],
            'search' => ['sometimes', 'string', 'max:255'],
        ]);

        $applications = $this->applicationService->getMyApplications(
            $request->user(),
            $validated['per_page'] ?? 10,
            $validated['status'] ?? null,
            $validated['search'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Applications retrieved successfully.',
            'data' => ApplicationResource::collection($applications),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
            ],
        ]);
    }

    /**
     * Submit a new job application.
     */
    public function store(
        StoreApplicationRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $data['job_seeker_id'] = $request->user()->id;
        $data['status'] = ApplicationStatus::Pending->value;
        $data['applied_at'] = now();

        $application = $this->applicationService->submitApplication(
            $data
        );

        $application->load([
            'job.company',
            'resume',
            'answers.jobQuestion',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'data' => new ApplicationResource($application),
        ], 201);
    }

    /**
     * Show one application detail belonging to the authenticated Job Seeker.
     */
    public function show(Request $request, int $application): JsonResponse
    {
        $application = $this->applicationService->getMyApplicationDetail(
            $request->user(),
            $application
        );

        return response()->json([
            'success' => true,
            'message' => 'Application details retrieved successfully.',
            'data' => new ApplicationResource($application),
        ]);
    }

    /**
     * Withdraw a pending application.
     */
    public function withdraw(
        Request $request,
        int $application
    ): JsonResponse {

        $withdrawnApplication = $this->applicationService
            ->withdrawMyApplication(
                $request->user(),
                $application
            );

        $withdrawnApplication->load([
            'job.company',
            'resume',
            'answers.jobQuestion',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application withdrawn successfully.',
            'data' => new ApplicationResource($withdrawnApplication),
        ]);
    }
}
