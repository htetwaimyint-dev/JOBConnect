<?php

namespace App\Services\Interviews;

use App\Models\Company;
use App\Models\Interview;
use App\Models\Interviewer;
use App\Models\User;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Employer\CompanyService;

class InterviewService
{
    public function __construct(
        protected CompanyService $companyService,
        private readonly InterviewRepositoryInterface $interviewRepository
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
        return $this->interviewRepository->requestReschedule($jobSeeker->id, $interviewId, $data);
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
        return DB::transaction(function () use ($company, $data) {
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

            // Create the interview.
            $interview = $this->interviewRepository->createInterview(
                $data,
                $interviewerIds,
            );

            // Update the associated application status.
            $this->interviewRepository->markApplicationAsInterview($application);

            return $interview;
        });
    }

    public function updateInterview(Company $company, int $interviewId, array $data): Interview
    {
        return DB::transaction(function () use ($company, $interviewId, $data) {
            $interview = $this->interviewRepository->findForCompanyForUpdate(
                $company->id,
                $interviewId,
            );

            if (!$interview) {
                throw (new ModelNotFoundException())->setModel(Interview::class, [$interviewId]);
            }

            if (($data['status'] ?? null) === 'rescheduled' && $interview->status !== 'scheduled') {
                throw ValidationException::withMessages([
                    'status' => 'Only scheduled interviews can be rescheduled.',
                ]);
            }

            $interviewerIds = array_key_exists('interviewer_ids', $data)
                ? $this->validatedInterviewerIds($company, $data['interviewer_ids'] ?? [])
                : null;

            unset($data['interviewer_ids']);

            return $this->interviewRepository->updateInterview(
                $interview,
                $data,
                $interviewerIds,
            );
        });
    }

    public function updateStatus(Company $company, int $interviewId, string $status): Interview
    {
        return DB::transaction(function () use ($company, $interviewId, $status) {
            $interview = $this->interviewRepository->findForCompanyForUpdate(
                $company->id,
                $interviewId,
            );

            if (!$interview) {
                throw (new ModelNotFoundException())->setModel(Interview::class, [$interviewId]);
            }

            if (!in_array($interview->status, ['scheduled', 'rescheduled'], true) && $status !== $interview->status) {
                throw ValidationException::withMessages([
                    'status' => 'Only scheduled interviews can be completed or cancelled.',
                ]);
            }

            return $this->interviewRepository->updateStatus($interview, $status);
        });
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

    //Interview - Employer Dashboard
    public function getDashboardInterviews(User $user): array
    {
        // Get employer's company
        $company = $this->companyService->getCompanyByEmployer($user);

        abort_unless(
            $company,
            403,
            'Please create your company profile first.'
        );

        // Get today's and upcoming interviews
        return [
            'today_interviews' => $this->interviewRepository
                ->getTodayInterviews($company->id),

            'upcoming_interviews' => $this->interviewRepository
                ->getUpcomingInterviews($company->id),
        ];
    }
}
