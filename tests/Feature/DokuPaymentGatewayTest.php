<?php

namespace Tests\Feature;

use App\DokuPaymentGateway;
use App\Models\PhotoOrder;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DokuPaymentGatewayTest extends TestCase
{
    public function test_generates_a_unique_dynamic_qris_for_the_order(): void
    {
        config([
            'doku.environment' => 'sandbox',
            'doku.client_id' => 'test-client',
            'doku.client_secret' => 'test-secret',
            'doku.merchant_id' => '12345',
            'doku.terminal_id' => 'A01',
            'doku.postal_code' => '12190',
            'doku.fee_type' => 1,
        ]);
        Cache::put('doku.b2b_access_token', 'test-token', now()->addMinute());
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.doku.com/snap-adapter/b2b/v1.0/qr/qr-mpm-generate' => Http::response([
                'responseCode' => '2004700',
                'referenceNo' => 'DOKU-QRIS-1',
                'partnerReferenceNo' => 'JPR-PHO-TEST',
                'qrContent' => '000201010212TESTQRIS',
            ]),
        ]);
        $order = new PhotoOrder([
            'order_id' => 'JPR-PHO-TEST',
            'gross_amount' => 25000,
            'currency' => 'IDR',
            'expires_at' => now()->addHour(),
        ]);

        $payment = app(DokuPaymentGateway::class)->createPhotoOrderPayment($order);

        $this->assertSame('DOKU-QRIS-1', $payment['reference']);
        $this->assertSame('000201010212TESTQRIS', $payment['qr_content']);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-sandbox.doku.com/snap-adapter/b2b/v1.0/qr/qr-mpm-generate'
            && $request['partnerReferenceNo'] === 'JPR-PHO-TEST'
            && $request['amount'] === ['value' => '25000.00', 'currency' => 'IDR']
            && $request->hasHeader('X-SIGNATURE'));
    }
}
