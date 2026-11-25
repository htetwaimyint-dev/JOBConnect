
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AI\JobMatchingController;

Route::prefix('employer')
    ->middleware(['auth:sanctum', 'employer'])
    ->group(function () {

        // AI Applicant Screening for all applicants of a specific job
        Route::post(
            '/jobs/{jobId}/ai-screening',
            [JobMatchingController::class, 'evaluateApplicants']
        );

        // AI Applicant Screening for a single application
        Route::post(
            '/applications/{applicationId}/ai-screening',
            [JobMatchingController::class, 'evaluateSingleApplicant']
        );
    });
