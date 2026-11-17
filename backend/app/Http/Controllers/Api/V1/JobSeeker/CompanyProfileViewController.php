<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CompanyResource;
use App\Services\Employer\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyProfileViewController extends Controller
{
    public function __construct(
        protected CompanyService $companyService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companies = $this->companyService->getCompanies($request->query('search'));

        return response()->json([
            'success' => true,
            'message' => 'Companies retrieved successfully.',
            'data' => CompanyResource::collection($companies),
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $company = $this->companyService->getCompanyById($id);

            return response()->json([
                'success' => true,
                'message' => 'Company retrieved successfully.',
                'data' => new CompanyResource($company),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}