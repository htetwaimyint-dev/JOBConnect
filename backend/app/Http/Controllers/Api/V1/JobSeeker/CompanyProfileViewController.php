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

    //get companies(for jobseeker)
    public function index(Request $request): JsonResponse
    {
        $companies = $this->companyService->getCompanies($request->query('search'));

        return response()->json([
            'success' => true,
            'message' => 'Companies retrieved successfully.',
            'data' => CompanyResource::collection($companies),
        ], 200);
    }

    //get companies details(for jobseeker)
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
    //show jobs of company(for jobseeker)
    public function showjobs(int $id): JsonResponse
    {
        $company = $this->companyService->getCompanyWithJobs($id);

        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'company' => $company,
            'jobs' => $company->jobs,
        ]);
    }
}