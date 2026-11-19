<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JobOffers\RespondToJobOfferRequest;
use App\Http\Resources\Api\JobOffers\JobOfferResource;
use App\Services\JobOffers\JobOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    public function __construct(
        private readonly JobOfferService $jobOfferService,
    ) {}

    public function index(Request $request)
    {
        $offers = $this->jobOfferService->getOffersForJobSeeker((int) $request->user()->id);

        return JobOfferResource::collection($offers)->additional([
            'success' => true,
            'message' => 'Job offers retrieved successfully.',
        ]);
    }

    public function respond(RespondToJobOfferRequest $request, int $jobOfferId): JsonResponse
    {
        $offer = $this->jobOfferService->respondToOffer(
            (int) $request->user()->id,
            $jobOfferId,
            $request->validated('status'),
        );

        return response()->json([
            'success' => true,
            'message' => 'Your response has been saved.',
            'data' => (new JobOfferResource($offer))->resolve($request),
        ]);
    }
}
