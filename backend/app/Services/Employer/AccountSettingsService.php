<?php

namespace App\Services\Employer;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccountSettingsService
{
    /**
     * Update employer account information.
     */
    public function updateAccountInformation(
        User $user,
        array $data
    ): User {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return $user->refresh();
    }

    /**
     * Change employer password.
     */
    public function changePassword(
        User $user,
        array $data
    ): void {
        if (! Hash::check(
            $data['current_password'],
            $user->password
        )) {
            throw ValidationException::withMessages([
                'current_password' => [
                    'The current password is incorrect.',
                ],
            ]);
        }

        $user->update([
            'password' => $data['new_password'],
        ]);
    }
}
