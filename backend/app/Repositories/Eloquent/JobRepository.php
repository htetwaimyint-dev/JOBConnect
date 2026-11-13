<?php

namespace App\Repositories\Eloquent;

use App\Models\Company;
use App\Models\Job;
use App\Repositories\Contracts\JobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class JobRepository implements JobRepositoryInterface
{
    public function getByCompany(
        Company $company, 
        int $perPage = 10
    ): LengthAwarePaginator {
        return $company->jobs()->with('category')->latest()->paginate($perPage);
    }

    public function findById(
        Company $company,
        int $jobId
    ): ?Job {
        return $company->jobs()->with('category')->whereKey($jobId)->first();
    }

    public function create(
        Company $company,
        array $data
    ): Job {
        return DB::transaction(function() use ($company, $data) {
            $job = $company->jobs()->create($data);

            return $job->load('category');
        });
    }

    public function update(
        Job $job,
        array $data
    ): Job {
        return DB::transaction(function() use ($job, $data) {
            $job->update($data);

            return $job->refresh()->load('category');
        });
    }

    public function delete(Job $job): bool {
        return $job->delete();
    }
}
