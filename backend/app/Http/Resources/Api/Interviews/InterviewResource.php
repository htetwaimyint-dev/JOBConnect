<?php

namespace App\Http\Resources\Api\Interviews;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_id' => $this->application_id,
            'interview_type' => $this->interview_type,
            'scheduled_at' => $this->scheduled_at?->toISOString(),
            'location' => $this->location,
            'meeting_link' => $this->meeting_link,
            'notes' => $this->notes,
            'status' => $this->status,
            'interviewers' => InterviewerResource::collection($this->whenLoaded('interviewers')),
            'application' => $this->whenLoaded('application', function () {
                $application = $this->application;
                $job = $application?->job;
                $company = $job?->company;
                $jobSeeker = $application?->jobSeeker;

                return $application ? [
                    'id' => $application->id,
                    'applicant_name' => $application->applicant_name,
                    'applicant_phone' => $application->applicant_phone,
                    'status' => data_get($application->status, 'value', $application->status),
                    'applied_at' => $application->applied_at?->toISOString(),
                    'job' => [
                        'id' => $job?->id,
                        'title' => $job?->title,
                        'company' => [
                            'id' => $company?->id,
                            'name' => $company?->name,
                            'logo' => $company?->logo
                                ? asset('storage/' . $company?->logo)
                                : null,
                        ],
                    ],
                    'job_seeker' => $jobSeeker ? [
                        'id' => $jobSeeker->id,
                        'name' => $jobSeeker->name,
                        'email' => $jobSeeker->email,
                    ] : null,
                ] : null;
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
