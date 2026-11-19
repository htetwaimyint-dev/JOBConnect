<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JobOffers\StoreJobOfferRequest;
use App\Http\Resources\Api\JobOffers\EmployerOfferJobResource;
use App\Http\Resources\Api\JobOffers\JobOfferCandidateResource;
use App\Http\Resources\Api\JobOffers\JobOfferCompanyResource;
use App\Http\Resources\Api\JobOffers\JobOfferResource;
use App\Models\Company;
use App\Models\JobOffer;
use App\Services\JobOffers\JobOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobOfferController extends Controller
{
    public function __construct(
        private readonly JobOfferService $jobOfferService,
    ) {}

    public function context(Request $request, int $jobSeekerId): JsonResponse
    {
        $company = $this->currentCompany($request);
        $context = $this->jobOfferService->getEmployerContext($company, $jobSeekerId);

        return response()->json([
            'success' => true,
            'message' => 'Job offer context retrieved successfully.',
            'data' => [
                'candidate' => (new JobOfferCandidateResource($context['candidate']))->resolve($request),
                'company' => (new JobOfferCompanyResource($context['company']))->resolve($request),
                'jobs' => EmployerOfferJobResource::collection($context['jobs'])->resolve($request),
            ],
        ]);
    }

    public function store(StoreJobOfferRequest $request): JsonResponse
    {
        $company = $this->currentCompany($request);

        $offer = $this->jobOfferService->sendOffer($company, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Direct job offer sent successfully.',
            'data' => (new JobOfferResource($offer))->resolve($request),
        ], 201);
    }

    private function currentCompany(Request $request): Company
    {
        $company = $request->user()?->company;

        abort_unless($company instanceof Company, 403, 'Your employer account is not linked to a company.');

        return $company;
    }

    public function withdraw(JobOffer $jobOffer)
    {
        if ($jobOffer->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending offers can be withdrawn.',
            ], 422);
        }

        $jobOffer->update([
            'status' => 'withdrawn',
        ]);

        return response()->json([
            'message' => 'Job offer withdrawn successfully.',
            'data' => $jobOffer->fresh(),
        ]);
    }
}
