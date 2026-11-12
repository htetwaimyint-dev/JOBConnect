<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateAccountInformationRequest;
use App\Http\Requests\Api\Admin\ChangePasswordRequest;
use App\Services\Admin\AccountSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountSettingsController extends Controller
{
    public function __construct(
        private AccountSettingsService $accountSettingsService
    ) {}

    /**
     * Get admin account information.
     */
    public function showAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Update admin account information.
     */
    public function updateAccount(
        UpdateAccountInformationRequest $request
    ): JsonResponse {
        $user = $this->accountSettingsService->updateAccountInformation(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Account information updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Change admin password.
     */
    public function changePassword(
        ChangePasswordRequest $request
    ): JsonResponse {
        $this->accountSettingsService->changePassword(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}
