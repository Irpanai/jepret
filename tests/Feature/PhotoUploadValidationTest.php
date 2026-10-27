<?php

namespace Tests\Feature;

use App\Models\Camera;
use App\Models\Event;
use App\Models\Photo;
use App\Models\User;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoUploadValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PackageSeeder::class);
        Storage::fake('local');
    }

    public function test_upload_fails_gracefully_when_no_event_is_selected_or_created(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($photographer)->postJson(route('fotografer.photos.store'), [
            'event_id' => '',
            'new_folder' => '',
            'harga' => 20000,
            'photos' => [UploadedFile::fake()->image('photo.jpg', 800, 600)],
            'watermark' => UploadedFile::fake()->image('watermark.png', 200, 100),
        ]);

        // Must return 422 Unprocessable Entity with clear validation message, NOT 404/500 ModelNotFoundException
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['event_id', 'new_folder']);
        $this->assertDatabaseCount('photos', 0);
    }

    public function test_upload_succeeds_with_existing_event(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $event = Event::factory()->create([
            'fotografer_id' => $photographer->id,
            'nama_event' => 'Makassar Marathon 2026',
        ]);

        $response = $this->actingAs($photographer)->post(route('fotografer.photos.store'), [
            'event_id' => $event->id,
            'harga' => 25000,
            'photos' => [UploadedFile::fake()->image('runner.jpg', 800, 600)],
            'watermark' => UploadedFile::fake()->image('watermark.png', 200, 100),
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('photos', 1);
        $this->assertSame($event->id, Photo::sole()->event_id);
    }

    public function test_upload_succeeds_with_new_event(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($photographer)->post(route('fotografer.photos.store'), [
            'new_folder' => 'Sunset Beach Run',
            'harga' => 30000,
            'photos' => [UploadedFile::fake()->image('sunset.jpg', 800, 600)],
            'watermark' => UploadedFile::fake()->image('watermark.png', 200, 100),
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('photos', 1);
        $this->assertDatabaseHas('events', [
            'fotografer_id' => $photographer->id,
            'nama_event' => 'Sunset Beach Run',
        ]);
    }

    public function test_upload_fails_when_selecting_another_photographers_event(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $otherPhotographer = User::factory()->fotografer()->create();
        $otherEvent = Event::factory()->create([
            'fotografer_id' => $otherPhotographer->id,
            'nama_event' => 'Private Session',
        ]);

        $response = $this->actingAs($photographer)->postJson(route('fotografer.photos.store'), [
            'event_id' => $otherEvent->id,
            'harga' => 20000,
            'photos' => [UploadedFile::fake()->image('photo.jpg', 800, 600)],
            'watermark' => UploadedFile::fake()->image('watermark.png', 200, 100),
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['event_id']);
        $this->assertDatabaseCount('photos', 0);
    }

    public function test_upload_page_renders_clean_ui_without_duplicate_watermark_section(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'verified_at' => now(),
            'photographer_watermark_locked' => true,
            'custom_watermark_path' => 'watermarks/test.png',
        ]);

        $response = $this->actingAs($photographer)->get(route('fotografer.photos.index'));

        $response->assertOk();
        // Segmented toggle is present
        $response->assertSee('Pilih Event yang Ada');
        $response->assertSee('+ Buat Event Baru');
        // Badge Terkunci is present in sidebar
        $response->assertSee('Terkunci');
        // Redundant top banner form buttons are NOT present
        $response->assertDontSee('Upload / Lock');
        $response->assertDontSee('Unlock');
        // Publish button is present with clean helper
        $response->assertSee('Upload & publish');
        $response->assertSee('Pilih setidaknya 1 foto di atas untuk mulai mengunggah.');
    }

    public function test_photo_detail_metadata_displays_camera_and_lens_and_omits_resolution(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'is_active' => true,
        ]);

        $camera = Camera::create([
            'fotografer_id' => $photographer->id,
            'name' => 'Kamera Utama CFD',
            'brand_model' => 'SONY A7 VI',
            'lens' => '70-200mm f/2.8',
        ]);

        $event = Event::factory()->create([
            'fotografer_id' => $photographer->id,
            'nama_event' => 'CFD Sudirman',
            'lokasi' => 'Jakarta',
        ]);

        $photo = Photo::create([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'camera_id' => $camera->id,
            'title' => 'Runner Action',
            'file_asli' => 'photos/original/test.jpg',
            'file_watermark' => 'photos/preview/test.jpg',
            'purchased_path' => 'photos/purchased/test.jpg',
            'original_sha256' => 'dummy',
            'harga' => 15000,
            'status' => 'active',
            'category' => 'Sports',
            'published_at' => now(),
            'storage_bytes' => 1048576,
        ]);

        $response = $this->get(route('marketplace.show', $photo));
        $response->assertOk();

        // Metadata displays camera body & lens from photographer's camera
        $response->assertSee('SONY A7 VI');
        $response->assertSee('70-200mm f/2.8');

        // Resolution section must be completely omitted from metadata
        $response->assertDontSee('Resolusi');

        // Fallback test: even if photo has camera_id = null, it retrieves photographer's camera
        $photoWithoutCameraId = Photo::create([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'camera_id' => null,
            'title' => 'Runner Action 2',
            'file_asli' => 'photos/original/test2.jpg',
            'file_watermark' => 'photos/preview/test2.jpg',
            'purchased_path' => 'photos/purchased/test2.jpg',
            'original_sha256' => 'dummy2',
            'harga' => 15000,
            'status' => 'active',
            'published_at' => now(),
            'storage_bytes' => 1048576,
        ]);
        $responseFallback = $this->get(route('marketplace.show', $photoWithoutCameraId));
        $responseFallback->assertOk();
        $responseFallback->assertSee('SONY A7 VI');
        $responseFallback->assertSee('70-200mm f/2.8');
        $responseFallback->assertDontSee('Resolusi');
    }

    public function test_upload_with_new_folder_uses_event_location_and_fallbacks(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'location' => 'Makassar',
            'custom_watermark_path' => 'watermarks/test.png',
            'photographer_watermark_locked' => true,
        ]);

        // 1. When event_location is provided
        $response = $this->actingAs($photographer)->postJson(route('fotografer.photos.store'), [
            'new_folder' => 'Karebosi Fun Run 2026',
            'event_location' => 'Lapangan Karebosi, Makassar',
            'harga' => 25000,
            'photos' => [UploadedFile::fake()->image('runner.jpg', 600, 400)],
        ]);
        $response->assertOk();

        $event1 = Event::where('nama_event', 'Karebosi Fun Run 2026')->first();
        $this->assertNotNull($event1);
        $this->assertSame('Lapangan Karebosi, Makassar', $event1->lokasi);

        // 2. When event_location is omitted, fallback to photographer's location
        $response2 = $this->actingAs($photographer)->postJson(route('fotografer.photos.store'), [
            'new_folder' => 'Pantai Losari Sunset 2026',
            'event_location' => '',
            'harga' => 25000,
            'photos' => [UploadedFile::fake()->image('sunset.jpg', 600, 400)],
        ]);
        $response2->assertOk();

        $event2 = Event::where('nama_event', 'Pantai Losari Sunset 2026')->first();
        $this->assertNotNull($event2);
        $this->assertSame('Makassar', $event2->lokasi);
    }

    public function test_gallery_page_renders_pagination_and_omits_infinite_scroll_sentinel(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'is_active' => true,
        ]);

        $event = Event::factory()->create([
            'fotografer_id' => $photographer->id,
            'nama_event' => 'Marathon 2026',
            'lokasi' => 'Gelora Bung Karno',
        ]);

        Photo::create([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'title' => 'Marathon Photo',
            'file_asli' => 'photos/original/marathon.jpg',
            'file_watermark' => 'photos/preview/marathon.jpg',
            'purchased_path' => 'photos/purchased/marathon.jpg',
            'original_sha256' => 'dummy-sha',
            'harga' => 20000,
            'status' => 'active',
            'published_at' => now(),
            'storage_bytes' => 1048576,
        ]);

        $response = $this->get(route('galeri'));
        $response->assertOk();

        // Infinite scroll sentinel and hidden loading must not exist
        $response->assertDontSee('data-gallery-sentinel');
        $response->assertDontSee('data-gallery-loading');

        // Gallery items and numeric pagination container exist
        $response->assertSee('data-gallery-items');
        $response->assertSee('Marathon Photo');
    }

    public function test_gallery_filter_by_location_category_photographer_and_date(): void
    {
        $photographer1 = User::factory()->fotografer()->create([
            'is_verified' => true,
            'is_active' => true,
            'location' => 'Makassar',
            'name' => 'Photographer One',
        ]);
        $photographer2 = User::factory()->fotografer()->create([
            'is_verified' => true,
            'is_active' => true,
            'location' => 'Banjarbaru',
            'name' => 'Photographer Two',
        ]);

        $event1 = Event::factory()->create([
            'fotografer_id' => $photographer1->id,
            'nama_event' => 'Losari Fest',
            'lokasi' => 'Makassar',
            'tanggal_event' => '2026-10-20',
        ]);
        $event2 = Event::factory()->create([
            'fotografer_id' => $photographer2->id,
            'nama_event' => 'Borneo Expo',
            'lokasi' => 'Banjarbaru',
            'tanggal_event' => '2026-10-25',
        ]);

        Photo::create([
            'event_id' => $event1->id,
            'fotografer_id' => $photographer1->id,
            'title' => 'Sunset Pantai',
            'category' => 'Landscape',
            'file_asli' => 'photos/original/1.jpg',
            'file_watermark' => 'photos/preview/1.jpg',
            'purchased_path' => 'photos/purchased/1.jpg',
            'original_sha256' => 'dummy-sha1',
            'harga' => 15000,
            'status' => 'active',
            'published_at' => now(),
            'storage_bytes' => 1048576,
        ]);

        Photo::create([
            'event_id' => $event2->id,
            'fotografer_id' => $photographer2->id,
            'title' => 'Hutan Borneo',
            'category' => 'Nature',
            'file_asli' => 'photos/original/2.jpg',
            'file_watermark' => 'photos/preview/2.jpg',
            'purchased_path' => 'photos/purchased/2.jpg',
            'original_sha256' => 'dummy-sha2',
            'harga' => 30000,
            'status' => 'active',
            'published_at' => now(),
            'storage_bytes' => 1048576,
        ]);

        // Filter by location Makassar
        $response = $this->get(route('galeri', ['location' => 'Makassar']));
        $response->assertOk();
        $response->assertSee('Sunset Pantai');
        $response->assertDontSee('Hutan Borneo');
        $response->assertSee('Lokasi: Makassar');

        // Filter by category Nature
        $responseCategory = $this->get(route('galeri', ['category' => 'Nature']));
        $responseCategory->assertOk();
        $responseCategory->assertSee('Hutan Borneo');
        $responseCategory->assertDontSee('Sunset Pantai');
        $responseCategory->assertSee('Kategori: Nature');

        // Filter by photographer
        $responsePhotographer = $this->get(route('galeri', ['photographer' => $photographer2->id]));
        $responsePhotographer->assertOk();
        $responsePhotographer->assertSee('Hutan Borneo');
        $responsePhotographer->assertDontSee('Sunset Pantai');

        // Filter by event date
        $responseDate = $this->get(route('galeri', ['date' => '2026-10-20']));
        $responseDate->assertOk();
        $responseDate->assertSee('Sunset Pantai');
        $responseDate->assertDontSee('Hutan Borneo');
    }

    public function test_gallery_filter_by_daypart_and_active_chips(): void
    {
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'is_active' => true,
        ]);

        $event = Event::factory()->create([
            'fotografer_id' => $photographer->id,
        ]);

        // 08:00 WITA is 00:00 UTC (morning)
        Photo::create([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'title' => 'Morning Run',
            'file_asli' => 'photos/original/m.jpg',
            'file_watermark' => 'photos/preview/m.jpg',
            'purchased_path' => 'photos/purchased/m.jpg',
            'original_sha256' => 'dummy-sha-m',
            'harga' => 10000,
            'status' => 'active',
            'published_at' => now(),
            'created_at' => '2026-10-27 00:00:00',
            'storage_bytes' => 1048576,
        ]);

        // 14:00 WITA is 06:00 UTC (afternoon)
        Photo::create([
            'event_id' => $event->id,
            'fotografer_id' => $photographer->id,
            'title' => 'Afternoon Match',
            'file_asli' => 'photos/original/a.jpg',
            'file_watermark' => 'photos/preview/a.jpg',
            'purchased_path' => 'photos/purchased/a.jpg',
            'original_sha256' => 'dummy-sha-a',
            'harga' => 20000,
            'status' => 'active',
            'published_at' => now(),
            'created_at' => '2026-10-27 06:00:00',
            'storage_bytes' => 1048576,
        ]);

        $responseMorning = $this->get(route('galeri', ['daypart' => 'morning']));
        $responseMorning->assertOk();
        $responseMorning->assertSee('Morning Run');
        $responseMorning->assertDontSee('Afternoon Match');
        $responseMorning->assertSee('Waktu: Pagi (05:00 - 11:59)');
        $responseMorning->assertSee('Reset Semua');

        $responseAfternoon = $this->get(route('galeri', ['daypart' => 'afternoon']));
        $responseAfternoon->assertOk();
        $responseAfternoon->assertSee('Afternoon Match');
        $responseAfternoon->assertDontSee('Morning Run');
        $responseAfternoon->assertSee('Waktu: Siang (12:00 - 15:59)');
    }
}

