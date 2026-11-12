<?php

namespace App\Http\Controllers\Api\V1\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display published jobs for public visitors.
     */
    public function index(Request $request)
    {
        $jobs = Job::with([
            'company:id,name,logo,verification_status',
            'category:id,name',
        ])
            ->where('status', 'published')
            ->whereHas('company', function ($query) {
                $query->where('verification_status', 'verified');
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($companyQuery) use ($search) {
                            $companyQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('employment_type'), function ($query) use ($request) {
                $query->where('employment_type', $request->employment_type);
            })
            ->when($request->filled('workplace_type'), function ($query) use ($request) {
                $query->where('workplace_type', $request->workplace_type);
            })
            ->when($request->filled('location'), function ($query) use ($request) {
                $query->where('location', 'like', '%' . $request->location . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json($jobs);
    }

    /**
     * Display a single published job.
     */
    public function show(string $id)
    {
        $job = Job::with([
            'company:id,name,logo,tagline,description,website,industry,city,verification_status',
            'category:id,name',
        ])
            ->where('status', 'published')
            ->whereHas('company', function ($query) {
                $query->where('verification_status', 'verified');
            })
            ->findOrFail($id);

        return response()->json([
            'data' => $job,
        ]);
    }
}
