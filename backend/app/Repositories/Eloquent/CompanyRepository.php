<?php

namespace App\Repositories\Eloquent;

use App\Models\Company;
use App\Models\User;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function findByEmployer(User $user): ?Company
    {
        return Company::where('employer_id', $user->id)->first();
    }

    public function createOrUpdate(
        User $user,
        array $data
    ): Company {
        return DB::transaction(function () use ($user, $data) {
            $company = Company::firstOrNew([
                'employer_id' => $user->id,
            ]);

            $company->fill($data);

            if (!$company->exists) {
                $company->verification_status = 'pending';
            }   

            $company->save();

            return $company->refresh();
        });
    }

    public function getAll(?string $search): Collection
    {
        $query = Company::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        return $query->latest()->get();
    }

    public function findById(int $id): ?Company
    {
        return Company::find($id);
    }
}