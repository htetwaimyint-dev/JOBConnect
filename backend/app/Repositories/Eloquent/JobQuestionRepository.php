<?php

namespace App\Repositories\Eloquent;


use App\Repositories\Contracts\JobQuestionRepositoryInterface;
use App\Models\Job;
use App\Models\JobQuestion;
use Illuminate\Support\Collection;

class JobQuestionRepository implements JobQuestionRepositoryInterface
{
    public function getByJob(Job $job): Collection
    {
        return $job->jobQuestion()->get();
    }

    public function create(Job $job, array $data)
    {
        return $job->jobQuestion()->create($data);
    }

    public function update(Job $job, int $id, array $data)
    {
        $question = $job->jobQuestion()
            ->whereKey($id)
            ->firstOrFail();

        $question->update($data);

        return $question->refresh();
    }

    public function deleteNotIn(Job $job, array $ids): void
    {
        $job->jobQuestion()
            ->whereNotIn('id', $ids)
            ->delete();
    }

    public function deleteAll(Job $job): void
    {
        $job->jobQuestion()->delete();
    }

    public function findByIds(Job $job, array $ids): Collection
    {
        return $job->jobQuestion()
            ->whereIn('id', $ids)
            ->get();
    }
}
