<?php

namespace App\Services\Jobs;

use App\Models\Company;
use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class EmployerJobService
{
    protected JobRepositoryInterface $jobRepository;

    public function __construct(JobRepositoryInterface $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function getJobs(
        Company $company,
        array $params,
        int $perPage
    ): LengthAwarePaginator {
        return $this->jobRepository->getByCompany($company, $params, $perPage);
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

        if ($job->status === 'closed') {
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

        if (array_key_exists('description', $data)) {
            $data['description_plain_text'] = $this->extractPlainText(
                $data['description']
            );
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
