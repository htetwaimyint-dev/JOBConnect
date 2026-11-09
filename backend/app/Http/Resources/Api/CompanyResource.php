<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'employer_id' => $this->employer_id,

            'name' => $this->name,
            'logo' => $this->logo,
            'description' => $this->description,
            'industry' => $this->industry,

            'website' => $this->website,
            'company_email' => $this->company_email,
            'phone' => $this->phone,

            'address' => $this->address,
            'city' => $this->city,

            'verification_status' => $this->verification_status,
            'verified_at' => $this->verified_at?->toISOString(),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
