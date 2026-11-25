<?php

namespace App\Services\AI;

use App\Models\Application;
use App\Models\Job;
use RuntimeException;

class ApplicantEvaluationInputBuilder
{
    /**
     * Build evaluation input data and generate its SHA-256 hash.
     */
    public function build(Job $job, Application $application): array
    {
        // Load all relationships required for evaluation.
        $application->loadMissing([
            'jobSeeker.jobSeekerProfile',
            'jobSeeker.skills',
            'jobSeeker.educations',
            'jobSeeker.experiences',
            'answers.jobQuestion',
        ]);

        $applicant = $application->jobSeeker;

        if (! $applicant) {
            throw new RuntimeException(
                'Applicant account could not be found.'
            );
        }

        $profile = $applicant->jobSeekerProfile;

        // Build the complete evaluation input.
        $inputData = [
            'job' => [
                'title' => $job->title,
                'description' => $job->description_plain_text
                    ?: strip_tags($job->description),
                'requirements' => $job->requirements,
                'responsibilities' => $job->responsibilities,
                'employment_type' => $job->employment_type,
                'workplace_type' => $job->workplace_type,
            ],

            'applicant' => [
                'professional_title' => $profile?->professional_title,
                'bio' => $profile?->bio,
                'experience_years' => $profile?->experience_years,

                'skills' => $applicant->skills
                    ->sortBy('id')
                    ->map(fn($skill) => [
                        'name' => $skill->name,
                        'level' => $skill->skill_level,
                    ])
                    ->values()
                    ->all(),

                'educations' => $applicant->educations
                    ->sortBy('id')
                    ->map(fn($education) => [
                        'institution' => $education->institution,
                        'degree' => $education->degree,
                        'field_of_study' => $education->field_of_study,
                        'description' => $education->description,
                    ])
                    ->values()
                    ->all(),

                'experiences' => $applicant->experiences
                    ->sortBy('id')
                    ->map(fn($experience) => [
                        'job_title' => $experience->job_title,
                        'company_name' => $experience->company_name,
                        'employment_type' => $experience->employment_type,
                        'start_date' => $experience->start_date?->toDateString(),
                        'end_date' => $experience->end_date?->toDateString(),
                        'is_current' => $experience->is_current,
                        'description' => $experience->description,
                    ])
                    ->values()
                    ->all(),

                'application_answers' => $application->answers
                    ->sortBy('id')
                    ->map(fn($answer) => [
                        'question' => $answer->jobQuestion?->question,
                        'answer' => $answer->answer,
                    ])
                    ->values()
                    ->all(),
            ],
        ];

        return [
            'input_data' => $inputData,
            'input_hash' => $this->generateInputHash($inputData),
        ];
    }

    /**
     * Normalize input data for consistent hashing.
     *
     * Associative array keys are sorted.
     * List order is preserved.
     */
    private function normalizeInputData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->normalizeInputData($value);
            }
        }

        if (! array_is_list($data)) {
            ksort($data);
        }

        return $data;
    }

    /**
     * Generate a SHA-256 fingerprint.
     */
    private function generateInputHash(array $inputData): string
    {
        $normalizedData = $this->normalizeInputData($inputData);

        return hash(
            'sha256',
            json_encode(
                $normalizedData,
                JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_THROW_ON_ERROR
            )
        );
    }
}
