<?php

namespace App\Services\Jobs;

use App\Models\Job;
use App\Repositories\Contracts\JobQuestionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JobQuestionService
{
    public function __construct(
        protected JobQuestionRepositoryInterface $repository
    ) {}

    public function sync(Job $job, array $questions): void
    {
        DB::transaction(function () use ($job, $questions) {

            $submittedIds = collect($questions)
                ->pluck('id')
                ->filter()
                ->values()
                ->all();

            // Verify that all submitted IDs belong to this job.
            $existingQuestions = $this->repository->findByIds(
                $job,
                $submittedIds
            );

            if ($existingQuestions->count() !== count(array_unique($submittedIds))) {
                throw ValidationException::withMessages([
                    'job_questions' => [
                        'One or more questions do not belong to this job.',
                    ],
                ]);
            }

            $keptIds = [];

            foreach ($questions as $index => $question) {

                $data = [
                    'question' => $question['question'],
                    'is_required' => $question['is_required'] ?? true,
                    'sort_order' => $question['sort_order'] ?? $index,
                ];

                if (!empty($question['id'])) {
                    $this->repository->update(
                        $job,
                        $question['id'],
                        $data
                    );

                    $keptIds[] = $question['id'];
                } else {
                    $created = $this->repository->create(
                        $job,
                        $data
                    );

                    $keptIds[] = $created->id;
                }
            }

            if (empty($keptIds)) {
                $this->repository->deleteAll($job);
            } else {
                $this->repository->deleteNotIn($job, $keptIds);
            }
        });
    }
}
