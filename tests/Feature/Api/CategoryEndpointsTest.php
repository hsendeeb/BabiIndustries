<?php

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('lists categories', function () {
    $owner = User::factory()->create();

    $category = Category::create([
        'name' => 'Infrastructure',
        'slug' => 'infrastructure',
    ]);

    $owner->industries()->create([
        'name' => 'Construction',
        'slug' => 'construction',
        'category_id' => $category->id,
    ]);

    $this->getJson('/api/v1/categories')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Infrastructure')
        ->assertJsonPath('data.0.slug', 'infrastructure')
        ->assertJsonPath('data.0.industries_count', 1);
});

it('shows a single category', function () {
    $category = Category::create([
        'name' => 'Education',
        'slug' => 'education',
    ]);

    $this->getJson("/api/v1/categories/{$category->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Category fetched successfully')
        ->assertJsonPath('data.id', $category->id)
        ->assertJsonPath('data.name', 'Education')
        ->assertJsonPath('data.industries_count', 0);
});

it('allows an admin to create a category', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    Sanctum::actingAs($admin);

    $this->postJson('/api/v1/categories', [
        'name' => 'Hospitality',
    ])
        ->assertCreated()
        ->assertJsonPath('message', 'Category created successfully')
        ->assertJsonPath('data.name', 'Hospitality')
        ->assertJsonPath('data.slug', 'hospitality')
        ->assertJsonPath('data.industries_count', 0);

    $this->assertDatabaseHas('categories', [
        'name' => 'Hospitality',
        'slug' => 'hospitality',
    ]);
});

it('allows an admin to update a category', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $category = Category::create([
        'name' => 'Travel',
        'slug' => 'travel',
    ]);

    Sanctum::actingAs($admin);

    $this->putJson("/api/v1/categories/{$category->id}", [
        'name' => 'Business Travel',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Category updated successfully')
        ->assertJsonPath('data.name', 'Business Travel')
        ->assertJsonPath('data.slug', 'business-travel');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Business Travel',
        'slug' => 'business-travel',
    ]);
});

it('allows an admin to delete a category', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $category = Category::create([
        'name' => 'Agriculture',
        'slug' => 'agriculture',
    ]);

    Sanctum::actingAs($admin);

    $this->deleteJson("/api/v1/categories/{$category->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Category deleted successfully');

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});
