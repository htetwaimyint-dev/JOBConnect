<?php

namespace App\Repositories\Contracts;

use App\Models\Application;
use App\Models\Interview;
use App\Models\Interviewer;
use Illuminate\Support\Collection;

interface InterviewRepositoryInterface
{
    public function getForCompany(int $companyId, array $filters = []): Collection;

    public function findForCompany(int $companyId, int $interviewId): ?Interview;

    public function findForCompanyForUpdate(int $companyId, int $interviewId): ?Interview;

    public function findAvailableApplicationForCompany(int $companyId, int $applicationId): ?Application;

    public function getAvailableApplicationsForCompany(int $companyId): Collection;

    public function createInterview(array $data, array $interviewerIds): Interview;

    public function updateInterview(Interview $interview, array $data, ?array $interviewerIds = null): Interview;

    public function updateStatus(Interview $interview, string $status): Interview;

    public function getInterviewersForCompany(int $companyId): Collection;

    public function findInterviewerForCompany(int $companyId, int $interviewerId): ?Interviewer;

    public function createInterviewer(int $companyId, array $data): Interviewer;

    public function updateInterviewer(Interviewer $interviewer, array $data): Interviewer;

    public function interviewerHasInterviews(Interviewer $interviewer): bool;

    public function deleteInterviewer(Interviewer $interviewer): void;

    public function markApplicationAsInterview(Application $application): void;

    public function getForJobSeeker(int $jobSeekerId, array $filters = []): Collection;

    public function requestReschedule(int $jobSeekerId, int $interviewId, array $data): Interview;

    public function getTodayInterviews(int $companyId): Collection;

    public function getUpcomingInterviews(int $companyId): Collection;
}
