<?php

namespace Tests\Feature;

use App\DokuPaymentGateway;
use App\Models\PaymentEvent;
use App\Models\Photo;
use App\Models\PhotoOrder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class PhotoOrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_webhook_pays_all_photographers_once_and_unlocks_downloads(): void
    {
        $this->configureDoku();
        [$order, $firstPhotographer, $secondPhotographer] = $this->pendingOrder();
        $payload = $this->payload($order, '00', 'PHOTO-TXN-1');

        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();
        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->processed_at);
        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(27000, (int) $secondPhotographer->fresh()->saldo);
        $this->assertSame(2, Transaction::where('payment_status', 'paid')->count());
        $this->assertSame(1, PaymentEvent::where('photo_order_id', $order->id)->count());
    }

    public function test_valid_qris_notification_fulfills_once_when_doku_repeats_the_callback(): void
    {
        $this->configureDoku();
        [$order, $firstPhotographer, $secondPhotographer] = $this->pendingOrder();
        $payload = $this->qrisPayload($order, 'SUCCESS');
        $headers = $this->qrisHeaders($payload, 'qris-notification-1');

        $this->postJson(route('payments.doku.notification'), $payload, $headers)->assertOk();
        $this->postJson(route('payments.doku.notification'), $payload, $headers)->assertOk();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(27000, (int) $secondPhotographer->fresh()->saldo);
        $this->assertSame(1, PaymentEvent::where('photo_order_id', $order->id)->count());
    }

    public function test_qris_notification_rejects_an_invalid_signature_and_records_a_valid_failure(): void
    {
        $this->configureDoku();
        [$order, $firstPhotographer] = $this->pendingOrder();
        $success = $this->qrisPayload($order, 'SUCCESS');
        $invalidHeaders = $this->qrisHeaders($success, 'qris-notification-invalid');
        $invalidHeaders['Signature'] = 'HMACSHA256=invalid';

        $this->postJson(route('payments.doku.notification'), $success, $invalidHeaders)->assertUnauthorized();
        $this->assertSame('pending', $order->fresh()->status);

        $wrongAmount = $this->qrisPayload($order, 'SUCCESS');
        $wrongAmount['order']['amount'] = 1;
        $this->postJson(route('payments.doku.notification'), $wrongAmount, $this->qrisHeaders($wrongAmount, 'qris-notification-amount'))->assertBadRequest();
        $this->assertSame('pending', $order->fresh()->status);

        $failed = $this->qrisPayload($order, 'FAILED');
        $this->postJson(route('payments.doku.notification'), $failed, $this->qrisHeaders($failed, 'qris-notification-failed'))->assertOk();

        $this->assertSame('failed', $order->fresh()->status);
        $this->assertSame(0, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(0, Transaction::where('payment_status', 'paid')->count());
    }

    public function test_invalid_signature_and_amount_do_not_pay_or_credit_photographers(): void
    {
        $this->configureDoku();
        [$order, $firstPhotographer] = $this->pendingOrder();
        $invalidSignature = $this->payload($order, '00', 'PHOTO-TXN-BAD-SIGNATURE');

        $headers = $this->headers($invalidSignature);
        $headers['X-SIGNATURE'] = 'invalid';
        $this->postJson(route('payments.doku.notification'), $invalidSignature, $headers)->assertUnauthorized();

        $invalidAmount = $this->payload($order, '00', 'PHOTO-TXN-BAD-AMOUNT');
        $invalidAmount['amount']['value'] = '1.00';
        $this->postJson(route('payments.doku.notification'), $invalidAmount, $this->headers($invalidAmount))->assertBadRequest();

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(0, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(0, Transaction::where('payment_status', 'paid')->count());
    }

    public function test_webhook_rejects_unknown_reference_and_missing_fields(): void
    {
        $this->configureDoku();
        [$order] = $this->pendingOrder();
        $unknownReference = $this->payload($order, '00', 'PHOTO-TXN-EXPECTED');
        $unknownReference['originalReferenceNo'] = 'PHOTO-TXN-UNKNOWN';

        $this->postJson(route('payments.doku.notification'), $unknownReference, $this->headers($unknownReference))->assertNotFound();

        $missingAmount = $this->payload($order, '00', 'PHOTO-TXN-EXPECTED');
        unset($missingAmount['amount']);
        $this->postJson(route('payments.doku.notification'), $missingAmount, $this->headers($missingAmount))->assertBadRequest();

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_late_non_paid_webhook_cannot_overwrite_paid_photo_order(): void
    {
        $this->configureDoku();
        [$order, $firstPhotographer] = $this->pendingOrder();

        $paid = $this->payload($order, '00', 'PHOTO-TXN-LATE');
        $cancelled = $this->payload($order, '05', 'PHOTO-TXN-LATE');
        $this->postJson(route('payments.doku.notification'), $paid, $this->headers($paid))->assertOk();
        $this->postJson(route('payments.doku.notification'), $cancelled, $this->headers($cancelled))->assertOk();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
    }

    public function test_expiry_scheduler_cannot_revert_a_paid_order(): void
    {
        $this->configureDoku();
        [$order, $firstPhotographer] = $this->pendingOrder();
        $payload = $this->payload($order, '00', 'PHOTO-TXN-SCHEDULER');
        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();
        $order->update(['expires_at' => now()->subMinute()]);

        $this->artisan('payments:expire')->assertSuccessful();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(1, PaymentEvent::where('photo_order_id', $order->id)->count());
    }

    public function test_verified_success_fulfills_an_order_that_expired_locally(): void
    {
        $this->configureDoku();
        [$order, $firstPhotographer] = $this->pendingOrder();
        $order->update(['expires_at' => now()->subMinute()]);
        $this->artisan('payments:expire')->assertSuccessful();
        $payload = $this->payload($order, '00', 'PHOTO-TXN-EXPIRED');

        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(2, Transaction::where('payment_status', 'paid')->count());
    }

    public function test_provider_timestamp_rounded_to_the_order_creation_second_is_accepted(): void
    {
        $this->configureDoku();
        [$order] = $this->pendingOrder();
        $order->forceFill(['created_at' => now()->setMicrosecond(900000)])->saveQuietly();
        $order->refresh();
        $payload = $this->payload($order, '00', 'PHOTO-TXN-ROUNDED-TIME');
        $payload['paidTime'] = $order->created_at->copy()->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i:sP');

        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();

        $this->assertSame('paid', $order->fresh()->status);
    }

    public function test_buyer_cannot_read_another_buyers_photo_order_status(): void
    {
        [$order] = $this->pendingOrder();
        $otherBuyer = User::factory()->pembeli()->create();

        $this->actingAs($otherBuyer)->getJson(route('checkout.payment.status', $order->order_id))->assertNotFound();
    }

    public function test_payment_page_polling_redirects_on_success_and_backs_off_on_connection_errors(): void
    {
        [$order] = $this->pendingOrder();
        $order->update(['qr_content' => '000201010212TESTQRIS']);

        $this->actingAs($order->user)
            ->get(route('checkout.payment', $order->order_id))
            ->assertOk()
            ->assertSee('window.location.assign(successUrl)', false)
            ->assertSee('Math.min(retryDelay * 2, 30000)', false)
            ->assertSee("['expired', 'failed', 'cancelled']", false);
    }

    public function test_status_poll_reconciles_with_doku_without_duplicate_fulfillment(): void
    {
        [$order, $firstPhotographer, $secondPhotographer] = $this->pendingOrder();
        $order->update(['provider_transaction_id' => 'PHOTO-TXN-QUERY']);
        $this->mock(DokuPaymentGateway::class, function (MockInterface $mock) use ($order): void {
            $mock->shouldReceive('queryPhotoOrder')->once()->andReturn([
                'responseCode' => '2005100',
                'originalReferenceNo' => 'PHOTO-TXN-QUERY',
                'originalPartnerReferenceNo' => $order->order_id,
                'serviceCode' => '47',
                'latestTransactionStatus' => '00',
                'paidTime' => now()->toIso8601String(),
                'amount' => ['value' => '50000.00', 'currency' => 'IDR'],
            ]);
        });
        $buyer = $order->user;

        $this->actingAs($buyer)->getJson(route('checkout.payment.status', $order->order_id))->assertOk()->assertJson(['status' => 'paid']);
        $this->actingAs($buyer)->getJson(route('checkout.payment.status', $order->order_id))->assertOk()->assertJson(['status' => 'paid']);

        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(27000, (int) $secondPhotographer->fresh()->saldo);
        $this->assertSame(1, PaymentEvent::where('photo_order_id', $order->id)->count());
    }

    public function test_reconcile_command_dry_run_does_not_fulfill_then_reconciles_expired_success(): void
    {
        Storage::fake('local');
        [$order, $firstPhotographer, $secondPhotographer] = $this->pendingOrder();
        $order->update([
            'provider_transaction_id' => 'PHOTO-TXN-COMMAND',
            'status' => 'expired',
            'expires_at' => now()->subHour(),
        ]);
        $order->transactions()->update(['status' => 'expired', 'payment_status' => 'expired']);
        $response = [
            'responseCode' => '2005100',
            'originalReferenceNo' => 'PHOTO-TXN-COMMAND',
            'originalPartnerReferenceNo' => $order->order_id,
            'serviceCode' => '47',
            'latestTransactionStatus' => '00',
            'paidTime' => now()->toIso8601String(),
            'amount' => ['value' => '50000.00', 'currency' => 'IDR'],
        ];
        $this->mock(DokuPaymentGateway::class, function (MockInterface $mock) use ($response): void {
            $mock->shouldReceive('queryPhotoOrder')->twice()->andReturn($response);
        });

        $this->artisan('payments:reconcile', ['invoice' => $order->order_id, '--dry-run' => true])
            ->expectsOutputToContain('No data changed')
            ->assertSuccessful();
        $this->assertSame('expired', $order->fresh()->status);

        $this->artisan('payments:reconcile', ['invoice' => $order->order_id])
            ->expectsOutputToContain('paid')
            ->assertSuccessful();

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(27000, (int) $secondPhotographer->fresh()->saldo);
        $this->assertSame(1, PaymentEvent::where('photo_order_id', $order->id)->count());

        $firstTransaction = $order->transactions()->orderBy('id')->firstOrFail();
        $this->assertSame(20000, (int) $firstTransaction->total_bayar);
        $this->assertSame(18000, (int) $firstTransaction->photographer_amount);
        $this->assertSame(2000, (int) $firstTransaction->platform_amount);
        $firstTransaction->photo->update([
            'purchased_path' => 'photos/purchased/reconciled-photo.jpg',
            'original_filename' => 'reconciled-photo.jpg',
        ]);
        Storage::disk('local')->put($firstTransaction->photo->purchased_path, 'purchased-photo');
        $this->actingAs($order->user)
            ->get(route('purchases.download', ['order' => $order->order_id, 'transaction' => $firstTransaction]))
            ->assertDownload($firstTransaction->photo->original_filename);

        $this->artisan('payments:reconcile', ['invoice' => $order->order_id])->assertSuccessful();
        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(1, PaymentEvent::where('photo_order_id', $order->id)->count());
    }

    public function test_expiry_command_expires_pending_order_and_transactions(): void
    {
        [$order] = $this->pendingOrder();
        $order->update(['expires_at' => now()->subMinute()]);

        $this->artisan('payments:expire')->assertSuccessful();

        $this->assertSame('expired', $order->fresh()->status);
        $this->assertSame(2, Transaction::where('payment_status', 'expired')->count());
    }

    /** @return array{PhotoOrder, User, User} */
    private function pendingOrder(): array
    {
        $buyer = User::factory()->pembeli()->create();
        $firstPhotographer = User::factory()->fotografer()->create(['saldo' => 0]);
        $secondPhotographer = User::factory()->fotografer()->create(['saldo' => 0]);
        $firstPhoto = Photo::factory()->for($firstPhotographer, 'fotografer')->create(['harga' => 20000]);
        $secondPhoto = Photo::factory()->for($secondPhotographer, 'fotografer')->create(['harga' => 30000]);
        $order = PhotoOrder::factory()->create([
            'user_id' => $buyer->id,
            'gross_amount' => 50000,
            'provider_external_id' => '123456789',
        ]);

        Transaction::factory()->create([
            'photo_order_id' => $order->id,
            'order_number' => $order->order_id,
            'pembeli_id' => $buyer->id,
            'photo_id' => $firstPhoto->id,
            'fotografer_id' => $firstPhotographer->id,
            'harga_foto' => 20000,
            'total_bayar' => 20000,
            'photographer_amount' => 18000,
            'platform_amount' => 2000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
        Transaction::factory()->create([
            'photo_order_id' => $order->id,
            'order_number' => $order->order_id,
            'pembeli_id' => $buyer->id,
            'photo_id' => $secondPhoto->id,
            'fotografer_id' => $secondPhotographer->id,
            'harga_foto' => 30000,
            'total_bayar' => 30000,
            'photographer_amount' => 27000,
            'platform_amount' => 3000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return [$order, $firstPhotographer, $secondPhotographer];
    }

    /** @return array<string, mixed> */
    private function payload(PhotoOrder $order, string $status, string $transactionId): array
    {
        $order->update(['provider_transaction_id' => $transactionId]);

        return [
            'originalPartnerReferenceNo' => $order->order_id,
            'originalReferenceNo' => $transactionId,
            'originalExternalId' => '123456789',
            'latestTransactionStatus' => $status,
            'transactionStatusDesc' => 'Test status',
            'amount' => ['value' => number_format($order->gross_amount, 2, '.', ''), 'currency' => 'IDR'],
            'paidTime' => now()->toIso8601String(),
        ];
    }

    private function configureDoku(): void
    {
        config(['doku.client_id' => 'test-client', 'doku.client_secret' => 'photo-test-key']);
    }

    /** @return array<string, mixed> */
    private function qrisPayload(PhotoOrder $order, string $status): array
    {
        return [
            'service' => ['id' => 'QRIS', 'name' => 'QRIS'],
            'acquirer' => ['id' => 'DOKU', 'name' => 'DOKU'],
            'channel' => ['id' => 'QRIS_DOKU', 'name' => 'QRIS-DOKU'],
            'customer' => ['doku_id' => 'customer-1', 'name' => 'Buyer', 'email' => 'buyer@example.com', 'phone' => '628123456789'],
            'order' => ['invoice_number' => $order->order_id, 'amount' => (float) $order->gross_amount],
            'emoney_payment' => ['account_id' => 'customer-1', 'approval_code' => 'approval-1'],
            'transaction' => ['status' => $status, 'date' => now()->utc()->format('Y-m-d\TH:i:s\Z')],
            'additional_info' => [
                'postalCode' => '70123',
                'feeType' => '1',
                'origin' => ['product' => 'QRIS', 'system' => 'snap-adapter', 'apiFormat' => 'SNAP', 'source' => 'DIRECT'],
            ],
        ];
    }

    /** @param array<string, mixed> $payload
     * @return array<string, string>
     */
    private function qrisHeaders(array $payload, string $requestId): array
    {
        $timestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
        $body = json_encode($payload, JSON_THROW_ON_ERROR);
        $digest = base64_encode(hash('sha256', $body, true));
        $component = implode("\n", [
            'Client-Id:test-client',
            'Request-Id:'.$requestId,
            'Request-Timestamp:'.$timestamp,
            'Request-Target:/payments/doku/notification',
            'Digest:'.$digest,
        ]);
        $signature = 'HMACSHA256='.base64_encode(hash_hmac('sha256', $component, 'photo-test-key', true));

        return [
            'Client-Id' => 'test-client',
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
        ];
    }

    /** @param array<string, mixed> $payload
     * @return array<string, string>
     */
    private function headers(array $payload): array
    {
        $timestamp = now()->format('Y-m-d\TH:i:sP');
        $token = 'notification-token';
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $signature = base64_encode(hash_hmac('sha512', 'POST:/payments/doku/notification:'.$token.':'.hash('sha256', $body).':'.$timestamp, 'photo-test-key', true));

        return ['X-PARTNER-ID' => 'test-client', 'X-EXTERNAL-ID' => 'notification-123', 'X-TIMESTAMP' => $timestamp, 'X-SIGNATURE' => $signature, 'Authorization' => 'Bearer '.$token];
    }
}
