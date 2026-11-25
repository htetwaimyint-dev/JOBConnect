<?php

namespace App\Services\AI;

use App\Enums\ApplicationStatus;
use App\Models\AiAnalysis;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Throwable;
use InvalidArgumentException;
use RuntimeException;

class JobMatchingService
{
    public function __construct(
        protected GroqService $groq
    ) {}

    /**
     * Evaluate all eligible applicants for one job.
     */
    public function evaluateApplicants(
        Job $job,
        User $employer
    ): array {
        $applications = $job->applications()
            ->whereIn('status', [
                ApplicationStatus::Pending->value,
                ApplicationStatus::Reviewed->value,
            ])
            ->with([
                'jobSeeker.jobSeekerProfile',
                'jobSeeker.skills',
                'jobSeeker.educations',
                'jobSeeker.experiences',
                'answers.jobQuestion',
            ])
            ->get();

        $evaluations = [];
        $failures = [];

        foreach ($applications as $application) {
            try {
                $evaluations[] = $this->evaluateApplicant(
                    $job,
                    $application,
                    $employer
                );
            } catch (Throwable $e) {
                report($e);

                $failures[] = [
                    'application_id' => $application->id,
                    'message' => 'AI evaluation failed. Please try again.',
                ];
            }
        }

        return [
            'job_id' => $job->id,
            'total_applicants' => $applications->count(),
            'successful' => count($evaluations),
            'failed' => count($failures),
            'evaluations' => $evaluations,
            'failures' => $failures,
        ];
    }


    /**
     * Evaluate one applicant for a specific job.
     */
    public function evaluateSingleApplicant(
        Job $job,
        Application $application,
        User $employer
    ): array {
        if (! in_array($application->status, [
            ApplicationStatus::Pending,
            ApplicationStatus::Reviewed,
        ], true)) {
            throw new InvalidArgumentException(
                'This application is not eligible for AI screening.'
            );
        }

        // Return existing successful evaluation instead of calling AI again.
        $existingAnalysis = $application->latestSuccessfulAiAnalysis;

        if ($existingAnalysis) {
            return [
                'application_id' => $application->id,
                'analysis_id' => $existingAnalysis->id,
                'match_score' => $existingAnalysis->result['match_score'],
                'recommendation' => $existingAnalysis->result['recommendation'],
                'result' => $existingAnalysis->result,
            ];
        }

        $application->loadMissing([
            'jobSeeker.jobSeekerProfile',
            'jobSeeker.skills',
            'jobSeeker.educations',
            'jobSeeker.experiences',
            'answers.jobQuestion',
        ]);

        return $this->evaluateApplicant(
            $job,
            $application,
            $employer
        );
    }


    /**
     * Evaluate one applicant using Groq AI.
     */
    public function evaluateApplicant(
        Job $job,
        Application $application,
        User $employer
    ): array {
        $applicant = $application->jobSeeker;

        if (! $applicant) {
            throw new RuntimeException(
                'Applicant account could not be found.'
            );
        }

        $profile = $applicant->jobSeekerProfile;

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
                    ->map(fn($skill) => [
                        'name' => $skill->name,
                        'level' => $skill->skill_level,
                    ])
                    ->values()
                    ->all(),

                'educations' => $applicant->educations
                    ->map(fn($education) => [
                        'institution' => $education->institution,
                        'degree' => $education->degree,
                        'field_of_study' => $education->field_of_study,
                        'description' => $education->description,
                    ])
                    ->values()
                    ->all(),

                'experiences' => $applicant->experiences
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
                    ->map(fn($answer) => [
                        'question' => $answer->jobQuestion?->question,
                        'answer' => $answer->answer,
                    ])
                    ->values()
                    ->all(),
            ],
        ];

        $analysis = AiAnalysis::create([
            'user_id' => $applicant->id,
            'job_id' => $job->id,
            'application_id' => $application->id,
            'requested_by' => $employer->id,
            'analysis_type' => 'applicant_evaluation',
            'input_data' => $inputData,
            'result' => null,
            'provider' => 'Groq',
            'model' => config('services.groq.model'),
            'status' => 'pending',
        ]);

        try {
            $result = $this->groq->chatJson([
                [
                    'role' => 'system',
                    'content' => <<<'PROMPT'
You are an objective job applicant evaluation assistant.

Evaluate the applicant using:
- Job description, requirements and responsibilities.
- Applicant profile and professional title.
- Applicant skills and skill levels.
- Education background.
- Relevant work experience.
- Application answers submitted for this specific job.

When evaluating application answers:
- Compare each answer with the job requirements.
- Consider answers as additional evidence of the applicant's knowledge and experience.
- Do not assume an answer is true without supporting evidence.
- Do not treat unanswered questions as proof that the applicant lacks a skill.
- Do not give credit for skills or experience that are not supported by the supplied information.

Rules:
- Do not make hiring decisions.
- Do not automatically shortlist or reject applicants.
- Do not infer missing information.
- Do not penalize applicants for information they did not provide.
- Do not evaluate age, gender, race, religion or other protected traits.
- Use only the supplied information.
- Return a valid JSON object only.

Return exactly these fields:
{
    "match_score": 0,
    "matched_skills": [],
    "missing_skills": [],
    "relevant_experience": [],
    "strengths": [],
    "weaknesses": [],
    "recommendation": "string",
    "reasoning": "string"
}

match_score must be a number from 0 to 100.

recommendation should be one of:
"strong_match", "moderate_match", "low_match".

Explain the evaluation using evidence from the supplied data.
PROMPT,
                ],
                [
                    'role' => 'user',
                    'content' => json_encode(
                        $inputData,
                        JSON_UNESCAPED_UNICODE
                            | JSON_UNESCAPED_SLASHES
                            | JSON_THROW_ON_ERROR
                    ),
                ],
            ]);

            $score = $result['match_score'] ?? null;

            if (
                ! is_numeric($score)
                || $score < 0
                || $score > 100
            ) {
                throw new RuntimeException(
                    'AI returned an invalid match score.'
                );
            }

            $requiredFields = [
                'matched_skills',
                'missing_skills',
                'relevant_experience',
                'strengths',
                'weaknesses',
            ];

            foreach ($requiredFields as $field) {
                if (
                    ! isset($result[$field])
                    || ! is_array($result[$field])
                ) {
                    throw new RuntimeException(
                        "AI returned an invalid {$field} field."
                    );
                }
            }

            if (! in_array($result['recommendation'] ?? null, [
                'strong_match',
                'moderate_match',
                'low_match',
            ], true)) {
                throw new RuntimeException(
                    'AI returned an invalid recommendation.'
                );
            }

            if (
                ! isset($result['reasoning'])
                || ! is_string($result['reasoning'])
                || trim($result['reasoning']) === ''
            ) {
                throw new RuntimeException(
                    'AI returned invalid reasoning.'
                );
            }

            $result['match_score'] = (float) $score;

            $analysis->update([
                'result' => $result,
                'status' => 'completed',
            ]);

            return [
                'application_id' => $application->id,
                'analysis_id' => $analysis->id,
                'match_score' => $result['match_score'],
                'recommendation' => $result['recommendation'],
                'result' => $result,
            ];
        } catch (Throwable $e) {
            $analysis->update([
                'status' => 'failed',
                'result' => [
                    'error' => 'AI evaluation failed.',
                ],
            ]);

            report($e);

            throw $e;
        }
    }
}
