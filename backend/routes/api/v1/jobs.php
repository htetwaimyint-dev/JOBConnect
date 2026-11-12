<?php

use App\Http\Controllers\Api\V1\Employer\CompanyJobController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('/employer/jobs')
    ->group(function () {

        Route::get('/', [CompanyJobController::class, 'index']);

        Route::post('/', [CompanyJobController::class, 'store']);

        Route::get('/{jobId}', [CompanyJobController::class, 'show']);

        Route::put('/{jobId}', [CompanyJobController::class, 'update']);

        Route::delete('/{jobId}', [CompanyJobController::class, 'destroy']);
    });
