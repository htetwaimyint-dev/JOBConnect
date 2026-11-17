
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\JobSeeker\ApplicationController;

Route::prefix('job-seeker')
    ->middleware(['auth:sanctum', 'job-seeker'])
    ->group(function () {

        // View my applications
        Route::get('/applications', [ApplicationController::class, 'index']);

        // View one application detail
        Route::get('/applications/{application}', [ApplicationController::class, 'show']);

        // Submit a job application
        Route::post('/applications', [ApplicationController::class, 'store']);
    });
