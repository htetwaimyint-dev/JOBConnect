<?php

namespace App\Http\Controllers\Api\V1\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Category;

class PublicCategoryController extends Controller
{
    /**
     * Display all categories for public job search.
     */
    public function index()
    {
        $categories = Category::select('id', 'name', 'logo')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
