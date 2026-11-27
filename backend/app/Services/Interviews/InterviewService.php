<?php

namespace App\Services\Interviews;

use App\Models\Company;
use App\Models\Interview;
use App\Models\Interviewer;
use App\Models\User;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use App\Notifications\InterviewScheduledNotification;
use App\Notifications\InterviewRescheduledNotification;
use App\Notifications\InterviewCancelledNotification;
use App\Notifications\InterviewRescheduleRequestedNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InterviewService
{
    public function __construct(
        private readonly InterviewRepositoryInterface $interviewRepository,
    ) {}

    public function getInterviews(Company $company, array $filters = []): Collection
    {
        return $this->interviewRepository->getForCompany($company->id, $filters);
    }

    public function getJobseekerInterviews(User $jobSeeker, array $filters = []): Collection
    {
        return $this->interviewRepository->getForJobSeeker($jobSeeker->id, $filters);
    }

    public function requestReschedule(User $jobSeeker, int $interviewId, array $data): Interview
    {
        $interview = $this->interviewRepository->requestReschedule(
            $jobSeeker->id,
            $interviewId,
            $data
        );

        $interview->loadMissing(['application.job.company.employer']);

        $interview->application?->job?->company?->employer?->notify(
            new InterviewRescheduleRequestedNotification($interview)
        );

        return $interview;
    }

    public function getInterview(Company $company, int $interviewId): Interview
    {
        return $this->interviewRepository->findForCompany($company->id, $interviewId)
            ?? throw (new ModelNotFoundException())->setModel(Interview::class, [$interviewId]);
    }

    public function getAvailableApplications(Company $company): Collection
    {
        return $this->interviewRepository->getAvailableApplicationsForCompany($company->id);
    }

    public function createInterview(Company $company, array $data): Interview
    {
        $interview = DB::transaction(function () use ($company, $data) {
            $application = $this->interviewRepository->findAvailableApplicationForCompany(
                $company->id,
                (int) $data['application_id'],
            );

            if (!$application) {
                throw ValidationException::withMessages([
                    'application_id' => 'Choose a shortlisted application belonging to your company that does not already have an interview.',
                ]);
            }

            $interviewerIds = $this->validatedInterviewerIds(
                $company,
                $data['interviewer_ids'] ?? [],
            );

            unset($data['interviewer_ids']);
            $data['status'] = 'scheduled';

            $interview = $this->interviewRepository->createInterview(
                $data,
                $interviewerIds,
            );

            $this->interviewRepository->markApplicationAsInterview($application);

            return $interview;
        });

        // Notify only after the scheduling transaction has committed.
        $interview->loadMissing(['application.job.company', 'application.jobSeeker']);
        $interview->application?->jobSeeker?->notify(
            new InterviewScheduledNotification($interview)
        );

        return $interview;
    }

    public function updateInterview(Company $company, int $interviewId, array $data): Interview
    {
        $previousScheduledAt = null;

        $interview = DB::transaction(function () use (
            $company,
            $interviewId,
            $data,
            &$previousScheduledAt
        ) {
            $lockedInterview = $this->interviewRepository->findForCompanyForUpdate(
                $company->id,
                $interviewId,
            );

            if (!$lockedInterview) {
                throw (new ModelNotFoundException())->setModel(Interview::class, [$interviewId]);
            }

            if (($data['status'] ?? null) === 'rescheduled' && $lockedInterview->status !== 'scheduled') {
                throw ValidationException::withMessages([
                    'status' => 'Only scheduled interviews can be rescheduled.',
                ]);
            }

            $previousScheduledAt = $lockedInterview->scheduled_at?->copy();

            $interviewerIds = array_key_exists('interviewer_ids', $data)
                ? $this->validatedInterviewerIds($company, $data['interviewer_ids'] ?? [])
                : null;

            unset($data['interviewer_ids']);

            return $this->interviewRepository->updateInterview(
                $lockedInterview,
                $data,
                $interviewerIds,
            );
        });

        $interview->loadMissing(['application.jobSeeker']);
        $newScheduledAt = $interview->scheduled_at;
        $dateChanged = $previousScheduledAt && $newScheduledAt
            && $previousScheduledAt->toIso8601String() !== $newScheduledAt->toIso8601String();
        $explicitlyRescheduled = ($data['status'] ?? null) === 'rescheduled';

        if ($dateChanged || $explicitlyRescheduled) {
            $interview->application?->jobSeeker?->notify(
                new InterviewRescheduledNotification($interview)
            );
        }

        return $interview;
    }

    public function updateStatus(Company $company, int $interviewId, string $status): Interview
    {
        $previousStatus = null;

        $interview = DB::transaction(function () use (
            $company,
            $interviewId,
            $status,
            &$previousStatus
        ) {
            $lockedInterview = $this->interviewRepository->findForCompanyForUpdate(
                $company->id,
                $interviewId,
            );

            if (!$lockedInterview) {
                throw (new ModelNotFoundException())->setModel(Interview::class, [$interviewId]);
            }

            $previousStatus = $lockedInterview->status;

            if (!in_array($lockedInterview->status, ['scheduled', 'rescheduled'], true) && $status !== $lockedInterview->status) {
                throw ValidationException::withMessages([
                    'status' => 'Only scheduled interviews can be completed or cancelled.',
                ]);
            }

            return $this->interviewRepository->updateStatus($lockedInterview, $status);
        });

        if ($status === 'cancelled' && $previousStatus !== 'cancelled') {
            $interview->loadMissing(['application.jobSeeker']);
            $interview->application?->jobSeeker?->notify(
                new InterviewCancelledNotification($interview)
            );
        }

        return $interview;
    }

    public function getInterviewers(Company $company): Collection
    {
        return $this->interviewRepository->getInterviewersForCompany($company->id);
    }

    public function createInterviewer(Company $company, array $data): Interviewer
    {
        return $this->interviewRepository->createInterviewer($company->id, $data);
    }

    public function updateInterviewer(Company $company, int $interviewerId, array $data): Interviewer
    {
        $interviewer = $this->findInterviewer($company, $interviewerId);

        return $this->interviewRepository->updateInterviewer($interviewer, $data);
    }

    public function deleteInterviewer(Company $company, int $interviewerId): void
    {
        $interviewer = $this->findInterviewer($company, $interviewerId);

        if ($this->interviewRepository->interviewerHasInterviews($interviewer)) {
            throw ValidationException::withMessages([
                'interviewer' => 'This interviewer is assigned to an interview. Remove the assignment before deleting the interviewer.',
            ]);
        }

        $this->interviewRepository->deleteInterviewer($interviewer);
    }

    private function findInterviewer(Company $company, int $interviewerId): Interviewer
    {
        return $this->interviewRepository->findInterviewerForCompany($company->id, $interviewerId)
            ?? throw (new ModelNotFoundException())->setModel(Interviewer::class, [$interviewerId]);
    }

    private function validatedInterviewerIds(Company $company, array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if ($ids === []) {
            return [];
        }

        $found = $this->interviewRepository->getInterviewersForCompany($company->id)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        if (count($found) !== count($ids)) {
            throw ValidationException::withMessages([
                'interviewer_ids' => 'One or more selected interviewers do not belong to your company.',
            ]);
        }

        return $found;
    }
}
