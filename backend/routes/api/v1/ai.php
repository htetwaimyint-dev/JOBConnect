
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AI\JobMatchingController;
use App\Http\Controllers\Api\V1\AI\RecommendationController;

Route::prefix('employer')
    ->middleware(['auth:sanctum', 'employer'])
    ->group(function () {

        // AI Applicant Screening for all applicants of a specific job
        Route::post(
            '/jobs/{jobId}/ai-screening',
            [JobMatchingController::class, 'evaluateApplicants']
        );

        // Get AI applicant evaluation batch progress and top 10 results.
        Route::get(
            '/jobs/{jobId}/ai-screening/batches/{batchId}',
            [JobMatchingController::class, 'getBatchProgress']
        );

        // AI Applicant Screening for a single application
        Route::post(
            '/applications/{applicationId}/ai-screening',
            [JobMatchingController::class, 'evaluateSingleApplicant']
        );
    });

Route::prefix('job-seeker')
    ->middleware(['auth:sanctum', 'job-seeker'])
    ->group(function () {


        Route::post(
            '/recommendations',
            [RecommendationController::class, 'store']
        );

        Route::get(
            '/recommendations',
            [
                RecommendationController::class,
                'index',
            ]
        );
    });
