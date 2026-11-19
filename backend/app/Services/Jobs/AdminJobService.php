<?php

namespace App\Services\Jobs;

use App\Enums\JobStatus;
use App\Models\Job;
use App\Models\User;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminJobService
{
    protected JobRepositoryInterface $jobRepository;

    public function __construct(JobRepositoryInterface $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    // Admin Job Listing
    public function getAllJobsForAdmin(
        int $perPage = 5,
        ?string $search = null
    ): LengthAwarePaginator {
        return $this->jobRepository->getAllJobsForAdmin(
            $perPage,
            $search
        );
    }

    // Admin Job Listing by Status
    public function getJobsByStatusForAdmin(
        JobStatus $status,
        int $perPage = 5,
        ?string $search = null
    ): LengthAwarePaginator {
        return $this->jobRepository->getJobsByStatusForAdmin(
            $status,
            $perPage,
            $search
        );
    }

    // Get Job Details for Admin
    public function getJobForAdmin(int $jobId): Job
    {
        $job = $this->jobRepository->findForAdmin($jobId);

        if (!$job) {
            throw new ModelNotFoundException('Job not found.');
        }

        return $job;
    }

    // Update Job Approval Status
    public function updateJobApproval(
        Job $job,
        User $admin,
        array $data
    ): Job {
        // To allow approval when a job was accidentally rejected
        // if ($job->status !== JobStatus::Pending) {
        //     throw ValidationException::withMessages([
        //         'status' => [
        //                 'Only pending jobs can be approved or rejected.',
        //             ],
        //         ]);
        // }

        if ($data['status'] === JobStatus::Published->value) {
            $data['approved_by'] = $admin->id;
            $data['approved_at'] = now();
            $data['rejection_reason'] = null;
        } else {
            $data['approved_by'] = null;
            $data['approved_at'] = null;
        }

        return $this->jobRepository->update($job, $data);
    }
}
