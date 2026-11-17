<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Enums\UserStatus;
use Exception;

class AuthService
{
    public function __construct(
        protected AuthRepositoryInterface $authRepository
    ) {}

    public function register(array $data): User
    {
        return $this->authRepository->createUser($data);
    }

    public function login(string $email, string $password, bool $remember): array
    {
        $user = $this->authRepository->findByEmail($email);

        if (!$user) {
            throw new Exception('User not found', 404);
        }

        if ($user->status !== UserStatus::ACTIVE) {
            throw new Exception('Your account is not active', 403);
        }

        if (!$this->authRepository->checkPassword($user, $password)) {
            throw new Exception('Invalid password', 401);
        }

        $token = $this->authRepository->generateToken($user, $remember);

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $this->authRepository->logout($user);
    }
}
