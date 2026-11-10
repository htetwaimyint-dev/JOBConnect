<?php

namespace App\Repositories\Contracts;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ResumeRepositoryInterface
{
    public function getAllByUser(User $user): Collection;

    public function findByUser(
        User $user,
        int $resumeId
    ): ?Resume;
    public function findById(int $resumeId): ?Resume;
    
    public function create(
        User $user,
        array $data
    ): Resume;

    public function update(
        Resume $resume,
        array $data
    ): Resume;

    public function delete(Resume $resume): bool;

    public function clearDefault(User $user): void;
}
