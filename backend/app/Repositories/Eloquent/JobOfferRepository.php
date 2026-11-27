<?php

namespace App\Repositories\Eloquent;


use App\Models\Company;
use App\Models\Job;
use App\Models\JobOffer;
use App\Models\User;
use App\Repositories\Contracts\JobOfferRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class JobOfferRepository implements JobOfferRepositoryInterface
{
    public function getPublishedJobsForCompany(Company $company): Collection
    {
        return $company->jobs()
            ->where('status', 'published')
            ->orderBy('title')
            ->get([
                'id',
                'company_id',
                'title',
                'employment_type',
                'workplace_type',
                'country',
                'city',
                'salary_min',
                'salary_max',
                'salary_type',
            ]);
    }

    public function findPublishedJobForCompany(Company $company, int $jobId): ?Job
    {
        return $company->jobs()
            ->where('status', 'published')
            ->whereKey($jobId)
            ->first();
    }

    public function findCandidateWithProfile(int $jobSeekerId): ?User
    {
        return User::query()
            ->with('jobSeekerProfile')
            ->whereKey($jobSeekerId)
            ->whereHas('jobSeekerProfile')
            ->first();
    }

    public function findCandidateForOfferContext(int $candidateId): User
    {
        return User::query()
            ->with([
                'jobSeekerProfile',
                'skills' => fn($query) => $query->orderBy('name'),
                'experiences' => fn($query) => $query->orderByDesc('start_date'),
            ])
            ->findOrFail($candidateId);
    }

    public function create(array $data): JobOffer
    {
        $offer = JobOffer::query()->create($data);

        return $this->loadOfferRelations($offer);
    }

    public function getForCompany(int $companyId): Collection
    {
        return JobOffer::query()
            ->with([
                'job:id,title,employment_type,workplace_type,country,city',
                'company:id,name,logo',
                'jobSeeker.jobSeekerProfile',
                'jobSeeker.skills',
                'jobSeeker.experiences',
            ])
            ->where('company_id', $companyId)
            ->latest('created_at')
            ->get();
    }

    public function findForCompanyForUpdate(int $companyId, int $offerId): ?JobOffer
    {
        return JobOffer::query()
            ->where('company_id', $companyId)
            ->whereKey($offerId)
            ->lockForUpdate()
            ->first();
    }

    public function getForJobSeeker(int $jobSeekerId): Collection
    {
        return JobOffer::query()
            ->with([
                'job:id,title,employment_type,workplace_type,country,city',
                'company:id,name,logo',
            ])
            ->where('job_seeker_id', $jobSeekerId)
            ->latest('created_at')
            ->get();
    }

    public function expirePendingForJobSeeker(int $jobSeekerId): int
    {
        return JobOffer::query()
            ->where('job_seeker_id', $jobSeekerId)
            ->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update([
                'status' => 'expired',
                'updated_at' => now(),
            ]);
    }

    public function findForJobSeekerForUpdate(int $jobSeekerId, int $offerId): ?JobOffer
    {
        return JobOffer::query()
            ->where('job_seeker_id', $jobSeekerId)
            ->whereKey($offerId)
            ->lockForUpdate()
            ->first();
    }

    public function updateStatus(JobOffer $offer, string $status, ?Carbon $respondedAt = null): JobOffer
    {
        $offer->status = $status;
        $offer->responded_at = $respondedAt;
        $offer->save();

        return $this->loadOfferRelations($offer->refresh());
    }

    public function loadOfferRelations(JobOffer $offer): JobOffer
    {
        return $offer->load([
            'job:id,title,employment_type,workplace_type,country,city',
            'company:id,name,logo',
            'jobSeeker.jobSeekerProfile',
            'jobSeeker.skills',
            'jobSeeker.experiences',
        ]);
    }

    public function existsForCandidateAndJob(
        int $companyId,
        int $jobId,
        int $jobSeekerId
    ): bool {
        return JobOffer::query()
            ->where('company_id', $companyId)
            ->where('job_id', $jobId)
            ->where('job_seeker_id', $jobSeekerId)
            ->exists();
    }

    //Direct Offer(Employer Dashboard)
    public function getEmployerOfferStats(Company $company): array
    {
        $startDate = now()->startOfMonth();
        $endDate = now();

        $stats = JobOffer::query()
            ->where('company_id', $company->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired,
                SUM(CASE WHEN status = 'withdrawn' THEN 1 ELSE 0 END) as withdrawn
            ")
            ->first();

        return [
            'total' => (int) $stats->total,
            'pending' => (int) $stats->pending,
            'accepted' => (int) $stats->accepted,
            'rejected' => (int) $stats->rejected,
            'expired' => (int) $stats->expired,
            'withdrawn' => (int) $stats->withdrawn,
            'date_range' => [
                'from' => $startDate->toDateString(),
                'to' => $endDate->toDateString(),
            ],
        ];
    }
}
