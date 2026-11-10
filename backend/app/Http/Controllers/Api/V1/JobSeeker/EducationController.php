<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JobSeeker\EducationRequest;
use App\Http\Resources\Api\EducationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EducationController extends Controller
{
    /**
     * Display all education records of authenticated job seeker.
     */
    public function index(Request $request)
    {
        $educations = $request->user()
            ->educations()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Education records retrieved successfully.',
            'data' => EducationResource::collection($educations),
        ], 200);
    }
    /**
     * Store a new education record.
     */
    public function store(EducationRequest $request)
    {
        $education = $request->user()
            ->educations()
            ->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Education record created successfully.',
            'data' => new EducationResource($education),
        ], 201);
    }
    /**
     * Display a specific education record.
     */
    public function show(Request $request, int $education)
    {
        $education = $request->user()
            ->educations()
            ->find($education);

        if (!$education) {
            return response()->json([
                'success' => false,
                'message' => 'Education record not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Education record retrieved successfully.',
            'data' => new EducationResource($education),
        ], 200);
    }
    /**
     * Update a specific education record.
     */
    public function update(EducationRequest $request, int $education)
    {
        $education = $request->user()
            ->educations()
            ->find($education);

        if (!$education) {
            return response()->json([
                'success' => false,
                'message' => 'Education record not found.',
            ], 404);
        }

        $education->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Education record updated successfully.',
            'data' => new EducationResource($education->fresh()),
        ], 200);
    }
    /**
     * Delete a specific education record.
     */
    public function destroy(Request $request, int $education)
    {
        $education = $request->user()
            ->educations()
            ->find($education);

        if (!$education) {
            return response()->json([
                'success' => false,
                'message' => 'Education record not found.',
            ], 404);
        }

        $education->delete();

        return response()->json([
            'success' => true,
            'message' => 'Education record deleted successfully.',
        ], 200);
    }
}
