<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource as ApiServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::query()
            ->select('id', 'name', 'slug', 'industry_id')
            ->with(['industry:id,name,slug'])
            ->latest('id')
            ->paginate($this->getPerPage($request));

        return ApiServiceResource::collection($services)->additional([
            'message' => 'Services fetched successfully',
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Service::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'industry_id' => 'required|integer|exists:industries,id',
        ]);

        $baseSlug = Str::slug(trim($validated['name']));
        if (Service::where('slug', $baseSlug)->exists()) {
            return response()->json([
                'message' => 'Slug already exists',
            ], 409);
        }

        $service = Service::create([
            'name' => $validated['name'],
            'slug' => $baseSlug,
            'industry_id' => $validated['industry_id'],
        ]);

        return response()->json([
            'message' => 'Service created successfully',
            'data' => new ApiServiceResource($service->load(['industry:id,name,slug'])),
        ], 201);
    }

    public function show(Service $service)
    {
        return response()->json([
            'message' => 'Service fetched successfully',
            'data' => new ApiServiceResource($service->load(['industry:id,name,slug'])),
        ], 200);
    }

    public function update(Request $request, Service $service)
    {
        Gate::authorize('update', $service);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,'.$service->id,
            'industry_id' => 'required|integer|exists:industries,id',
        ]);

        $baseSlug = Str::slug(trim($validated['name']));
        $slugExists = Service::where('slug', $baseSlug)
            ->where('id', '!=', $service->id)
            ->exists();

        if ($slugExists) {
            return response()->json([
                'message' => 'Slug already exists',
            ], 409);
        }

        $service->update([
            'name' => $validated['name'],
            'slug' => $baseSlug,
            'industry_id' => $validated['industry_id'],
        ]);

        return response()->json([
            'message' => 'Service updated successfully',
            'data' => new ApiServiceResource($service->load(['industry:id,name,slug'])),
        ], 200);
    }

    public function destroy(Service $service)
    {
        Gate::authorize('delete', $service);

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully',
        ], 200);
    }

    private function getPerPage(Request $request): int
    {
        return max(1, min(100, $request->integer('per_page', 15)));
    }
}
