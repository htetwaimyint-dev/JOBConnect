
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\JobSeeker\ProfileController;
use App\Http\Controllers\Api\V1\JobSeeker\EducationController;

Route::middleware(['auth:sanctum', 'job-seeker'])->prefix('job-seeker')->group(function () {

    // Job Seeker Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Job Seeker Education
    Route::apiResource('/educations', EducationController::class);
});
