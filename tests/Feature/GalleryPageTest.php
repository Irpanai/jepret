<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_renders_editorial_masonry_with_navigable_pagination(): void
    {
        $photographer = User::factory()->fotografer()->create(['studio_name' => 'Dwi Visual', 'is_verified' => true, 'verified_at' => now()]);
        $event = Event::factory()->create(['fotografer_id' => $photographer->id, 'nama_event' => 'CFD Banjarbaru']);

        Photo::factory()->count(26)->create([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'status' => 'active',
            'published_at' => now(),
        ]);

        $response = $this->get(route('galeri'));

        $response->assertOk()
            ->assertSee('Momen, Orang, Kota')
            ->assertSee('gallery-masonry', false)
            ->assertSee('data-next-page=', false)
            ->assertSee('CFD Banjarbaru')
            ->assertSee('Dwi Visual');
    }

    public function test_gallery_filters_and_sorts_active_photos(): void
    {
        $photographer = User::factory()->fotografer()->create(['studio_name' => 'Lensa Kota', 'is_verified' => true, 'verified_at' => now()]);
        $event = Event::factory()->create([
            'fotografer_id' => $photographer->id,
            'nama_event' => 'Sunday Run',
            'lokasi' => 'Lapangan Murjani',
            'tanggal_event' => '2026-09-15',
        ]);

        $lowPricePhoto = Photo::factory()->create([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'title' => 'Pelari Pagi',
            'status' => 'active',
            'category' => 'Olahraga',
            'daypart' => 'morning',
            'harga' => 15000,
            'published_at' => now()->subDay(),
        ]);
        $highPricePhoto = Photo::factory()->create([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'title' => 'Finish Line',
            'status' => 'active',
            'category' => 'Olahraga',
            'daypart' => 'morning',
            'harga' => 35000,
            'published_at' => now(),
        ]);
        Photo::factory()->create(['status' => 'inactive', 'title' => 'Foto Privat']);

        $response = $this->get(route('galeri', [
            'q' => 'Run',
            'event' => $event->id,
            'photographer' => $photographer->id,
            'category' => 'Olahraga',
            'location' => 'Murjani',
            'daypart' => 'morning',
            'date' => '2026-09-15',
            'sort' => 'price_low',
        ]));

        $response->assertOk()
            ->assertSeeInOrder([$lowPricePhoto->title, $highPricePhoto->title])
            ->assertDontSee('Foto Privat');
    }
}
