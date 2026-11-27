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
        protected GroqService $groq,
        protected ApplicantEvaluationInputBuilder $inputBuilder
    ) {}

    /**
     * Evaluate all eligible applicants for one job.
     */
    public function evaluateApplicants(
        Job $job,
        User $employer
    ): array {
        // Get only applications eligible for AI screening.
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

        // Evaluate every eligible applicant.
        foreach ($applications as $application) {
            try {
                $evaluations[] = $this->evaluateSingleApplicant(
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

        // Count all successful evaluations before limiting results.
        $totalSuccessful = count($evaluations);

        // Sort by match score (highest first).
        // Use application ID as a consistent tie-breaker.
        usort($evaluations, function ($a, $b) {
            $scoreComparison = $b['match_score'] <=> $a['match_score'];

            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            return $a['application_id'] <=> $b['application_id'];
        });

        // Return only the top 10 applicants.
        $topTenEvaluations = array_slice($evaluations, 0, 10);

        return [
            'job_id' => $job->id,
            'total_applicants' => $applications->count(),
            'successful' => $totalSuccessful,
            'failed' => count($failures),
            'returned' => count($topTenEvaluations),
            'evaluations' => $topTenEvaluations,
            'failures' => $failures,
        ];
    }

    /**
     * Evaluate one applicant for a specific job.
     *
     * This method is shared by individual and batch evaluation.
     */
    public function evaluateSingleApplicant(
        Job $job,
        Application $application,
        User $employer
    ): array {
        // Check whether the application is eligible for AI screening.
        if (! in_array($application->status, [
            ApplicationStatus::Pending,
            ApplicationStatus::Reviewed,
        ], true)) {
            throw new InvalidArgumentException(
                'This application is not eligible for AI screening.'
            );
        }

        // Load applicant information required for evaluation.
        $application->loadMissing([
            'jobSeeker.jobSeekerProfile',
            'jobSeeker.skills',
            'jobSeeker.educations',
            'jobSeeker.experiences',
            'answers.jobQuestion',
        ]);

        // Run the shared evaluation process.
        return $this->evaluateApplicant(
            $job,
            $application,
            $employer
        );
    }

    /**
     * Evaluate one applicant using Groq AI.
     *
     * Before calling Groq:
     * 1. Build the current input data.
     * 2. Generate its SHA-256 hash.
     * 3. Check for an existing completed evaluation.
     * 4. Reuse the result if the input hash is unchanged.
     */
    public function evaluateApplicant(
        Job $job,
        Application $application,
        User $employer
    ): array {
        // Build the current evaluation input and its hash.
        $evaluationInput = $this->inputBuilder->build(
            $job,
            $application
        );

        $inputData = $evaluationInput['input_data'];
        $inputHash = $evaluationInput['input_hash'];

        // Get the applicant account for the AI analysis record.
        $applicant = $application->jobSeeker;

        if (! $applicant) {
            throw new RuntimeException(
                'Applicant account could not be found.'
            );
        }


        // Find an existing analysis with the same input hash.
        $existingAnalysis = $application->aiAnalyses()
            ->where('analysis_type', 'applicant_evaluation')
            ->where('input_hash', $inputHash)
            ->whereIn('status', ['completed', 'pending'])
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhereNotNull('result');
            })
            ->latest('id')
            ->first();

        // Reuse a valid completed evaluation.
        if (
            $existingAnalysis
            && $existingAnalysis->status === 'completed'
            && is_array($existingAnalysis->result)
            && isset(
                $existingAnalysis->result['match_score'],
                $existingAnalysis->result['recommendation']
            )
        ) {
            return [
                'application_id' => $application->id,
                'analysis_id' => $existingAnalysis->id,
                'match_score' => $existingAnalysis->result['match_score'],
                'recommendation' => $existingAnalysis->result['recommendation'],
                'result' => $existingAnalysis->result,
            ];
        }

        // Reuse a pending analysis after a temporary interruption.
        if (
            $existingAnalysis
            && $existingAnalysis->status === 'pending'
        ) {
            $analysis = $existingAnalysis;
        } else {
            // Create a new analysis only when no reusable record exists.
            $analysis = AiAnalysis::create([
                'user_id' => $applicant->id,
                'job_id' => $job->id,
                'application_id' => $application->id,
                'requested_by' => $employer->id,
                'analysis_type' => 'applicant_evaluation',
                'input_data' => $inputData,
                'input_hash' => $inputHash,
                'result' => null,
                'provider' => 'Groq',
                'model' => config('services.groq.model'),
                'status' => 'pending',
            ]);
        }



        $evaluationSchema = [
            'type' => 'object',
            'properties' => [
                'match_score' => [
                    'type' => 'number',
                ],
                'matched_skills' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'missing_skills' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'relevant_experience' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'strengths' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'weaknesses' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'recommendation' => [
                    'type' => 'string',
                    'enum' => [
                        'strong_match',
                        'moderate_match',
                        'low_match',
                    ],
                ],
                'reasoning' => [
                    'type' => 'string',
                ],
            ],
            'required' => [
                'match_score',
                'matched_skills',
                'missing_skills',
                'relevant_experience',
                'strengths',
                'weaknesses',
                'recommendation',
                'reasoning',
            ],
            'additionalProperties' => false,
        ];

        try {
            // Send applicant and job information to Groq AI.
            $result = $this->groq->chatStructuredJson([
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


Output requirements:
- match_score must be a number from 0 to 100.
- matched_skills must be an array of strings.
- missing_skills must be an array of strings.
- relevant_experience must be an array of full, readable sentences, NOT JSON objects.
- Each relevant_experience sentence must describe one relevant work experience using the available job title, company name, employment type, start date, end date, and description.
- If the experience is current, use "Present" as the end date.
- Do not include unavailable or null information as literal text.
- Do not invent missing details or dates.
- If no relevant work experience is available, return an empty array.
- strengths must be an array of strings.
- weaknesses must be an array of strings.
- recommendation must be exactly one of:
  "strong_match", "moderate_match", "low_match".
- reasoning must be a clear, complete paragraph explaining the evaluation using evidence from the supplied data.

Example relevant_experience output:
[
    "The applicant worked as an AI Engineer at ABCD on a full-time basis from January 2023 to Present."
]


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
            ], $evaluationSchema, [
                'max_completion_tokens' => 8192,
                'reasoning_effort' => 'low',
            ]);

            // Validate the match score.
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

            // Validate required array fields.
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

            // Ensure all evaluation array items are strings.
            foreach ($requiredFields as $field) {
                foreach ($result[$field] as $item) {
                    if (! is_string($item) || trim($item) === '') {
                        throw new RuntimeException(
                            "AI returned an invalid item in {$field}."
                        );
                    }
                }
            }

            // Validate the recommendation value.
            if (! in_array($result['recommendation'] ?? null, [
                'strong_match',
                'moderate_match',
                'low_match',
            ], true)) {
                throw new RuntimeException(
                    'AI returned an invalid recommendation.'
                );
            }

            // Validate the reasoning text.
            if (
                ! isset($result['reasoning'])
                || ! is_string($result['reasoning'])
                || trim($result['reasoning']) === ''
            ) {
                throw new RuntimeException(
                    'AI returned invalid reasoning.'
                );
            }

            // Ensure the match score is stored as a float.
            $result['match_score'] = (float) $score;

            // Save the completed AI evaluation.
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
            // Keep the analysis pending when Groq rate limits the request.
            if ($e instanceof GroqRateLimitException) {
                report($e);

                throw $e;
            }

            // Mark other errors as failed.
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
