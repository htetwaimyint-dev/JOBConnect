<?php

namespace App\Repositories\Eloquent;

use App\Traits\PaginationHelper;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
public function getUsersByRole(
    UserRole $role,
    ?string $search = null,
    ?string $status = null,
    int $perPage = 10
):  LengthAwarePaginator {
    $query = $role === UserRole::EMPLOYER
        ? User::withTrashed()->where('role', $role->value)
        : User::where('role', $role->value);

    // Relationships
    if ($role === UserRole::EMPLOYER) {
        $query->with('company')
              ->withCount('jobs');
    } elseif ($role === UserRole::JOB_SEEKER) {
        $query->with('jobSeekerProfile')
              ->withCount('resumes');
    }

    // Search
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");

            if ($role === UserRole::EMPLOYER) {
                $q->orWhereHas('company', function ($companyQuery) use ($search) {
                    $companyQuery->where(
                        'name',
                        'LIKE',
                        "%{$search}%"
                    );
                });
            }
        });
    }

    // Status Filter (Employer only)
    if ($role === UserRole::EMPLOYER) {
        if ($status === 'Active') {
            $query->whereNull('deleted_at');
        } elseif ($status === 'Suspended') {
            $query->whereNotNull('deleted_at');
        }
    }

    // Common Pagination
    return PaginationHelper::paginate(
        $query->latest(),
        $perPage
    );
}

   public function updateEmployerStatus(
    int $id,
    string $status
): User {

    $user = User::withTrashed()
        ->where('role', UserRole::EMPLOYER->value)
        ->findOrFail($id);

    if ($status === 'suspended') {
   
        $user->delete();

        $user->status = 'suspended';
        $user->save();

    } elseif ($status === 'active') {

        $user->restore();

        $user->status = 'active';
        $user->save();
    }

    return $user;
}

public function updateJobSeekerStatus(
    int $id,
    string $status
): User {

    $user = User::withTrashed()
        ->where('role', UserRole::JOB_SEEKER->value)
        ->findOrFail($id);

    if ($status === 'suspended') {

        $user->status = 'suspended';
        $user->save();

        $user->delete();

    } elseif ($status === 'active') {

        $user->restore();

        $user->status = 'active';
        $user->save();
    }

    return $user;
}
}
