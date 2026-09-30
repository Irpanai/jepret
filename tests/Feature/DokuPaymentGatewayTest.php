<?php

namespace Tests\Feature;

use App\DokuPaymentGateway;
use App\Models\PhotoOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class DokuPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const PRIVATE_KEY = <<<'PEM'
-----BEGIN PRIVATE KEY-----
MIIEwAIBADANBgkqhkiG9w0BAQEFAASCBKowggSmAgEAAoIBAQDQEFlc9hpV1Q+9
iKCHOJ5KQEi5mHUbwqQOnGyXSKQZyMpwICGzxqrjQE0XXRshGehYpQhY/HATArEg
F2EHZT4NhH/pYA4Hb174buIhP004cCf1jO/LrxKKuRnIH1+ekKWeGxQaPG0h0Xwj
xNCim9csuZlSDCj2IW+cGk5j8+bGHHrJ5lLbIF6gl+He/wT0y3zklyNWHG0TNKIz
c03RJw2bwN9di0pWcmYYVEmVNW9cFZcThFtVm8wKLfmrJqgtz+kTgb+UIO1X8K+S
s5nErqjLSiVVNPozlBtvXuzfBxHx78MqgwQh8ITXwL1yQj4l/zA5NvXJZNFc7fg2
t4szNBfpAgMBAAECggEBAMCmgrgKv/O9piduvIS4Lgi+YRhITBb6MIG+4OVrQ1cE
jj0n40zcpRpqTXdWpGoP3Q1HMeWDSBqyIWN/gj4gxkYp624hnJvRyXPq58K1JEEf
yEAa0WYdouOD5JViR11AvbkZhZGNZdcsau+Lq0sFVUV6TLalv7+20esOiIsQKRxB
Uwu6Da6fxrrXl7srs/kknWMthEe6WvoodCZa7rbQc4t+VmzCUdyWCtQ23W9SC3MS
6Kb1NeeXwSwVq0NWYQzatl/0fUVXCRmN3Adl7clW/4CBpF6kqJ9WDc6oPMU3e6ZA
Qq7HAbuUYYvcn0+MFFRUTOvzMQ8dLRhirdZMgQ8dXykCgYEA66ifIcgXglglxN2T
En8ifNDugB4BE6Ya9VyyN76216KVc48hom1yzFcaGj11PrHsrRvtSUjCRbFpyp9q
d3dvvTQdD+SF4XGkLN2tY6Tic+yScAYBz2sC1/ArIxNtxI61JKQ7jVsT9G63F7Gr
dcB2R6ujv3l5+PK/L2HnKqUqm0sCgYEA4gX2JO4W4t65MjscWh5HmK1PFcKsCjAo
vjvDN2R6nNe5YZV9ncGP81nLTT6GaEeM0pq+yCbdHaw1fiR5pITzQYDdetFYe1SJ
yxp3txOV3a4A5jP8HbJxD8W/WIIdv9eqdjs/FV09ytJT4ADrJAZ+Mi92p9hMiHSC
2HyvAFx/xRsCgYEAyMsvdBhKt7wlyl1FcHA1UYawgSePoU9aDxCBpe/xWUDl+MXP
UdfSBfx9eDg/i0ENOd7eyx+csMdfLc+xZsO0yND7pkwAKzyqN7RWhd27Oi0sBRmJ
N1Ol45p3Fvb6A43ZGLR7LZGaKh6gESdIwhdQcPb1mjOGUzF654OS9YowCIsCgYEA
lnh9i8xttA+un0A2+c/avkvysHvvaMDy/uJubjwYXL5JDiwlepbYLJwE+qG7fUTU
/YhDpqAo6I4y9o115g8UmvEdgZxJGaIIMgym0lzZksE6nAbTuzmGh4eQgW3uPD9p
nliHAMQYVSR87k3uPQeNj/+FMOyQ1u8qBNHM96Kc9S0CgYEAwdaAQEoA+8uQshQv
W+4A9UuhEoYJHdz1ZC3+b1ldkOWlfc6W8a115LDlJtXwRhXhJriju6ZaqXq4sutG
FaGal1PitWgdKJkwIZ/l+t7/stje7TNho2i/SJm/C/Rg89sPhtzqIwvzqg7Y5RWQ
gEqQJujSAU5F4pcptmtTXhh3U6U=
-----END PRIVATE KEY-----
PEM;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->configureDoku();
    }

    public function test_generates_signed_dynamic_qris_with_authoritative_order_amount(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.doku.com/authorization/v1/access-token/b2b' => Http::response(['accessToken' => 'test-token', 'expiresIn' => 900]),
            'https://api-sandbox.doku.com/snap-adapter/b2b/v1.0/qr/qr-mpm-generate' => Http::response($this->generateResponse('JPR-PHO-TEST', 'DOKU-QRIS-1')),
        ]);

        $payment = app(DokuPaymentGateway::class)->createPhotoOrderPayment($this->order());

        $this->assertSame('DOKU-QRIS-1', $payment['reference']);
        $this->assertSame('000201010212TESTQRIS', $payment['qr_content']);
        $this->assertMatchesRegularExpression('/^\d+$/', $payment['external_id']);
        Http::assertSent(function (Request $request): bool {
            if (! str_ends_with($request->url(), '/qr/qr-mpm-generate')) {
                return false;
            }

            $body = $request->body();
            $timestamp = $request->header('X-TIMESTAMP')[0];
            $expected = base64_encode(hash_hmac(
                'sha512',
                'POST:/snap-adapter/b2b/v1.0/qr/qr-mpm-generate:test-token:'.hash('sha256', $body).':'.$timestamp,
                'test-secret',
                true,
            ));

            return $request['amount'] === ['value' => '25000.00', 'currency' => 'IDR']
                && $request['additionalInfo']['feeType'] === '1'
                && $request->header('CHANNEL-ID')[0] === 'H2H'
                && $request->header('X-SIGNATURE')[0] === $expected;
        });
    }

    public function test_uses_production_url_and_caches_token_until_provider_expiry(): void
    {
        config(['doku.environment' => 'production']);
        Http::preventStrayRequests();
        Http::fake([
            'https://api.doku.com/authorization/v1/access-token/b2b' => Http::response(['accessToken' => 'production-token', 'expiresIn' => 120]),
            'https://api.doku.com/snap-adapter/b2b/v1.0/qr/qr-mpm-generate' => Http::sequence()
                ->push($this->generateResponse('JPR-PHO-TEST', 'DOKU-1'))
                ->push($this->generateResponse('JPR-PHO-SECOND', 'DOKU-2')),
        ]);

        $gateway = app(DokuPaymentGateway::class);
        $gateway->createPhotoOrderPayment($this->order());
        $gateway->createPhotoOrderPayment($this->order('JPR-PHO-SECOND'));

        Http::assertSentCount(3);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.doku.com/authorization/v1/access-token/b2b'
            && $request->hasHeader('X-CLIENT-KEY', 'test-client')
            && $request->hasHeader('X-SIGNATURE'));
    }

    public function test_queries_qris_with_service_code_and_original_references(): void
    {
        $this->cacheToken();
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.doku.com/snap-adapter/b2b/v1.0/qr/qr-mpm-query' => Http::response([
                'responseCode' => '2005100',
                'originalReferenceNo' => 'DOKU-QRIS-1',
                'originalPartnerReferenceNo' => 'JPR-PHO-TEST',
                'serviceCode' => '47',
                'latestTransactionStatus' => '00',
                'paidTime' => now()->toIso8601String(),
                'amount' => ['value' => '25000.00', 'currency' => 'IDR'],
            ]),
        ]);
        $order = $this->order();
        $order->provider_transaction_id = 'DOKU-QRIS-1';
        $order->provider_external_id = '123456789';

        $response = app(DokuPaymentGateway::class)->queryPhotoOrder($order);

        $this->assertSame('00', $response['latestTransactionStatus']);
        Http::assertSent(fn (Request $request): bool => $request['serviceCode'] === '47'
            && $request['originalReferenceNo'] === 'DOKU-QRIS-1'
            && $request['originalPartnerReferenceNo'] === 'JPR-PHO-TEST');
    }

    public function test_reuses_the_persisted_qris_instead_of_creating_a_duplicate(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.doku.com/authorization/v1/access-token/b2b' => Http::response(['accessToken' => 'test-token', 'expiresIn' => 900]),
            'https://api-sandbox.doku.com/snap-adapter/b2b/v1.0/qr/qr-mpm-generate' => Http::response($this->generateResponse('JPR-PHO-PERSISTED', 'DOKU-PERSISTED')),
        ]);
        $order = PhotoOrder::factory()->create([
            'order_id' => 'JPR-PHO-PERSISTED',
            'gross_amount' => 25000,
            'currency' => 'IDR',
            'expires_at' => now()->addHour(),
        ]);
        $gateway = app(DokuPaymentGateway::class);

        $first = $gateway->ensurePhotoOrderPayment($order);
        $second = $gateway->ensurePhotoOrderPayment($order);

        $this->assertSame('DOKU-PERSISTED', $first->provider_transaction_id);
        $this->assertSame($first->qr_content, $second->qr_content);
        Http::assertSentCount(2);
    }

    public function test_rejects_malformed_and_error_responses(): void
    {
        $this->cacheToken();
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.doku.com/snap-adapter/b2b/v1.0/qr/qr-mpm-generate' => Http::sequence()
                ->push('not-json', 200)
                ->push(['responseCode' => '5004700'], 500),
        ]);
        $gateway = app(DokuPaymentGateway::class);

        try {
            $gateway->createPhotoOrderPayment($this->order());
            $this->fail('Malformed JSON should fail.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Invalid JSON', $exception->getMessage());
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('HTTP 500');
        $gateway->createPhotoOrderPayment($this->order('JPR-PHO-SECOND'));
    }

    public function test_surfaces_network_timeout_without_retrying_qris_creation(): void
    {
        $this->cacheToken();
        Http::preventStrayRequests();
        Http::fake([
            'https://api-sandbox.doku.com/snap-adapter/b2b/v1.0/qr/qr-mpm-generate' => Http::failedConnection(),
        ]);

        $this->expectException(ConnectionException::class);

        app(DokuPaymentGateway::class)->createPhotoOrderPayment($this->order());
    }

    public function test_fails_fast_for_missing_credentials_and_invalid_private_key(): void
    {
        config(['doku.client_secret' => null]);

        try {
            app(DokuPaymentGateway::class)->createPhotoOrderPayment($this->order());
            $this->fail('Missing credentials should fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame('DOKU configuration is incomplete.', $exception->getMessage());
        }

        config(['doku.client_secret' => 'test-secret', 'doku.private_key' => 'invalid-key']);
        Http::fake();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('DOKU private key is invalid.');
        app(DokuPaymentGateway::class)->createPhotoOrderPayment($this->order());
    }

    private function configureDoku(): void
    {
        config([
            'doku.environment' => 'sandbox',
            'doku.client_id' => 'test-client',
            'doku.client_secret' => 'test-secret',
            'doku.merchant_id' => '12345',
            'doku.terminal_id' => 'A01',
            'doku.private_key' => self::PRIVATE_KEY,
            'doku.private_key_path' => null,
            'doku.channel_id' => 'H2H',
            'doku.postal_code' => '12190',
            'doku.fee_type' => 1,
            'doku.qris_ttl_minutes' => 60,
        ]);
    }

    private function cacheToken(): void
    {
        Cache::put('doku.b2b_access_token.'.hash('sha256', 'sandbox|test-client'), 'test-token', now()->addMinute());
    }

    private function order(string $orderId = 'JPR-PHO-TEST'): PhotoOrder
    {
        return new PhotoOrder([
            'order_id' => $orderId,
            'gross_amount' => 25000,
            'currency' => 'IDR',
            'expires_at' => now()->addHour(),
        ]);
    }

    /** @return array<string, string> */
    private function generateResponse(string $orderId, string $reference): array
    {
        return [
            'responseCode' => '2004700',
            'referenceNo' => $reference,
            'partnerReferenceNo' => $orderId,
            'qrContent' => '000201010212TESTQRIS',
        ];
    }
}
