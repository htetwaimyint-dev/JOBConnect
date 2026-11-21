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
    ): LengthAwarePaginator {

        // 1. Get Active + Suspended users
        $query = User::withTrashed()
            ->where('role', $role->value);

        // 2. Relationships
        if ($role === UserRole::EMPLOYER) {

            $query->with('company')
                  ->withCount('jobs');

        } elseif ($role === UserRole::JOB_SEEKER) {

            $query->with('jobSeekerProfile')
                  ->withCount('resumes');
        }

        // 3. Search
        if (!empty($search)) {

            $query->where(function ($q) use ($search, $role) {

                // Search Name
                $q->where('name', 'LIKE', "%{$search}%")

                  // Search Email
                  ->orWhere('email', 'LIKE', "%{$search}%");

                // Employer Company Name
                if ($role === UserRole::EMPLOYER) {

                    $q->orWhereHas('company', function ($companyQuery) use ($search) {

                        $companyQuery->where(
                            'name',
                            'LIKE',
                            "%{$search}%"
                        );

                    });

                }

                // Job Seeker Professional Title
                if ($role === UserRole::JOB_SEEKER) {

                    $q->orWhereHas('jobSeekerProfile', function ($profileQuery) use ($search) {

                        $profileQuery->where(
                            'professional_title',
                            'LIKE',
                            "%{$search}%"
                        );

                    });

                }

            });

        }

        // 4. Status Filter (Both Roles)
        if ($status === 'Active') {

            $query->where('status', 'active');

        } elseif ($status === 'Suspended') {

            $query->where('status', 'suspended');

        }

        // 5. Pagination
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
