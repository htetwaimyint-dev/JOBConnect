<?php

namespace App\Repositories\Eloquent;

use App\Models\Company;
use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class JobRepository implements JobRepositoryInterface
{
    /*
    |--------------------------------------------------------------------------
    | Employer Job Management
    |--------------------------------------------------------------------------
    */

    public function getByCompany(
        Company $company,
        int $perPage = 10
    ): LengthAwarePaginator {
        return $company->jobs()
            ->with('category')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(
        Company $company,
        int $jobId
    ): ?Job {
        return $company->jobs()
            ->with('category')
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
        int $perPage = 10
    ): LengthAwarePaginator {
        return Job::with([
            'company:id,name,logo,city',
            'category:id,name',
        ])
            ->where('status', 'pending')
            ->latest()
            ->paginate($perPage);
    }

    public function findForAdmin(int $jobId): ?Job
    {
        return Job::with([
            'company:id,name,logo,city',
            'category:id,name',
            'approvedBy:id,name,email',
        ])->find($jobId);
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
        return Job::with([
            'company:id,name,logo,verification_status',
            'category:id,name,logo',
        ])
            ->where('status', 'published')
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
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
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
            ->when(!empty($filters['location']), function ($query) use ($filters) {
                $query->where(
                    'location',
                    'like',
                    '%' . $filters['location'] . '%'
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
            ->where('status', 'published')
            ->whereHas('company', function ($query) {
                $query->where(
                    'verification_status',
                    'verified'
                );
            })
            ->whereKey($jobId)
            ->first();
    }
}
