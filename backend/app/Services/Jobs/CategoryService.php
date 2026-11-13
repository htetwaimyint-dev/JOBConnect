<?php

namespace App\Services\Jobs;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    // Get all categories
    public function all(?string $search = null)
    {
        return $this->categoryRepository->all($search);
    }

    // Find category by ID
    public function findById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }
}
