<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Account\AccountController;

Route::middleware('auth:sanctum')
    ->prefix('account')
    ->group(function () {

        // Update Name & Email
        Route::put('/profile', [AccountController::class, 'updateProfile']);

        // Change Password
        Route::put('/password', [AccountController::class, 'updatePassword']);
    });
