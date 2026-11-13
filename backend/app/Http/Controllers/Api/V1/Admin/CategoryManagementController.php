<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreCategoryRequest;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CategoryManagementController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Display all categories.
     */
    // public function index(): JsonResponse
    // {
    //     $categories = $this->categoryService->all();

    //     return response()->json([
    //         'data' => CategoryResource::collection($categories)
    //     ], 200);
    // }
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $perPage = $request->input('per_page', 10);
        $categories = $query->latest()->paginate($perPage);

        return response()->json($categories);
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
    // public function update(
    //     StoreCategoryRequest $request,
    //     Category $category
    // ): JsonResponse {
    //     $updatedCategory = $this->categoryService->createOrUpdate(
    //         $category,
    //         $request->validated()
    //     );

    //     return response()->json([
    //         'message' => 'Category updated successfully',
    //         'data' => new CategoryResource($updatedCategory)
    //     ], 200);
    // }
    public function update(Request $request, Category $category)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $category->name = $request->name;

    // 1. If user clicked "Remove" and did not upload a new image
    if ($request->boolean('remove_logo')) {
        if ($category->logo && Storage::disk('public')->exists($category->logo)) {
            Storage::disk('public')->delete($category->logo);
        }
        $category->logo = null;
    }

    // 2. If user uploaded a new image
    if ($request->hasFile('logo')) {
        // Delete old logo first if it exists
        if ($category->logo && Storage::disk('public')->exists($category->logo)) {
            Storage::disk('public')->delete($category->logo);
        }
        $category->logo = $request->file('logo')->store('categories', 'public');
    }

    $category->save();

    return response()->json([
        'message' => 'Category updated successfully',
        'data' => $category
    ]);
}

    /**
     * Delete a category.
     */
     public function destroy(Category $category): JsonResponse
    {
        // 1. Block deletion if jobs exist under this category
        if ($category->jobs()->exists()) {
            return response()->json([
                'message' => 'Cannot delete this category because it still has jobs assigned to it.'
            ], 422);
        }

        // 2. Delete the logo image file from storage if it exists
        if ($category->logo && Storage::disk('public')->exists($category->logo)) {
            Storage::disk('public')->delete($category->logo);
        }

        // 3. Delete the category from the database
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.'
        ], 200);
    }
}