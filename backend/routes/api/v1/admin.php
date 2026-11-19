<?php

use App\Http\Controllers\Api\V1\Admin\CategoryManagementController;
use App\Http\Controllers\Api\V1\Admin\CompanyApprovalController;
use App\Http\Controllers\Api\V1\Admin\JobApprovalController;
use App\Http\Controllers\Api\V1\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::delete('/categories/bulk-delete', [
        CategoryManagementController::class,
        'bulkDestroy'
    ]);

    Route::delete('/categories/delete-all', [
        CategoryManagementController::class,
        'deleteAllUnused'
    ]);

    // Category Management
    Route::apiResource('/categories', CategoryManagementController::class);

    // Admin Management
    Route::prefix('admin')->group(function () {

        //User Management
        Route::get('/companies', [UserManagementController::class, 'companies']);
        Route::get('/job-seekers', [UserManagementController::class, 'jobSeekers']);

        Route::delete('/companies/{id}', [UserManagementController::class, 'deleteCompany']);
        Route::delete('/job-seekers/{id}', [UserManagementController::class, 'deleteJobSeeker']);

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

        // Get all jobs (supports status filtering)
        Route::get('/jobs', [
            JobApprovalController::class,
            'index'
        ]);

        // Get pending jobs
        Route::get('/jobs/pending', [
            JobApprovalController::class,
            'index'
        ]);

        // Get job detail
        Route::get('/jobs/{jobId}', [
            JobApprovalController::class,
            'show'
        ]);

        // Approve / Reject job
        Route::patch('/jobs/{jobId}/approval', [
            JobApprovalController::class,
            'update'
        ]);
    });
});
