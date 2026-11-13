<?php 
use App\Http\Controllers\Api\V1\Admin\CategoryManagementController;
use Illuminate\Support\Facades\Route;

Route::delete('/categories/bulk-delete', [
    CategoryManagementController::class,
    'bulkDestroy'
]);

Route::delete('/categories/delete-all', [
    CategoryManagementController::class,
    'deleteAllUnused'
]);

// Resource routes LAST
Route::apiResource('categories', CategoryManagementController::class);
?>