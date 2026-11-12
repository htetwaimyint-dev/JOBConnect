<?php

namespace App\Services\Jobs;


use App\Models\Company;
use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class JobService
{
    protected JobRepositoryInterface $jobRepository;

    public function __construct(JobRepositoryInterface $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function getJobs(
        Company $company,
        int $perPage
    ): LengthAwarePaginator {
        return $this->jobRepository->getByCompany($company, $perPage);
    }

    public function getJob(
        Company $company,
        int $jobId
    ): Job {
        $job = $this->jobRepository->findById($company, $jobId);

        if (!$job) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                'Job not found.'
            );
        }

        return $job;
    }

    public function createJob(
        Company $company,
        array $data
    ): Job {
        $status = $data['submission_status'] ?? 'pending';

        unset($data['submission_status']);

        $data['status'] = $status;

        return $this->jobRepository->create($company, $data);
    }

    public function updateJob(
        Company $company,
        int $jobId,
        array $data
    ): Job {
        $job = $this->getJob($company, $jobId);

        if ($job->status === 'closed') {
            throw ValidationException::withMessages([
                'job' => [
                    'Closed jobs cannot be edited.'
                ],
            ]);
        }

        $submissionStatus = $data['submission_status'] ?? null;

        unset(
            $data['company_id'],
            $data['status'],
            $data['approved_by'],
            $data['approved_at'],
            $data['rejection_reason']
        );

        if ($job->status === 'published') {
            $data['status'] = 'pending';
        } elseif ($job->status === 'rejected') {
            $data['status'] = $submissionStatus === 'draft'
                ? 'draft'
                : 'pending';
        } elseif ($submissionStatus !== null) {
            $data['status'] = $submissionStatus;
        }

        if (($data['status'] ?? $job->status) === 'pending') {
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['rejection_reason'] = null;
        }

        return $this->jobRepository->update(
            $job,
            $data
        );
    }

    public function deleteJob(
        Company $company,
        int $jobId
    ): bool {
        $job = $this->getJob($company, $jobId);

        if ($job->status === 'published') {
            throw ValidationException::withMessages([
                'job' => [
                    'Published jobs cannot be deleted.'
                ],
            ]);
        }

        return $this->jobRepository->delete($job);
    }
}
