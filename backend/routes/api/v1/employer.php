<?php  

use App\Http\Controllers\Api\V1\Employer\CompanyProfileController;
use Illuminate\Support\Facades\Route;

// Protected Routes
Route::middleware(['auth:sanctum', 'employer'])->prefix('company')->group(function () {
    Route::get('/profile', [CompanyProfileController::class, 'show']);
});