<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Category;

    public function createOrUpdate(?Category $category,array $data): Category;

    public function delete(Category $category): void;
}