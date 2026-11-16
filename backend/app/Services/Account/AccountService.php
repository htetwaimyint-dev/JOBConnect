<?php

namespace App\Services\Account;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    // Update Name & Email
    public function updateProfile(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return $user->fresh();
    }

    // Change Password
    public function updatePassword(User $user, array $data): void
    {
        $user->update([
            'password' => $data['new_password'],
        ]);
    }
}
