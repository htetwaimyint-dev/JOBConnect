<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function createUser(array $data): User;
    public function findByEmail(string $email): ?User;
    public function checkPassword(User $user, string $password): bool;
    public function generateToken(User $user, bool $remember): string;
    public function logout(User $user): void;
}