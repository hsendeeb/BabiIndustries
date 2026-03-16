<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Industry;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;    

class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Industry::all()->select('id', 'name', 'slug', 'description'),200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Industry::class);
        $validated=$request->validate([
            'name'=>'required|string|max:255|unique:industries,name',
            'description'=>'nullable|string'
        ]);
        $baseSlug = Str::slug($validated['name']);
        if (Industry::where('slug', $baseSlug)->exists()) {
            return response()->json([
                'message' => 'Slug already exists',
            ], 409);
        }

        $industry=auth()->user()->industries()->create([
            'name' => $validated['name'],
            'slug' => $baseSlug,
            'description' => $validated['description'] ?? null,
        ]);
        return response()->json([
            'message' => 'Industry created successfully',
            'data' => $industry 
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Industry $industry)
    {
        return response()->json($industry,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Industry $industry)
    {
        Gate::authorize('update', $industry);
        $validated=$request->validate([
            'name'=>'required|string|max:255|unique:industries,name,'.$industry->id,
            'description'=>'nullable|string'
        ]);
        $industry->update($validated);
        return response()->json($industry,200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Industry $industry)
    {
        $industry->delete();
        return response()->json([
            'message' => 'Industry deleted successfully',
        ],200);
    }
}
