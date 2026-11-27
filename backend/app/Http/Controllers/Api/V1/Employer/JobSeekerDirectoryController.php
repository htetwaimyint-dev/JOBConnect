<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\Employer\JobSeekerDirectoryResource;
use App\Models\User;
use Illuminate\Http\Request;

class JobSeekerDirectoryController extends Controller
{
    /**
     * Display all job seekers.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|in:2,5,10,15,20',
        ]);

        $query = User::query()
            ->where('role', UserRole::JOB_SEEKER->value)
            ->with([
                'jobSeekerProfile',
                'skills',
                'educations',
            ]);

        if (!empty($validated['search'])) {
            $keyword = $validated['search'];

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhereHas('jobSeekerProfile', function ($profileQuery) use ($keyword) {
                        $profileQuery
                            ->where('professional_title', 'like', "%{$keyword}%")
                            ->orWhere('city', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('skills', function ($skillQuery) use ($keyword) {
                        $skillQuery->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $perPage = $validated['per_page'] ?? 10;

        $jobSeekers = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return JobSeekerDirectoryResource::collection($jobSeekers)
            ->additional([
                'success' => true,
                'message' => 'Job seekers retrieved successfully.',
            ]);
    }

    /**
     * Display a single job seeker's profile for employers.
     */
    public function show(int $id)
    {
        $jobSeeker = User::query()
            ->where('role', UserRole::JOB_SEEKER->value)
            ->with([
                'jobSeekerProfile',
                'skills',
                'educations',
                'experiences',
                'resumes' => function ($query) {
                    $query->where('visibility', 'public')
                        ->latest();
                },
            ])
            ->findOrFail($id);

        return (new JobSeekerDirectoryResource($jobSeeker))
            ->additional([
                'success' => true,
                'message' => 'Job seeker profile retrieved successfully.',
            ]);
    }
}
