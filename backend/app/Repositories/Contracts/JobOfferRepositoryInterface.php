<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Support\Collection;

interface JobOfferRepositoryInterface
{
    public function getPublishedJobsForCompany(Company $company): Collection;

    public function findPublishedJobForCompany(Company $company, int $jobId): ?Job;

    public function findCandidateWithProfile(int $jobSeekerId): ?User;

    public function findCandidateForOfferContext(int $candidateId): User;

    public function create(array $data): JobOffer;

    public function getForJobSeeker(int $jobSeekerId): Collection;

    public function expirePendingForJobSeeker(int $jobSeekerId): int;

    public function findForJobSeekerForUpdate(int $jobSeekerId, int $offerId): ?JobOffer;

    public function updateStatus(JobOffer $offer, string $status, ?\Illuminate\Support\Carbon $respondedAt = null): JobOffer;

    public function loadOfferRelations(JobOffer $offer): JobOffer;

    public function existsForCandidateAndJob(
        int $companyId,
        int $jobId,
        int $jobSeekerId
    ): bool;
}
