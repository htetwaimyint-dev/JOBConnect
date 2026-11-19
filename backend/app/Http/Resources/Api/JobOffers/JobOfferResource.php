<?php

namespace App\Http\Resources\Api\JobOffers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_id' => $this->job_id,
            'job_seeker_id' => $this->job_seeker_id,
            'company_id' => $this->company_id,
            'salary_offered' => $this->salary_offered,
            'offer_details' => $this->offer_details,
            'expires_at' => $this->expires_at?->toISOString(),
            'status' => $this->status,
            'responded_at' => $this->responded_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'job' => new EmployerOfferJobResource($this->whenLoaded('job')),
            'company' => new JobOfferCompanyResource($this->whenLoaded('company')),
            'job_seeker' => new JobOfferCandidateResource($this->whenLoaded('jobSeeker')),
        ];
    }
}
