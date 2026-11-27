<?php

namespace App\Services\Applications;

use App\Events\Notifications\NotificationCreated;
use Illuminate\Support\Facades\Log;
use Throwable;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\Notifications\Notification as LaravelNotification;
use App\Services\AI\ApplicantEvaluationInputBuilder;
use App\Notifications\ApplicationSubmittedNotification;
use App\Notifications\NewApplicationReceivedNotification;
use App\Notifications\ApplicationStatusChangedNotification;
use App\Notifications\ApplicationWithdrawnNotification;

class ApplicationService
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepository,
        protected ApplicantEvaluationInputBuilder $inputBuilder
    ) {}

    /**
     * Get applications belonging to a Job Seeker.
     */
    public function getMyApplications(
        User $jobSeeker,
        int $perPage = 10,
        ?string $status = null,
        ?string $search = null
    ): LengthAwarePaginator {
        return $this->applicationRepository->getByJobSeeker(
            $jobSeeker,
            $perPage,
            $status,
            $search
        );
    }

    /**
     * Get one application belonging to a Job Seeker.
     */
    public function getMyApplicationDetail(
        User $jobSeeker,
        int $applicationId
    ): Application {
        return $this->applicationRepository->findForJobSeeker(
            $jobSeeker,
            $applicationId
        ) ?? throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
            'Application not found.'
        );
    }

    /**
     * Get applications belonging to a Company's jobs.
     */
    public function getCompanyApplications(
        Company $company,
        int $perPage = 10,
        ?string $status = null,
        ?int $categoryId = null,
        ?string $search = null
    ): LengthAwarePaginator {
        return $this->applicationRepository->getByCompany(
            $company,
            $perPage,
            $status,
            $categoryId,
            $search
        );
    }

    /**
     * Get applications for a specific Company job.
     */
    public function getCompanyJobApplications(
        Company $company,
        int $jobId,
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->applicationRepository->getByCompanyJob(
            $company,
            $jobId,
            $perPage
        );
    }

    /**
     * Create an application with validated answers.
     */
    public function submitApplication(array $data): Application
    {
        $job = Job::with('jobQuestion')->findOrFail($data['job_id']);

        $answers = $data['answers'] ?? [];

        // Get all question IDs belonging to this job.
        $jobQuestionIds = $job->jobQuestion
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        // Get submitted question IDs.
        $submittedQuestionIds = array_map(
            fn($answer) => (int) $answer['job_question_id'],
            $answers
        );

        // Check whether submitted questions belong to this job.
        $invalidQuestionIds = array_diff(
            $submittedQuestionIds,
            $jobQuestionIds
        );

        if (! empty($invalidQuestionIds)) {
            throw ValidationException::withMessages([
                'answers' => 'Some questions do not belong to this job.',
            ]);
        }

        // Get all required question IDs.
        $requiredQuestionIds = $job->jobQuestion
            ->where('is_required', true)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        // Check whether all required questions are answered.
        $missingQuestionIds = array_diff(
            $requiredQuestionIds,
            $submittedQuestionIds
        );

        if (! empty($missingQuestionIds)) {
            throw ValidationException::withMessages([
                'answers' => 'Please answer all required questions.',
            ]);
        }

        // Remove answers from application data.
        unset($data['answers']);

        // Save application and answers first. The repository transaction
        // completes before any queued notification is dispatched.
        $application = $this->applicationRepository->createWithAnswers(
            $data,
            $answers
        );

        $application->loadMissing([
            'job.company.employer',
            'jobSeeker',
        ]);

        $this->sendNotification(
            $application->jobSeeker,
            new ApplicationSubmittedNotification($application)
        );

        $this->sendNotification(
            $application->job?->company?->employer,
            new NewApplicationReceivedNotification($application)
        );

        return $application;
    }

    /**
     * Find an application belonging to a Company.
     */
    public function findCompanyApplication(
        Company $company,
        int $applicationId
    ): ?Application {
        return $this->applicationRepository->findForCompany(
            $company,
            $applicationId
        );
    }
    /**
     * Attach the AI evaluation matching the applicant's current input.
     */
    public function attachCurrentAiEvaluation(
        Application $application
    ): Application {
        $application->loadMissing([
            'job',
            'jobSeeker.jobSeekerProfile',
            'jobSeeker.skills',
            'jobSeeker.educations',
            'jobSeeker.experiences',
            'answers.jobQuestion',
        ]);

        $evaluationInput = $this->inputBuilder->build(
            $application->job,
            $application
        );

        $inputHash = $evaluationInput['input_hash'];

        $analysis = $application->aiAnalyses()
            ->where('analysis_type', 'applicant_evaluation')
            ->where('status', 'completed')
            ->where('input_hash', $inputHash)
            ->whereNotNull('result')
            ->latest('id')
            ->first();

        $application->setRelation('currentAiEvaluation', $analysis);

        return $application;
    }
    /**
     * Update application status with transition validation.
     */
    public function updateApplicationStatus(
        Application $application,
        array $data
    ): Application {
        $currentStatus = $application->status;

        $newStatus = ApplicationStatus::from(
            $data['new_status']
        );

        // Define allowed status transitions.
        $allowedTransitions = [
            ApplicationStatus::Pending->value => [
                ApplicationStatus::Reviewed->value,
            ],

            ApplicationStatus::Reviewed->value => [
                ApplicationStatus::Shortlisted->value,
                ApplicationStatus::Rejected->value,
            ],

            ApplicationStatus::Shortlisted->value => [
                ApplicationStatus::Accepted->value,
                ApplicationStatus::Rejected->value,
            ],

            ApplicationStatus::Interview->value => [
                ApplicationStatus::Accepted->value,
                ApplicationStatus::Rejected->value,
            ],
        ];

        $allowedStatuses = $allowedTransitions[$currentStatus->value] ?? [];

        if (! in_array($newStatus->value, $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'new_status' => sprintf(
                    'Cannot change application status from %s to %s.',
                    $currentStatus->value,
                    $newStatus->value
                ),
            ]);
        }

        $updatedApplication = $this->applicationRepository->updateStatus(
            $application,
            $data
        );

        $updatedApplication->loadMissing([
            'job.company',
            'jobSeeker',
        ]);

        $this->sendNotification(
            $updatedApplication->jobSeeker,
            new ApplicationStatusChangedNotification($updatedApplication)
        );

        return $updatedApplication;
    }

    /**
     * Withdraw an application belonging to a Job Seeker.
     */
    public function withdrawMyApplication(
        User $jobSeeker,
        int $applicationId
    ): Application {
        // Find application belonging to this Job Seeker.
        $application = $this->getMyApplicationDetail(
            $jobSeeker,
            $applicationId
        );

        // Only pending applications can be withdrawn.
        if ($application->status !== ApplicationStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => 'Only pending applications can be withdrawn.',
            ]);
        }

        // Update status and create history.
        $updatedApplication = $this->applicationRepository->updateStatus(
            $application,
            [
                'new_status' => ApplicationStatus::Withdrawn->value,
                'changed_by_company_id' => null,
                'note' => 'Withdrawn by job seeker.',
            ]
        );

        $updatedApplication->loadMissing([
            'job.company.employer',
            'jobSeeker',
        ]);

        $this->sendNotification(
            $updatedApplication->job?->company?->employer,
            new ApplicationWithdrawnNotification($updatedApplication)
        );

        return $updatedApplication;
    }

    /**
     * Persist the in-app notification immediately, then queue only the email.
     * This keeps the bell responsive even when the mail queue is busy.
     */
    private function sendNotification(?User $recipient, LaravelNotification $notification): void
    {
        if (! $recipient) {
            return;
        }

        // 1. Persist the in-app notification synchronously.
        $recipient->notifyNow($notification, ['database']);

        // 2. Fetch the just-persisted DatabaseNotification model so we have its exact DB ID & data.
        $persisted = $recipient->notifications()->whereKey($notification->id)->first();

        // 3. Push to Reverb safely without failing the HTTP request if WebSockets are down.
        if ($persisted) {
            try {
                event(new NotificationCreated(
                    userId: (int) $recipient->getKey(),
                    notification: [
                        'id' => (string) $persisted->id,
                        'type' => $persisted->type,
                        'data' => $persisted->data,
                        'read_at' => $persisted->read_at?->toISOString(),
                        'created_at' => $persisted->created_at?->toISOString(),
                    ],
                ));
            } catch (Throwable $exception) {
                Log::warning('Unable to broadcast a user notification.', [
                    'user_id' => $recipient->getKey(),
                    'notification_id' => $persisted->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        // 4. Queue email delivery separately (relies on via() returning ['mail']).
        $recipient->notify($notification);
    }
}
