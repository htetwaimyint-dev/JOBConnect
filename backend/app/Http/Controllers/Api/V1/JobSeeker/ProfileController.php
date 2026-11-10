<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JobSeeker\UpdateProfileRequest;
use App\Http\Resources\Api\JobSeekerProfileResource;
use App\Services\JobSeeker\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function show(Request $request): JsonResponse
    {
        $profile = $this->profileService->getProfile(
            $request->user()
        );

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully.',
            'data' => new JobSeekerProfileResource($profile),
        ]);
    }

    public function update(
        UpdateProfileRequest $request
    ): JsonResponse {

        $data = $request->validated();

        $profile = $this->profileService->updateProfile(
            $request->user(),
            $data,
            $request->file('profile_photo'),
            (bool) ($data['remove_profile_photo'] ?? false)
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => new JobSeekerProfileResource($profile),
        ]);
    }
}
