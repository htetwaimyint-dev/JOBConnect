<?php

namespace App\Services\Applications;

use App\Models\Application;
use App\Models\Company;
use App\Models\User;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Job;
use Illuminate\Validation\ValidationException;
use App\Enums\ApplicationStatus;

class ApplicationService
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepository
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
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->applicationRepository->getByCompany(
            $company,
            $perPage
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
        $job = Job::with('jobQuestions')->findOrFail($data['job_id']);

        $answers = $data['answers'] ?? [];

        // Get all question IDs belonging to this job.
        $jobQuestionIds = $job->jobQuestions
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

        if (!empty($invalidQuestionIds)) {
            throw ValidationException::withMessages([
                'answers' => 'Some questions do not belong to this job.',
            ]);
        }

        // Get all required question IDs.
        $requiredQuestionIds = $job->jobQuestions
            ->where('is_required', true)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        // Check whether all required questions are answered.
        $missingQuestionIds = array_diff(
            $requiredQuestionIds,
            $submittedQuestionIds
        );

        if (!empty($missingQuestionIds)) {
            throw ValidationException::withMessages([
                'answers' => 'Please answer all required questions.',
            ]);
        }

        // Remove answers from application data.
        unset($data['answers']);

        // Save application and answers together.
        return $this->applicationRepository->createWithAnswers(
            $data,
            $answers
        );
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
     * Update application status.
     */
    public function updateApplicationStatus(
        Application $application,
        array $data
    ): Application {
        return $this->applicationRepository->updateStatus(
            $application,
            $data
        );
    }

    /**
     * Withdraw an application belonging to a Job Seeker.
     */
    public function withdrawMyApplication(
        User $jobSeeker,
        int $applicationId
    ): Application {

        // Find application belonging to this Job Seeker
        $application = $this->getMyApplicationDetail(
            $jobSeeker,
            $applicationId
        );

        // Only pending applications can be withdrawn
        if ($application->status !== ApplicationStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => 'Only pending applications can be withdrawn.',
            ]);
        }

        // Update status and create history
        return $this->applicationRepository->updateStatus(
            $application,
            [
                'new_status' => ApplicationStatus::Withdrawn->value,
                'changed_by_company_id' => null,
                'note' => 'Withdrawn by job seeker.',
            ]
        );
    }
}
