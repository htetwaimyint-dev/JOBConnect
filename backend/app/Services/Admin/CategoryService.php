<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Exception;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getPaginated(?string $search, int $perPage)
    {
        return $this->categoryRepository->getAll($search, $perPage);
    }

    public function findById(int $id): Category
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new Exception('Category not found', 404);
        }
        return $category;
    }

    public function create(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->findById($id);
        return $this->categoryRepository->update($category, $data);
    }

    public function delete(int $id): void
    {
        $category = $this->findById($id);
        
        if ($category->jobs()->exists()) {
            throw new Exception('Cannot delete this category because it still has jobs assigned to it.', 422);
        }

        $this->categoryRepository->delete($category);
    }

    public function bulkDelete(array $ids): array
    {
        $result = $this->categoryRepository->bulkDelete($ids);
    
        $result['skipped_categories'] = $result['skipped']->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'reason' => 'This category has related jobs.',
            ];
        })->values();

        unset($result['skipped']);
        return $result;
    }

    public function deleteAllUnused(): array
    {
        $result = $this->categoryRepository->deleteAllUnused();
        
        $result['skipped_categories'] = $result['skipped']->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'reason' => 'This category has related jobs.',
            ];
        })->values();

        unset($result['skipped']);
        return $result;
    }
}