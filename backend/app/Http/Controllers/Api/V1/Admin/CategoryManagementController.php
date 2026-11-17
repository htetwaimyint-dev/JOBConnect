<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreCategoryRequest;
use App\Http\Resources\Api\CategoryResource;
use App\Services\Admin\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CategoryManagementController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->getPaginated(
            $request->query('search'),
            $request->integer('per_page', 10)
        );

        return response()->json($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryService->create($request->validated());

            return response()->json([
                'message' => 'Category created successfully',
                'data' => new CategoryResource($category)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->findById($id);

            return response()->json([
                'data' => new CategoryResource($category)
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function update(StoreCategoryRequest $request, int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->update($id, $request->validated());

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => new CategoryResource($category)
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->categoryService->delete($id);

            return response()->json([
                'message' => 'Category deleted successfully.'
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:categories,id'],
        ]);

        try {
            $result = $this->categoryService->bulkDelete($request->ids);

            return response()->json([
                'message' => 'Bulk delete completed.',
                'deleted_count' => $result['deleted_count'],
                'skipped_count' => $result['skipped_categories']->count(),
                'skipped_categories' => $result['skipped_categories'],
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function deleteAllUnused(): JsonResponse
    {
        try {
            $result = $this->categoryService->deleteAllUnused();

            return response()->json([
                'message' => 'Delete all unused categories completed.',
                'deleted_count' => $result['deleted_count'],
                'skipped_count' => $result['skipped_categories']->count(),
                'skipped_categories' => $result['skipped_categories'],
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}