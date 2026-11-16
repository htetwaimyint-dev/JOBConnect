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

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);

            return response()->json([
                'message' => 'User suspended/deleted successfully.'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}