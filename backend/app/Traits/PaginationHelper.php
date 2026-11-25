<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginationHelper
{
    public static function paginate(
        Builder $query,
        int $perPage = 10
    ): LengthAwarePaginator {

        $perPage = max(1, min($perPage, 100));

        return $query->paginate($perPage);
    }
}