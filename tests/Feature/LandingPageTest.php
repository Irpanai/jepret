<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Package;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_successfully_for_guests(): void
    {
        $response = $this->get(route('landing'));

        $response->assertOk()
            ->assertSee('Jepret')
            ->assertSee('FAQ')
            ->assertSee('Pertanyaan yang sering')
            ->assertSee('JEPRET');
    }

    public function test_landing_page_displays_featured_gallery_and_photographers(): void
    {
        $package = Package::factory()->create([
            'is_active' => true,
            'price' => 50000,
        ]);

        $photographer = User::factory()->fotografer()->create([
            'name' => 'Budi Santoso',
            'studio_name' => 'Budi Studio',
            'is_verified' => true,
            'verified_at' => now(),
            'is_active' => true,
        ]);

        // Give photographer an active subscription
        $photographer->subscriptions()->create([
            'package_id' => $package->id,
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ]);

        $event = Event::factory()->create([
            'fotografer_id' => $photographer->id,
            'nama_event' => 'Maraton Senja 2026',
        ]);

        $photo = Photo::factory()->create([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'title' => 'Lari Pagi Semangat',
            'status' => 'active',
            'published_at' => now(),
            'harga' => 25000,
        ]);

        $response = $this->get(route('landing'));

        $response->assertOk()
            ->assertSee('Budi Studio')
            ->assertSee('Lari Pagi Semangat')
            ->assertSee('Fotografer')
            ->assertSee('Kreator Terverifikasi');
    }

    public function test_authenticated_buyer_is_redirected_to_gallery(): void
    {
        $buyer = User::factory()->pembeli()->create();

        $response = $this->actingAs($buyer)->get(route('landing'));

        $response->assertRedirect(route('galeri'));
    }
}
