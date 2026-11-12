<?php

namespace App\Http\Resources\Api\JobSeeker;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class JobSeekerProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // User information
            'name' => $this->jobSeeker?->name,
            'email' => $this->jobSeeker?->email,

            // Profile information
            'profile_photo' => $this->profile_photo
                ? asset('storage/' . $this->profile_photo)
                : null,

            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'gender' => $this->gender,
            'address' => $this->address,
            'city' => $this->city,
            'professional_title' => $this->professional_title,
            'bio' => $this->bio,
            'expected_salary' => $this->expected_salary,
            'experience_years' => $this->experience_years,
            'linkedin_url' => $this->linkedin_url,
            'portfolio_url' => $this->portfolio_url,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
