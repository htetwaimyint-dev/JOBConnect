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

        return response()->json([
            'success' => true,
            'message' => 'Pending companies retrieved successfully.',
            'data' => $companies,
        ]);
    }

    /**
     * Display a specific company profile.
     */
    public function show(Company $company): JsonResponse
    {
        $company->load('employer:id,name,email');

        return response()->json([
            'success' => true,
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
        $status = $request->validated()['verification_status'];

        $company->update([
            'verification_status' => $status,
            'verified_at' => $status === 'verified' ? now() : null,
            'rejection_reason' => $status === 'rejected'
                ? $request->validated()['rejection_reason']
                : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $status === 'verified'
                ? 'Company approved successfully.'
                : 'Company rejected successfully.',
            'data' => new CompanyResource($company->fresh()),
        ]);
    }
}
