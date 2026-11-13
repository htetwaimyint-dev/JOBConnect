<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Employer\StoreCompanyRequest;
use App\Http\Requests\Api\Employer\UpdateCompanyRequest;
use App\Http\Resources\Api\CompanyResource;
use App\Services\Employer\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Get the authenticated employer's company profile.
     */
    public function show(Request $request): JsonResponse
    {
        $company = $this->companyService->getCompanyByEmployer(
            $request->user()
        );

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company profile not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Company profile retrieved successfully.',
            'data' => new CompanyResource($company),
        ]);
    }

    /**
     * Create a company profile.
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = $this->companyService->createOrUpdateCompany(
            $request->user(),
            $request->validated(),
            [
                'logo' => $request->file('logo'),
                'cover_image' => $request->file('cover_image'),
                'gallery' => $request->file('gallery', []),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Company profile created successfully.',
            'data' => new CompanyResource($company),
        ], 201);
    }

    /**
     * Update the authenticated employer's company profile.
     */
    public function update(UpdateCompanyRequest $request): JsonResponse
    {
        $company = $this->companyService->createOrUpdateCompany(
            $request->user(),
            $request->validated(),
            [
                'logo' => $request->file('logo'),
                'cover_image' => $request->file('cover_image'),
                'gallery' => $request->file('gallery', []),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Company profile updated successfully.',
            'data' => new CompanyResource($company),
        ]);
    }
}