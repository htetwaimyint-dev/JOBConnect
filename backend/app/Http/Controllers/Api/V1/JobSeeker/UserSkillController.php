<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JobSeeker\UserSkillRequest;
use App\Services\JobSeeker\UserSkillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\Api\JobSeeker\SkillResource;

class UserSkillController extends Controller
{
    public function __construct(
        private UserSkillService $skillService
    ) {}

    // Get all skills
    public function index(Request $request): JsonResponse
    {
        $skills = $this->skillService->getSkills(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Skills retrieved successfully.',
            'data' => SkillResource::collection($skills),
        ]);
    }

    // Create skill
    public function store(UserSkillRequest $request): JsonResponse
    {
        $skill = $this->skillService->createSkill(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Skill created successfully.',
            'data' => new SkillResource($skill),
        ], 201);
    }

    // Update skill
    public function update(
        UserSkillRequest $request,
        int $skill
    ): JsonResponse {
        $updatedSkill = $this->skillService->updateSkill(
            $request->user(),
            $skill,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Skill updated successfully.',
            'data' => new SkillResource($updatedSkill),
        ]);
    }

    // Delete skill
    public function destroy(
        Request $request,
        int $skill
    ): JsonResponse {
        $this->skillService->deleteSkill(
            $request->user(),
            $skill
        );

        return response()->json([
            'success' => true,
            'message' => 'Skill deleted successfully.',
        ]);
    }
}
