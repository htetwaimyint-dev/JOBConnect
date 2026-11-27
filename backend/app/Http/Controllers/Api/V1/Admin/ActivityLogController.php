<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display all activity logs with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:2', 'max:20'],
        ]);

        $activities = ActivityLog::query()
            ->with('actor:id,name')
            ->latest()
            ->paginate($validated['per_page'] ?? 4)
            ->through(function ($activity) {
                return [
                    'id' => $activity->id,
                    'actor' => $activity->actor?->name ?? 'Unknown User',
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Activity logs retrieved successfully.',
            'data' => $activities->items(),
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }
}
