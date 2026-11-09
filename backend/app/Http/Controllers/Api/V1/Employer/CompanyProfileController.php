<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\CompanyResource;

class CompanyProfileController extends Controller
{

    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

     public function show(Request $request)
    {
        // Get authenticated user
        $user = $request->user();

        // Find company using employer_id
        $company = $this->companyService->getCompanyByEmployerId(
            $user->id
        );

        // Check if company exists
        if (!$company) {
            return response([
                'success' => 'false',
                'message' => 'Company profile not found',
                'data' => null,
            ], 404);
        }

        return response([
            'success' => 'true',
            'message' => 'Company profile retrieved successfully',
            'data' => new CompanyResource($company),
        ]);
    }
}
