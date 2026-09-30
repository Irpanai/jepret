<?php

namespace App;

use App\Models\PhotoOrder;
use App\Models\SubscriptionOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JsonException;
use RuntimeException;
use Throwable;

class DokuPaymentGateway
{
    private const TOKEN_PATH = '/authorization/v1/access-token/b2b';

    private const QRIS_GENERATE_PATH = '/snap-adapter/b2b/v1.0/qr/qr-mpm-generate';

    private const QRIS_QUERY_PATH = '/snap-adapter/b2b/v1.0/qr/qr-mpm-query';

    private const QRIS_SERVICE_CODE = '47';

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

    public function ensurePhotoOrderPayment(PhotoOrder $order): PhotoOrder
    {
        return $this->ensurePayment($order);
    }

    public function ensureSubscriptionPayment(SubscriptionOrder $order): SubscriptionOrder
    {
        return $this->ensurePayment($order);
    }

    /** @return array<string, mixed> */
    public function queryPhotoOrder(PhotoOrder $order): array
    {
        return $this->queryQris($order);
    }

    /** @return array<string, mixed> */
    public function querySubscriptionOrder(SubscriptionOrder $order): array
    {
        return $this->queryQris($order);
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

        try {
            if (Carbon::parse($timestamp)->diffInMinutes(now(), absolute: true) > 15) {
                return false;
            }

            $payload = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
            if (! is_array($payload)) {
                return false;
            }

            $body = $this->encodeBody($payload);
        } catch (Throwable) {
            return false;
        }

        return hash_equals(
            $this->symmetricSignature('POST', $request->getPathInfo(), $token, $body, $timestamp),
            $signature,
        );
    }

    /** @return array{reference:string, external_id:string, qr_content:string} */
    private function createQris(string $orderId, int $amount, string $currency, mixed $expiresAt): array
    {
        $this->validateConfiguration();

        if ($currency !== 'IDR' || $amount < 1 || ! $expiresAt instanceof Carbon) {
            throw new RuntimeException('DOKU QRIS order data is invalid.');
        }

        $externalId = $this->externalId();
        $body = [
            'partnerReferenceNo' => $orderId,
            'amount' => ['value' => $this->amount($amount), 'currency' => $currency],
            'merchantId' => config('doku.merchant_id'),
            'terminalId' => config('doku.terminal_id'),
            'validityPeriod' => $expiresAt->toIso8601String(),
            'additionalInfo' => [
                'postalCode' => config('doku.postal_code'),
                'feeType' => (string) config('doku.fee_type'),
            ],
        ];
        $response = $this->snapRequest(self::QRIS_GENERATE_PATH, $body, $externalId);

        if (($response['responseCode'] ?? null) !== '2004700'
            || ($response['partnerReferenceNo'] ?? null) !== $orderId
            || empty($response['qrContent'])
            || empty($response['referenceNo'])) {
            throw new RuntimeException('DOKU returned an invalid QRIS response.');
        }

        return [
            'reference' => (string) $response['referenceNo'],
            'external_id' => $externalId,
            'qr_content' => (string) $response['qrContent'],
        ];
    }

    /** @return array<string, mixed> */
    private function queryQris(PhotoOrder|SubscriptionOrder $order): array
    {
        if (! $order->provider_transaction_id || ! $order->provider_external_id) {
            throw new RuntimeException('DOKU QRIS references are missing.');
        }

        $this->validateConfiguration();
        $response = $this->snapRequest(self::QRIS_QUERY_PATH, [
            'originalReferenceNo' => $order->provider_transaction_id,
            'originalPartnerReferenceNo' => $order->order_id,
            'serviceCode' => self::QRIS_SERVICE_CODE,
            'merchantId' => config('doku.merchant_id'),
        ], $this->externalId());

        if (($response['responseCode'] ?? null) !== '2005100'
            || ($response['originalReferenceNo'] ?? null) !== $order->provider_transaction_id
            || ($response['originalPartnerReferenceNo'] ?? null) !== $order->order_id
            || ($response['serviceCode'] ?? null) !== self::QRIS_SERVICE_CODE
            || ! isset($response['latestTransactionStatus'])) {
            throw new RuntimeException('DOKU returned an invalid QRIS query response.');
        }

        return $response;
    }

    /** @return array<string, mixed> */
    private function snapRequest(string $path, array $body, string $externalId): array
    {
        $timestamp = now()->format('Y-m-d\TH:i:sP');
        $token = $this->accessToken();
        $encodedBody = $this->encodeBody($body);
        $response = $this->client()->withBody($encodedBody, 'application/json')->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'X-PARTNER-ID' => config('doku.client_id'),
            'X-EXTERNAL-ID' => $externalId,
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $this->symmetricSignature('POST', $path, $token, $encodedBody, $timestamp),
            'CHANNEL-ID' => config('doku.channel_id'),
        ])->post($path);

        return $this->successfulJson($response, 'DOKU QRIS request failed.');
    }

    private function accessToken(): string
    {
        $cacheKey = 'doku.b2b_access_token.'.hash('sha256', config('doku.environment').'|'.config('doku.client_id'));
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        return Cache::lock($cacheKey.'.lock', 10)->block(5, function () use ($cacheKey): string {
            $cached = Cache::get($cacheKey);
            if (is_string($cached) && $cached !== '') {
                return $cached;
            }

            $this->validateConfiguration();
            $timestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
            $signed = openssl_sign(config('doku.client_id').'|'.$timestamp, $signature, $this->privateKey(), OPENSSL_ALGO_SHA256);
            if (! $signed) {
                throw new RuntimeException('Unable to sign DOKU access-token request.');
            }

            $response = $this->client()->withHeaders([
                'X-CLIENT-KEY' => config('doku.client_id'),
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => base64_encode($signature),
            ])->post(self::TOKEN_PATH, ['grantType' => 'client_credentials']);
            $payload = $this->successfulJson($response, 'DOKU access-token request failed.');
            $token = $payload['accessToken'] ?? null;
            if (! is_string($token) || $token === '') {
                throw new RuntimeException('DOKU did not return a B2B access token.');
            }

            $expiresIn = max(60, (int) ($payload['expiresIn'] ?? 900));
            Cache::put($cacheKey, $token, now()->addSeconds(max(30, $expiresIn - 60)));

            return $token;
        });
    }

    private function ensurePayment(PhotoOrder|SubscriptionOrder $order): PhotoOrder|SubscriptionOrder
    {
        if (! $order->exists) {
            throw new RuntimeException('DOKU payment order must be persisted first.');
        }

        return Cache::lock('doku.qris.order.'.get_class($order).'.'.$order->getKey(), 30)->block(10, function () use ($order): Model {
            $order->refresh();
            if ($order->qr_content && $order->provider_transaction_id && $order->provider_external_id && $order->expires_at?->isFuture()) {
                return $order;
            }
            if ($order->status !== 'pending' || $order->expires_at?->isPast()) {
                throw new RuntimeException('DOKU payment order is no longer payable.');
            }

            $payment = $order instanceof PhotoOrder
                ? $this->createPhotoOrderPayment($order)
                : $this->createSubscriptionPayment($order);
            $order->update([
                'provider_transaction_id' => $payment['reference'],
                'provider_external_id' => $payment['external_id'],
                'qr_content' => $payment['qr_content'],
                ...($order instanceof PhotoOrder ? ['payment_method' => 'qris'] : []),
            ]);

            return $order->refresh();
        });
    }

    private function symmetricSignature(string $method, string $path, string $token, string $body, string $timestamp): string
    {
        $stringToSign = $method.':'.$path.':'.$token.':'.strtolower(hash('sha256', $body)).':'.$timestamp;

        return base64_encode(hash_hmac('sha512', $stringToSign, (string) config('doku.client_secret'), true));
    }

    private function client(): PendingRequest
    {
        $baseUrl = config('doku.base_urls.'.config('doku.environment'));
        if (! is_string($baseUrl) || $baseUrl === '') {
            throw new RuntimeException('DOKU_ENV must be sandbox or production.');
        }

        return Http::baseUrl($baseUrl)->acceptJson()->asJson()->connectTimeout(5)->timeout(15);
    }

    private function privateKey(): string
    {
        $inline = config('doku.private_key');
        if (is_string($inline) && $inline !== '') {
            $key = str_replace('\\n', "\n", $inline);
        } else {
            $path = config('doku.private_key_path');
            if (! is_string($path) || $path === '') {
                throw new RuntimeException('DOKU private key is not configured.');
            }

            $absolutePath = preg_match('/^(?:[A-Za-z]:[\\\\\/]|[\\\\\/])/', $path) === 1 ? $path : base_path($path);
            if (! is_readable($absolutePath)) {
                throw new RuntimeException('DOKU private key is not readable.');
            }

            $key = (string) file_get_contents($absolutePath);
        }

        if (openssl_pkey_get_private($key) === false) {
            throw new RuntimeException('DOKU private key is invalid.');
        }

        return $key;
    }

    private function validateConfiguration(): void
    {
        foreach (['client_id', 'client_secret', 'merchant_id', 'terminal_id', 'postal_code', 'channel_id'] as $key) {
            if (! is_string(config('doku.'.$key)) || trim((string) config('doku.'.$key)) === '') {
                throw new RuntimeException('DOKU configuration is incomplete.');
            }
        }

        if (! in_array(config('doku.environment'), ['sandbox', 'production'], true)
            || config('doku.fee_type') !== 1
            || ! preg_match('/^\d{5}$/', (string) config('doku.postal_code'))
            || config('doku.qris_ttl_minutes') < 1) {
            throw new RuntimeException('DOKU configuration is invalid.');
        }
    }

    /** @return array<string, mixed> */
    private function successfulJson(Response $response, string $message): array
    {
        if (! $response->successful()) {
            throw new RuntimeException($message.' HTTP '.$response->status());
        }

        try {
            $payload = $response->json();
        } catch (JsonException) {
            throw new RuntimeException($message.' Invalid JSON.');
        }

        if (! is_array($payload)) {
            throw new RuntimeException($message.' Invalid JSON.');
        }

        return $payload;
    }

    /** @param array<string, mixed> $body */
    private function encodeBody(array $body): string
    {
        return json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    private function amount(int $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    private function externalId(): string
    {
        return now()->format('YmdHisv').random_int(100000000, 999999999);
    }
}
