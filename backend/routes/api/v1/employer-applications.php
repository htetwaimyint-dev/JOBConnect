
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Employer\ApplicationController;

Route::prefix('employer')
    ->middleware(['auth:sanctum', 'employer'])
    ->group(function () {

        // Get all company applications with filters
        Route::get(
            '/applications',
            [ApplicationController::class, 'index']
        );

        // Get applicants for a specific job
        Route::get(
            '/jobs/{jobId}/applications',
            [ApplicationController::class, 'jobIndex']
        );

        // Get application details
        Route::get(
            '/applications/{applicationId}',
            [ApplicationController::class, 'show']
        );

        // Update application status
        Route::patch(
            '/applications/{applicationId}/status',
            [ApplicationController::class, 'updateStatus']
        );
    });
