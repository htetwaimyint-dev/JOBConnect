<?php

namespace App\Http\Controllers\Api\V1\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Services\Jobs\CategoryService;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(
        CategoryService $categoryService
    ) {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->all();

        return CategoryResource::collection($categories)->additional([
            'success' => true,
            'message' => 'Categories retrieved successfully.',
        ]);
    }

    public function show(int $id)
    {
        $category = $this->categoryService->findById($id);

        return response()->json([
            'success' => 'true',
            'message' => 'Category retrieved successfully.',
            'data' => new CategoryResource($category),
        ]);
    }
}
