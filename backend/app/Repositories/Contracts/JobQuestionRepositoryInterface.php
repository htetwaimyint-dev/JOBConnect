<?php

namespace App\Repositories\Contracts;

use App\Models\Job;
use Illuminate\Support\Collection;

interface JobQuestionRepositoryInterface
{
    public function getByJob(Job $job): Collection;

    public function create(Job $job, array $data);

    public function update(
        Job $job,
        int $id,
        array $data
    );

    public function deleteNotIn(Job $job, array $ids): void;

    public function deleteAll(Job $job): void;

    public function findByIds(Job $job, array $ids): Collection;
}
