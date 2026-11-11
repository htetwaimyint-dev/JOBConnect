<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\JobSeeker\ProfileController;
use App\Http\Controllers\Api\V1\JobSeeker\EducationController;
use App\Http\Controllers\Api\V1\JobSeeker\ExperienceController;
use App\Http\Controllers\Api\V1\JobSeeker\ResumeController;
use App\Http\Controllers\Api\V1\JobSeeker\AccountSettingsController;
use App\Http\Controllers\Api\V1\JobSeeker\UserSkillController;


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

        // Account Settings
        Route::put('/settings/account', [
            AccountSettingsController::class,
            'updateAccount'
        ]);

        Route::put('/settings/password', [
            AccountSettingsController::class,
            'changePassword'
        ]);
        // Job Seeker Skills
        Route::apiResource('/skills', UserSkillController::class)
            ->except(['show']);
    });
