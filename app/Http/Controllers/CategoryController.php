<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    public function index() {
        return response()->json(Category::all(),200);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $baseSlug = Str::slug($validated['name']);
        if (Category::where('slug', $baseSlug)->exists()) {
            return response()->json([
                'message' => 'Slug already exists',
            ], 409);
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $baseSlug,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }
    public function show(Category $category){
        return response()->json($category,200);
    }
}
