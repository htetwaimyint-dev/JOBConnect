<?php

use App\Http\Controllers\Api\V1\Admin\CategoryManagementController;
use App\Http\Controllers\Api\V1\Admin\CompanyApprovalController;
use App\Http\Controllers\Api\V1\Admin\JobApprovalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // Category Management
    Route::apiResource('/categories', CategoryManagementController::class);

    // Admin Management
    Route::prefix('admin')->group(function () {

        // Company Approval Management
        Route::get('/companies/pending', [
            CompanyApprovalController::class,
            'index'
        ]);

        Route::get('/companies/{company}', [
            CompanyApprovalController::class,
            'show'
        ]);

        Route::patch('/companies/{company}/approval', [
            CompanyApprovalController::class,
            'update'
        ]);

        // Job Approval Management
        Route::get('/jobs/pending', [
            JobApprovalController::class,
            'index'
        ]);

        Route::get('/jobs/{jobId}', [
            JobApprovalController::class,
            'show'
        ]);

        Route::patch('/jobs/{jobId}/approval', [
            JobApprovalController::class,
            'update'
        ]);
    });
});
