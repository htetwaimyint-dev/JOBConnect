<?php

namespace App\Http\Controllers\Api\V1\AI;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\User;
use App\Services\Employer\CompanyService;
use App\Services\Jobs\EmployerJobService;
use App\Services\AI\JobMatchingService;
use Illuminate\Http\Request;
use App\Enums\ApplicationStatus;
use App\Jobs\EvaluateApplicantJob;
use App\Models\ApplicantEvaluationBatch;
use App\Models\Job;
use Illuminate\Support\Facades\DB;

class JobMatchingController extends Controller
{
    protected CompanyService $companyService;

    protected EmployerJobService $jobService;

    protected JobMatchingService $jobMatchingService;

    public function __construct(
        CompanyService $companyService,
        EmployerJobService $jobService,
        JobMatchingService $jobMatchingService
    ) {
        $this->companyService = $companyService;
        $this->jobService = $jobService;
        $this->jobMatchingService = $jobMatchingService;
    }

    /**
     * Dispatch background evaluation for all eligible applicants.
     */
    public function evaluateApplicants(
        Request $request,
        int $jobId
    ) {
        $employer = $request->user();

        $company = $this->getEmployerCompany($employer);

        // Verify that this job belongs to the employer's company.
        $job = $this->jobService->getJob($company, $jobId);

        $result = DB::transaction(function () use ($job, $employer) {

            // Lock the job row to prevent duplicate active batches.
            Job::query()
                ->whereKey($job->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Reuse an existing active batch if one is already running.
            $activeBatch = ApplicantEvaluationBatch::query()
                ->where('job_id', $job->id)
                ->whereIn('status', ['queued', 'processing'])
                ->latest('id')
                ->first();

            if ($activeBatch) {
                return [
                    'batch' => $activeBatch,
                    'created' => false,
                ];
            }

            // Get only eligible applications.
            $applicationIds = $job->applications()
                ->whereIn('status', [
                    ApplicationStatus::Pending->value,
                    ApplicationStatus::Reviewed->value,
                ])
                ->orderBy('id')
                ->pluck('id');

            $hasApplicants = $applicationIds->isNotEmpty();

            // Create the evaluation batch.
            $batch = ApplicantEvaluationBatch::create([
                'job_id' => $job->id,
                'requested_by' => $employer->id,
                'total_applicants' => $applicationIds->count(),
                'status' => $hasApplicants ? 'queued' : 'completed',
                'completed_at' => $hasApplicants ? null : now(),
            ]);

            // Create one batch item for each applicant.
            foreach ($applicationIds as $applicationId) {
                $batch->items()->create([
                    'application_id' => $applicationId,
                    'status' => 'queued',
                ]);
            }

            return [
                'batch' => $batch,
                'created' => true,
            ];
        });

        $batch = $result['batch'];

        // Dispatch jobs only after the database transaction commits.
        if ($result['created'] && $batch->total_applicants > 0) {
            $batch->items()
                ->orderBy('id')
                ->pluck('id')
                ->each(function ($batchItemId) {
                    EvaluateApplicantJob::dispatch($batchItemId);
                });
        }

        return response()->json([
            'success' => true,
            'message' => $result['created']
                ? 'Applicant evaluation batch queued successfully.'
                : 'An applicant evaluation batch is already running.',
            'data' => [
                'batch_id' => $batch->id,
                'job_id' => $batch->job_id,
                'status' => $batch->status,
                'total_applicants' => $batch->total_applicants,
                'created' => $result['created'],
            ],
        ], 202);
    }

    /**
     * Evaluate one applicant for a specific application.
     */
    public function evaluateSingleApplicant(
        Request $request,
        int $applicationId
    ) {
        $employer = $request->user();

        $company = $this->getEmployerCompany($employer);

        // Find an application belonging to this employer's company.
        $application = Application::query()
            ->whereHas('job', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->findOrFail($applicationId);

        // Evaluate the selected applicant using AI.
        $result = $this->jobMatchingService->evaluateSingleApplicant(
            $application->job,
            $application,
            $employer
        );

        return response()->json([
            'success' => true,
            'message' => 'Applicant evaluation completed.',
            'data' => $result,
        ]);
    }

    /**
     * Get progress and ranked results of an applicant evaluation batch.
     */
    public function getBatchProgress(
        Request $request,
        int $jobId,
        int $batchId
    ) {
        $employer = $request->user();

        $company = $this->getEmployerCompany($employer);

        // Verify that the job belongs to this employer's company.
        $job = $this->jobService->getJob($company, $jobId);

        // Only allow access to batches belonging to this job.
        $batch = ApplicantEvaluationBatch::query()
            ->where('job_id', $job->id)
            ->with([
                'items' => function ($query) {
                    $query->with([
                        'application.jobSeeker.jobSeekerProfile',
                        'analysis',
                    ])->orderBy('id');
                },
            ])
            ->findOrFail($batchId);

        $items = $batch->items;

        // Calculate applicant counts.
        $completedCount = $items->where('status', 'completed')->count();
        $failedCount = $items->where('status', 'failed')->count();
        $processingCount = $items->where('status', 'processing')->count();
        $queuedCount = $items->where('status', 'queued')->count();

        // Failed applicants are also considered processed.
        $processedCount = $completedCount + $failedCount;

        $progressPercentage = $batch->total_applicants > 0
            ? (int) round(
                ($processedCount / $batch->total_applicants) * 100
            )
            : 100;

        // Identify final batch states.
        $isFinal = in_array(
            $batch->status,
            ['completed', 'completed_with_errors', 'failed'],
            true
        );

        // Get all successfully completed evaluations with valid scores.
        $completedApplicants = $items
            ->filter(function ($item) {
                return $item->status === 'completed'
                    && $item->analysis
                    && is_array($item->analysis->result)
                    && is_numeric(
                        $item->analysis->result['match_score'] ?? null
                    );
            })
            ->sort(function ($a, $b) {
                $scoreComparison =
                    $b->analysis->result['match_score']
                    <=>
                    $a->analysis->result['match_score'];

                if ($scoreComparison !== 0) {
                    return $scoreComparison;
                }

                // Use application ID as a consistent tie-breaker.
                return $a->application_id <=> $b->application_id;
            })
            ->values()
            ->map(function ($item) {
                $application = $item->application;
                $jobSeeker = $application?->jobSeeker;
                $profile = $jobSeeker?->jobSeekerProfile;
                $analysis = $item->analysis;
                $result = $analysis->result;

                return [
                    'application_id' => $application?->id,
                    'applicant_id' => $jobSeeker?->id,
                    'applicant_name' => $application?->applicant_name
                        ?? $jobSeeker?->name,

                    'professional_title' => $profile?->professional_title,
                    'city' => $profile?->city,

                    'profile_photo' => $profile?->profile_photo
                        ? asset('storage/' . $profile->profile_photo)
                        : null,

                    'analysis_id' => $analysis->id,
                    'match_score' => (float) $result['match_score'],
                    'recommendation' => $result['recommendation'] ?? null,

                    'matched_skills' => $result['matched_skills'] ?? [],
                    'missing_skills' => $result['missing_skills'] ?? [],
                    'strengths' => $result['strengths'] ?? [],
                    'weaknesses' => $result['weaknesses'] ?? [],
                    'reasoning' => $result['reasoning'] ?? null,
                ];
            });

        // Get only the highest-scoring 10 applicants.
        $topApplicants = $completedApplicants->take(10)->values();

        return response()->json([
            'success' => true,
            'message' => 'Applicant evaluation batch progress retrieved successfully.',
            'data' => [
                'batch_id' => $batch->id,
                'job_id' => $batch->job_id,
                'status' => $batch->status,
                'is_final' => $isFinal,

                'total_applicants' => $batch->total_applicants,
                'completed_count' => $completedCount,
                'failed_count' => $failedCount,
                'processing_count' => $processingCount,
                'queued_count' => $queuedCount,

                'progress_percentage' => $progressPercentage,

                'started_at' => $batch->started_at,
                'completed_at' => $batch->completed_at,

                'completed_applicants' => $completedApplicants,
                'top_applicants' => $topApplicants,
            ],
        ]);
    }

    /**
     * Get the company belonging to the employer.
     */
    private function getEmployerCompany(User $user): Company
    {
        $company = $this->companyService->getCompanyByEmployer($user);

        abort_unless(
            $company,
            403,
            'Please create your company profile before using AI screening.'
        );

        return $company;
    }
}
