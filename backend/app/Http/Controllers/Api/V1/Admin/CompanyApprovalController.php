<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\CompanyApprovalRequest;
use App\Http\Resources\Api\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyApprovalController extends Controller
{
    /**
     * Display pending company profiles.
     */
    public function index(): JsonResponse
    {
        $companies = Company::with('employer:id,name,email')
            ->where('verification_status', 'pending')
            ->latest()
            ->paginate(10);

        return CompanyResource::collection($companies)
            ->additional([
                'success' => true,
                'message' => 'Pending companies retrieved successfully.',
            ])
            ->response();
    }

    /**
     * Display a specific company profile.
     */
    public function show(Company $company): JsonResponse
    {
        $company->load('employer:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Company details retrieved successfully.',
            'data' => new CompanyResource($company),
        ]);
    }

    /**
     * Approve or reject a company profile.
     */
    public function update(
        CompanyApprovalRequest $request,
        Company $company
    ): JsonResponse {
        $validated = $request->validated();

        $status = $validated['verification_status'];

        $company->update([
            'verification_status' => $status,
            'verified_at' => $status === 'verified' ? now() : null,
            'rejection_reason' => $status === 'rejected'
                ? $validated['rejection_reason']
                : null,
        ]);

        $company->refresh()->load('employer:id,name,email');

        return response()->json([
            'success' => true,
            'message' => $status === 'verified'
                ? 'Company approved successfully.'
                : 'Company rejected successfully.',
            'data' => new CompanyResource($company),
        ]);
    }
}
