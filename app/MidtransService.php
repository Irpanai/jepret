<?php

namespace App;

use App\Models\PhotoOrder;
use App\Models\SubscriptionOrder;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    /** @return array{token:string, redirect_url:string} */
    public function createSnapTransaction(SubscriptionOrder $order): array
    {
        $this->configure();

        $response = Snap::createTransaction([
            'transaction_details' => ['order_id' => $order->order_id, 'gross_amount' => $order->gross_amount],
            'item_details' => [[
                'id' => $order->package_snapshot['code'],
                'price' => $order->gross_amount,
                'quantity' => 1,
                'name' => $order->package_snapshot['name'],
            ]],
            'customer_details' => ['first_name' => $order->user->name, 'email' => $order->user->email],
            'expiry' => ['unit' => 'hours', 'duration' => 24],
        ]);

        return ['token' => $response->token, 'redirect_url' => $response->redirect_url];
    }

    /** @return array{token:string, redirect_url:string} */
    public function createPhotoOrderTransaction(PhotoOrder $order): array
    {
        $this->configure();
        $order->loadMissing(['user', 'transactions.photo']);

        $response = Snap::createTransaction([
            'transaction_details' => ['order_id' => $order->order_id, 'gross_amount' => $order->gross_amount],
            'item_details' => $order->transactions->map(fn ($transaction): array => [
                'id' => 'PHOTO-'.$transaction->photo_id,
                'price' => (int) $transaction->total_bayar,
                'quantity' => 1,
                'name' => Str::limit($transaction->photo?->title ?: 'Foto Jepret', 50, ''),
            ])->all(),
            'customer_details' => ['first_name' => $order->user?->name, 'email' => $order->user?->email],
            'expiry' => ['unit' => 'hours', 'duration' => 24],
        ]);

        return ['token' => $response->token, 'redirect_url' => $response->redirect_url];
    }

    /** @param array<string, mixed> $payload */
    public function hasValidSignature(array $payload): bool
    {
        $expected = hash('sha512', ($payload['order_id'] ?? '').($payload['status_code'] ?? '').($payload['gross_amount'] ?? '').config('midtrans.server_key'));

        return isset($payload['signature_key']) && hash_equals($expected, (string) $payload['signature_key']);
    }

    private function configure(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');
    }
}
