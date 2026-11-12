<?php

namespace App\Repositories\Eloquent;

use App\Models\Company;
use App\Models\User;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Facades\DB;

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
}