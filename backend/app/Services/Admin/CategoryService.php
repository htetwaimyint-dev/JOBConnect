<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    // Get all categories
    public function all()
    {
        return $this->categoryRepository->all();
    }

    // Find category by ID
    public function findById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function createOrUpdate(?Category $category, array $data): Category
    {
        if (isset($data['logo'])) {
            if ($category && $category->logo) {
                Storage::disk('public')->delete($category->logo);
            }
            $data['logo'] = Storage::disk('public')->put('categories', $data['logo']);
        }

        return $this->categoryRepository->createOrUpdate($category, $data);
    }

    // Delete category
    public function delete(Category $category): void
    {
        if ($category->logo) {
            Storage::disk('public')->delete($category->logo);
        }

        $this->categoryRepository->delete($category);
    }
}
