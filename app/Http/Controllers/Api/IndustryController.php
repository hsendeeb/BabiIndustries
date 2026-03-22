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
        $industries=Industry::with(['services','category'])
        ->select('id', 'name', 'slug', 'description', 'icon', 'category_id')
        ->get();
        return response()->json( $industries ,200);
      
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
            'icon' => $validated['icon'] ?? null,
            'category_id' => $validated['category_id']
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
            'description'=>'nullable|string',
            'icon'=>'nullable|string|max:255',
            'category_id'=>'required|integer|exists:categories,id'
        ]);
        $industry->update($validated);
        return response()->json([
            'message' => 'Industry updated successfully',
            'data' => $industry
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
}
