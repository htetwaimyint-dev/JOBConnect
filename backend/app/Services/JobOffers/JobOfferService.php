<?php

namespace App\Services\JobOffers;


use App\Models\Company;
use App\Models\JobOffer;
use App\Models\User;
use App\Repositories\Contracts\JobOfferRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JobOfferService
{
    protected JobOfferRepositoryInterface $jobOfferRepository;

    public function __construct(
        JobOfferRepositoryInterface $jobOfferRepository,
    ) {
        $this->jobOfferRepository = $jobOfferRepository;
    }

    public function getEmployerContext(Company $company, int $jobSeekerId): array
    {
        $candidate = $this->jobOfferRepository->findCandidateForOfferContext($jobSeekerId);

        if (!$candidate) {
            throw (new ModelNotFoundException())->setModel(User::class, [$jobSeekerId]);
        }

        return [
            'candidate' => $candidate,
            'company' => $company,
            'jobs' => $this->jobOfferRepository->getPublishedJobsForCompany($company, $jobSeekerId),
        ];
    }

    public function sendOffer(Company $company, array $data): JobOffer
    {
        $job = $this->jobOfferRepository->findPublishedJobForCompany(
            $company,
            (int) $data['job_id'],
        );

        if (!$job) {
            throw ValidationException::withMessages([
                'job_id' => 'Choose one of your published jobs.',
            ]);
        }

        $candidate = $this->jobOfferRepository->findCandidateWithProfile(
            (int) $data['job_seeker_id'],
        );

        if (!$candidate) {
            throw ValidationException::withMessages([
                'job_seeker_id' => 'The selected job seeker profile could not be found.',
            ]);
        }

        $exists = $this->jobOfferRepository->existsForCandidateAndJob(
            $company->id,
            $job->id,
            $candidate->id
        );

        if ($exists) {
            throw ValidationException::withMessages([
                'job_id' => 'A job offer has already been sent to this candidate for this job.',
            ]);
        }

        return DB::transaction(fn() => $this->jobOfferRepository->create([
            'job_id' => $job->id,
            'job_seeker_id' => $candidate->id,
            'company_id' => $company->id,
            'salary_offered' => $data['salary_offered'],
            'offer_details' => $data['offer_details'] ?? null,
            'expires_at' => Carbon::parse($data['expires_at']),
            'status' => 'pending',
            'responded_at' => null,
        ]));
    }

    public function getOffersForEmployer(Company $company): Collection
    {
        return $this->jobOfferRepository->getForCompany($company->id);
    }

    public function withdrawOffer(Company $company, int $offerId): JobOffer
    {
        return DB::transaction(function () use ($company, $offerId) {
            $offer = $this->jobOfferRepository->findForCompanyForUpdate(
                $company->id,
                $offerId,
            );

            if (!$offer) {
                throw (new ModelNotFoundException())->setModel(JobOffer::class, [$offerId]);
            }

            if ($offer->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Only pending offers can be withdrawn.',
                ]);
            }

            return $this->jobOfferRepository->updateStatus($offer, 'withdrawn');
        });
    }

    public function getOffersForJobSeeker(int $jobSeekerId): Collection
    {
        $this->jobOfferRepository->expirePendingForJobSeeker($jobSeekerId);

        return $this->jobOfferRepository->getForJobSeeker($jobSeekerId);
    }

    public function respondToOffer(int $jobSeekerId, int $offerId, string $status): JobOffer
    {
        $result = DB::transaction(function () use ($jobSeekerId, $offerId, $status) {
            $offer = $this->jobOfferRepository->findForJobSeekerForUpdate($jobSeekerId, $offerId);

            if (!$offer) {
                throw (new ModelNotFoundException())->setModel(JobOffer::class, [$offerId]);
            }

            if ($offer->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Only pending offers can be accepted or rejected.',
                ]);
            }

            if ($offer->expires_at && $offer->expires_at->isPast()) {
                $this->jobOfferRepository->updateStatus($offer, 'expired');

                return ['expired' => true, 'offer' => null];
            }

            $updatedOffer = $this->jobOfferRepository->updateStatus(
                $offer,
                $status,
                now(),
            );

            return ['expired' => false, 'offer' => $updatedOffer];
        });

        if ($result['expired']) {
            throw ValidationException::withMessages([
                'status' => 'This offer has expired and can no longer be answered.',
            ]);
        }

        return $result['offer'];
    }

    public function getCandidateForOfferContext(int $candidateId): User
    {
        return $this->jobOfferRepository
            ->findCandidateForOfferContext($candidateId);
    }
}
