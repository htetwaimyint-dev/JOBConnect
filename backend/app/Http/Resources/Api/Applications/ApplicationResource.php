<?php

namespace App\Http\Resources\Api\Applications;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'status' => $this->status->value,
            'cover_letter' => $this->cover_letter,
            'applied_at' => $this->applied_at,

            'job' => [
                'id' => $this->job?->id,
                'title' => $this->job?->title,

                'company' => [
                    'id' => $this->job?->company?->id,
                    'name' => $this->job?->company?->name,
                    'logo' => $this->job?->company?->logo,
                ],
            ],

            'resume' => [
                'id' => $this->resume?->id,
                'title' => $this->resume?->title,
                'file_type' => $this->resume?->file_type,
            ],

            'job_seeker' => $this->whenLoaded('jobSeeker', function () {
                return [
                    'id' => $this->jobSeeker->id,
                    'name' => $this->jobSeeker->name,
                    'email' => $this->jobSeeker->email,
                ];
            }),

            'created_at' => $this->created_at,
        ];
    }
}
