<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json(
            Service::query()
                ->select('id', 'name', 'slug', 'industry_id')
                ->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'industry_id' => 'required|integer|exists:industries,id',
        ]);

        $baseSlug = Str::slug($validated['name']);
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
            'data' => $service,
        ], 201);
    }

    public function show(Service $service)
    {
        return response()->json($service, 200);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'industry_id' => 'required|integer|exists:industries,id',
        ]);

        $baseSlug = Str::slug($validated['name']);
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
            'data' => $service
        ], 200);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully',
        ], 200);
    }
}
