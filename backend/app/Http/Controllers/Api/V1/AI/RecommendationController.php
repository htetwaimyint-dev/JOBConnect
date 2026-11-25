<?php

namespace App\Http\Controllers\Api\V1\AI;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Jobs\JobResource;
use App\Jobs\GenerateRecommendationsJob;
use App\Services\AI\RecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class RecommendationController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $limit = min(
            max($request->integer('limit', 10), 1),
            20
        );

        $isStale = $this->recommendationService
            ->needsRefresh($user);

        if ($isStale) {
            GenerateRecommendationsJob::dispatch(
                $user->id,
                $limit
            );
        }

        // Return the existing recommendations immediately.
        $recommendations = $this->recommendationService
            ->getRecommendations($user, $limit);

        return response()->json([
            'message' => 'Recommendations retrieved successfully.',
            'data' => [
                'recommendations' => $recommendations->map(
                    fn($recommendation) => [
                        'id' => $recommendation->id,
                        'match_score' => $recommendation->match_score,
                        'reason' => $recommendation->reason,
                        'is_viewed' => $recommendation->is_viewed,
                        'job' => JobResource::make($recommendation->job)
                            ->resolve($request),
                    ]
                )->values(),

                'total' => $recommendations->count(),

                'is_refreshing' => $isStale,
            ],
        ]);
    }

    /**
     * Generate AI job recommendations for the authenticated jobseeker.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['sometimes', 'integer', 'min:1', 'max:20'],
        ]);

        $user = $request->user();

        try {
            $result = $this->recommendationService->recommend(
                $user,
                $validated['limit'] ?? 10
            );

            $recommendations = collect($result['recommendations'])
                ->map(function ($recommendation) use ($request) {
                    return [
                        'id' => $recommendation->id,
                        'match_score' => $recommendation->match_score,
                        'reason' => $recommendation->reason,
                        'is_viewed' => $recommendation->is_viewed,
                        'job' => $recommendation->job
                            ? JobResource::make($recommendation->job)
                            ->resolve($request)
                            : null,
                    ];
                })
                ->values();

            return response()->json([
                'message' => $result['message'],
                'data' => [
                    'analysis' => $result['analysis'] ? [
                        'id' => $result['analysis']->id,
                        'status' => $result['analysis']->status,
                        'created_at' => $result['analysis']->created_at,
                    ] : null,

                    'recommendations' => $recommendations,
                    'total' => $recommendations->count(),
                ],
            ]);
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        }
    }
}
