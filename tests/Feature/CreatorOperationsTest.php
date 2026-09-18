<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
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

    public function test_withdrawal_uses_platform_minimum_and_prevents_duplicate_balance_mutation(): void
    {
        PlatformSetting::create(['key' => 'minimum_withdrawal', 'value' => 50000]);
        $photographer = User::factory()->fotografer()->create(['saldo' => 100000]);
        $key = (string) Str::uuid();
        $payload = ['jumlah_tarik' => 50000, 'destination_type' => 'bank', 'metode_pembayaran' => 'BCA', 'nomor_tujuan' => '123', 'account_holder' => 'Creator', 'idempotency_key' => $key];

        $this->actingAs($photographer)->post(route('fotografer.withdrawals.store'), $payload)->assertSessionHasNoErrors();
        $this->actingAs($photographer)->post(route('fotografer.withdrawals.store'), $payload)->assertSessionHasNoErrors();

        $this->assertSame(1, Withdrawal::count());
        $this->assertSame(50000, $photographer->fresh()->saldo);
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

        $paths = app(PhotoProcessor::class)->process(
            UploadedFile::fake()->image('finish.webp', 1200, 800),
            'test-event',
            $watermark,
        );

        Storage::disk('local')->assertExists([$paths['original'], $paths['preview'], $paths['purchased']]);
        $this->assertSame('image', $paths['media_type']);
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
        ]);
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
