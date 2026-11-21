<?php

namespace App\Repositories\Eloquent;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function getUsersByRole(UserRole $role, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
         $query = User::withTrashed()
        ->where('role', $role->value);

        // Eager load the matching profile table
        if ($role === UserRole::EMPLOYER) {
            $query->with('company')
                  ->withCount('jobs');
        } elseif ($role === UserRole::JOB_SEEKER) {
            $query->with('jobSeekerProfile');
        }

        // Support the search bar in your UI
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");

                if ($role === UserRole::EMPLOYER) {
                    $q->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        return $query->latest()->paginate($perPage);
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
}
