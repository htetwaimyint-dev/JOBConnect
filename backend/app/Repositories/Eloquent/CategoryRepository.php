<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): Collection
    {
        return Category::all();
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function createOrUpdate(?Category $category, array $data): Category
    {
        if ($category) {
            $category->update($data);
            return $category;
        } else {
            return Category::create($data);
        }
    }

    public function delete(Category $category): void
    {
        if ($category->jobs()->exists()) {
            $category->jobs()->delete();
        }
        $category->delete();
    }
}
