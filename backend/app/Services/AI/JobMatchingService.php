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

        // Find a previously completed evaluation with exactly the same input hash.
        // This also supports reusing an older result if input changes
        // and later returns to a previously evaluated version.
        $existingAnalysis = $application->aiAnalyses()
            ->where('analysis_type', 'applicant_evaluation')
            ->where('status', 'completed')
            ->where('input_hash', $inputHash)
            ->whereNotNull('result')
            ->latest('id')
            ->first();

        // Reuse the existing result if the input has not changed.
        if (
            $existingAnalysis
            && $existingAnalysis->input_hash === $inputHash
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

        // Create a new analysis record when:
        // - No successful evaluation exists for this input.
        // - The input hash is different.
        // - The previous result is invalid.
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

        try {
            // Send applicant and job information to Groq AI.
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
            // Mark the analysis as failed if Groq or validation fails.
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
