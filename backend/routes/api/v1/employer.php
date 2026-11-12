<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Employer\AccountSettingsController;

Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('employer')
    ->group(function () {

        Route::get(
            '/resumes/{resume}/download',
            [\App\Http\Controllers\Api\V1\Employer\ResumeController::class, 'download']
        );
        // Account Settings
        Route::get('/settings/account', [
            AccountSettingsController::class,
            'showAccount',
        ]);

        Route::put('/settings/account', [
            AccountSettingsController::class,
            'updateAccount',
        ]);

        // Change Password
        Route::put('/settings/password', [
            AccountSettingsController::class,
            'changePassword',
        ]);
    });
