<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminPhotographerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_view_update_and_delete_photographer(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)->post(route('superadmin.photographers.store'), [
            'name' => 'Dina Photographer',
            'email' => 'dina@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'slug' => 'dina-photo',
            'studio_name' => 'Dina Visual',
            'instagram_username' => 'dinavisual',
            'is_active' => '1',
            'is_verified' => '1',
        ])->assertRedirect();

        $photographer = User::where('email', 'dina@example.test')->firstOrFail();
        $this->assertSame('fotografer', $photographer->role);
        $this->assertTrue($photographer->is_active);
        $this->assertTrue($photographer->is_verified);

        $this->actingAs($admin)
            ->get(route('superadmin.photographers.show', $photographer))
            ->assertOk()
            ->assertSee('Dina Visual');

        $this->actingAs($admin)->put(route('superadmin.photographers.update', $photographer), [
            'name' => 'Dina Updated',
            'email' => 'dina@example.test',
            'slug' => 'dina-photo',
            'studio_name' => 'Dina Studio',
            'instagram_username' => 'dinastudio',
        ])->assertRedirect(route('superadmin.photographers.show', $photographer));

        $this->assertDatabaseHas('users', ['id' => $photographer->id, 'name' => 'Dina Updated', 'studio_name' => 'Dina Studio']);

        $this->actingAs($admin)
            ->delete(route('superadmin.photographers.destroy', $photographer))
            ->assertRedirect(route('superadmin.photographers.index'));

        $this->assertDatabaseMissing('users', ['id' => $photographer->id]);
    }

    public function test_inactive_photographer_is_hidden_publicly_and_cannot_access_creator_center(): void
    {
        $admin = User::factory()->superadmin()->create();
        $photographer = User::factory()->fotografer()->create([
            'name' => 'Inactive Target',
            'slug' => 'inactive-target',
            'is_verified' => true,
            'is_active' => true,
        ]);
        $event = Event::factory()->create(['fotografer_id' => $photographer->id]);
        Photo::factory()->create(['fotografer_id' => $photographer->id, 'event_id' => $event->id, 'title' => 'Hidden after deactivation', 'status' => 'active']);

        $this->actingAs($admin)->patch(route('superadmin.photographers.status', $photographer), ['is_active' => false])->assertSessionHasNoErrors();

        $this->assertFalse($photographer->fresh()->is_active);
        $this->get(route('galeri'))->assertOk()->assertDontSee('Hidden after deactivation');
        $this->get(route('photographers.show', $photographer->slug))->assertNotFound();
        $this->actingAs($photographer->fresh())->get(route('fotografer.dashboard'))->assertForbidden();
    }

    public function test_non_superadmin_cannot_manage_photographers(): void
    {
        $photographer = User::factory()->fotografer()->create();

        $this->actingAs($photographer)
            ->get(route('superadmin.photographers.index'))
            ->assertForbidden();
    }
}
