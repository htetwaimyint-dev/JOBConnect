<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Services\Admin\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class UserManagementController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    // GET /api/v1/admin/users/companies
    public function companies(Request $request): JsonResponse
    {
        $users = $this->userService->getCompanies(
            $request->query('search'),
            $request->integer('per_page', 10)
        );

        return UserResource::collection($users)
            ->response()
            ->setStatusCode(200);
    }

    // GET /api/v1/admin/users/job-seekers
    public function jobSeekers(Request $request): JsonResponse
    {
        $users = $this->userService->getJobSeekers(
            $request->query('search'),
            $request->integer('per_page', 10)
        );

        return UserResource::collection($users)
            ->response()
            ->setStatusCode(200);
    }

    // DELETE /api/v1/admin/companies/{id}
    // public function deleteCompany(int $id): JsonResponse
    // {
    //     try {
    //         $this->userService->deleteCompany($id);
    //         return response()->json(['message' => 'Company suspended successfully.'], 200);
    //     } catch (Exception $e) {
    //         return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
    //     }
    // }
    // PATCH /api/v1/admin/companies/{id}/suspend
public function suspendCompany(int $id): JsonResponse
{
    try {
        $this->userService->suspendEmployer($id);

        return response()->json([
            'message' => 'Employer suspended successfully.'
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'message' => $e->getMessage()
        ], $e->getCode() ?: 500);
    }
}

// PATCH /api/v1/admin/companies/{id}/restore
public function restoreCompany(int $id): JsonResponse
{
    try {
        $this->userService->restoreEmployer($id);

        return response()->json([
            'message' => 'Employer restored successfully.'
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'message' => $e->getMessage()
        ], $e->getCode() ?: 500);
    }
}

    // DELETE /api/v1/admin/job-seekers/{id}
    public function deleteJobSeeker(int $id): JsonResponse
    {
        try {
            $this->userService->deleteJobSeeker($id);
            return response()->json(['message' => 'Job Seeker suspended successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

}