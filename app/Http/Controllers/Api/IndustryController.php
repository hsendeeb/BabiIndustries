<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IndustryResource as ApiIndustryResource;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $industries = Industry::query()
            ->select('id', 'name', 'slug', 'description', 'icon', 'category_id')
            ->with(['category:id,name,slug'])
            ->with(['services:id,name,slug,industry_id'])
            ->withCount('services')
            ->latest('id')
            ->paginate($this->getPerPage($request));

        return ApiIndustryResource::collection($industries)->additional([
            'message' => 'Industries fetched successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Industry::class);
        $validated=$request->validate([
            'name'=>'required|string|max:255|unique:industries,name',
            'description'=>'nullable|string',
            'icon'=>'nullable|string|max:255',
            'category_id'=>'required|integer|exists:categories,id'
        ]);
        $baseSlug = Str::slug(trim($validated['name']));
        if (Industry::where('slug', $baseSlug)->exists()) {
            return response()->json([
                'message' => 'Slug already exists',
            ], 409);
        }

        $industry=auth()->user()->industries()->create([
            'name' => $validated['name'],
            'slug' => $baseSlug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'category_id' => $validated['category_id']
        ]);
        return response()->json([
            'message' => 'Industry created successfully',
            'data' => new ApiIndustryResource($industry->load(['category:id,name,slug'])->loadCount('services')),
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Industry $industry)
    {
        return response()->json([
            'message' => 'Industry fetched successfully',
            'data' => new ApiIndustryResource(
                $industry->load([
                    'category:id,name,slug',
                    'services:id,name,slug,industry_id',
                ])->loadCount('services')
            ),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Industry $industry)
    {
        Gate::authorize('update', $industry);
        $validated=$request->validate([
            'name'=>'required|string|max:255|unique:industries,name,'.$industry->id,
            'description'=>'nullable|string',
            'icon'=>'nullable|string|max:255',
            'category_id'=>'required|integer|exists:categories,id'
        ]);

        $baseSlug = Str::slug(trim($validated['name']));
        if (
            Industry::query()
                ->where('slug', $baseSlug)
                ->where('id', '!=', $industry->id)
                ->exists()
        ) {
            return response()->json([
                'message' => 'Slug already exists',
            ], 409);
        }

        $industry->update([
            'name' => $validated['name'],
            'slug' => $baseSlug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'category_id' => $validated['category_id'],
        ]);

        return response()->json([
            'message' => 'Industry updated successfully',
            'data' => new ApiIndustryResource($industry->load(['category:id,name,slug'])->loadCount('services')),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Industry $industry)
    {
        Gate::authorize('delete',$industry);
        $industry->delete();
        return response()->json([
            'message' => 'Industry deleted successfully',
        ],200);
    }

    private function getPerPage(Request $request): int
    {
        return max(1, min(100, $request->integer('per_page', 15)));
    }
}
