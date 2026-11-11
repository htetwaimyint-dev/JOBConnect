<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $disk = Storage::disk('public');

        return [
            'id' => $this->id,
            'employer_id' => $this->employer_id,

            'name' => $this->name,
            'tagline' => $this->tagline,

            'logo' => $this->logo
                ? $disk->url($this->logo)
                : null,

            'cover_image' => $this->cover_image
                ? $disk->url($this->cover_image)
                : null,

            'founded_year' => $this->founded_year,
            'employee_count' => $this->employee_count,

            'description' => $this->description,
            'industry' => $this->industry,
            'website' => $this->website,
            'company_email' => $this->company_email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,

            'gallery' => collect($this->gallery ?? [])
                ->map(fn ($path) => $disk->url($path))
                ->values(),

            'verification_status' => $this->verification_status,
            'verified_at' => $this->verified_at?->toISOString(),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}