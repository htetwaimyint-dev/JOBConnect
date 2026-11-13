<?php

namespace App\Services\JobSeeker;

use App\Models\Skill;
use App\Models\User;
use App\Repositories\Contracts\SkillRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserSkillService
{
    public function __construct(
        private SkillRepositoryInterface $skillRepository
    ) {}

    public function getSkills(User $user): Collection
    {
        return $this->skillRepository->getByJobSeeker(
            $user->id
        );
    }

    public function createSkill(
        User $user,
        array $data
    ): Skill {
        $this->ensureSkillIsUnique($user, $data['name']);

        return $this->skillRepository->create(
            $user->id,
            $data
        );
    }

    public function updateSkill(
        User $user,
        int $skillId,
        array $data
    ): Skill {
        $skill = $this->findOwnSkill($user, $skillId);

        if (isset($data['name'])) {
            $this->ensureSkillIsUnique(
                $user,
                $data['name'],
                $skill->id
            );
        }

        return $this->skillRepository->update(
            $skill,
            $data
        );
    }

    public function deleteSkill(
        User $user,
        int $skillId
    ): bool {
        $skill = $this->findOwnSkill($user, $skillId);

        return $this->skillRepository->delete($skill);
    }

    private function findOwnSkill(
        User $user,
        int $skillId
    ): Skill {
        $skill = $this->skillRepository->findById(
            $skillId,
            $user->id
        );

        if (!$skill) {
            throw (new ModelNotFoundException())
                ->setModel(Skill::class, [$skillId]);
        }

        return $skill;
    }

    private function ensureSkillIsUnique(
        User $user,
        string $name,
        ?int $ignoreSkillId = null
    ): void {
        if ($this->skillRepository->existsByName(
            $user->id,
            $name,
            $ignoreSkillId
        )) {
            throw ValidationException::withMessages([
                'name' => ['You already have this skill.'],
            ]);
        }
    }
}
