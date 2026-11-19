<?php

namespace App\Http\Resources\Api\JobOffers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class JobOfferCandidateProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photo = $this->profile_photo;

        return [
            'profile_photo' => $photo
                ? (Str::startsWith($photo, ['http://', 'https://']) ? $photo : Storage::disk('public')->url($photo))
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
        ];
    }
}
