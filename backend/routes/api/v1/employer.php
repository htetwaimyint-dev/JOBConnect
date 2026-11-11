<?php

use App\Http\Controllers\Api\V1\Employer\CompanyProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('employer')
    ->group(function () {

        Route::get('/profile', [
            CompanyProfileController::class,
            'show',
        ]);

        Route::post('/profile', [
            CompanyProfileController::class,
            'store',
        ]);

        Route::put('/profile', [
            CompanyProfileController::class,
            'update',
        ]);

        Route::get(
            '/resumes/{resume}/download',
            [\App\Http\Controllers\Api\V1\Employer\ResumeController::class, 'download']
        );
    });

