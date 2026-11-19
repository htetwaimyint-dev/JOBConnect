<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Services\Jobs\JobService;
use App\Repositories\Eloquent\CompanyRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CompanyResource;
use App\Services\Employer\CompanyService;
use Illuminate\Database\Eloquent\ModelNotFoundException; 
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CompanyProfileViewController extends Controller
{
    public function __construct(
        protected CompanyRepository $companyRepository,
        protected CompanyService $companyService,
        protected JobService $jobService
    ) {}

   public function companyJobs(Request $request, int $companyId)
{
    try {
        $company = $this->companyRepository->findById($companyId);

        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found.'
            ], 404);
        }

        $filters = $request->only(['keyword', 'job_type', 'location']);

        $jobs = $this->jobService->getCompanyJobsForJobseeker($companyId, $filters, 10);

        return response()->json([
            'status' => true,
            'company' => $company,
            'jobs' => $jobs
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 404);
    }
}

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