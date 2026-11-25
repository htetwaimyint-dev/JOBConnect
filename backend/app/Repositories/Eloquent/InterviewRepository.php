<?php

namespace App\Repositories\Eloquent;

use App\Models\Application;
use App\Models\Interview;
use App\Models\Interviewer;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use Illuminate\Support\Collection;

class InterviewRepository implements InterviewRepositoryInterface
{
    private function interviewRelations(): array
    {
        return [
            'application.job.company:id,name,logo',
            'application.job:id,company_id,title',
            'application.jobSeeker:id,name,email',
            'interviewers:id,company_id,name,position',
        ];
    }

    public function getForCompany(int $companyId, array $filters = []): Collection
    {
        $query = Interview::query()
            ->with($this->interviewRelations())
            ->whereHas('application.job', fn($job) => $job->where('company_id', $companyId));

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['interview_type']) && $filters['interview_type'] !== 'all') {
            $query->where('interview_type', $filters['interview_type']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($nested) use ($search) {
                $nested->whereHas(
                    'application',
                    fn($application) =>
                    $application->where('applicant_name', 'like', "%{$search}%")
                )->orWhereHas(
                    'application.job',
                    fn($job) =>
                    $job->where('title', 'like', "%{$search}%")
                )->orWhereHas(
                    'interviewers',
                    fn($interviewer) =>
                    $interviewer->where('name', 'like', "%{$search}%")
                );
            });
        }

        if (!empty($filters['from'])) {
            $query->where('scheduled_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->where('scheduled_at', '<=', $filters['to']);
        }

        return $query->orderBy('scheduled_at')->get();
    }

    public function findForCompany(int $companyId, int $interviewId): ?Interview
    {
        return Interview::query()
            ->with($this->interviewRelations())
            ->whereKey($interviewId)
            ->whereHas('application.job', fn($job) => $job->where('company_id', $companyId))
            ->first();
    }

    public function findForCompanyForUpdate(int $companyId, int $interviewId): ?Interview
    {
        return Interview::query()
            ->whereKey($interviewId)
            ->whereHas('application.job', fn($job) => $job->where('company_id', $companyId))
            ->lockForUpdate()
            ->first();
    }

    public function findAvailableApplicationForCompany(
        int $companyId,
        int $applicationId
    ): ?Application {
        return Application::query()
            ->with(['job.company', 'jobSeeker'])
            ->whereKey($applicationId)
            ->where('status', 'shortlisted')
            ->whereHas('job', fn($job) => $job->where('company_id', $companyId))
            ->whereDoesntHave('interview')
            ->lockForUpdate()
            ->first();
    }

    public function getAvailableApplicationsForCompany(int $companyId): Collection
    {
        return Application::query()
            ->with(['job.company', 'jobSeeker'])
            ->whereHas('job', fn($job) => $job->where('company_id', $companyId))
            ->where('status', 'shortlisted')
            ->whereDoesntHave('interview')
            ->latest('applied_at')
            ->get();
    }

    public function createInterview(array $data, array $interviewerIds): Interview
    {
        $interview = Interview::query()->create($data);
        $interview->interviewers()->sync($interviewerIds);

        return $interview->load($this->interviewRelations());
    }

    public function updateInterview(Interview $interview, array $data, ?array $interviewerIds = null): Interview
    {
        $interview->fill($data);
        $interview->save();

        if ($interviewerIds !== null) {
            $interview->interviewers()->sync($interviewerIds);
        }

        return $interview->refresh()->load($this->interviewRelations());
    }

    public function updateStatus(Interview $interview, string $status): Interview
    {
        $interview->status = $status;
        $interview->save();

        return $interview->refresh()->load($this->interviewRelations());
    }

    public function getInterviewersForCompany(int $companyId): Collection
    {
        return Interviewer::query()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function findInterviewerForCompany(int $companyId, int $interviewerId): ?Interviewer
    {
        return Interviewer::query()
            ->where('company_id', $companyId)
            ->whereKey($interviewerId)
            ->first();
    }

    public function createInterviewer(int $companyId, array $data): Interviewer
    {
        return Interviewer::query()->create([
            ...$data,
            'company_id' => $companyId,
        ]);
    }

    public function updateInterviewer(Interviewer $interviewer, array $data): Interviewer
    {
        $interviewer->update($data);

        return $interviewer->refresh();
    }

    public function interviewerHasInterviews(Interviewer $interviewer): bool
    {
        return $interviewer->interviews()->exists();
    }

    public function deleteInterviewer(Interviewer $interviewer): void
    {
        $interviewer->delete();
    }

    public function markApplicationAsInterview(Application $application): void
    {
        $application->newQuery()
            ->whereKey($application->getKey())
            ->update([
                'status' => 'interview',
            ]);
    }

    public function getForJobSeeker(int $jobSeekerId, array $filters = []): Collection
    {
        $query = Interview::query()
            ->with($this->interviewRelations())
            ->whereHas('application.jobSeeker', fn($jobSeeker) => $jobSeeker->whereKey($jobSeekerId));

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['interview_type']) && $filters['interview_type'] !== 'all') {
            $query->where('interview_type', $filters['interview_type']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($nested) use ($search) {
                $nested->whereHas(
                    'application.job',
                    fn($job) => $job->where('title', 'like', "%{$search}%")
                )->orWhereHas(
                    'application.job.company',
                    fn($company) => $company->where('name', 'like', "%{$search}%")
                )->orWhereHas(
                    'interviewers',
                    fn($interviewer) => $interviewer->where('name', 'like', "%{$search}%")
                );
            });
        }

        if (!empty($filters['from'])) {
            $query->where('scheduled_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->where('scheduled_at', '<=', $filters['to']);
        }

        return $query->orderBy('scheduled_at')->get();
    }

    public function requestReschedule(int $jobSeekerId, int $interviewId, array $data): Interview
    {
        $interview = Interview::query()
            ->with($this->interviewRelations())
            ->whereKey($interviewId)
            ->whereHas('application.jobSeeker', fn($jobSeeker) => $jobSeeker->whereKey($jobSeekerId))
            ->firstOrFail();

        $reason = trim($data['reason'] ?? '');
        $noteLine = $reason !== ''
            ? "\n\nReschedule requested by job seeker: {$reason}"
            : "\n\nReschedule requested by job seeker.";

        $interview->update([
            'scheduled_at' => $data['scheduled_at'] ?? $interview->scheduled_at,
            'status' => 'rescheduled',
            'notes' => trim(($interview->notes ?? '') . $noteLine),
        ]);

        return $interview->refresh()->load($this->interviewRelations());
    }
}
