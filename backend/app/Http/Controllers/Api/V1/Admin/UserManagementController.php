<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AdminUserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return AdminUserResource::collection($users)
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

        return AdminUserResource::collection($users)
            ->response()
            ->setStatusCode(200);
    }
}
