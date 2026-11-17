<?php

namespace App\Http\Resources\Api\Jobs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedJobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $job = $this->job;

        return [
            'id' => $this->id,

            'saved_at' => $this->created_at,

            'job' => $job ? [
                'id' => $job->id,
                'company_id' => $job->company_id,

                'company' => $job->company ? [
                    'id' => $job->company->id,
                    'name' => $job->company->name,
                    'logo' => $job->company->logo
                        ? asset('storage/' . $job->company->logo)
                        : null,
                ] : null,

                'category' => $job->category ? [
                    'id' => $job->category->id,
                    'name' => $job->category->name,
                    'logo' => $job->category->logo,
                ] : null,

                'title' => $job->title,
                'description' => $job->description,
                'requirements' => $job->requirements,
                'responsibilities' => $job->responsibilities,
                'employment_type' => $job->employment_type,
                'workplace_type' => $job->workplace_type,
                'gender' => $job->gender,
                'country' => $job->country,
                'city' => $job->city,
                'salary_type' => $job->salary_type,
                'salary_min' => $job->salary_min,
                'salary_max' => $job->salary_max,
                'vacancies' => $job->vacancies,
                'deadline' => $job->deadline?->format('Y-m-d'),
                'status' => $job->status?->value,
                'created_at' => $job->created_at,
            ] : null,
        ];
    }
}
