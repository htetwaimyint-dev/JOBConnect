<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
        'name' => 'required|unique:categories',
         'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('categorylogos', 'public');
        }

        $category = Category::create([
            'name' => $request->name,
            'logo' => $logoPath,
        ]);


        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('jobs'); // 1 category can have many jobs, so we load the jobs relationship
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
        'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
    ]);

    
        if ($request->hasFile('logo')) {
            
            if ($category->logo) {
                Storage::disk('public')->delete($category->logo);
            }
 
            $category->logo = $request->file('logo')->store('categorylogos', 'public');
        }

        $category->name = $request->name;
        
        $category->save();
        
        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        if ($category->jobs()->exists()) {
            $category->jobs()->delete(); 
        }

        if ($category->logo) {
            Storage::disk('public')->delete($category->logo); 
        }
            $category->delete(); 
            return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
