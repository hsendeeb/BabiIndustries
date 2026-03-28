<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource as ApiCategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
            ->select('id', 'name', 'slug')
            ->withCount('industries')
            ->latest('id')
            ->paginate($this->getPerPage($request));

        return ApiCategoryResource::collection($categories)->additional([
            'message' => 'Categories fetched successfully',
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Category::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $baseSlug = Str::slug(trim($validated['name']));
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
            'data' => new ApiCategoryResource($category->loadCount('industries')),
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json([
            'message' => 'Category fetched successfully',
            'data' => new ApiCategoryResource($category->loadCount('industries')),
        ], 200);
    }

    public function update(Request $request, Category $category)
    {
        Gate::authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
        ]);

        $baseSlug = Str::slug(trim($validated['name']));
        if (Category::where('slug', $baseSlug)->where('id', '!=', $category->id)->exists()) {
            return response()->json([
                'message' => 'Slug already exists',
            ], 409);
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $baseSlug,
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new ApiCategoryResource($category->loadCount('industries')),
        ], 200);
    }

    public function destroy(Category $category)
    {
        Gate::authorize('delete', $category);

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }

    private function getPerPage(Request $request): int
    {
        return max(1, min(100, $request->integer('per_page', 15)));
    }
}
