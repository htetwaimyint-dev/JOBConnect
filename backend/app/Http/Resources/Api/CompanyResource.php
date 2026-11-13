<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {


        return [
            'id' => $this->id,
            'employer_id' => $this->employer_id,

            // Company information
            'name' => $this->name,
            'tagline' => $this->tagline,

            // Company images
            'logo' => $this->logo
                ? asset('storage/' . $this->logo)
                : null,

            'cover_image' => $this->cover_image
                ? asset('storage/' . $this->cover_image)
                : null,

            // Company details
            'founded_year' => $this->founded_year,
            'employee_count' => $this->employee_count,
            'description' => $this->description,
            'industry' => $this->industry,
            'website' => $this->website,
            'company_email' => $this->company_email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,

            // Company gallery
            'gallery' => collect($this->gallery ?? [])
                ->map(fn($path) => asset('storage/' . $path))
                ->values(),

            // Verification information
            'verification_status' => $this->verification_status,
            'verified_at' => $this->verified_at?->toISOString(),
            'rejection_reason' => $this->rejection_reason,

            // Employer information (only when relationship is loaded)
            'employer' => $this->whenLoaded('employer', function () {
                return [
                    'id' => $this->employer->id,
                    'name' => $this->employer->name,
                    'email' => $this->employer->email,
                ];
            }),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
