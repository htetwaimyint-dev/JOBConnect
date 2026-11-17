
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Employer\ApplicationController;

Route::prefix('employer')
    ->middleware(['auth:sanctum', 'employer'])
    ->group(function () {
        Route::get(
            '/jobs/{jobId}/applications',
            [ApplicationController::class, 'index']
        );

        Route::patch(
            '/applications/{applicationId}/status',
            [ApplicationController::class, 'updateStatus']
        );
    });
