<?php

namespace App\Http\Resources\Employer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobSeekerDirectoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $profile = $this->jobSeekerProfile;

        return [
            'id' => $this->id,
            'name' => $this->name,

            'profile_photo' => $profile?->profile_photo
                ? asset('storage/' . $profile->profile_photo)
                : null,

            'professional_title' => $profile?->professional_title,
            'city' => $profile?->city,
            'bio' => $profile?->bio,
            'phone' => $profile?->phone,
            'gender' => $profile?->gender,
            'date_of_birth' => $profile?->date_of_birth,
            'address' => $profile?->address,
            'expected_salary' => $profile?->expected_salary,
            'experience_years' => $profile?->experience_years,
            'linkedin_url' => $profile?->linkedin_url,
            'portfolio_url' => $profile?->portfolio_url,

            'skills' => $this->skills->map(fn($skill) => [
                'id' => $skill->id,
                'name' => $skill->name,
                'skill_level' => $skill->skill_level,
            ])->values(),

            'educations' => $this->educations->map(fn($education) => [
                'id' => $education->id,
                'institution' => $education->institution,
                'degree' => $education->degree,
                'field_of_study' => $education->field_of_study,
                'start_date' => $education->start_date,
                'end_date' => $education->end_date,
                'description' => $education->description,
            ])->values(),

            'experiences' => $this->experiences->map(fn($experience) => [
                'id' => $experience->id,
                'job_title' => $experience->job_title,
                'company_name' => $experience->company_name,
                'employment_type' => $experience->employment_type,
                'start_date' => $experience->start_date,
                'end_date' => $experience->end_date,
                'is_current' => $experience->is_current,
                'description' => $experience->description,
            ])->values(),

            'resumes' => $this->whenLoaded(
                'resumes',
                fn() => $this->resumes
                    ->where('visibility', 'public')
                    ->map(fn($resume) => [
                        'id' => $resume->id,
                        'title' => $resume->title,
                        'file_type' => $resume->file_type,
                        'is_default' => $resume->is_default,
                        'visibility' => $resume->visibility,
                        'created_at' => $resume->created_at,
                        'updated_at' => $resume->updated_at,
                    ])->values()
            ),
        ];
    }
}
