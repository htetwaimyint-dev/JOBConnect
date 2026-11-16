<?php

namespace App\Http\Controllers\Api\V1\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\UpdateAccountProfileRequest;
use App\Http\Requests\Api\Account\UpdateAccountPasswordRequest;
use App\Services\Account\AccountService;
use App\Traits\HttpResopnseTrait;

class AccountController extends Controller
{
    use HttpResopnseTrait;

    public function __construct(
        protected AccountService $accountService
    ) {}

    public function updateProfile(UpdateAccountProfileRequest $request)
    {
        $user = $this->accountService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            'Account profile updated successfully.',
            $user,
            null
        );
    }

    public function updatePassword(UpdateAccountPasswordRequest $request)
    {
        $this->accountService->updatePassword(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Password updated successfully.',
            'success' => true,
        ], 200);
    }
}
