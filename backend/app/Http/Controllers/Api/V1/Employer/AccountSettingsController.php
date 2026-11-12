<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Employer\UpdateAccountInformationRequest;
use App\Http\Requests\Api\Employer\ChangePasswordRequest;
use App\Services\Employer\AccountSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountSettingsController extends Controller
{
    public function __construct(
        private AccountSettingsService $accountSettingsService
    ) {}

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
