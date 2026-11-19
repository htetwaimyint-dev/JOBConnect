<?php

namespace App\Repositories\Eloquent;

use App\Models\Company;
use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Enums\JobStatus;

class JobRepository implements JobRepositoryInterface
{
    /*
    |--------------------------------------------------------------------------
    | Employer Job Management
    |--------------------------------------------------------------------------
    */

    public function getByCompany(
        Company $company,
        array $params = [],
        int $perPage = 5
    ): LengthAwarePaginator {
        $perPage = $params['perPage'] ?? 10;
        return $company->jobs()
            ->with('category')

            ->when($params['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description_plain_text', 'like', "%{$search}%");
                });
            })

            ->when($params['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })

            ->when($params['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })

            ->when($params['employment_type'] ?? null, function ($query, $employmentType) {
                $query->where('employment_type', $employmentType);
            })

            ->when($params['workplace_type'] ?? null, function ($query, $workplaceType) {
                $query->where('workplace_type', $workplaceType);
            })

            ->when($params['location'] ?? null, function ($query, $location) {
                $query->where(function ($q) use ($location) {
                    $q->where('city', 'like', "%{$location}%")
                        ->orWhere('country', 'like', "%{$location}%");
                });
            })

            ->latest()
            ->paginate($perPage);
    }

    public function findById(
        Company $company,
        int $jobId
    ): ?Job {
        return $company->jobs()
            ->with(['category', 'jobQuestion'])
            ->whereKey($jobId)
            ->first();
    }

    public function create(
        Company $company,
        array $data
    ): Job {
        return DB::transaction(function () use ($company, $data) {
            $job = $company->jobs()->create($data);

            return $job->load('category');
        });
    }

    public function update(
        Job $job,
        array $data
    ): Job {
        return DB::transaction(function () use ($job, $data) {
            $job->update($data);

            return $job->refresh()->load('category');
        });
    }

    public function delete(Job $job): bool
    {
        return (bool) $job->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Job Approval
    |--------------------------------------------------------------------------
    */

    public function getPendingForAdmin(
        int $perPage = 5
    ): LengthAwarePaginator {
        return Job::with([
            'company:id,name,logo,city',
            'category:id,name',
        ])
            ->where('status', JobStatus::Pending->value)
            ->latest()
            ->paginate($perPage);
    }

    public function findForAdmin(int $jobId): ?Job
    {
        return Job::with([
            'company:id,name,logo,city',
            'category:id,name',
            'approvedBy:id,name,email',
            'jobQuestion',
        ])->find($jobId);
    }
    /*
    |--------------------------------------------------------------------------
    | Public Job Listing
    |--------------------------------------------------------------------------
    */

    public function getPublishedJobs(
        array $filters,
        int $perPage = 5
    ): LengthAwarePaginator {
        return Job::with([
            'company:id,name,logo,verification_status',
            'category:id,name,logo',
        ])
            ->where('status', JobStatus::Published->value)
            ->whereHas('company', function ($query) {
                $query->where(
                    'verification_status',
                    'verified'
                );
            })
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description_plain_text', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($companyQuery) use ($search) {
                            $companyQuery->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        });
                });
            })
            ->when(!empty($filters['category_id']), function ($query) use ($filters) {
                $query->where(
                    'category_id',
                    $filters['category_id']
                );
            })
            ->when(!empty($filters['employment_type']), function ($query) use ($filters) {
                $query->where(
                    'employment_type',
                    $filters['employment_type']
                );
            })
            ->when(!empty($filters['workplace_type']), function ($query) use ($filters) {
                $query->where(
                    'workplace_type',
                    $filters['workplace_type']
                );
            })
            ->when(!empty($filters['city']), function ($query) use ($filters) {
                $query->where(
                    'city',
                    'like',
                    "%{$filters['city']}%"
                );
            })
            ->when(!empty($filters['country']), function ($query) use ($filters) {
                $query->where(
                    'country',
                    'like',
                    "%{$filters['country']}%"
                );
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findPublishedJob(int $jobId): ?Job
    {
        return Job::with([
            'company:id,name,logo,tagline,description,website,industry,city,verification_status',
            'category:id,name,logo',
        ])
            ->where('status', JobStatus::Published->value)
            ->whereHas('company', function ($query) {
                $query->where(
                    'verification_status',
                    'verified'
                );
            })
            ->whereKey($jobId)
            ->first();
    }

   
// for Job Seeker to view published jobs posted by a specific company
    public function getPublishedJobsByCompany(int $companyId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Job::query()
            ->where('company_id', $companyId)
            ->where('status', JobStatus::Published) 
            ->latest();

        // Keyword filter
        if (!empty($filters['keyword'])) {
            $query->where('title', 'like', '%' . $filters['keyword'] . '%');
        }

        // Employment type filter
        if (!empty($filters['job_type'])) {
            $query->where('employment_type', $filters['job_type']);
        }

        // Location filter
        if (!empty($filters['location'])) {
            $location = $filters['location'];
            $query->where(function ($q) use ($location) {
                $q->where('city', 'like', "%{$location}%")
                  ->orWhere('country', 'like', "%{$location}%");
            });
        }

        return $query->paginate($perPage);
    }
}
