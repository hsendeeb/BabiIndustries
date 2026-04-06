<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIndustryRequest;
use App\Http\Requests\UpdateIndustryRequest;
use App\Http\Resources\IndustryResource as ApiIndustryResource;
use App\Models\Industry;
use App\Services\SlugService;
use App\Traits\PaginatedResults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IndustryController extends Controller
{
    use PaginatedResults;

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

        return ApiIndustryResource::collection($industries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIndustryRequest $request, SlugService $slugService)
    {
        $validated = $request->validated();
        $baseSlug = $slugService->make($validated['name']);

        $industry = auth()->user()->industries()->create([
            'name' => $validated['name'],
            'slug' => $baseSlug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'category_id' => $validated['category_id'],
        ]);

        return response()->json([
            'message' => 'Industry created successfully',
            'data' => new ApiIndustryResource($industry->load(['category:id,name,slug'])->loadCount('services')),
        ], 201);
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
    public function update(UpdateIndustryRequest $request, Industry $industry, SlugService $slugService)
    {
        $validated = $request->validated();
        $baseSlug = $slugService->make($validated['name']);

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
        Gate::authorize('delete', $industry);
        $industry->delete();
        return response()->json([
            'message' => 'Industry deleted successfully',
        ], 200);
    }
}
