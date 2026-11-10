<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JobSeeker\ExperienceRequest;
use App\Http\Resources\Api\ExperienceResource;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Get all experiences of authenticated job seeker.
     */
    public function index(Request $request)
    {
        $experiences = $request->user()
            ->experiences()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Experience records retrieved successfully.',
            'data' => ExperienceResource::collection($experiences),
        ], 200);
    }

    /**
     * Create a new experience.
     */
    public function store(ExperienceRequest $request)
    {
        $experience = $request->user()
            ->experiences()
            ->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Experience record created successfully.',
            'data' => new ExperienceResource($experience),
        ], 201);
    }

    /**
     * Get a specific experience.
     */
    public function show(Request $request, int $experience)
    {
        $experience = $request->user()
            ->experiences()
            ->find($experience);

        if (!$experience) {
            return response()->json([
                'success' => false,
                'message' => 'Experience record not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Experience record retrieved successfully.',
            'data' => new ExperienceResource($experience),
        ], 200);
    }

    /**
     * Update an existing experience.
     */
    public function update(
        ExperienceRequest $request,
        int $experience
    ) {
        $experience = $request->user()
            ->experiences()
            ->find($experience);

        if (!$experience) {
            return response()->json([
                'success' => false,
                'message' => 'Experience record not found.',
            ], 404);
        }

        $experience->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Experience record updated successfully.',
            'data' => new ExperienceResource($experience->fresh()),
        ], 200);
    }

    /**
     * Delete an experience.
     */
    public function destroy(Request $request, int $experience)
    {
        $experience = $request->user()
            ->experiences()
            ->find($experience);

        if (!$experience) {
            return response()->json([
                'success' => false,
                'message' => 'Experience record not found.',
            ], 404);
        }

        $experience->delete();

        return response()->json([
            'success' => true,
            'message' => 'Experience record deleted successfully.',
        ], 200);
    }
}
