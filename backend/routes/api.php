<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/v1/auth.php';
    require __DIR__ . '/api/v1/job-seeker.php';
    require __DIR__ . '/api/v1/employer.php';
    require __DIR__ . '/api/v1/jobs.php';
    require __DIR__ . '/api/v1/applications.php';
    require __DIR__ . '/api/v1/interviews.php';
    require __DIR__ . '/api/v1/job-offers.php';
    require __DIR__ . '/api/v1/ai.php';
    require __DIR__ . '/api/v1/admin.php';
    require __DIR__ . '/api/v1/notifications.php';

    // Temporary test route
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'JobConnect API is working!',
        ]);
    });
});
