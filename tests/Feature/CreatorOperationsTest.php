<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\Camera;
use App\Models\Event;
use App\Models\Photo;
use App\Models\PlatformSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use App\PhotoProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Gif\Builder;
use Intervention\Gif\Decoder;
use Tests\TestCase;

class CreatorOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_photographer_dashboard_uses_scoped_real_metrics(): void
    {
        $photographer = User::factory()->fotografer()->create(['saldo' => 45000]);
        $other = User::factory()->fotografer()->create();
        $buyer = User::factory()->pembeli()->create();
        $ownPhoto = $this->photo($photographer, ['views_count' => 10]);
        $otherPhoto = $this->photo($other, ['views_count' => 90]);
        $this->transaction($buyer, $ownPhoto, ['photographer_amount' => 18000, 'platform_amount' => 2000]);
        $this->transaction($buyer, $otherPhoto, ['photographer_amount' => 90000, 'platform_amount' => 10000]);

        $this->actingAs($photographer)->get(route('fotografer.dashboard'))
            ->assertOk()->assertSee('Rp18.000')->assertSee('10')->assertSee('10%')->assertDontSee('Rp90.000');
    }

    public function test_photographer_can_search_and_filter_only_their_photo_catalog(): void
    {
        $photographer = User::factory()->fotografer()->create();
        $other = User::factory()->fotografer()->create();
        $raceEvent = Event::factory()->create(['fotografer_id' => $photographer->id, 'nama_event' => 'Bali Marathon']);
        $studioEvent = Event::factory()->create(['fotografer_id' => $photographer->id, 'nama_event' => 'Studio Session']);
        $raceCamera = Camera::factory()->create(['fotografer_id' => $photographer->id, 'name' => 'Track Camera']);
        $studioCamera = Camera::factory()->create(['fotografer_id' => $photographer->id, 'name' => 'Studio Camera']);
        Photo::factory()->create(['fotografer_id' => $photographer->id, 'event_id' => $raceEvent->id, 'camera_id' => $raceCamera->id, 'title' => 'Sunset Sprint', 'category' => 'Race', 'status' => 'active']);
        Photo::factory()->create(['fotografer_id' => $photographer->id, 'event_id' => $studioEvent->id, 'camera_id' => $studioCamera->id, 'title' => 'Indoor Portrait', 'category' => 'Portrait', 'status' => 'inactive']);
        $otherPhoto = $this->photo($other, ['title' => 'Sunset Private']);

        $this->actingAs($photographer)->get(route('fotografer.photos.index', ['q' => 'Sunset']))
            ->assertOk()
            ->assertSee('Sunset Sprint')
            ->assertDontSee('Indoor Portrait')
            ->assertDontSee($otherPhoto->title);

        $this->actingAs($photographer)->get(route('fotografer.photos.index', [
            'event' => $studioEvent->id,
            'camera' => $studioCamera->id,
            'category' => 'Portrait',
            'status' => 'inactive',
        ]))->assertOk()
            ->assertSee('Indoor Portrait')
            ->assertDontSee('Sunset Sprint');
    }

    public function test_withdrawal_uses_platform_minimum_and_prevents_duplicate_balance_mutation(): void
    {
        PlatformSetting::updateOrCreate(['key' => 'minimum_withdrawal'], ['value' => 50000]);
        $photographer = User::factory()->fotografer()->create(['saldo' => 100000]);
        $key = (string) Str::uuid();
        $payload = ['jumlah_tarik' => 50000, 'destination_type' => 'bank', 'metode_pembayaran' => 'BCA', 'nomor_tujuan' => '123', 'account_holder' => 'Creator', 'idempotency_key' => $key];

        $this->actingAs($photographer)->post(route('fotografer.withdrawals.store'), $payload)->assertSessionHasNoErrors();
        $this->actingAs($photographer)->post(route('fotografer.withdrawals.store'), $payload)->assertSessionHasNoErrors();

        $this->assertSame(1, Withdrawal::count());
        $this->assertSame(50000, $photographer->fresh()->saldo);
    }

    public function test_withdrawal_rejects_amount_below_25000_and_disables_form_for_insufficient_balance(): void
    {
        $photographer = User::factory()->fotografer()->create(['saldo' => 24999]);
        $payload = ['jumlah_tarik' => 24999, 'destination_type' => 'bank', 'metode_pembayaran' => 'BCA', 'nomor_tujuan' => '123', 'idempotency_key' => (string) Str::uuid()];

        $this->actingAs($photographer)->post(route('fotografer.withdrawals.store'), $payload)
            ->assertSessionHasErrors('jumlah_tarik');
        $this->actingAs($photographer)->get(route('fotografer.dashboard'))
            ->assertOk()
            ->assertSee('Minimum penarikan Rp25.000.')
            ->assertSee('Saldo belum mencukupi minimum penarikan.')
            ->assertSee('disabled', false);

        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertSame(24999, $photographer->fresh()->saldo);
    }

    public function test_withdrawal_accepts_exactly_25000(): void
    {
        $photographer = User::factory()->fotografer()->create(['saldo' => 25000]);

        $this->actingAs($photographer)->post(route('fotografer.withdrawals.store'), [
            'jumlah_tarik' => 25000,
            'destination_type' => 'bank',
            'metode_pembayaran' => 'BCA',
            'nomor_tujuan' => '123',
            'idempotency_key' => (string) Str::uuid(),
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('withdrawals', ['fotografer_id' => $photographer->id, 'jumlah_tarik' => 25000]);
        $this->assertSame(0, $photographer->fresh()->saldo);
    }

    public function test_rejected_withdrawal_restores_balance_exactly_once(): void
    {
        $admin = User::factory()->superadmin()->create();
        $photographer = User::factory()->fotografer()->create(['saldo' => 20000]);
        $withdrawal = Withdrawal::factory()->create(['fotografer_id' => $photographer->id, 'jumlah_tarik' => 80000, 'status' => 'pending']);
        $payload = ['status' => 'rejected', 'reason' => 'Data rekening tidak cocok'];

        $this->actingAs($admin)->post(route('superadmin.withdrawals.review', $withdrawal), $payload)->assertSessionHasNoErrors();
        $this->actingAs($admin)->post(route('superadmin.withdrawals.review', $withdrawal), $payload)->assertSessionHasErrors();

        $this->assertSame(100000, $photographer->fresh()->saldo);
        $this->assertSame('rejected', $withdrawal->fresh()->status);
        $this->assertSame(1, AdminAuditLog::where('action', 'withdrawal.rejected')->count());
    }

    public function test_admin_can_approve_and_reject_photographers_with_audit_history(): void
    {
        $admin = User::factory()->superadmin()->create();
        $approved = User::factory()->fotografer()->unverified()->create();
        $rejected = User::factory()->fotografer()->unverified()->create();

        $this->actingAs($admin)->post(route('superadmin.fotografer.review', $approved), ['decision' => 'approve'])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post(route('superadmin.fotografer.review', $rejected), ['decision' => 'reject', 'reason' => 'Profil belum lengkap'])->assertSessionHasNoErrors();

        $this->assertTrue($approved->fresh()->is_verified);
        $this->assertSame('Profil belum lengkap', $rejected->fresh()->verification_rejection_reason);
        $this->assertSame(2, AdminAuditLog::count());
    }

    public function test_orders_and_csv_are_scoped_and_formula_safe(): void
    {
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);
        $other = User::factory()->fotografer()->create();
        $buyer = User::factory()->pembeli()->create(['name' => '=HYPERLINK("bad")']);
        $own = $this->transaction($buyer, $this->photo($photographer));
        $otherTransaction = $this->transaction($buyer, $this->photo($other));

        $this->actingAs($photographer)->get(route('fotografer.orders'))->assertSee($own->order_number)->assertDontSee($otherTransaction->order_number);
        $response = $this->actingAs($photographer)->get(route('fotografer.orders.export'));
        $response->assertOk();
        $this->assertStringContainsString("'=HYPERLINK", $response->streamedContent());
    }

    public function test_superadmin_can_download_executive_pdf_report(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)->get(route('superadmin.report'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_image_processor_creates_preview_and_purchased_variants(): void
    {
        Storage::fake('local');
        $watermark = UploadedFile::fake()->image('watermark.png', 200, 100)
            ->storeAs('watermarks', 'personal.png', 'local');
        $upload = UploadedFile::fake()->image('finish.webp', 1200, 800);
        $sourceChecksum = hash_file('sha256', $upload->getRealPath());

        $paths = app(PhotoProcessor::class)->process(
            $upload,
            'test-event',
            $watermark,
        );

        Storage::disk('local')->assertExists([$paths['original'], $paths['preview'], $paths['purchased']]);
        $this->assertSame('image', $paths['media_type']);
        $this->assertSame($sourceChecksum, $paths['original_sha256']);
        $this->assertSame($sourceChecksum, hash_file('sha256', Storage::disk('local')->path($paths['original'])));
    }

    public function test_image_processor_preserves_animated_gif_derivatives(): void
    {
        Storage::fake('local');
        $watermark = UploadedFile::fake()->image('watermark.png', 10, 10)
            ->storeAs('watermarks', 'personal.png', 'local');
        $animatedGif = Builder::canvas(2, 2)
            ->addFrame(UploadedFile::fake()->image('first.gif', 2, 2)->getContent(), .1)
            ->addFrame(UploadedFile::fake()->image('second.gif', 2, 2)->getContent(), .1)
            ->setLoops(0)
            ->encode();
        $upload = UploadedFile::fake()->createWithContent('animated.gif', $animatedGif);

        $paths = app(PhotoProcessor::class)->process($upload, 'animated-event', $watermark);

        $this->assertStringEndsWith('.gif', $paths['preview']);
        $this->assertStringEndsWith('.gif', $paths['purchased']);
        $this->assertGreaterThan(1, count(Decoder::decode(Storage::disk('local')->path($paths['preview']))->frames()));
        $this->assertGreaterThan(1, count(Decoder::decode(Storage::disk('local')->path($paths['purchased']))->frames()));
    }

    public function test_photographer_can_upload_without_optional_title(): void
    {
        Storage::fake('local');
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);

        $response = $this->actingAs($photographer)->post(route('fotografer.photos.store'), [
            'new_folder' => 'Running',
            'harga' => 20000,
            'photos' => [UploadedFile::fake()->image('finish-line.webp', 1200, 800)],
            'watermark' => UploadedFile::fake()->image('personal-watermark.png', 200, 100),
            'lock_watermark' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('photos', [
            'fotografer_id' => $photographer->id,
            'title' => 'finish-line',
            'harga' => 20000,
            'sell_without_watermark' => true,
        ]);
    }

    public function test_photo_taken_at_input_is_interpreted_as_wita_and_stored_as_utc(): void
    {
        Storage::fake('local');
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);

        $this->actingAs($photographer)->post(route('fotografer.photos.store'), [
            'new_folder' => 'WITA Event',
            'harga' => 20000,
            'taken_at' => '2026-10-21T19:45',
            'photos' => [UploadedFile::fake()->image('wita.webp', 1200, 800)],
            'watermark' => UploadedFile::fake()->image('watermark.png', 200, 100),
        ])->assertSessionHasNoErrors();

        $this->assertSame('2026-10-21 11:45:00', Photo::sole()->taken_at->utc()->format('Y-m-d H:i:s'));
    }

    public function test_photographer_header_contains_a_client_side_wita_clock(): void
    {
        $photographer = User::factory()->fotografer()->create();

        $this->actingAs($photographer)->get(route('fotografer.dashboard'))
            ->assertOk()
            ->assertSee("timeZone: 'Asia/Makassar'", false)
            ->assertSee('Waktu Indonesia Tengah saat ini');
    }

    public function test_photographer_watermark_editor_settings_are_saved_per_photo(): void
    {
        Storage::fake('local');
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);
        $firstSettings = ['x' => 24.5, 'y' => 61.25, 'scale' => 18, 'opacity' => 72];
        $secondSettings = ['x' => 78, 'y' => 22, 'scale' => 35, 'opacity' => 90];

        $response = $this->actingAs($photographer)->post(route('fotografer.photos.store'), [
            'new_folder' => 'Watermark Editor',
            'harga' => 20000,
            'photos' => [
                UploadedFile::fake()->image('first-position.webp', 1200, 800),
                UploadedFile::fake()->image('second-position.webp', 800, 1200),
            ],
            'watermark' => UploadedFile::fake()->image('personal-watermark.png', 200, 100),
            'watermark_settings' => [$firstSettings, $secondSettings],
            'photo_titles' => ['Finish Line Pilihan', ''],
        ]);

        $response->assertSessionHasNoErrors();
        $firstPhoto = Photo::where('original_filename', 'first-position.webp')->sole();
        $secondPhoto = Photo::where('original_filename', 'second-position.webp')->sole();
        $this->assertSame('Finish Line Pilihan', $firstPhoto->title);
        $this->assertSame('second-position', $secondPhoto->title);
        $this->assertEquals($firstSettings, $firstPhoto->watermark_settings);
        $this->assertEquals($secondSettings, $secondPhoto->watermark_settings);
        Storage::disk('local')->assertExists([$firstPhoto->purchased_path, $secondPhoto->purchased_path]);
    }

    public function test_sequential_upload_is_idempotent_and_returns_actual_storage_usage(): void
    {
        Storage::fake('local');
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);
        $token = (string) Str::uuid();
        $payload = fn () => [
            'new_folder' => 'Sequential',
            'harga' => 20000,
            'upload_token' => $token,
            'photos' => [UploadedFile::fake()->image('sequential.webp', 1200, 800)],
            'watermark' => UploadedFile::fake()->image('watermark.png', 200, 100),
        ];

        $first = $this->actingAs($photographer)->postJson(route('fotografer.photos.store'), $payload());
        $second = $this->actingAs($photographer)->postJson(route('fotografer.photos.store'), $payload());

        $first->assertOk()->assertJsonPath('duplicate', false)->assertJsonStructure(['photo_id', 'used_bytes', 'stored_bytes']);
        $second->assertOk()->assertJsonPath('duplicate', true)->assertJsonPath('photo_id', $first->json('photo_id'));
        $this->assertDatabaseCount('photos', 1);
        $this->assertSame((int) Photo::sum('storage_bytes'), $second->json('used_bytes'));
    }

    public function test_upload_rejects_source_that_cannot_fit_before_creating_photo(): void
    {
        Storage::fake('local');
        $photographer = User::factory()->fotografer()->create([
            'is_verified' => true,
            'verified_at' => now(),
            'storage_quota_override_mb' => 0,
        ]);

        $this->actingAs($photographer)->postJson(route('fotografer.photos.store'), [
            'new_folder' => 'No Space',
            'harga' => 20000,
            'upload_token' => (string) Str::uuid(),
            'photos' => [UploadedFile::fake()->image('too-large.webp', 1200, 800)],
            'watermark' => UploadedFile::fake()->image('watermark.png', 200, 100),
        ])->assertUnprocessable()->assertJsonValidationErrors('photos');

        $this->assertDatabaseCount('photos', 0);
    }

    public function test_upload_rejects_video_and_unreadable_image_files(): void
    {
        Storage::fake('local');
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);

        foreach ([
            UploadedFile::fake()->create('clip.mov', 100, 'video/quicktime'),
            UploadedFile::fake()->createWithContent('broken.jpg', 'not-an-image'),
        ] as $file) {
            $this->actingAs($photographer)->post(route('fotografer.photos.store'), [
                'new_folder' => 'Rejected file',
                'harga' => 20000,
                'photos' => [$file],
                'watermark' => UploadedFile::fake()->image('watermark.png', 20, 20),
            ])->assertSessionHasErrors('photos.0');
        }

        $this->assertDatabaseCount('photos', 0);
    }

    public function test_upload_page_exposes_storage_projection_and_per_file_controls(): void
    {
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);

        $this->actingAs($photographer)->get(route('fotografer.photos.index'))
            ->assertOk()
            ->assertSee('Terpakai aktual')
            ->assertSee('Proyeksi sumber')
            ->assertSee('uploadAll($event)', false)
            ->assertSee('Progress upload')
            ->assertSee('role="progressbar"', false)
            ->assertSee('accept=".jpg,.jpeg,.png,.webp,.avif,.gif,.bmp"', false)
            ->assertDontSee('statusLabel(item)', false)
            ->assertDontSee('Coba lagi')
            ->assertSee('Batalkan upload');
    }

    public function test_photographer_watermark_editor_rejects_out_of_bounds_settings(): void
    {
        Storage::fake('local');
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);

        $response = $this->actingAs($photographer)->post(route('fotografer.photos.store'), [
            'new_folder' => 'Invalid Watermark',
            'harga' => 20000,
            'photos' => [UploadedFile::fake()->image('invalid.webp', 1200, 800)],
            'watermark' => UploadedFile::fake()->image('personal-watermark.png', 200, 100),
            'watermark_settings' => [['x' => 101, 'y' => 50, 'scale' => 90, 'opacity' => 0]],
        ]);

        $response->assertSessionHasErrors([
            'watermark_settings.0.x',
            'watermark_settings.0.scale',
            'watermark_settings.0.opacity',
        ]);
        $this->assertDatabaseCount('photos', 0);
    }

    public function test_saved_watermark_preview_is_private_to_the_authenticated_photographer(): void
    {
        Storage::fake('local');
        $ownerPath = UploadedFile::fake()->image('owner.png')->store('watermarks/photographers/1', 'local');
        $otherPath = UploadedFile::fake()->image('other.png')->store('watermarks/photographers/2', 'local');
        $owner = User::factory()->fotografer()->create(['custom_watermark_path' => $ownerPath]);
        $other = User::factory()->fotografer()->create(['custom_watermark_path' => $otherPath]);

        $this->actingAs($owner)->get(route('fotografer.watermark.preview'))
            ->assertOk()
            ->assertStreamedContent(file_get_contents(Storage::disk('local')->path($ownerPath)));

        $this->actingAs($other)->get(route('fotografer.watermark.preview'))
            ->assertOk()
            ->assertStreamedContent(file_get_contents(Storage::disk('local')->path($otherPath)));
    }

    public function test_unapproved_photographer_and_photos_are_hidden_from_public_routes(): void
    {
        $photographer = User::factory()->fotografer()->unverified()->create(['name' => 'Pending Creator', 'slug' => 'pending-creator']);
        $photo = $this->photo($photographer, ['title' => 'Hidden pending photo', 'status' => 'active']);

        $this->get(route('galeri'))->assertOk()->assertDontSee('Hidden pending photo');
        $this->get(route('photographers.index'))->assertOk()->assertDontSee('Pending Creator');
        $this->get(route('photographers.show', $photographer->slug))->assertNotFound();
        $this->get(route('marketplace.show', $photo))->assertNotFound();
        $this->get(route('media.preview', $photo))->assertNotFound();
        $this->actingAs($photographer)->post(route('fotografer.photos.store'), [])->assertForbidden();
    }

    public function test_approved_photographer_is_not_approved_twice_or_reset_by_profile_update(): void
    {
        $admin = User::factory()->superadmin()->create();
        $photographer = User::factory()->fotografer()->unverified()->create();

        $this->actingAs($admin)->post(route('superadmin.fotografer.review', $photographer), ['decision' => 'approve'])->assertSessionHasNoErrors();
        $verifiedAt = $photographer->fresh()->verified_at;
        $this->actingAs($admin)->post(route('superadmin.fotografer.review', $photographer), ['decision' => 'reject', 'reason' => 'Tidak berlaku'])->assertSessionHasErrors('decision');
        $this->actingAs($photographer)->patch(route('profile.update'), ['name' => 'Nama Baru', 'email' => $photographer->email])->assertSessionHasNoErrors();

        $photographer->refresh();
        $this->assertTrue($photographer->is_verified);
        $this->assertTrue($photographer->verified_at->equalTo($verifiedAt));
    }

    public function test_photographer_can_upload_profile_photo_from_portfolio(): void
    {
        Storage::fake('public');
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);

        $this->actingAs($photographer)->patch(route('fotografer.portfolio.update'), [
            'name' => $photographer->name,
            'slug' => 'portfolio-photo-test',
            'profile_photo' => UploadedFile::fake()->image('profile.webp', 600, 600),
        ])->assertSessionHasNoErrors();

        $photographer->refresh();
        $this->assertNotNull($photographer->profile_photo_path);
        Storage::disk('public')->assertExists($photographer->profile_photo_path);
        $this->get(route('media.profile', $photographer))->assertOk();
    }

    public function test_photographer_social_links_are_saved_and_shown_on_public_profile(): void
    {
        $photographer = User::factory()->fotografer()->create(['is_verified' => true, 'verified_at' => now()]);

        $this->actingAs($photographer)->patch(route('fotografer.portfolio.update'), [
            'name' => $photographer->name,
            'slug' => 'social-links-test',
            'whatsapp' => '+62 812-3456-7890',
            'instagram_username' => '@winantioo',
        ])->assertSessionHasNoErrors();

        $photographer->refresh();
        $this->assertSame('6281234567890', $photographer->whatsapp);
        $this->assertSame('winantioo', $photographer->instagram_username);

        $this->get(route('photographers.show', $photographer->slug))
            ->assertOk()
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee('https://www.instagram.com/winantioo', false)
            ->assertSee('@winantioo');
    }

    private function photo(User $photographer, array $attributes = []): Photo
    {
        $event = Event::factory()->create(['fotografer_id' => $photographer->id]);

        return Photo::factory()->create($attributes + ['event_id' => $event->id, 'fotografer_id' => $photographer->id, 'title' => 'Finish line']);
    }

    private function transaction(User $buyer, Photo $photo, array $attributes = []): Transaction
    {
        return Transaction::factory()->create($attributes + ['order_number' => 'ORDER-'.Str::random(8), 'pembeli_id' => $buyer->id, 'photo_id' => $photo->id, 'fotografer_id' => $photo->fotografer_id, 'harga_foto' => 20000, 'total_bayar' => 20000, 'photographer_amount' => 18000, 'platform_amount' => 2000, 'revenue_share_snapshot' => ['photographer_percent' => 90, 'platform_percent' => 10], 'status' => 'paid', 'payment_status' => 'paid', 'paid_at' => now()]);
    }
}
