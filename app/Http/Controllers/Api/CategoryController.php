<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource as ApiCategoryResource;
use App\Models\Category;
use App\Services\SlugService;
use App\Traits\PaginatedResults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    use PaginatedResults;

    public function index(Request $request)
    {
        $categories = Category::query()
            ->select('id', 'name', 'slug')
            ->withCount('industries')
            ->latest('id')
            ->paginate($this->getPerPage($request));

        return ApiCategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request, SlugService $slugService)
    {
        $validated = $request->validated();
        $baseSlug = $slugService->make($validated['name']);

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

    public function update(UpdateCategoryRequest $request, Category $category, SlugService $slugService)
    {
        $validated = $request->validated();
        $baseSlug = $slugService->make($validated['name']);

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
}
