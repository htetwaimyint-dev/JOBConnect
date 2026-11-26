<?php

namespace App\Http\Controllers\Api\V1\AI;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Jobs\JobResource;
use App\Jobs\GenerateRecommendationsJob;
use App\Services\AI\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService
    ) {}

    /**
     * Retrieve saved recommendations.
     *
     * If the current input hash has no completed analysis,
     * dispatch background generation.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $limit = min(
            max($request->integer('limit', 10), 1),
            20
        );

        $inputHash = $this->recommendationService
            ->getCurrentInputHash($user);

        $hasCompletedAnalysis = $this->recommendationService
            ->hasCompletedAnalysisForHash($user, $inputHash);

        $hasPendingAnalysis = $this->recommendationService
            ->hasPendingAnalysisForHash($user, $inputHash);

        $isRefreshing = !$hasCompletedAnalysis;

        if ($isRefreshing && !$hasPendingAnalysis) {
            GenerateRecommendationsJob::dispatch(
                $user->id,
                $limit
            );
        }

        $result = $this->recommendationService
            ->getRecommendations($user, $limit);

        return response()->json([
            'message' => 'Recommendations retrieved successfully.',
            'data' => [
                'analysis' => $result['analysis'] ? [
                    'id' => $result['analysis']->id,
                    'status' => $result['analysis']->status,
                    'created_at' => $result['analysis']->created_at,
                ] : null,

                'recommendations' => $result['recommendations']
                    ->map(fn($recommendation) => [
                        'id' => $recommendation->id,
                        'match_score' => $recommendation->match_score,
                        'reason' => $recommendation->reason,
                        'is_viewed' => $recommendation->is_viewed,
                        'job' => JobResource::make($recommendation->job)
                            ->resolve($request),
                    ])
                    ->values(),

                'total' => $result['total'],
                'is_refreshing' => $isRefreshing,
            ],
        ]);
    }
}
