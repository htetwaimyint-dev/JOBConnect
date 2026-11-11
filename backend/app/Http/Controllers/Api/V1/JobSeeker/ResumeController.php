<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JobSeeker\ResumeRequest;
use App\Http\Resources\Api\JobSeeker\ResumeResource;
use App\Models\User;
use App\Services\JobSeeker\ResumeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ResumeController extends Controller
{
    public function __construct(
        private ResumeService $resumeService
    ) {}

    /**
     * Get all resumes belonging to the authenticated job seeker.
     */
    public function index(Request $request): JsonResponse
    {
        $resumes = $this->resumeService->getAll(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Resumes retrieved successfully.',
            'data' => ResumeResource::collection($resumes),
        ]);
    }

    /**
     * Upload a new resume.
     */
    public function store(ResumeRequest $request): JsonResponse
    {
        $resume = $this->resumeService->create(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Resume uploaded successfully.',
            'data' => new ResumeResource($resume),
        ], 201);
    }

    /**
     * Get a specific resume.
     */
    public function show(
        Request $request,
        int $resume
    ): JsonResponse {
        $resumeData = $this->resumeService->findByUser(
            $request->user(),
            $resume
        );

        if (!$resumeData) {
            return response()->json([
                'success' => false,
                'message' => 'Resume not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resume retrieved successfully.',
            'data' => new ResumeResource($resumeData),
        ]);
    }

    /**
     * Update an existing resume.
     */
    public function update(
        ResumeRequest $request,
        int $resume
    ): JsonResponse {
        $resumeData = $this->resumeService->findByUser(
            $request->user(),
            $resume
        );

        if (!$resumeData) {
            return response()->json([
                'success' => false,
                'message' => 'Resume not found.',
            ], 404);
        }

        $updatedResume = $this->resumeService->update(
            $resumeData,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Resume updated successfully.',
            'data' => new ResumeResource($updatedResume),
        ]);
    }
    //======Download Resume======
    public function download(
        Request $request,
        int $resume
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse {

        $resumeData = $this->resumeService->findByUser(
            $request->user(),
            $resume
        );

        if (!$resumeData) {
            return response()->json([
                'success' => false,
                'message' => 'Resume not found.',
            ], 404);
        }

        if (!Storage::disk('local')->exists($resumeData->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Resume file not found.',
            ], 404);
        }

        return response()->download(
            Storage::disk('local')->path($resumeData->file_path)
        );
    }


    /**
     * Delete an existing resume.
     */
    public function destroy(
        Request $request,
        int $resume
    ): JsonResponse {
        $resumeData = $this->resumeService->findByUser(
            $request->user(),
            $resume
        );

        if (!$resumeData) {
            return response()->json([
                'success' => false,
                'message' => 'Resume not found.',
            ], 404);
        }

        $this->resumeService->delete($resumeData);

        return response()->json([
            'success' => true,
            'message' => 'Resume deleted successfully.',
        ]);
    }
}
