<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JobSeeker\UpdateProfileRequest;
use App\Http\Resources\Api\JobSeekerProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Get authenticated job seeker's profile.
     */
    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()
            ->jobSeekerProfile()
            ->first();

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

    /**
     * Update authenticated job seeker's profile.
     */
    public function update(
        UpdateProfileRequest $request
    ): JsonResponse {
        $profile = DB::transaction(function () use ($request) {
            return $request->user()
                ->jobSeekerProfile()
                ->updateOrCreate(
                    [],
                    $request->validated()
                );
        });

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => new JobSeekerProfileResource($profile),
        ]);
    }
}
