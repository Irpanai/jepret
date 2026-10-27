<?php

namespace Tests\Feature;

use App\Models\Camera;
use App\Models\Event;
use App\Models\Package;
use App\Models\PaymentEvent;
use App\Models\Photo;
use App\Models\Subscription;
use App\Models\SubscriptionOrder;
use App\Models\User;
use App\SubscriptionBilling;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubscriptionStorageAccumulationTest extends TestCase
{
    use RefreshDatabase;

    private const HALF_GB_BYTES = 536870912; // 0.5 GB = 512 MB = 536,870,912 bytes

    private const FIVE_GB_BYTES = 5368709120; // 5.0 GB = 5,120 MB = 5,368,709,120 bytes

    private const FIVE_HALF_GB_BYTES = 5905580032; // 5.5 GB = 5,632 MB = 5,905,580,032 bytes

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PackageSeeder::class);
        $this->configureDoku();
    }

    /**
     * 1. Storage 0,5 GB ditambah pembelian 5 GB menghasilkan 5,5 GB.
     */
    public function test_storage_accumulates_0_5_gb_plus_5_gb_equals_5_5_gb(): void
    {
        $photographer = $this->createPhotographerWithInitialStorage(self::HALF_GB_BYTES);
        $this->assertSame(self::HALF_GB_BYTES, $photographer->effectiveQuotaBytes());
        $this->assertSame('0,5 GB', $photographer->formattedQuota());

        $proPackage = $this->createOrGetPackageWithStorage('pro', 'Paket Pro', 5120, self::FIVE_GB_BYTES);

        $billing = app(SubscriptionBilling::class);
        $order = $billing->createOrder($photographer, $proPackage);
        $order->update(['status' => 'paid', 'provider_paid_at' => now()]);
        $billing->applyPaidOrder($order, now());

        $photographer->refresh();
        $subscription = $photographer->subscription;

        $this->assertSame(self::FIVE_HALF_GB_BYTES, (int) $subscription->entitlement_snapshot['storage_quota_bytes']);
        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->effectiveQuotaBytes());
        $this->assertEquals(5632.0, $photographer->effectiveQuotaMb());
        $this->assertSame('5,5 GB', $photographer->formattedQuota());
    }

    /**
     * 2. Pembayaran gagal atau pending tidak menambah storage.
     */
    public function test_failed_or_pending_payment_does_not_add_storage(): void
    {
        $photographer = $this->createPhotographerWithInitialStorage(self::HALF_GB_BYTES);
        $proPackage = $this->createOrGetPackageWithStorage('pro', 'Paket Pro', 5120, self::FIVE_GB_BYTES);

        $billing = app(SubscriptionBilling::class);
        $order = $billing->createOrder($photographer, $proPackage);

        // State pending
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(self::HALF_GB_BYTES, $photographer->fresh()->effectiveQuotaBytes());
        $this->assertSame('0,5 GB', $photographer->fresh()->formattedQuota());

        // Webhook notification gagal / dibatalkan
        $payload = $this->payload($order, '05', now()->toIso8601String()); // 05 = failed/cancelled
        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(self::HALF_GB_BYTES, $photographer->fresh()->effectiveQuotaBytes());
        $this->assertSame('0,5 GB', $photographer->fresh()->formattedQuota());
    }

    /**
     * 3. Webhook sukses yang sama diproses ulang tidak menggandakan storage (idempotency).
     */
    public function test_duplicate_successful_webhook_does_not_multiply_storage(): void
    {
        $photographer = $this->createPhotographerWithInitialStorage(self::HALF_GB_BYTES);
        $proPackage = $this->createOrGetPackageWithStorage('pro', 'Paket Pro', 5120, self::FIVE_GB_BYTES);

        $billing = app(SubscriptionBilling::class);
        $order = $billing->createOrder($photographer, $proPackage);
        $payload = $this->payload($order, '00', now()->toIso8601String());

        // First webhook invocation
        $firstResponse = $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload));
        $firstResponse->assertOk();
        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->fresh()->effectiveQuotaBytes());

        // Duplicate webhook invocation with identical payload
        $secondResponse = $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload));
        $secondResponse->assertOk();
        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->fresh()->effectiveQuotaBytes());

        // Duplicate webhook invocation with new notification ID on already-paid order
        $duplicatePayload = $payload;
        $duplicatePayload['notification_id'] = 'new-notification-id';
        $thirdResponse = $this->postJson(route('payments.doku.notification'), $duplicatePayload, $this->headers($duplicatePayload));
        $thirdResponse->assertOk();

        // Storage MUST remain exactly 5,5 GB
        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->fresh()->effectiveQuotaBytes());
        $this->assertSame('5,5 GB', $photographer->fresh()->formattedQuota());
    }

    /**
     * 4. Dua proses konfirmasi bersamaan tidak memberikan storage dua kali (concurrency).
     */
    public function test_concurrent_confirmation_does_not_grant_duplicate_storage(): void
    {
        $photographer = $this->createPhotographerWithInitialStorage(self::HALF_GB_BYTES);
        $proPackage = $this->createOrGetPackageWithStorage('pro', 'Paket Pro', 5120, self::FIVE_GB_BYTES);

        $billing = app(SubscriptionBilling::class);
        $order = $billing->createOrder($photographer, $proPackage);
        $order->update(['status' => 'paid', 'provider_paid_at' => now()]);

        // First application
        $billing->applyPaidOrder($order, now());
        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->fresh()->effectiveQuotaBytes());

        // Second simultaneous / concurrent attempt with the same order
        $billing->applyPaidOrder($order, now());
        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->fresh()->effectiveQuotaBytes());
        $this->assertSame('5,5 GB', $photographer->fresh()->formattedQuota());
    }

    /**
     * 5. Perubahan konfigurasi paket oleh superadmin tidak mengubah snapshot transaksi yang sudah dibuat.
     */
    public function test_superadmin_package_reconfiguration_does_not_alter_order_snapshot_or_past_quota(): void
    {
        $photographer = $this->createPhotographerWithInitialStorage(self::HALF_GB_BYTES);
        $proPackage = $this->createOrGetPackageWithStorage('pro', 'Paket Pro', 5120, self::FIVE_GB_BYTES);

        // Photographer creates order when Pro is 5 GB
        $billing = app(SubscriptionBilling::class);
        $order = $billing->createOrder($photographer, $proPackage);
        $this->assertSame(self::FIVE_GB_BYTES, (int) $order->package_snapshot['storage_quota_bytes']);

        // Superadmin alters Paket Pro in catalog to 0.5 GB
        $proPackage->update([
            'kuota_storage_mb' => 512,
            'storage_quota_bytes' => self::HALF_GB_BYTES,
            'revision' => $proPackage->revision + 1,
        ]);

        // Historical order snapshot MUST NOT change
        $this->assertSame(self::FIVE_GB_BYTES, (int) $order->fresh()->package_snapshot['storage_quota_bytes']);

        // Payment completes
        $payload = $this->payload($order, '00', now()->toIso8601String());
        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();

        // Total quota must reflect the snapshot from the purchase (0.5 GB initial + 5.0 GB purchased = 5.5 GB)
        $photographer->refresh();
        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->effectiveQuotaBytes());
        $this->assertSame('5,5 GB', $photographer->formattedQuota());
    }

    /**
     * 6. Kapasitas terbaru ditampilkan dengan benar pada dashboard.
     */
    public function test_dashboard_displays_authoritative_accumulated_storage_capacity(): void
    {
        $photographer = $this->createPhotographerWithInitialStorage(self::HALF_GB_BYTES);
        $proPackage = $this->createOrGetPackageWithStorage('pro', 'Paket Pro', 5120, self::FIVE_GB_BYTES);

        $billing = app(SubscriptionBilling::class);
        $order = $billing->createOrder($photographer, $proPackage);
        $order->update(['status' => 'paid', 'provider_paid_at' => now()]);
        $billing->applyPaidOrder($order, now());

        $response = $this->actingAs($photographer->fresh())->get(route('fotografer.dashboard'));

        $response->assertOk();
        $response->assertSee('5,5 GB');
        $response->assertSee('data-testid="storage-capacity"', false);

        // Also verify storage details page
        $storageResponse = $this->actingAs($photographer->fresh())->get(route('fotografer.storage'));
        $storageResponse->assertOk();
        $storageResponse->assertSee('5,5 GB');
    }

    /**
     * 7. Pemakaian dan validasi batas upload mengikuti total kapasitas terbaru (5,5 GB).
     */
    public function test_photo_upload_validation_respects_accumulated_5_5_gb_quota(): void
    {
        Storage::fake('local');
        $photographer = $this->createPhotographerWithInitialStorage(self::HALF_GB_BYTES);
        $proPackage = $this->createOrGetPackageWithStorage('pro', 'Paket Pro', 5120, self::FIVE_GB_BYTES);

        $billing = app(SubscriptionBilling::class);
        $order = $billing->createOrder($photographer, $proPackage);
        $order->update(['status' => 'paid', 'provider_paid_at' => now()]);
        $billing->applyPaidOrder($order, now());

        $photographer->refresh();
        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->effectiveQuotaBytes());

        $camera = Camera::factory()->create(['fotografer_id' => $photographer->id]);
        $event = Event::factory()->create(['fotografer_id' => $photographer->id]);

        // Uploading a photo within quota succeeds
        $response = $this->actingAs($photographer)->post(route('fotografer.photos.store'), [
            'event_id' => $event->id,
            'camera_id' => $camera->id,
            'harga' => 20000,
            'photos' => [UploadedFile::fake()->image('test_photo.jpg', 800, 600)->size(5000)], // 5 MB
            'watermark' => UploadedFile::fake()->image('wm.png', 200, 100),
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('photos', 1);

        // Simulate photographer storage being almost full at 5.5 GB
        // Set existing photo storage_bytes to 5.5 GB
        Photo::query()->where('fotografer_id', $photographer->id)->update([
            'storage_bytes' => self::FIVE_HALF_GB_BYTES,
        ]);

        // Attempting to upload when accumulated storage limit is reached must be rejected
        $rejectResponse = $this->actingAs($photographer)->post(route('fotografer.photos.store'), [
            'event_id' => $event->id,
            'camera_id' => $camera->id,
            'harga' => 20000,
            'photos' => [UploadedFile::fake()->image('overflow_photo.jpg', 800, 600)->size(5000)],
            'watermark' => UploadedFile::fake()->image('wm.png', 200, 100),
        ]);
        $rejectResponse->assertSessionHasErrors(['photos' => 'Storage tidak mencukupi.']);
    }

    /**
     * 8. Pembelian paket tidak menghapus storage yang sebelumnya telah diperoleh, termasuk saat expired.
     */
    public function test_expired_subscription_preserves_accumulated_storage_and_subsequent_purchase_accumulates(): void
    {
        $photographer = $this->createPhotographerWithInitialStorage(self::HALF_GB_BYTES);
        $proPackage = $this->createOrGetPackageWithStorage('pro', 'Paket Pro', 5120, self::FIVE_GB_BYTES);

        $billing = app(SubscriptionBilling::class);
        $firstOrder = $billing->createOrder($photographer, $proPackage);
        $firstOrder->update(['status' => 'paid', 'provider_paid_at' => now()]);
        $billing->applyPaidOrder($firstOrder, now());

        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->fresh()->effectiveQuotaBytes());

        // Simulate subscription expiration
        $photographer->subscription->update([
            'status' => 'expired',
            'ends_at' => now()->subDay(),
        ]);

        // Storage MUST NOT be decreased upon expiration
        $this->assertFalse($photographer->fresh()->subscription->isActive());
        $this->assertSame(self::FIVE_HALF_GB_BYTES, $photographer->fresh()->effectiveQuotaBytes());
        $this->assertSame('5,5 GB', $photographer->fresh()->formattedQuota());

        // Subsequent purchase adds another 5 GB
        $secondOrder = $billing->createOrder($photographer->fresh(), $proPackage);
        $secondOrder->update(['status' => 'paid', 'provider_paid_at' => now()]);
        $billing->applyPaidOrder($secondOrder, now());

        $expectedTotalBytes = self::FIVE_HALF_GB_BYTES + self::FIVE_GB_BYTES; // 5.5 GB + 5 GB = 10.5 GB
        $photographer->refresh();
        $this->assertSame($expectedTotalBytes, $photographer->effectiveQuotaBytes());
        $this->assertSame('10,5 GB', $photographer->formattedQuota());
    }

    /**
     * Helper to create a photographer with an initial storage amount in bytes.
     */
    private function createPhotographerWithInitialStorage(int $bytes): User
    {
        $initialPackage = Package::updateOrCreate(
            ['code' => 'initial-pro'],
            [
                'nama_paket' => 'Initial Pro',
                'display_name' => 'Initial Pro',
                'harga' => 49000,
                'currency' => 'IDR',
                'duration_days' => 30,
                'billing_period' => '30 hari',
                'kuota_storage_mb' => (int) round($bytes / 1048576),
                'storage_quota_bytes' => $bytes,
                'is_custom' => false,
                'is_active' => true,
                'is_trial' => false,
                'is_legacy' => false,
                'sort_order' => 15,
                'revision' => 1,
                'bisa_custom_watermark' => true,
                'bisa_broadcast_lokasi' => false,
            ]
        );

        $photographer = User::factory()->create([
            'role' => 'fotografer',
            'is_verified' => true,
            'verified_at' => now(),
            'photographer_onboarded_at' => now(),
            'package_id' => $initialPackage->id,
        ]);

        Subscription::create([
            'user_id' => $photographer->id,
            'package_id' => $initialPackage->id,
            'status' => 'active',
            'entitlement_snapshot' => [
                'code' => $initialPackage->code,
                'name' => $initialPackage->display_name,
                'storage_quota_bytes' => $bytes,
                'kuota_storage_mb' => (int) round($bytes / 1048576),
                'duration_days' => 30,
            ],
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(25),
            'last_payment_at' => now()->subDays(5),
            'last_order_id' => 'INIT-'.Str::random(8),
            'version' => 1,
            'is_legacy_transition' => false,
        ]);

        return $photographer->fresh()->load('subscription');
    }

    private function createOrGetPackageWithStorage(string $code, string $name, int $mb, int $bytes): Package
    {
        return Package::updateOrCreate(
            ['code' => $code],
            [
                'nama_paket' => $name,
                'display_name' => $name,
                'harga' => 99000,
                'currency' => 'IDR',
                'duration_days' => 30,
                'billing_period' => '30 hari',
                'kuota_storage_mb' => $mb,
                'storage_quota_bytes' => $bytes,
                'is_custom' => false,
                'is_active' => true,
                'is_trial' => false,
                'is_legacy' => false,
                'sort_order' => 25,
                'revision' => 1,
                'bisa_custom_watermark' => true,
                'bisa_broadcast_lokasi' => true,
            ]
        );
    }

    private function configureDoku(): void
    {
        config(['doku.client_id' => 'test-client', 'doku.client_secret' => 'test-server-key']);
    }

    /** @return array<string, mixed> */
    private function payload(SubscriptionOrder $order, string $status, string $settlementTime): array
    {
        $order->update(['provider_external_id' => '987654321', 'provider_transaction_id' => 'doku-'.$order->id]);

        return [
            'originalPartnerReferenceNo' => $order->order_id,
            'originalReferenceNo' => 'doku-'.$order->id,
            'originalExternalId' => '987654321',
            'latestTransactionStatus' => $status,
            'transactionStatusDesc' => 'Test status',
            'amount' => ['value' => number_format($order->gross_amount, 2, '.', ''), 'currency' => 'IDR'],
            'paidTime' => $settlementTime,
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, string>
     */
    private function headers(array $payload): array
    {
        $timestamp = now()->format('Y-m-d\TH:i:sP');
        $token = 'notification-token';
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $signature = base64_encode(hash_hmac('sha512', 'POST:/payments/doku/notification:'.$token.':'.hash('sha256', $body).':'.$timestamp, 'test-server-key', true));

        return [
            'X-PARTNER-ID' => 'test-client',
            'X-EXTERNAL-ID' => 'notification-'.Str::random(8),
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'Authorization' => 'Bearer '.$token,
        ];
    }
}

