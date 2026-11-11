<?php 
use App\Http\Controllers\Api\V1\Admin\CategoryManagementController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/categories', CategoryManagementController::class);
?>