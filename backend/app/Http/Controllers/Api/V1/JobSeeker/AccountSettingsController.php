<?php

namespace App\Http\Controllers\Api\V1\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JobSeeker\ChangePasswordRequest;
use App\Http\Requests\Api\JobSeeker\UpdateAccountInformationRequest;
use App\Http\Resources\Api\JobSeeker\JobSeekerProfileResource;
use App\Services\JobSeeker\AccountSettingsService;
use Illuminate\Http\JsonResponse;

class AccountSettingsController extends Controller
{
    public function __construct(
        private AccountSettingsService $accountSettingsService
    ) {}

    /**
     * Update account information.
     */
    public function updateAccount(
        UpdateAccountInformationRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $profile = $this->accountSettingsService->updateAccountInformation(
            $request->user(),
            $data,
            $request->file('profile_photo'),
            (bool) ($data['remove_profile_photo'] ?? false)
        );

        return response()->json([
            'success' => true,
            'message' => 'Account information updated successfully.',
            'data' => new JobSeekerProfileResource($profile),
        ]);
    }

    /**
     * Change account password.
     */
    public function changePassword(
        ChangePasswordRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $this->accountSettingsService->changePassword(
            $request->user(),
            $data['current_password'],
            $data['new_password']
        );

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}
