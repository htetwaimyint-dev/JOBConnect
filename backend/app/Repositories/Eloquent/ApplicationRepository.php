<?php

namespace App\Repositories\Eloquent;

use App\Models\Application;
use App\Models\Company;
use App\Models\User;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;


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
        int $perPage = 10,
        ?string $status = null,
        ?int $categoryId = null,
        ?string $search = null
    ): LengthAwarePaginator {
        return Application::query()
            ->with([
                'job.company',
                'job.category',
                'resume',
                'jobSeeker',
                'latestSuccessfulAiAnalysis',
            ])
            ->whereHas('job', function ($query) use ($company, $categoryId) {
                $query->where('company_id', $company->id)
                    ->when($categoryId, function ($jobQuery) use ($categoryId) {
                        $jobQuery->where('category_id', $categoryId);
                    });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('applicant_name', 'like', "%{$search}%")
                        ->orWhereHas('jobSeeker', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('job', function ($jobQuery) use ($search) {
                            $jobQuery->where('title', 'like', "%{$search}%");
                        });
                });
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
            ->with([
                'job.company',
                'resume',
                'jobSeeker',
                'latestSuccessfulAiAnalysis',
            ])
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
            ->with([
                'job.company',
                'resume',
                'jobSeeker.jobSeekerProfile',
                'jobSeeker.skills',
                'jobSeeker.educations',
                'jobSeeker.experiences',
                'answers.jobQuestion',
                'statusHistories',
                'latestSuccessfulAiAnalysis',
            ])
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
                'jobSeeker.jobSeekerProfile',
                'jobSeeker.skills',
                'jobSeeker.educations',
                'jobSeeker.experiences',
                'answers.jobQuestion',
                'statusHistories',
                'latestSuccessfulAiAnalysis',
            ]);
        });
    }

    // Employer Applicant Statistics -Employer Dashboard
    public function getEmployerApplicantStats(
    Company $company
    ): array {
    $startDate = now()->startOfMonth();
    $endDate = now();

    $stats = Application::query()
        ->whereBetween('applied_at', [$startDate, $endDate])
        ->whereHas('job', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
        ->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
            SUM(CASE WHEN status = 'shortlisted' THEN 1 ELSE 0 END) as shortlisted,
            SUM(CASE WHEN status = 'interview' THEN 1 ELSE 0 END) as interview,
            SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = 'withdrawn' THEN 1 ELSE 0 END) as withdrawn
        ")
        ->first();

        return [
            'total' => (int) $stats->total,
            'pending' => (int) $stats->pending,
            'reviewed' => (int) $stats->reviewed,
            'shortlisted' => (int) $stats->shortlisted,
            'interview' => (int) $stats->interview,
            'accepted' => (int) $stats->accepted,
            'rejected' => (int) $stats->rejected,
            'withdrawn' => (int) $stats->withdrawn,
            'date_range' => [
                'from' => $startDate->toDateString(),
                'to' => $endDate->toDateString(),
            ],
        ];
    }

    //Applicant Graph(Month)- Employer Dashboard
    public function getApplicantYears(int $companyId): Collection
    {
        return Application::query()
            ->whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
    }

    //Applicant Graph(Month)- Employer Dashboard
    public function getMonthlyApplicantStats(
    int $companyId,
    int  $selectedYear
    ): Collection {
        return Application::query()
            ->whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereYear('created_at',  $selectedYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get();
    }
}
