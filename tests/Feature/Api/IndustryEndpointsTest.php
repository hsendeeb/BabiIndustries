<?php

use App\Models\Category;
use App\Models\Industry;
use App\Models\Service;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('lists industries', function () {
    $owner = User::factory()->create();
    $category = Category::create([
        'name' => 'Manufacturing',
        'slug' => 'manufacturing',
    ]);

    $industry = $owner->industries()->create([
        'name' => 'Automotive',
        'slug' => 'automotive',
        'description' => 'Vehicle production',
        'icon' => 'car',
        'category_id' => $category->id,
    ]);

    $service = Service::create([
        'name' => 'Assembly',
        'slug' => 'assembly',
        'industry_id' => $industry->id,
    ]);

    $this->getJson('/api/v1/industries')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Automotive')
        ->assertJsonPath('data.0.slug', 'automotive')
        ->assertJsonPath('data.0.category.id', $category->id)
        ->assertJsonPath('data.0.services.0.id', $service->id)
        ->assertJsonPath('data.0.services_count', 1);
});

it('shows a single industry', function () {
    $owner = User::factory()->create();
    $category = Category::create([
        'name' => 'Technology',
        'slug' => 'technology',
    ]);

    $industry = $owner->industries()->create([
        'name' => 'Software',
        'slug' => 'software',
        'description' => 'Apps and platforms',
        'icon' => 'code',
        'category_id' => $category->id,
    ]);

    $service = Service::create([
        'name' => 'Custom Development',
        'slug' => 'custom-development',
        'industry_id' => $industry->id,
    ]);

    $this->getJson("/api/v1/industries/{$industry->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Industry fetched successfully')
        ->assertJsonPath('data.id', $industry->id)
        ->assertJsonPath('data.category.slug', 'technology')
        ->assertJsonPath('data.services.0.id', $service->id)
        ->assertJsonPath('data.services_count', 1);
});

it('allows an admin to create an industry', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $category = Category::create([
        'name' => 'Energy',
        'slug' => 'energy',
    ]);

    Sanctum::actingAs($admin);

    $this->postJson('/api/v1/industries', [
        'name' => 'Renewables',
        'description' => 'Clean power',
        'icon' => 'bolt',
        'category_id' => $category->id,
    ])
        ->assertCreated()
        ->assertJsonPath('message', 'Industry created successfully')
        ->assertJsonPath('data.name', 'Renewables')
        ->assertJsonPath('data.slug', 'renewables')
        ->assertJsonPath('data.category.id', $category->id)
        ->assertJsonPath('data.services_count', 0);

    $this->assertDatabaseHas('industries', [
        'name' => 'Renewables',
        'slug' => 'renewables',
        'description' => 'Clean power',
        'icon' => 'bolt',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);
});

it('allows an admin to update an industry', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $originalCategory = Category::create([
        'name' => 'Retail',
        'slug' => 'retail',
    ]);

    $newCategory = Category::create([
        'name' => 'Commerce',
        'slug' => 'commerce',
    ]);

    $industry = $admin->industries()->create([
        'name' => 'Ecommerce',
        'slug' => 'ecommerce',
        'description' => 'Online stores',
        'icon' => 'shop',
        'category_id' => $originalCategory->id,
    ]);

    Sanctum::actingAs($admin);

    $this->putJson("/api/v1/industries/{$industry->id}", [
        'name' => 'Digital Commerce',
        'description' => 'Unified online sales',
        'icon' => 'cart',
        'category_id' => $newCategory->id,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Industry updated successfully')
        ->assertJsonPath('data.name', 'Digital Commerce')
        ->assertJsonPath('data.slug', 'digital-commerce')
        ->assertJsonPath('data.category.id', $newCategory->id);

    $this->assertDatabaseHas('industries', [
        'id' => $industry->id,
        'name' => 'Digital Commerce',
        'slug' => 'digital-commerce',
        'description' => 'Unified online sales',
        'icon' => 'cart',
        'category_id' => $newCategory->id,
    ]);
});

it('allows an admin to delete an industry', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $category = Category::create([
        'name' => 'Healthcare',
        'slug' => 'healthcare',
    ]);

    $industry = $admin->industries()->create([
        'name' => 'Diagnostics',
        'slug' => 'diagnostics',
        'category_id' => $category->id,
    ]);

    Sanctum::actingAs($admin);

    $this->deleteJson("/api/v1/industries/{$industry->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Industry deleted successfully');

    $this->assertDatabaseMissing('industries', [
        'id' => $industry->id,
    ]);
});
