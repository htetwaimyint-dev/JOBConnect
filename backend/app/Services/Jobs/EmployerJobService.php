<?php

namespace App\Services\Jobs;

use App\Enums\JobStatus;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Services\Employer\CompanyService;

class EmployerJobService
{
    protected JobRepositoryInterface $jobRepository;
    protected CompanyService $companyService;

    public function __construct(JobRepositoryInterface $jobRepository, CompanyService $companyService)
    {
        $this->jobRepository = $jobRepository;
        $this->companyService = $companyService;
    }

    // Job Statistics -Employer Dashboard
    public function getJobStats(User $user): array
    {
        $company = $this->companyService->getCompanyByEmployer($user);

        abort_unless(
            $company,
            403,
            'Please create your company profile before viewing job statistics.'
        );

        return $this->jobRepository->getJobStats($company);
    }
    

    public function getJobs(
        Company $company,
        array $params,
        int $perPage
    ): LengthAwarePaginator {
        return $this->jobRepository->getByCompany(
            $company,
            $params,
            $perPage
        );
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
        $status = $data['submission_status']
            ?? JobStatus::Pending->value;

        if (($data['salary_type'] ?? 'fixed') === 'negotiable') {
            $data['salary_min'] = null;
            $data['salary_max'] = null;
        }

        unset($data['submission_status']);

        $data['status'] = $status;

        $data['description_plain_text'] = $this->extractPlainText(
            $data['description'] ?? ''
        );

        return $this->jobRepository->create($company, $data);
    }


    public function updateJob(
        Company $company,
        int $jobId,
        array $data
    ): Job {
        $job = $this->getJob($company, $jobId);

        // Published jobs cannot be edited by employers.
        if ($job->status === JobStatus::Published) {
            throw ValidationException::withMessages([
                'job' => ['Published jobs cannot be edited.'],
            ]);
        }

        // Closed jobs cannot be edited.
        if ($job->status === JobStatus::Closed) {
            throw ValidationException::withMessages([
                'job' => ['Closed jobs cannot be edited.'],
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

        if ($job->status === JobStatus::Rejected) {
            $data['status'] = $submissionStatus === JobStatus::Draft->value
                ? JobStatus::Draft->value
                : JobStatus::Pending->value;
        } elseif ($job->status === JobStatus::Draft) {
            $data['status'] = $submissionStatus
                ?? JobStatus::Draft->value;
        } elseif ($job->status === JobStatus::Pending) {
            $data['status'] = JobStatus::Pending->value;
        }

        if (($data['status'] ?? null) === JobStatus::Pending->value) {
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['rejection_reason'] = null;
        }

        if (array_key_exists('description', $data)) {
            $data['description_plain_text'] = $this->extractPlainText(
                $data['description']
            );
        }

        return $this->jobRepository->update($job, $data);
    }
    //HWM add 
    public function closeJob(
        Company $company,
        int $jobId
    ): Job {
        $job = $this->getJob($company, $jobId);

        if ($job->status !== JobStatus::Published) {
            throw ValidationException::withMessages([
                'job' => ['Only published jobs can be closed.'],
            ]);
        }

        return $this->jobRepository->update($job, [
            'status' => JobStatus::Closed->value,
        ]);
    }
    public function deleteJob(
        Company $company,
        int $jobId
    ): bool {
        $job = $this->getJob($company, $jobId);

        if ($job->status === JobStatus::Published) {
            throw ValidationException::withMessages([
                'job' => ['Published jobs cannot be deleted.'],
            ]);
        }

        return $this->jobRepository->delete($job);
    }

    private function extractPlainText(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Replace closing block tags and line breaks with spaces.
        $html = preg_replace(
            '/<\/(p|div|li|h[1-6]|blockquote)>|<br\s*\/?>/i',
            ' ',
            $html
        );

        // Remove HTML tags.
        $text = strip_tags($html);

        // Decode HTML entities.
        $text = html_entity_decode(
            $text,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        // Normalize whitespace.
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }

    
}
