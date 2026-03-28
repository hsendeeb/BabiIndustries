<?php

use App\Models\Category;
use App\Models\User;

it('forbids non-admin users from creating services and categories', function () {
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $category = Category::create([
        'name' => 'Operations',
        'slug' => 'operations',
    ]);

    $industry = $user->industries()->create([
        'name' => 'Warehousing',
        'slug' => 'warehousing',
        'category_id' => $category->id,
    ]);

    $token = $user->createToken('access_token')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/services', [
            'name' => 'Inventory Tracking',
            'industry_id' => $industry->id,
        ])
        ->assertForbidden();

    $this->withToken($token)
        ->postJson('/api/v1/categories', [
            'name' => 'Logistics',
        ])
        ->assertForbidden();
});

it('allows admin users to create services and categories', function () {
    $user = User::factory()->create([
        'role' => 'admin',
    ]);

    $category = Category::create([
        'name' => 'Operations',
        'slug' => 'operations',
    ]);

    $industry = $user->industries()->create([
        'name' => 'Warehousing',
        'slug' => 'warehousing',
        'category_id' => $category->id,
    ]);

    $token = $user->createToken('access_token')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/services', [
            'name' => 'Inventory Tracking',
            'industry_id' => $industry->id,
        ])
        ->assertCreated();

    $this->withToken($token)
        ->postJson('/api/v1/categories', [
            'name' => 'Logistics',
        ])
        ->assertCreated();
});
