<?php

namespace App;

use App\Models\PaymentEvent;
use App\Models\Photo;
use App\Models\PhotoOrder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhotoOrderBilling
{
    /**
     * @param  Collection<int, Photo>  $photos
     */
    public function createOrder(User $user, Collection $photos): PhotoOrder
    {
        return DB::transaction(function () use ($user, $photos): PhotoOrder {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $orderId = 'JPR-PHO-'.now()->format('YmdHis').'-'.Str::upper(Str::random(8));
            $order = PhotoOrder::create([
                'order_id' => $orderId,
                'user_id' => $user->id,
                'gross_amount' => $photos->sum(fn (Photo $photo): int => (int) $photo->harga),
                'currency' => 'IDR',
                'provider' => 'midtrans',
                'status' => 'pending',
                'expires_at' => now()->addDay(),
            ]);

            foreach ($photos as $photo) {
                $price = (int) $photo->harga;
                Transaction::create([
                    'photo_order_id' => $order->id,
                    'order_number' => $orderId,
                    'pembeli_id' => $user->id,
                    'photo_id' => $photo->id,
                    'fotografer_id' => $photo->fotografer_id,
                    'harga_foto' => $price,
                    'tip_amount' => 0,
                    'total_bayar' => $price,
                    'photographer_amount' => Photo::photographerAmount($price),
                    'platform_amount' => Photo::platformAmount($price),
                    'revenue_share_snapshot' => [
                        'photographer_percent' => Photo::PHOTOGRAPHER_SHARE_PERCENT,
                        'platform_percent' => Photo::PLATFORM_SHARE_PERCENT,
                        'photographer_name' => $photo->fotografer->name ?? null,
                    ],
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'expires_at' => $order->expires_at,
                ]);
            }

            return $order->load(['user', 'transactions.photo']);
        });
    }

    /** @param array<string, mixed> $payload */
    public function processNotification(PhotoOrder $order, array $payload, bool $signatureValid): string
    {
        $status = (string) ($payload['transaction_status'] ?? '');
        $eventKey = hash('sha256', implode('|', [$order->order_id, $payload['transaction_id'] ?? '', $status, $payload['status_code'] ?? '', $payload['settlement_time'] ?? $payload['transaction_time'] ?? '', $payload['signature_key'] ?? '']));

        return DB::transaction(function () use ($order, $payload, $signatureValid, $status, $eventKey): string {
            $order = PhotoOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (PaymentEvent::where('event_key', $eventKey)->exists()) {
                return 'duplicate';
            }

            $amount = (int) round((float) ($payload['gross_amount'] ?? 0));
            $validAmount = $amount === (int) $order->gross_amount;
            $result = ! $signatureValid ? 'invalid_signature' : (! $validAmount ? 'invalid_amount' : 'recorded');
            $event = PaymentEvent::create([
                'photo_order_id' => $order->id,
                'event_key' => $eventKey,
                'provider_transaction_id' => $payload['transaction_id'] ?? null,
                'provider_status' => $status,
                'gross_amount' => $amount,
                'signature_valid' => $signatureValid,
                'payload' => collect($payload)->except(['signature_key'])->only(['order_id', 'status_code', 'gross_amount', 'transaction_status', 'transaction_id', 'payment_type', 'fraud_status', 'transaction_time', 'settlement_time'])->all(),
                'processing_result' => $result,
                'provider_event_at' => $this->providerTime($payload),
            ]);
            if (! $signatureValid || ! $validAmount) {
                return $result;
            }

            if ($order->processed_at && $order->status === 'paid') {
                $event->update(['processing_result' => 'ignored_paid']);

                return 'ignored_paid';
            }

            if (in_array($status, ['refund', 'partial_refund'], true)) {
                $order->update(['requires_review' => true, 'provider_status' => $status]);
                $event->update(['processing_result' => 'requires_review']);

                return 'requires_review';
            }

            $providerValues = [
                'provider_status' => $status,
                'provider_transaction_id' => $payload['transaction_id'] ?? $order->provider_transaction_id,
                'payment_method' => $payload['payment_type'] ?? $order->payment_method,
            ];
            $paid = $status === 'settlement' || ($status === 'capture' && ($payload['fraud_status'] ?? null) === 'accept');
            if ($paid) {
                $paidAt = $this->providerTime($payload) ?? now();
                $order->update($providerValues + ['status' => 'paid', 'provider_paid_at' => $paidAt]);
                $this->applyPaidOrder($order, $paidAt, (string) ($payload['transaction_id'] ?? ''));
                $event->update(['processing_result' => 'paid']);

                return 'paid';
            }

            $mapped = match ($status) {
                'deny' => 'failed',
                'cancel' => 'cancelled',
                'expire' => 'expired',
                default => 'pending',
            };
            $order->update($providerValues + ['status' => $mapped]);
            $order->transactions()->where('payment_status', 'pending')->update(['status' => $mapped, 'payment_status' => $mapped]);
            $event->update(['processing_result' => $mapped]);

            return $mapped;
        });
    }

    public function markPaidLocally(PhotoOrder $order): PhotoOrder
    {
        return DB::transaction(function () use ($order): PhotoOrder {
            $order = PhotoOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->processed_at) {
                return $order;
            }

            $reference = 'LOCAL-'.Str::upper(Str::random(10));
            $paidAt = now();
            $order->update([
                'provider' => 'local',
                'provider_status' => 'settlement',
                'provider_transaction_id' => $reference,
                'payment_method' => 'local',
                'status' => 'paid',
                'provider_paid_at' => $paidAt,
            ]);

            return $this->applyPaidOrder($order, $paidAt, $reference);
        });
    }

    private function applyPaidOrder(PhotoOrder $order, Carbon $paidAt, string $reference): PhotoOrder
    {
        if ($order->processed_at) {
            return $order;
        }

        $transactions = Transaction::where('photo_order_id', $order->id)->orderBy('id')->lockForUpdate()->get();
        foreach ($transactions->where('payment_status', '!=', 'paid') as $transaction) {
            User::whereKey($transaction->fotografer_id)->lockForUpdate()->increment('saldo', $transaction->jumlah_fotografer);
            $transaction->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'payment_method' => $order->payment_method,
                'payment_reference' => $reference,
                'paid_at' => $paidAt,
            ]);
        }

        $order->update(['processed_at' => now()]);

        return $order->refresh();
    }

    /** @param array<string, mixed> $payload */
    private function providerTime(array $payload): ?Carbon
    {
        $value = $payload['settlement_time'] ?? $payload['transaction_time'] ?? null;

        return $value ? Carbon::parse($value, 'Asia/Jakarta')->utc() : null;
    }
}
