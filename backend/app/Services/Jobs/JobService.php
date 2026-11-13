<?php

namespace App\Services\Jobs;

use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class JobService
{
    protected JobRepositoryInterface $jobRepository;

    public function __construct(JobRepositoryInterface $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    /*
    |--------------------------------------------------------------------------
    | Employer Job Management
    |--------------------------------------------------------------------------
    */

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
            throw new ModelNotFoundException('Job not found.');
        }

        return $job;
    }

    public function createJob(
        Company $company,
        array $data
    ): Job {
        $status = $data['submission_status'] ?? 'pending';

        if (($data['salary_type'] ?? 'fixed') === 'negotiable') {
            $data['salary_min'] = null;
            $data['salary_max'] = null;
        }

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

        if (($data['salary_type'] ?? 'fixed') === 'negotiable') {
            $data['salary_min'] = null;
            $data['salary_max'] = null;
        }

        unset(
            $data['submission_status'],
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
        } elseif ($job->status === 'draft') {
            $data['status'] = $submissionStatus ?? 'draft';
        } elseif ($job->status === 'pending') {
            $data['status'] = 'pending';
        }

        if ($data['status'] === 'pending') {
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['rejection_reason'] = null;
        }

        return $this->jobRepository->update($job, $data);
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

    /*
    |--------------------------------------------------------------------------
    | Admin Job Approval
    |--------------------------------------------------------------------------
    */

    public function getPendingJobsForAdmin(
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->jobRepository->getPendingForAdmin($perPage);
    }

    public function getJobForAdmin(int $jobId): Job
    {
        $job = $this->jobRepository->findForAdmin($jobId);

        if (!$job) {
            throw new ModelNotFoundException('Job not found.');
        }

        return $job;
    }

    public function updateJobApproval(
        Job $job,
        User $admin,
        array $data
    ): Job {
        if ($job->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => [
                    'Only pending jobs can be approved or rejected.'
                ],
            ]);
        }

        if ($data['status'] === 'published') {
            $data['approved_by'] = $admin->id;
            $data['approved_at'] = now();
            $data['rejection_reason'] = null;
        } else {
            $data['approved_by'] = null;
            $data['approved_at'] = null;
        }

        return $this->jobRepository->update($job, $data);
    }

    /*
    |--------------------------------------------------------------------------
    | Public Job Listing
    |--------------------------------------------------------------------------
    */

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
