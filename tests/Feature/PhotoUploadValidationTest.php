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
}

