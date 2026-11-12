<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreCategoryRequest;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryManagementController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Display all categories.
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->all();

        return response()->json([
            'data' => CategoryResource::collection($categories)
        ], 200);
    }

    /**
     * Store a new category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createOrUpdate(
            null,
            $request->validated()
        );

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category)
        ], 201);
    }

    /**
     * Display a specific category.
     */
    public function show(Category $category): JsonResponse
    {
        $category = $this->categoryService->findById($category->id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'data' => new CategoryResource($category)
        ], 200);
    }

    /**
     * Update an existing category.
     */
    public function update(
        StoreCategoryRequest $request,
        Category $category
    ): JsonResponse {
        $updatedCategory = $this->categoryService->createOrUpdate(
            $category,
            $request->validated()
        );

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($updatedCategory)
        ], 200);
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}