<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at?->toDateTimeString(),
            // Include related details depending on role
            'company' => $this->whenLoaded('company'),
            'jobs_count' => $this->whenCounted('jobs'), 
            'profile' => $this->whenLoaded('jobSeekerProfile'),
            // Job Seeker Profile
            'profile' => $this->whenLoaded(
                'jobSeekerProfile',
                function () {
                    return [
                        'profile_photo' => $this->jobSeekerProfile?->profile_photo,
                    ];
                }
            ),

            // Resume Count
            'resume_count' => $this->whenCounted('resumes'),
            ];
    }
}