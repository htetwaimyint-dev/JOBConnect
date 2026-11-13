<?php

use App\Http\Controllers\Api\V1\Employer\CompanyJobController;
use App\Http\Controllers\Api\V1\Jobs\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Jobs\JobController;
use App\Http\Controllers\Api\V1\Jobs\PublicCategoryController;


// Public job routes
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);

// Public category route
Route::get('/public/categories', [PublicCategoryController::class, 'index']);



Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('/employer/jobs')
    ->group(function () {

        Route::get('/', [CompanyJobController::class, 'index']);

        Route::post('/', [CompanyJobController::class, 'store']);

        Route::get('/{jobId}', [CompanyJobController::class, 'show']);

        Route::put('/{jobId}', [CompanyJobController::class, 'update']);

        Route::delete('/{jobId}', [CompanyJobController::class, 'destroy']);
    });

Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('/employer/categories')
    ->group(function () {

        Route::get('/', [CategoryController::class, 'index']);

        Route::get('/{jobId}', [CategoryController::class, 'show']);
    });
