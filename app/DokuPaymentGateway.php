<?php

namespace App;

use App\Models\PhotoOrder;
use App\Models\SubscriptionOrder;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class DokuPaymentGateway
{
    private const QRIS_PATH = '/snap-adapter/b2b/v1.0/qr/qr-mpm-generate';

    /** @return array{reference:string, external_id:string, qr_content:string} */
    public function createPhotoOrderPayment(PhotoOrder $order): array
    {
        return $this->createQris($order->order_id, (int) $order->gross_amount, $order->currency, $order->expires_at);
    }

    /** @return array{reference:string, external_id:string, qr_content:string} */
    public function createSubscriptionPayment(SubscriptionOrder $order): array
    {
        return $this->createQris($order->order_id, (int) $order->gross_amount, $order->currency, $order->expires_at);
    }

    public function hasValidNotificationSignature(Request $request): bool
    {
        $timestamp = (string) $request->header('X-TIMESTAMP');
        $signature = (string) $request->header('X-SIGNATURE');
        $partnerId = (string) $request->header('X-PARTNER-ID');
        $authorization = (string) $request->header('Authorization');
        $token = Str::after($authorization, 'Bearer ');

        if ($timestamp === '' || $signature === '' || ! str_starts_with($authorization, 'Bearer ') || $token === '' || $partnerId !== config('doku.client_id')) {
            return false;
        }

        $payload = json_decode($request->getContent(), true);
        if (! is_array($payload)) {
            return false;
        }

        $minifiedBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $bodyHash = strtolower(hash('sha256', $minifiedBody));
        $stringToSign = 'POST:'.$request->getPathInfo().':'.$token.':'.$bodyHash.':'.$timestamp;
        $expected = base64_encode(hash_hmac('sha512', $stringToSign, (string) config('doku.client_secret'), true));

        return hash_equals($expected, $signature);
    }

    /** @return array{reference:string, external_id:string, qr_content:string} */
    private function createQris(string $orderId, int $amount, string $currency, mixed $expiresAt): array
    {
        if (! config('doku.postal_code') || config('doku.fee_type') !== 1) {
            throw new RuntimeException('DOKU_POSTAL_CODE is required and DOKU_FEE_TYPE must be 1.');
        }

        $externalId = now()->format('YmdHis').random_int(100000, 999999);
        $body = [
            'partnerReferenceNo' => $orderId,
            'amount' => ['value' => number_format($amount, 2, '.', ''), 'currency' => $currency],
            'merchantId' => config('doku.merchant_id'),
            'terminalId' => config('doku.terminal_id'),
            'validityPeriod' => $expiresAt->toIso8601String(),
            'additionalInfo' => [
                'postalCode' => config('doku.postal_code'),
                'feeType' => config('doku.fee_type'),
            ],
        ];
        $timestamp = now()->format('Y-m-d\TH:i:sP');
        $token = $this->accessToken();
        $encodedBody = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $bodyHash = strtolower(hash('sha256', $encodedBody));
        $signature = base64_encode(hash_hmac('sha512', 'POST:'.self::QRIS_PATH.':'.$token.':'.$bodyHash.':'.$timestamp, (string) config('doku.client_secret'), true));

        $response = $this->client()->withBody($encodedBody)->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'X-PARTNER-ID' => config('doku.client_id'),
            'X-EXTERNAL-ID' => $externalId,
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'CHANNEL-ID' => 'H2H',
        ])->post(self::QRIS_PATH)->throw()->json();

        if (($response['responseCode'] ?? null) !== '2004700' || empty($response['qrContent']) || empty($response['referenceNo'])) {
            throw new RuntimeException('DOKU returned an invalid QRIS response.');
        }

        return ['reference' => (string) $response['referenceNo'], 'external_id' => $externalId, 'qr_content' => (string) $response['qrContent']];
    }

    private function accessToken(): string
    {
        return Cache::remember('doku.b2b_access_token', now()->addMinutes(14), function (): string {
            $timestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
            $signed = openssl_sign(config('doku.client_id').'|'.$timestamp, $signature, $this->privateKey(), OPENSSL_ALGO_SHA256);
            if (! $signed) {
                throw new RuntimeException('Unable to sign DOKU access-token request.');
            }

            $response = $this->client()->withHeaders([
                'X-CLIENT-KEY' => config('doku.client_id'),
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => base64_encode($signature),
            ])->post('/authorization/v1/access-token/b2b', ['grantType' => 'client_credentials'])->throw()->json();

            if (empty($response['accessToken'])) {
                throw new RuntimeException('DOKU did not return a B2B access token.');
            }

            return (string) $response['accessToken'];
        });
    }

    private function client(): PendingRequest
    {
        $baseUrl = config('doku.base_urls.'.config('doku.environment'));
        if (! is_string($baseUrl)) {
            throw new RuntimeException('DOKU_ENV must be sandbox or production.');
        }

        return Http::baseUrl($baseUrl)->acceptJson()->asJson()->connectTimeout(5)->timeout(15);
    }

    private function privateKey(): string
    {
        $inline = config('doku.private_key');
        if (is_string($inline) && $inline !== '') {
            return str_replace('\\n', "\n", $inline);
        }

        $path = config('doku.private_key_path');
        if (! is_string($path) || $path === '' || ! is_readable($path)) {
            throw new RuntimeException('DOKU private key is not configured.');
        }

        return (string) file_get_contents($path);
    }
}
