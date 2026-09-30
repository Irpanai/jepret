<?php

namespace Tests\Feature;

use App\Models\PaymentEvent;
use App\Models\Photo;
use App\Models\PhotoOrder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_paid_notification_cannot_activate_an_expired_qris_order(): void
    {
        $this->configureDoku();
        [$order, $firstPhotographer] = $this->pendingOrder();
        $order->update(['expires_at' => now()->subMinute()]);
        $payload = $this->payload($order, '00', 'PHOTO-TXN-EXPIRED');

        $this->postJson(route('payments.doku.notification'), $payload, $this->headers($payload))->assertOk();

        $this->assertSame('expired', $order->fresh()->status);
        $this->assertSame(0, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(0, Transaction::where('payment_status', 'paid')->count());
    }

    public function test_buyer_cannot_read_another_buyers_photo_order_status(): void
    {
        [$order] = $this->pendingOrder();
        $otherBuyer = User::factory()->pembeli()->create();

        $this->actingAs($otherBuyer)->getJson(route('checkout.payment.status', $order->order_id))->assertNotFound();
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
        return [
            'originalPartnerReferenceNo' => $order->order_id,
            'originalReferenceNo' => $transactionId,
            'originalExternalId' => '123456789',
            'latestTransactionStatus' => $status,
            'transactionStatusDesc' => 'Test status',
            'amount' => ['value' => number_format($order->gross_amount, 2, '.', ''), 'currency' => 'IDR'],
        ];
    }

    private function configureDoku(): void
    {
        config(['doku.client_id' => 'test-client', 'doku.client_secret' => 'photo-test-key']);
    }

    /** @param array<string, mixed> $payload
     * @return array<string, string>
     */
    private function headers(array $payload): array
    {
        $timestamp = '2026-09-26T12:00:00+07:00';
        $token = 'notification-token';
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $signature = base64_encode(hash_hmac('sha512', 'POST:/payments/doku/notification:'.$token.':'.hash('sha256', $body).':'.$timestamp, 'photo-test-key', true));

        return ['X-PARTNER-ID' => 'test-client', 'X-EXTERNAL-ID' => 'notification-123', 'X-TIMESTAMP' => $timestamp, 'X-SIGNATURE' => $signature, 'Authorization' => 'Bearer '.$token];
    }
}
