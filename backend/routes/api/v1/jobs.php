
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Jobs\JobController;
use App\Http\Controllers\Api\V1\Jobs\PublicCategoryController;

// Public job routes
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);

// Public category route
Route::get('/public/categories', [PublicCategoryController::class, 'index']);
