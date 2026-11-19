<?php

use App\Http\Controllers\Api\V1\Employer\JobOfferController as EmployerJobOfferController;
use App\Http\Controllers\Api\V1\JobSeeker\JobOfferController as JobSeekerJobOfferController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('employer/job-offers')
    ->group(function () {
        Route::get(
            '/context/{jobSeekerId}',
            [EmployerJobOfferController::class, 'context']
        );

        Route::post(
            '/',
            [EmployerJobOfferController::class, 'store']
        );
    });

Route::middleware(['auth:sanctum', 'job-seeker'])
    ->prefix('job-seeker/job-offers')
    ->group(function () {
        Route::get(
            '/',
            [JobSeekerJobOfferController::class, 'index']
        );

        Route::patch(
            '/{jobOfferId}/response',
            [JobSeekerJobOfferController::class, 'respond']
        );
    });
