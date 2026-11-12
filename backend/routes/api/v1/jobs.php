<?php

use App\Http\Controllers\Api\V1\Jobs\JobController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('/employer/jobs')
    ->group(function () {

        Route::get('/', [JobController::class, 'index']);

        Route::post('/', [JobController::class, 'store']);

        Route::get('/{jobId}', [JobController::class, 'show']);

        Route::put('/{jobId}', [JobController::class, 'update']);

        Route::delete('/{jobId}', [JobController::class, 'destroy']);
    });
