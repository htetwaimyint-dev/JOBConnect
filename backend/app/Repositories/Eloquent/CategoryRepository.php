<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(?string $search, int $perPage): LengthAwarePaginator
    {
        $query = Category::query();
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function create(array $data): Category
    {
        if (isset($data['logo'])) {
            $data['logo'] = $data['logo']->store('categories', 'public');
        }
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        // Logo ဖျက်မလား၊ အသစ်တင်မလား စစ်တယ်
        if (isset($data['remove_logo']) && $data['remove_logo']) {
            $this->deleteLogoFile($category->logo);
            $data['logo'] = null;
        }

        if (isset($data['logo'])) {
            $this->deleteLogoFile($category->logo);
            $data['logo'] = $data['logo']->store('categories', 'public');
        }

        $category->update($data);
        return $category;
    }

    public function delete(Category $category): bool
    {
        $this->deleteLogoFile($category->logo);
        return $category->delete();
    }

    public function bulkDelete(array $ids): array
    {
        $categories = Category::whereIn('id', $ids)->withCount('jobs')->get();
        
        $deletable = $categories->where('jobs_count', 0);
        $protected = $categories->where('jobs_count', '>', 0);

        $deletedIds = $deletable->pluck('id')->all();
        $logoPaths = $deletable->pluck('logo')->filter()->all();

        DB::transaction(function () use ($deletedIds) {
            if (!empty($deletedIds)) {
                Category::whereIn('id', $deletedIds)->delete();
            }
        });

        foreach ($logoPaths as $path) {
            $this->deleteLogoFile($path);
        }

        return [
            'deleted_count' => count($deletedIds),
            'skipped' => $protected,
        ];
    }

    public function deleteAllUnused(): array
    {
        $categories = Category::withCount('jobs')->get();
        $deletable = $categories->where('jobs_count', 0);
        $protected = $categories->where('jobs_count', '>', 0);

        $deletedIds = $deletable->pluck('id')->all();
        $logoPaths = $deletable->pluck('logo')->filter()->all();

        DB::transaction(function () use ($deletedIds) {
            if (!empty($deletedIds)) {
                Category::whereIn('id', $deletedIds)->delete();
            }
        });

        foreach ($logoPaths as $path) {
            $this->deleteLogoFile($path);
        }

        return [
            'deleted_count' => count($deletedIds),
            'skipped' => $protected,
        ];
    }

    private function deleteLogoFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}