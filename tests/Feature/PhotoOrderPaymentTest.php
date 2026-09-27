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
        config(['midtrans.server_key' => 'photo-test-key']);
        [$order, $firstPhotographer, $secondPhotographer] = $this->pendingOrder();
        $payload = $this->payload($order, 'settlement', 'PHOTO-TXN-1');

        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk()->assertJson(['result' => 'paid']);
        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk()->assertJson(['result' => 'duplicate']);

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->processed_at);
        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(27000, (int) $secondPhotographer->fresh()->saldo);
        $this->assertSame(2, Transaction::where('payment_status', 'paid')->count());
        $this->assertSame(1, PaymentEvent::where('photo_order_id', $order->id)->count());
    }

    public function test_invalid_signature_and_amount_do_not_pay_or_credit_photographers(): void
    {
        config(['midtrans.server_key' => 'photo-test-key']);
        [$order, $firstPhotographer] = $this->pendingOrder();
        $invalidSignature = $this->payload($order, 'settlement', 'PHOTO-TXN-BAD-SIGNATURE');
        $invalidSignature['signature_key'] = 'invalid';

        $this->postJson(route('payments.midtrans.notification'), $invalidSignature)->assertUnprocessable()->assertJson(['result' => 'invalid_signature']);

        $invalidAmount = $this->payload($order, 'settlement', 'PHOTO-TXN-BAD-AMOUNT');
        $invalidAmount['gross_amount'] = '1.00';
        $invalidAmount['signature_key'] = $this->signature($invalidAmount);
        $this->postJson(route('payments.midtrans.notification'), $invalidAmount)->assertUnprocessable()->assertJson(['result' => 'invalid_amount']);

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(0, (int) $firstPhotographer->fresh()->saldo);
        $this->assertSame(0, Transaction::where('payment_status', 'paid')->count());
    }

    public function test_late_non_paid_webhook_cannot_overwrite_paid_photo_order(): void
    {
        config(['midtrans.server_key' => 'photo-test-key']);
        [$order, $firstPhotographer] = $this->pendingOrder();

        $this->postJson(route('payments.midtrans.notification'), $this->payload($order, 'settlement', 'PHOTO-TXN-LATE'))->assertOk();
        $this->postJson(route('payments.midtrans.notification'), $this->payload($order, 'expire', 'PHOTO-TXN-LATE'))->assertOk()->assertJson(['result' => 'ignored_paid']);

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame(18000, (int) $firstPhotographer->fresh()->saldo);
    }

    public function test_buyer_cannot_read_another_buyers_photo_order_status(): void
    {
        [$order] = $this->pendingOrder();
        $otherBuyer = User::factory()->pembeli()->create();

        $this->actingAs($otherBuyer)->getJson(route('checkout.payment.status', $order->order_id))->assertNotFound();
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

    /** @return array<string, string> */
    private function payload(PhotoOrder $order, string $status, string $transactionId): array
    {
        $payload = [
            'order_id' => $order->order_id,
            'status_code' => '200',
            'gross_amount' => number_format($order->gross_amount, 2, '.', ''),
            'transaction_status' => $status,
            'transaction_id' => $transactionId,
            'payment_type' => 'qris',
            'fraud_status' => 'accept',
            'transaction_time' => '2026-09-26 12:00:00',
        ];
        $payload['signature_key'] = $this->signature($payload);

        return $payload;
    }

    /** @param array<string, string> $payload */
    private function signature(array $payload): string
    {
        return hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key'));
    }
}
