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

            'applicant_name' => $this->applicant_name,
            'applicant_phone' => $this->applicant_phone,

            'status' => $this->status->value,
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

            // Return only the AI evaluation matching current input data.
            'ai_evaluation' => $this->when(
                $this->resource->relationLoaded('currentAiEvaluation'),
                function () {
                    $analysis = $this->resource->getRelation(
                        'currentAiEvaluation'
                    );

                    if (! $analysis) {
                        return null;
                    }

                    return [
                        ...($analysis->result ?? []),
                        'analysis_id' => $analysis->id,
                    ];
                }
            ),

            'answers' => $this->whenLoaded('answers', function () {
                return $this->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'question' => [
                            'id' => $answer->jobQuestion?->id,
                            'question' => $answer->jobQuestion?->question,
                            'is_required' => $answer->jobQuestion?->is_required,
                        ],
                        'answer' => $answer->answer,
                    ];
                });
            }),

            'job_seeker' => $this->whenLoaded('jobSeeker', function () {
                $jobSeeker = $this->jobSeeker;

                return [
                    'id' => $jobSeeker->id,
                    'name' => $jobSeeker->name,
                    'email' => $jobSeeker->email,

                    'profile' => $this->when(
                        $jobSeeker->relationLoaded('jobSeekerProfile'),
                        function () use ($jobSeeker) {
                            $profile = $jobSeeker->jobSeekerProfile;

                            return $profile ? $profile->toArray() : null;
                        }
                    ),

                    'skills' => $this->whenLoadedRelation(
                        $jobSeeker,
                        'skills'
                    ),

                    'educations' => $this->whenLoadedRelation(
                        $jobSeeker,
                        'educations'
                    ),

                    'experiences' => $this->whenLoadedRelation(
                        $jobSeeker,
                        'experiences'
                    ),
                ];
            }),

            'status_histories' => $this->whenLoaded(
                'statusHistories',
                function () {
                    return $this->statusHistories->map(function ($history) {
                        return [
                            'id' => $history->id,
                            'old_status' => $history->old_status,
                            'new_status' => $history->new_status,
                            'note' => $history->note,
                            'changed_by_company_id' =>
                            $history->changed_by_company_id,
                            'created_at' => $history->created_at,
                        ];
                    });
                }
            ),

            'created_at' => $this->created_at,
        ];
    }

    private function whenLoadedRelation(
        $model,
        string $relation
    ) {
        return $model->relationLoaded($relation)
            ? $model->getRelation($relation)
            : null;
    }
}
