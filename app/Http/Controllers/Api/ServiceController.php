<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource as ApiServiceResource;
use App\Models\Service;
use App\Services\SlugService;
use App\Traits\PaginatedResults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    use PaginatedResults;

    public function index(Request $request)
    {
        $services = Service::query()
            ->select('id', 'name', 'slug', 'industry_id')
            ->with(['industry:id,name,slug'])
            ->latest('id')
            ->paginate($this->getPerPage($request));

        return ApiServiceResource::collection($services);
    }

    public function store(StoreServiceRequest $request, SlugService $slugService)
    {
        $validated = $request->validated();
        $baseSlug = $slugService->make($validated['name']);

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

    public function update(UpdateServiceRequest $request, Service $service, SlugService $slugService)
    {
        $validated = $request->validated();
        $baseSlug = $slugService->make($validated['name']);

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
}
