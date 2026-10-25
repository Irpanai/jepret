<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminStorageSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_search_storage_by_photographer_name_or_email(): void
    {
        $admin = User::factory()->superadmin()->create();
        User::factory()->fotografer()->create(['name' => 'Ayu Makassar', 'email' => 'ayu@example.test']);
        User::factory()->fotografer()->create(['name' => 'Budi Bali', 'email' => 'budi@example.test']);

        $this->actingAs($admin)->get(route('superadmin.storage', ['q' => 'Makassar']))
            ->assertOk()
            ->assertSee('Ayu Makassar')
            ->assertDontSee('Budi Bali');

        $this->actingAs($admin)->get(route('superadmin.storage', ['q' => 'budi@example.test']))
            ->assertOk()
            ->assertSee('Budi Bali')
            ->assertDontSee('Ayu Makassar');
    }

    public function test_storage_search_returns_paginated_partial_for_ajax_requests(): void
    {
        $admin = User::factory()->superadmin()->create();
        User::factory()->fotografer()->count(16)->create(['name' => 'Searchable Creator']);

        $this->actingAs($admin)->get(route('superadmin.storage', ['q' => 'Searchable']), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('Searchable Creator')
            ->assertSee('q=Searchable', false)
            ->assertDontSee('Storage Management');

        $this->actingAs($admin)->get(route('superadmin.storage'))
            ->assertOk()
            ->assertSee('storage-search-input', false)
            ->assertSee('setTimeout(() => form.requestSubmit(), 300)', false);
    }
}
