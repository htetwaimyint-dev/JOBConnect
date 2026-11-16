<?php

namespace App\Services\Jobs;

use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PublicJobService
{
    protected JobRepositoryInterface $jobRepository;

    public function __construct(JobRepositoryInterface $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function getPublishedJobs(
        array $filters,
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->jobRepository->getPublishedJobs(
            $filters,
            $perPage
        );
    }

    public function getPublishedJob(int $jobId): Job
    {
        $job = $this->jobRepository->findPublishedJob($jobId);

        if (!$job) {
            throw new ModelNotFoundException('Job not found.');
        }

        return $job;
    }
}
