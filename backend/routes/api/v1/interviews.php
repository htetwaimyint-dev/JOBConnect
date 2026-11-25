<?php

use App\Http\Controllers\Api\V1\Interviews\InterviewController;
use App\Http\Controllers\Api\V1\Interviews\InterviewerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'employer'])
    ->prefix('employer')
    ->group(function () {
        Route::get('/interviews/available-applications', [InterviewController::class, 'availableApplications']);
        Route::get('/interviews', [InterviewController::class, 'index']);
        Route::post('/interviews', [InterviewController::class, 'store']);
        Route::get('/interviews/{interviewId}', [InterviewController::class, 'show']);
        Route::put('/interviews/{interviewId}', [InterviewController::class, 'update']);
        Route::patch('/interviews/{interviewId}/status', [InterviewController::class, 'updateStatus']);

        Route::get('/interviewers', [InterviewerController::class, 'index']);
        Route::post('/interviewers', [InterviewerController::class, 'store']);
        Route::put('/interviewers/{interviewerId}', [InterviewerController::class, 'update']);
        Route::delete('/interviewers/{interviewerId}', [InterviewerController::class, 'destroy']);
    });
