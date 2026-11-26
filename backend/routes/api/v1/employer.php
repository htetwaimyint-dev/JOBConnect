<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Employer\CompanyProfileController;
use App\Http\Controllers\Api\V1\Employer\CompanyJobController;
use App\Http\Controllers\Api\V1\Employer\JobSeekerDirectoryController;
use App\Http\Controllers\Api\V1\Employer\ResumeController;
use App\Http\Controllers\Api\V1\Employer\ApplicationController;



Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('employer')
    ->group(function () {

       
        // Company Profile
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


        // Employer Jobs
        Route::get('/jobs', [
            CompanyJobController::class,
            'index',
        ]);

        Route::post('/jobs', [
            CompanyJobController::class,
            'store',
        ]);

        // Job Statistics -Employer Dashboard
        Route::get('/jobs/stats', [
            CompanyJobController::class,
            'stats',
        ]);
      
        // Employer Applicant Statistics -Employer Dashboard
        Route::get('/applicants/stats', [
            ApplicationController::class,
            'stats',
        ]);


        Route::get('/jobs/{jobId}', [
            CompanyJobController::class,
            'show',
        ]);

        Route::put('/jobs/{jobId}', [
            CompanyJobController::class,
            'update',
        ]);

        Route::delete('/jobs/{jobId}', [
            CompanyJobController::class,
            'destroy',
        ]);

        // Job Seeker Directory
        Route::get('/job-seekers', [
            JobSeekerDirectoryController::class,
            'index',
        ]);

        // Resume Preview and Download
        Route::get('/resumes/{resume}/preview', [
            ResumeController::class,
            'preview',
        ]);

        Route::get('/resumes/{resume}/download', [
            ResumeController::class,
            'download',
        ]);
    });

    //allow admin and employer
    Route::middleware(['auth:sanctum', 'role:admin,employer'])->group(function () {
    Route::get('employer/job-seekers/{id}/profile', [
            JobSeekerDirectoryController::class,
            'show',
        ]);
});

