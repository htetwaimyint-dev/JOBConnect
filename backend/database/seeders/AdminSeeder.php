<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@jobconnect.test',
            ],
            [
                'name' => 'JobConnect Admin',
                'password' => 'admin@12345',
                'role' => UserRole::ADMIN,
                'status' => UserStatus::ACTIVE,
            ]
        );
    }
}
