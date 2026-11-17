<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\CompanyApprovalRequest;
use App\Http\Resources\Api\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyApprovalController extends Controller
{
    /**
     * Display all companies with status filtering and pagination.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:all,pending,verified,rejected',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|in:2,5,10,15,20',
        ]);

        $query = Company::with('employer:id,name,email');

        // Filter by verification status
        if (
            !empty($validated['status']) &&
            $validated['status'] !== 'all'
        ) {
            $query->where(
                'verification_status',
                $validated['status']
            );
        }

        // Backend search
        if (!empty($validated['search'])) {
            $search = trim($validated['search']);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('industry', 'like', "%{$search}%")
                    ->orWhere('company_email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhereHas('employer', function ($employerQuery) use ($search) {
                        $employerQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $validated['per_page'] ?? 5;
        $companies = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return CompanyResource::collection($companies)
            ->additional([
                'success' => true,
                'message' => 'Companies retrieved successfully.',
            ]);
    }

    /**
     * Display company details.
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
     * Approve or reject a company.
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
