<?php

use App\Http\Controllers\Api\V1\Employer\CompanyJobController;
use App\Http\Controllers\Api\V1\Jobs\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Jobs\PublicJobController;
use App\Http\Controllers\Api\V1\Jobs\PublicCategoryController;
use App\Http\Controllers\Api\V1\Jobs\JobApplicationQuestionController;

// Public job routes
Route::get('/jobs', [PublicJobController::class, 'index']);
Route::get('/jobs/{id}', [PublicJobController::class, 'show']);

// Job Seeker application questions
Route::middleware(['auth:sanctum', 'job-seeker'])
    ->get(
        '/jobs/{jobId}/application-questions',
        [JobApplicationQuestionController::class, 'index']
    );
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
        // Close a published job(HWM)
        Route::patch('/{jobId}/close', [CompanyJobController::class, 'close']);
    });

Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('/employer/job-categories')
    ->group(function () {

        Route::get('/', [CategoryController::class, 'index']);

        Route::get('/{jobId}', [CategoryController::class, 'show']);
    });
