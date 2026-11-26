<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AI\RecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateRecommendationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public int $timeout = 180;

    public int $uniqueFor = 600;

    public function __construct(
        public int $userId,
        public int $limit = 10
    ) {}

    public function uniqueId(): string
    {
        return 'recommendations-user-' . $this->userId;
    }

    public function backoff(): array
    {
        return [30, 60];
    }

    public function handle(
        RecommendationService $recommendationService
    ): void {
        $user = User::find($this->userId);

        if (!$user) {
            return;
        }

        $recommendationService->recommend(
            $user,
            $this->limit
        );
    }

    public function failed(Throwable $exception): void
    {
        Log::error('AI recommendation job failed.', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
