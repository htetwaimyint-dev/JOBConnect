<?php

namespace App\Repositories\Eloquent;

use App\Models\Application;
use App\Models\Company;
use App\Models\User;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;


class ApplicationRepository implements ApplicationRepositoryInterface
{
    /**
     * Get applications submitted by a Job Seeker.
     */
    public function getByJobSeeker(
        User $jobSeeker,
        int $perPage = 10,
        ?string $status = null,
        ?string $search = null
    ): LengthAwarePaginator {
        return Application::query()
            ->with(['job.company', 'resume'])
            ->where('job_seeker_id', $jobSeeker->id)
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('job', function ($jobQuery) use ($search) {
                    $jobQuery->where('title', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($companyQuery) use ($search) {
                            $companyQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('applied_at')
            ->paginate($perPage);
    }
    /**
     * Get applications for jobs belonging to a Company.
     */
    public function getByCompany(
        Company $company,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Application::query()
            ->with(['job.company', 'resume', 'jobSeeker'])
            ->whereHas('job', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->latest('applied_at')
            ->paginate($perPage);
    }

    /**
     * Get applications for a specific job belonging to a Company.
     */
    public function getByCompanyJob(
        Company $company,
        int $jobId,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Application::query()
            ->with(['job.company', 'resume', 'jobSeeker'])
            ->whereHas('job', function ($query) use ($company, $jobId) {
                $query->where('company_id', $company->id)
                    ->where('id', $jobId);
            })
            ->latest('applied_at')
            ->paginate($perPage);
    }



    /**
     * Create an application with its answers in a database transaction.
     */
    public function createWithAnswers(
        array $applicationData,
        array $answers
    ): Application {
        return DB::transaction(function () use ($applicationData, $answers) {
            $application = Application::create($applicationData);

            if (!empty($answers)) {
                $application->answers()->createMany($answers);
            }

            return $application->fresh([
                'job.company',
                'resume',
                'answers.jobQuestion',
            ]);
        });
    }

    /**
     * Find an application belonging to a Company's job.
     */
    public function findForCompany(
        Company $company,
        int $applicationId
    ): ?Application {
        return Application::query()
            ->with(['job.company', 'resume', 'jobSeeker'])
            ->whereHas('job', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->find($applicationId);
    }

    /**
     * Find an application belonging to a Job Seeker.
     */
    public function findForJobSeeker(
        User $jobSeeker,
        int $applicationId
    ): ?Application {
        return Application::query()
            ->with([
                'job.company',
                'resume',
                'answers.jobQuestion',
            ])
            ->where('job_seeker_id', $jobSeeker->id)
            ->find($applicationId);
    }

    /**
     * Update application status and create status history.
     */
    public function updateStatus(
        Application $application,
        array $data
    ): Application {
        return DB::transaction(function () use ($application, $data) {
            $oldStatus = $application->status->value;

            $application->update([
                'status' => $data['new_status'],
            ]);

            $application->statusHistories()->create([
                'changed_by_company_id' => $data['changed_by_company_id'],
                'old_status' => $oldStatus,
                'new_status' => $data['new_status'],
                'note' => $data['note'] ?? null,
            ]);

            return $application->fresh([
                'job.company',
                'resume',
                'jobSeeker',
                'statusHistories',
            ]);
        });
    }
}
