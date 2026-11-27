<?php

use App\Http\Controllers\Api\V1\Admin\CategoryManagementController;
use App\Http\Controllers\Api\V1\Admin\CompanyApprovalController;
use App\Http\Controllers\Api\V1\Admin\JobApprovalController;
use App\Http\Controllers\Api\V1\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\ActivityLogController;
use App\Http\Controllers\Api\V1\JobSeeker\CompanyProfileViewController;

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

        // Admin Dashboard
        Route::get('/dashboard', [
            DashboardController::class,
            'index'
        ]);

        // Activity Logs
        Route::get('/activity-logs', [
            ActivityLogController::class,
            'index'
        ]);

        //User Management
        Route::get('/companies', [UserManagementController::class, 'companies']);
        Route::get('/job-seekers', [UserManagementController::class, 'jobSeekers']);

        Route::patch('/companies/{id}/suspend', [
            UserManagementController::class,
            'suspendCompany'
        ]);

        Route::patch('/companies/{id}/restore', [
            UserManagementController::class,
            'restoreCompany'
        ]);

        Route::patch(
            '/job-seekers/{id}/suspend',
            [UserManagementController::class, 'suspendJobSeeker']
        );

        Route::patch(
            '/job-seekers/{id}/restore',
            [UserManagementController::class, 'restoreJobSeeker']
        );

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
