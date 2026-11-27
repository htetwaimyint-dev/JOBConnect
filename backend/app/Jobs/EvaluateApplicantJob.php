<?php

namespace App\Jobs;

use App\Models\ApplicantEvaluationBatch;
use App\Models\ApplicantEvaluationBatchItem;
use App\Services\AI\GroqRateLimitException;
use App\Services\AI\JobMatchingService;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\MaxExceptions;
use Illuminate\Support\Facades\DB;
use Throwable;

#[MaxExceptions(3)]
class EvaluateApplicantJob implements ShouldQueue
{
    use Queueable;

    /**
     * Maximum execution time in seconds.
     */
    public int $timeout = 90;

    /**
     * Retry delays for ordinary exceptions.
     */
    public array $backoff = [10, 30, 60];

    /**
     * Fixed retry deadline.
     *
     * This is created when the job is dispatched,
     * not every time the job is retried.
     */
    private DateTime $retryDeadline;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $batchItemId
    ) {
        $this->retryDeadline = new DateTime('+48 hours');
    }

    /**
     * Allow retries until the fixed deadline.
     */
    public function retryUntil(): DateTime
    {
        return $this->retryDeadline;
    }

    /**
     * Execute the job.
     */
    public function handle(JobMatchingService $matchingService): void
    {
        $item = ApplicantEvaluationBatchItem::query()
            ->with([
                'batch.job',
                'batch.requester',
                'application',
            ])
            ->findOrFail($this->batchItemId);

        // Prevent processing an already completed item.
        if ($item->status === 'completed') {
            return;
        }

        $batch = $item->batch;

        // Mark the batch as processing.
        $batch->update([
            'status' => 'processing',
            'started_at' => $batch->started_at ?? now(),
        ]);

        // Mark this applicant as processing.
        $item->update([
            'status' => 'processing',
            'started_at' => now(),
            'completed_at' => null,
            'error_message' => null,
        ]);

        try {
            // Evaluate one applicant using the existing AI service.
            $evaluation = $matchingService->evaluateSingleApplicant(
                $batch->job,
                $item->application,
                $batch->requester
            );
        } catch (GroqRateLimitException $e) {
            report($e);

            // Use Groq's Retry-After value when available.
            // Otherwise, wait for 60 seconds before retrying.
            $delay = max(
                1,
                $e->retryAfterSeconds ?? 60
            );

            // Return the applicant item to the queued state.
            $item->update([
                'status' => 'queued',
                'error_message' => 'Waiting for Groq rate limit reset. Retry scheduled in '
                    . $delay . ' seconds.',
                'completed_at' => null,
            ]);

            // Keep the batch active while this item is waiting.
            $batch->update([
                'status' => 'processing',
                'completed_at' => null,
            ]);

            // Release this job back to the queue.
            $this->release($delay);

            return;
        }

        // Save the evaluation result reference.
        $item->update([
            'analysis_id' => $evaluation['analysis_id'],
            'status' => 'completed',
            'error_message' => null,
            'completed_at' => now(),
        ]);

        // Refresh the parent batch progress.
        $this->updateBatchProgress($batch->id);
    }

    /**
     * Handle a job failure after retries are exhausted.
     */
    public function failed(Throwable $exception): void
    {
        report($exception);

        $item = ApplicantEvaluationBatchItem::query()
            ->find($this->batchItemId);

        if (! $item) {
            return;
        }

        // Do not overwrite a completed item.
        if ($item->status === 'completed') {
            return;
        }

        $item->update([
            'status' => 'failed',
            'error_message' => 'AI evaluation failed after all retry attempts.',
            'completed_at' => now(),
        ]);

        $this->updateBatchProgress($item->batch_id);
    }

    /**
     * Update the batch status after an item finishes.
     */
    private function updateBatchProgress(int $batchId): void
    {
        DB::transaction(function () use ($batchId) {
            $batch = ApplicantEvaluationBatch::query()
                ->lockForUpdate()
                ->find($batchId);

            if (! $batch) {
                return;
            }

            $items = $batch->items();

            $hasPendingItems = (clone $items)
                ->whereIn('status', ['queued', 'processing'])
                ->exists();

            // Other applicants are still being evaluated or waiting.
            if ($hasPendingItems) {
                $batch->update([
                    'status' => 'processing',
                    'completed_at' => null,
                ]);

                return;
            }

            $completedCount = (clone $items)
                ->where('status', 'completed')
                ->count();

            $failedCount = (clone $items)
                ->where('status', 'failed')
                ->count();

            // Determine the final batch status.
            if ($failedCount > 0 && $completedCount > 0) {
                $status = 'completed_with_errors';
            } elseif ($failedCount > 0) {
                $status = 'failed';
            } else {
                $status = 'completed';
            }

            $batch->update([
                'status' => $status,
                'completed_at' => now(),
            ]);
        });
    }
}
