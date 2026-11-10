<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('employer')
    ->group(function () {

        Route::get(
            '/resumes/{resume}/download',
            [\App\Http\Controllers\Api\V1\Employer\ResumeController::class, 'download']
        );
    });
