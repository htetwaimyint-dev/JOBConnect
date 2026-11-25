<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CompanyResource;
use App\Http\Resources\Api\Jobs\JobResource;
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
    //old
    // public function showjobs(int $id): JsonResponse
    // {
    //     $company = $this->companyService->getCompanyWithJobs($id);

    //     if (!$company) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Company not found',
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'company' => $company,
    //         'jobs' => $company->jobs,
    //     ]);
    // }
    //new
    // Show company's jobs with search & filters (for jobseeker)
public function showjobs(Request $request, int $id): JsonResponse
{
    try {
        // 1. Get company details
        $company = $this->companyService->getCompanyById($id);

        // 2. Get filters from URL query parameters
        $filters = [
            'keyword' => $request->query('keyword'),
            'location' => $request->query('location'),
            'employment_type' => $request->query('employment_type'),
            'workplace_type' => $request->query('workplace_type'),
        ];

        // 3. Get filtered jobs from Service
        $jobs = $this->companyService->getCompanyJobs(
            $id,
            $filters
        );

        // 4. Return API response
        return response()->json([
            'success' => true,
            'message' => 'Company jobs retrieved successfully.',

            'company' => (new CompanyResource($company))
                ->resolve($request),

            'jobs' => JobResource::collection($jobs)
                ->resolve($request),

        ], 200);

    } catch (Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], $e->getCode() ?: 500);
    }
}
}