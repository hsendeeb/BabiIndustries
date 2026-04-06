<?php

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('lists services', function () {
    $owner = User::factory()->create();
    $category = Category::create([
        'name' => 'Operations',
        'slug' => 'operations',
    ]);

    $industry = $owner->industries()->create([
        'name' => 'Logistics',
        'slug' => 'logistics',
        'category_id' => $category->id,
    ]);

    $service = Service::create([
        'name' => 'Route Planning',
        'slug' => 'route-planning',
        'industry_id' => $industry->id,
    ]);

    $this->getJson('/api/v1/services')
        ->assertOk()
        ->assertJsonPath('data.0.id', $service->id)
        ->assertJsonPath('data.0.name', 'Route Planning')
        ->assertJsonPath('data.0.slug', 'route-planning')
        ->assertJsonPath('data.0.industry.id', $industry->id);
});

it('shows a single service', function () {
    $owner = User::factory()->create();
    $category = Category::create([
        'name' => 'Finance',
        'slug' => 'finance',
    ]);

    $industry = $owner->industries()->create([
        'name' => 'Accounting',
        'slug' => 'accounting',
        'category_id' => $category->id,
    ]);

    $service = Service::create([
        'name' => 'Payroll',
        'slug' => 'payroll',
        'industry_id' => $industry->id,
    ]);

    $this->getJson("/api/v1/services/{$service->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Service fetched successfully')
        ->assertJsonPath('data.id', $service->id)
        ->assertJsonPath('data.industry.id', $industry->id)
        ->assertJsonPath('data.industry.slug', 'accounting');
});

it('allows an admin to create a service', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $category = Category::create([
        'name' => 'Media',
        'slug' => 'media',
    ]);

    $industry = $admin->industries()->create([
        'name' => 'Broadcasting',
        'slug' => 'broadcasting',
        'category_id' => $category->id,
    ]);

    Sanctum::actingAs($admin);

    $this->postJson('/api/v1/services', [
        'name' => 'Content Scheduling',
        'industry_id' => $industry->id,
    ])
        ->assertCreated()
        ->assertJsonPath('message', 'Service created successfully')
        ->assertJsonPath('data.name', 'Content Scheduling')
        ->assertJsonPath('data.slug', 'content-scheduling')
        ->assertJsonPath('data.industry.id', $industry->id);

    $this->assertDatabaseHas('services', [
        'name' => 'Content Scheduling',
        'slug' => 'content-scheduling',
        'industry_id' => $industry->id,
    ]);
});

it('allows an admin to update a service', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $category = Category::create([
        'name' => 'Security',
        'slug' => 'security',
    ]);

    $industry = $admin->industries()->create([
        'name' => 'Cybersecurity',
        'slug' => 'cybersecurity',
        'category_id' => $category->id,
    ]);

    $service = Service::create([
        'name' => 'Threat Monitoring',
        'slug' => 'threat-monitoring',
        'industry_id' => $industry->id,
    ]);

    Sanctum::actingAs($admin);

    $this->putJson("/api/v1/services/{$service->id}", [
        'name' => 'Managed Threat Monitoring',
        'industry_id' => $industry->id,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Service updated successfully')
        ->assertJsonPath('data.name', 'Managed Threat Monitoring')
        ->assertJsonPath('data.slug', 'managed-threat-monitoring');

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Managed Threat Monitoring',
        'slug' => 'managed-threat-monitoring',
        'industry_id' => $industry->id,
    ]);
});

it('allows an admin to delete a service', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $category = Category::create([
        'name' => 'Legal',
        'slug' => 'legal',
    ]);

    $industry = $admin->industries()->create([
        'name' => 'Compliance',
        'slug' => 'compliance',
        'category_id' => $category->id,
    ]);

    $service = Service::create([
        'name' => 'Audit Support',
        'slug' => 'audit-support',
        'industry_id' => $industry->id,
    ]);

    Sanctum::actingAs($admin);

    $this->deleteJson("/api/v1/services/{$service->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Service deleted successfully');

    $this->assertDatabaseMissing('services', [
        'id' => $service->id,
    ]);
});
