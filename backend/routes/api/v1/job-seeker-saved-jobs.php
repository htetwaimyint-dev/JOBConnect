
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Jobs\SavedJobController;

Route::middleware(['auth:sanctum', 'job-seeker'])
    ->prefix('job-seeker/saved-jobs')
    ->group(function () {

        // View my saved jobs
        Route::get('/', [SavedJobController::class, 'index']);

        // Save a job
        Route::post('/', [SavedJobController::class, 'store']);

        // Remove a saved job
        Route::delete('/{jobId}', [SavedJobController::class, 'destroy']);
    });
