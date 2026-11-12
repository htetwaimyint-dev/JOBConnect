<?php

use App\Http\Controllers\Api\V1\Admin\CategoryManagementController;
use App\Http\Controllers\Api\V1\Admin\AccountSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {

        // Category Management
        Route::apiResource('/categories', CategoryManagementController::class);

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
