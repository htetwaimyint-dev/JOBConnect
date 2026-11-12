<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'company_id' => $this->company_id,

            'company' => [
                'id' => $this->company?->id,
                'name' => $this->company?->name,
                'logo' => $this->company?->logo
                    ? asset('storage/' . $this->company->logo)
                    : null,
            ],

            'category' => $this->category
                ? [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'logo' => $this->category->logo,
                ]
                : null,

            'title' => $this->title,

            'description' => $this->description,

            'requirements' => $this->requirements,

            'responsibilities' => $this->responsibilities,

            'employment_type' => $this->employment_type,

            'workplace_type' => $this->workplace_type,

            'location' => $this->location,

            'salary_min' => $this->salary_min,

            'salary_max' => $this->salary_max,

            'vacancies' => $this->vacancies,

            'deadline' => $this->deadline?->format('Y-m-d'),

            'status' => $this->status,

            'approved_at' => $this->approved_at,

            'rejection_reason' => $this->rejection_reason,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}
