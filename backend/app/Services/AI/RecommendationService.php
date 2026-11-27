<?php

namespace App\Services\AI;

use App\Enums\JobStatus;
use App\Models\AiAnalysis;
use App\Models\AiRecommendation;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class RecommendationService
{
    private const DATABASE_JOB_LIMIT = 100;

    private const AI_JOB_LIMIT = 30;

    public function __construct(
        protected GroqService $groq
    ) {}

    /**
     * Build the current candidate and eligible-job input.
     *
     * The database-filtered job pool is included in the hash.
     * The requested recommendation limit is also included so that
     * analyses generated with different limits are not reused.
     */
    private function buildRecommendationInput(
        User $user,
        int $limit = 10
    ): array {
        $user->loadMissing([
            'jobSeekerProfile',
            'skills',
            'educations',
            'experiences',
        ]);

        $profile = $user->jobSeekerProfile;

        $candidateProfile = [
            'professional_title' => $profile?->professional_title,
            'bio' => $profile?->bio,
            'city' => $profile?->city,
            'expected_salary' => $profile?->expected_salary,
            'experience_years' => $profile?->experience_years,

            'skills' => $user->skills->map(fn($skill) => [
                'name' => $skill->name,
                'skill_level' => $skill->skill_level,
            ])->values()->all(),

            'educations' => $user->educations->map(fn($education) => [
                'institution' => $education->institution,
                'degree' => $education->degree,
                'field_of_study' => $education->field_of_study,
                'description' => $education->description,
            ])->values()->all(),

            'experiences' => $user->experiences->map(fn($experience) => [
                'job_title' => $experience->job_title,
                'company_name' => $experience->company_name,
                'employment_type' => $experience->employment_type,
                'description' => $experience->description,
                'is_current' => $experience->is_current,
            ])->values()->all(),
        ];

        // Stage 1: Filter and rank jobs in the database.
        $jobs = $this->getCandidateJobs(
            $user,
            self::DATABASE_JOB_LIMIT
        );

        $jobsInput = $jobs->map(fn(Job $job) => [
            'id' => $job->id,
            'title' => $job->title,
            'description' => $job->description_plain_text
                ?? strip_tags($job->description ?? ''),
            'requirements' => $job->requirements,
            'responsibilities' => $job->responsibilities,
            'employment_type' => $job->employment_type,
            'workplace_type' => $job->workplace_type,
            'country' => $job->country,
            'city' => $job->city,
            'salary_min' => $job->salary_min,
            'salary_max' => $job->salary_max,
            'salary_type' => $job->salary_type,
            'category' => $job->category?->name,
            'company' => $job->company?->name,
        ])->values()->all();

        $inputData = [
            'candidate' => $candidateProfile,
            'jobs' => $jobsInput,
            'recommendation_limit' => $limit,
        ];

        $inputHash = hash(
            'sha256',
            json_encode(
                $inputData,
                JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_THROW_ON_ERROR
            )
        );

        return [
            'input_data' => $inputData,
            'input_hash' => $inputHash,
            'jobs' => $jobs,
        ];
    }

    /**
     * Get the current input hash.
     *
     * Keep the default limit consistent with recommend().
     */
    public function getCurrentInputHash(
        User $user,
        int $limit = 10
    ): string {
        $limit = min(max($limit, 1), 20);

        return $this->buildRecommendationInput(
            $user,
            $limit
        )['input_hash'];
    }

    /**
     * Check whether a completed analysis exists for this exact input.
     */
    public function hasCompletedAnalysisForHash(
        User $user,
        string $inputHash
    ): bool {
        return AiAnalysis::query()
            ->where('user_id', $user->id)
            ->where('analysis_type', 'job_matching')
            ->where('status', 'completed')
            ->where('input_hash', $inputHash)
            ->whereNotNull('result')
            ->exists();
    }

    /**
     * Check whether generation for this exact input is already running.
     */
    public function hasPendingAnalysisForHash(
        User $user,
        string $inputHash
    ): bool {
        return AiAnalysis::query()
            ->where('user_id', $user->id)
            ->where('analysis_type', 'job_matching')
            ->where('status', 'pending')
            ->where('input_hash', $inputHash)
            ->exists();
    }

    /**
     * Generate recommendations or reuse an existing completed analysis.
     */
    public function recommend(User $user, int $limit = 10): array
    {
        $limit = min(max($limit, 1), 20);

        $recommendationInput = $this->buildRecommendationInput(
            $user,
            $limit
        );

        $inputData = $recommendationInput['input_data'];
        $inputHash = $recommendationInput['input_hash'];
        $jobs = $recommendationInput['jobs'];

        // Reuse an existing completed analysis when the hash matches.
        $existingAnalysis = AiAnalysis::query()
            ->where('user_id', $user->id)
            ->where('analysis_type', 'job_matching')
            ->where('status', 'completed')
            ->where('input_hash', $inputHash)
            ->whereNotNull('result')
            ->latest('id')
            ->first();

        if ($existingAnalysis) {
            $recommendations = $existingAnalysis
                ->recommendations()
                ->whereHas('job', function ($query) use ($user) {
                    $query
                        ->where('status', JobStatus::Published->value)
                        ->where(function ($query) {
                            $query->whereNull('deadline')
                                ->orWhereDate('deadline', '>=', today());
                        })
                        ->whereDoesntHave(
                            'applications',
                            fn($query) => $query->where(
                                'job_seeker_id',
                                $user->id
                            )
                        );
                })
                ->with(['job.company', 'job.category'])
                ->orderByDesc('match_score')
                ->limit($limit)
                ->get();

            return [
                'analysis' => $existingAnalysis,
                'recommendations' => $recommendations,
                'total' => $recommendations->count(),
                'reused' => true,
            ];
        }

        // Prevent another analysis for the same input if one is pending.
        $pendingAnalysis = AiAnalysis::query()
            ->where('user_id', $user->id)
            ->where('analysis_type', 'job_matching')
            ->where('status', 'pending')
            ->where('input_hash', $inputHash)
            ->first();

        if ($pendingAnalysis) {
            return [
                'analysis' => $pendingAnalysis,
                'recommendations' => collect(),
                'total' => 0,
                'reused' => false,
                'pending' => true,
            ];
        }

        $analysis = AiAnalysis::create([
            'user_id' => $user->id,
            'job_id' => null,
            'application_id' => null,
            'requested_by' => $user->id,

            'analysis_type' => 'job_matching',
            'input_data' => $inputData,
            'input_hash' => $inputHash,
            'result' => null,

            'provider' => 'Groq',
            'model' => config('services.groq.model'),
            'status' => 'pending',
        ]);

        try {
            // No eligible jobs: save a completed empty result.
            // This prevents repeated attempts for an empty job list.
            if ($jobs->isEmpty()) {
                $analysis->update([
                    'result' => [
                        'recommendations' => [],
                    ],
                    'status' => 'completed',
                ]);

                return [
                    'analysis' => $analysis->fresh(),
                    'recommendations' => collect(),
                    'total' => 0,
                    'reused' => false,
                ];
            }

            /*
             * Stage 2: Send only the top database-ranked jobs to Groq.
             *
             * Keep all 100 jobs in $inputData for hashing.
             * Only the first 30 are sent to the AI.
             */
            $aiInputData = $inputData;

            $aiInputData['jobs'] = array_slice(
                $inputData['jobs'],
                0,
                self::AI_JOB_LIMIT
            );

            // These are the only IDs Groq is allowed to recommend.
            $allowedJobIds = collect($aiInputData['jobs'])
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            $aiInputData['available_job_ids'] = $allowedJobIds;

            $result = $this->groq->chatJson([
                [
                    'role' => 'system',
                    'content' => <<<'PROMPT'
You are an AI job recommendation assistant.

Recommend jobs to the candidate based only on the supplied candidate profile and jobs.

Evaluate:
- Relevant skills and skill levels.
- Professional experience.
- Education and field of study.
- Job requirements and responsibilities.
- Employment type and workplace type.
- Candidate location and job location.
- Expected salary and job salary, when available.

Rules:
- Only recommend jobs included in the supplied jobs list.
- Only use IDs included in available_job_ids.
- Never invent job IDs.
- Do not infer missing candidate information.
- Do not evaluate protected characteristics.
- Do not recommend jobs that are not supplied.
- Do not treat missing information as proof that the candidate is unsuitable.
- Return valid JSON only.
- Return at most recommendation_limit recommendations.
- Sort recommendations from highest match score to lowest.

Return this JSON structure:

{
    "recommendations": [
        {
            "job_id": 1,
            "match_score": 90,
            "reason": "Explain why this job matches the candidate."
        }
    ]
}

match_score must be a number from 0 to 100.
PROMPT,
                ],

                [
                    'role' => 'user',
                    'content' => json_encode(
                        $aiInputData,
                        JSON_UNESCAPED_UNICODE
                            | JSON_UNESCAPED_SLASHES
                            | JSON_THROW_ON_ERROR
                    ),
                ],
            ]);

            if (
                ! isset($result['recommendations'])
                || ! is_array($result['recommendations'])
            ) {
                throw new RuntimeException(
                    'Groq returned an invalid recommendations structure.'
                );
            }

            $validatedRecommendations = [];
            $usedJobIds = [];

            foreach ($result['recommendations'] as $recommendation) {
                $jobId = $recommendation['job_id'] ?? null;
                $score = $recommendation['match_score'] ?? null;
                $reason = $recommendation['reason'] ?? null;

                /*
                 * Validate against the 30 jobs actually sent to Groq.
                 * Do not validate against all 100 database results.
                 */
                if (
                    ! is_numeric($jobId)
                    || ! in_array((int) $jobId, $allowedJobIds, true)
                    || in_array((int) $jobId, $usedJobIds, true)
                ) {
                    continue;
                }

                if (
                    ! is_numeric($score)
                    || $score < 0
                    || $score > 100
                ) {
                    continue;
                }

                if (
                    ! is_string($reason)
                    || trim($reason) === ''
                ) {
                    continue;
                }

                $jobId = (int) $jobId;

                $usedJobIds[] = $jobId;

                $validatedRecommendations[] = [
                    'job_id' => $jobId,
                    'match_score' => (float) $score,
                    'reason' => trim($reason),
                ];
            }

            // Sort by match score, highest first.
            usort(
                $validatedRecommendations,
                fn($a, $b) => $b['match_score'] <=> $a['match_score']
            );

            $validatedRecommendations = array_slice(
                $validatedRecommendations,
                0,
                $limit
            );

            // Save recommendations and completed analysis atomically.
            DB::transaction(function () use (
                $user,
                $analysis,
                $validatedRecommendations
            ) {

                foreach ($validatedRecommendations as $recommendation) {
                    AiRecommendation::create([
                        'user_id' => $user->id,
                        'job_id' => $recommendation['job_id'],
                        'analysis_id' => $analysis->id,
                        'match_score' => $recommendation['match_score'],
                        'reason' => $recommendation['reason'],
                        'is_viewed' => false,
                    ]);
                }

                $analysis->update([
                    'result' => [
                        'recommendations' => $validatedRecommendations,
                    ],
                    'status' => 'completed',
                ]);
            });

            $savedRecommendations = $analysis
                ->recommendations()
                ->with(['job.company', 'job.category'])
                ->orderByDesc('match_score')
                ->get();

            return [
                'analysis' => $analysis->fresh(),
                'recommendations' => $savedRecommendations,
                'total' => $savedRecommendations->count(),
                'reused' => false,
            ];
        } catch (Throwable $e) {
            $analysis->update([
                'status' => 'failed',
                'result' => [
                    'error' => 'AI recommendation generation failed.',
                ],
            ]);

            report($e);

            throw $e;
        }
    }


    public function getRecommendations(
        User $user,
        int $limit = 10
    ): array {
        $limit = min(max($limit, 1), 20);

        // Get hash from the candidate's CURRENT input data.
        $currentInputHash = $this->getCurrentInputHash($user, $limit);

        // Only retrieve analysis generated from the current input.
        $analysis = AiAnalysis::query()
            ->where('user_id', $user->id)
            ->where('analysis_type', 'job_matching')
            ->where('status', 'completed')
            ->where('input_hash', $currentInputHash)
            ->whereNotNull('result')
            ->latest('id')
            ->first();

        if (! $analysis) {
            return [
                'analysis' => null,
                'recommendations' => collect(),
                'total' => 0,
            ];
        }

        $recommendations = $analysis
            ->recommendations()
            ->whereHas('job', function ($query) use ($user) {
                $query
                    ->where('status', JobStatus::Published->value)
                    ->where(function ($query) {
                        $query->whereNull('deadline')
                            ->orWhereDate('deadline', '>=', today());
                    })
                    ->whereDoesntHave(
                        'applications',
                        fn($query) => $query->where(
                            'job_seeker_id',
                            $user->id
                        )
                    );
            })
            ->with(['job.company', 'job.category'])
            ->orderByDesc('match_score')
            ->limit($limit)
            ->get();

        return [
            'analysis' => $analysis,
            'recommendations' => $recommendations,
            'total' => $recommendations->count(),
        ];
    }

    /**
     * Retrieve a manageable, relevant pool of published jobs
     * before calling AI.
     *
     * Database filtering:
     * - Published jobs only.
     * - Jobs that have not expired.
     * - Jobs the candidate has not already applied to.
     * - Jobs matching at least one candidate skill/title term.
     *
     * Ranking:
     * - Number of matching candidate terms.
     * - Candidate's city.
     * - Most recently approved jobs.
     *
     * If the candidate has no usable search terms, recent published
     * jobs are used as a fallback.
     */
    private function getCandidateJobs(
        User $user,
        int $databaseLimit = self::DATABASE_JOB_LIMIT
    ): Collection {
        $user->loadMissing([
            'jobSeekerProfile',
            'skills',
        ]);

        $profile = $user->jobSeekerProfile;

        /*
         * Build search terms from skills and professional title.
         *
         * Keep both complete phrases and individual words.
         * Limit the number of terms to prevent excessively large SQL.
         */
        $terms = collect($user->skills)
            ->pluck('name')
            ->push($profile?->professional_title)
            ->filter(fn($value) => is_string($value) && trim($value) !== '')
            ->flatMap(function ($value) {
                $value = mb_strtolower(trim($value));

                $words = preg_split(
                    '/[^\p{L}\p{N}]+/u',
                    $value
                );

                return array_merge(
                    [$value],
                    $words ?: []
                );
            })
            ->map(fn($term) => trim($term))
            ->filter(fn($term) => mb_strlen($term) >= 2)
            ->unique()
            ->take(30)
            ->values();

        /*
         * Search across job title, descriptions, requirements
         * and responsibilities.
         */
        $searchableSql = "
            LOWER(CONCAT_WS(' ',
                COALESCE(jobs.title, ''),
                COALESCE(jobs.description, ''),
                COALESCE(jobs.description_plain_text, ''),
                COALESCE(jobs.requirements, ''),
                COALESCE(jobs.responsibilities, '')
            ))
        ";

        $query = Job::query()
            ->where('jobs.status', JobStatus::Published->value)
            ->where(function ($query) {
                $query->whereNull('jobs.deadline')
                    ->orWhereDate('jobs.deadline', '>=', today());
            })
            ->whereDoesntHave('applications', function ($query) use ($user) {
                $query->where('job_seeker_id', $user->id);
            })
            ->with([
                'company',
                'category',
            ]);

        /*
         * Require at least one matching term when the candidate
         * has skills or a professional title.
         *
         * Without search terms, fall back to recent published jobs.
         */
        if ($terms->isNotEmpty()) {
            $query->where(function ($query) use (
                $terms,
                $searchableSql
            ) {
                foreach ($terms as $term) {
                    $query->orWhereRaw(
                        "{$searchableSql} LIKE ?",
                        [
                            '%' . addcslashes($term, '\\%_') . '%',
                        ]
                    );
                }
            });

            /*
             * Rank jobs by the number of matched candidate terms.
             */
            $scoreSql = $terms
                ->map(
                    fn() => "CASE WHEN {$searchableSql} LIKE ? THEN 1 ELSE 0 END"
                )
                ->implode(' + ');

            $scoreBindings = $terms
                ->map(
                    fn($term) => '%' . addcslashes($term, '\\%_') . '%'
                )
                ->all();

            $query->orderByRaw(
                "({$scoreSql}) DESC",
                $scoreBindings
            );
        }

        /*
         * Prefer jobs in the candidate's city.
         * This is a ranking preference, not an exclusion.
         *
         * JobSeekerProfile has a city field; it does not have
         * a country field in the model information provided.
         */
        if ($profile?->city) {
            $query->orderByRaw(
                'CASE WHEN LOWER(jobs.city) = ? THEN 1 ELSE 0 END DESC',
                [mb_strtolower($profile->city)]
            );
        }

        return $query
            ->orderByDesc('jobs.approved_at')
            ->orderByDesc('jobs.id')
            ->limit($databaseLimit)
            ->get();
    }
}
