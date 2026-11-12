<?php

use App\Http\Controllers\Api\V1\Admin\CategoryManagementController;
use App\Http\Controllers\Api\V1\Admin\CompanyApprovalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AccountSettingsController;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // Category Management
    Route::apiResource('/categories', CategoryManagementController::class);

    // Company Approval Management
    Route::prefix('admin')->group(function () {

        // Get pending companies
        Route::get('/companies/pending', [
            CompanyApprovalController::class,
            'index'
        ]);

        // Get company details
        Route::get('/companies/{company}', [
            CompanyApprovalController::class,
            'show'
        ]);

        // Approve or reject company
        Route::patch('/companies/{company}/approval', [
            CompanyApprovalController::class,
            'update'
        ]);

        // Account Settings
        Route::get('/settings/account', [
            AccountSettingsController::class,
            'showAccount',
        ]);
        //Email and Name
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
});
