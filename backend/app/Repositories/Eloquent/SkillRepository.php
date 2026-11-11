<?php

namespace App\Repositories\Eloquent;

use App\Models\Skill;
use App\Repositories\Contracts\SkillRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SkillRepository implements SkillRepositoryInterface
{
    public function getByJobSeeker(int $jobSeekerId): Collection
    {
        return Skill::where('job_seeker_id', $jobSeekerId)
            ->latest()
            ->get();
    }

    public function findById(
        int $skillId,
        int $jobSeekerId
    ): ?Skill {
        return Skill::where('job_seeker_id', $jobSeekerId)
            ->find($skillId);
    }


    public function create(
        int $jobSeekerId,
        array $data
    ): Skill {
        return Skill::create([
            'job_seeker_id' => $jobSeekerId,
            'name' => $data['name'],
            'skill_level' => $data['skill_level'],
        ]);
    }


    public function update(
        Skill $skill,
        array $data
    ): Skill {
        $skill->update($data);

        return $skill->refresh();
    }

    public function delete(Skill $skill): bool
    {
        return $skill->delete();
    }
    public function existsByName(
        int $jobSeekerId,
        string $name,
        ?int $ignoreSkillId = null
    ): bool {
        $query = Skill::where('job_seeker_id', $jobSeekerId)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);

        if ($ignoreSkillId !== null) {
            $query->where('id', '!=', $ignoreSkillId);
        }

        return $query->exists();
    }
}
