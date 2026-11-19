<?php

namespace App\Http\Resources\Api\JobOffers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class JobOfferCandidateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $profile = $this->whenLoaded('jobSeekerProfile');

        return [
            'id' => $this->id,
            'name' => $this->name,

            'profile' => $profile instanceof \App\Models\JobSeekerProfile
                ? [
                    'profile_photo' => $profile->profile_photo
                        ? Storage::disk('public')->url($profile->profile_photo)
                        : null,
                    'phone' => $profile->phone,
                    'date_of_birth' => $profile->date_of_birth?->format('Y-m-d'),
                    'gender' => $profile->gender,
                    'address' => $profile->address,
                    'city' => $profile->city,
                    'professional_title' => $profile->professional_title,
                    'bio' => $profile->bio,
                    'expected_salary' => $profile->expected_salary,
                    'experience_years' => $profile->experience_years,
                    'linkedin_url' => $profile->linkedin_url,
                    'portfolio_url' => $profile->portfolio_url,
                ]
                : null,

            'skills' => $this->whenLoaded(
                'skills',
                fn() => $this->skills->map(fn($skill) => [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'skill_level' => $skill->skill_level,
                ])->values()
            ),

            'experiences' => $this->whenLoaded(
                'experiences',
                fn() => $this->experiences->map(fn($experience) => [
                    'id' => $experience->id,
                    'job_title' => $experience->job_title,
                    'company_name' => $experience->company_name,
                    'employment_type' => $experience->employment_type,
                    'start_date' => $experience->start_date?->format('Y-m-d'),
                    'end_date' => $experience->end_date?->format('Y-m-d'),
                    'is_current' => (bool) $experience->is_current,
                    'description' => $experience->description,
                ])->values()
            ),
        ];
    }
}
