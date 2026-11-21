<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\JobSeeker\ProfileController;
use App\Http\Controllers\Api\V1\JobSeeker\EducationController;
use App\Http\Controllers\Api\V1\JobSeeker\ExperienceController;
use App\Http\Controllers\Api\V1\JobSeeker\ResumeController;
use App\Http\Controllers\Api\V1\JobSeeker\UserSkillController;
use App\Http\Controllers\Api\V1\JobSeeker\CompanyProfileViewController;

use App\Http\Controllers\Api\V1\Jobs\SavedJobController;

Route::get('job-seeker/companies', [CompanyProfileViewController::class, 'index']);


Route::middleware(['auth:sanctum', 'job-seeker'])
    ->prefix('job-seeker')
    ->group(function () {

        // Job Seeker Profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);

        // Job Seeker Education
        Route::apiResource('/educations', EducationController::class);

        // Job Seeker Experience
        Route::apiResource('/experiences', ExperienceController::class);

        // Job Seeker Resume
        Route::apiResource('/resumes', ResumeController::class);
        Route::get(
            '/resumes/{resume}/download',
            [ResumeController::class, 'download']
        );

        // Job Seeker Skills
        Route::apiResource('/skills', UserSkillController::class)
            ->except(['show']);

     
        // Saved Jobs
        Route::get(
            '/saved-jobs',
            [SavedJobController::class, 'index']
        );

        Route::post(
            '/saved-jobs',
            [SavedJobController::class, 'store']
        );

        Route::delete(
            '/saved-jobs/{jobId}',
            [SavedJobController::class, 'destroy']
        );
    });

    Route::middleware(['auth:sanctum', 'role:admin,job_seeker'])->group(function () {
    Route::get('job-seeker/companies/{id}', [CompanyProfileViewController::class, 'show']);
});
