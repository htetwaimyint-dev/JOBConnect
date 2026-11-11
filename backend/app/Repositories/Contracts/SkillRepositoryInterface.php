<?php

namespace App\Repositories\Contracts;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Collection;

interface SkillRepositoryInterface
{
    public function getByJobSeeker(int $jobSeekerId): Collection;

    public function findById(
        int $skillId,
        int $jobSeekerId
    ): ?Skill;

    public function create(
        int $jobSeekerId,
        array $data
    ): Skill;

    public function update(
        Skill $skill,
        array $data
    ): Skill;

    public function delete(Skill $skill): bool;
    //existsByName checks if a skill with the given name exists for the specified job seeker.
    public function existsByName(
        int $jobSeekerId,
        string $name,
        ?int $ignoreSkillId = null
    ): bool;
}
