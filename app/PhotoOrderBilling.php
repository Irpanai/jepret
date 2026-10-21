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
    public function createOrder(User $user, Collection $photos, ?string $voucherCode = null): PhotoOrder
    {
        return DB::transaction(function () use ($user, $photos, $voucherCode): PhotoOrder {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $orderId = 'JPR-PHO-'.now()->format('YmdHis').'-'.Str::upper(Str::random(8));
            $subtotal = (int) $photos->sum(fn (Photo $photo): int => (int) $photo->harga);
            $photographerAmount = (int) $photos->sum(fn (Photo $photo): int => Photo::photographerAmount((int) $photo->harga));
            $platformCommission = $subtotal - $photographerAmount;
            $order = PhotoOrder::create([
                'order_id' => $orderId,
                'user_id' => $user->id,
                'gross_amount' => $subtotal,
                'original_subtotal' => $subtotal,
                'discount_amount' => 0,
                'final_payable' => $subtotal,
                'photographer_amount' => $photographerAmount,
                'platform_commission' => $platformCommission,
                'platform_revenue' => $platformCommission,
                'currency' => 'IDR',
                'provider' => 'doku',
                'status' => 'pending',
                'expires_at' => now()->addMinutes(config('doku.qris_ttl_minutes')),
            ]);

            $voucher = null;
            if (filled($voucherCode)) {
                $voucher = app(VoucherService::class)->reserve($voucherCode, 'photo', $subtotal, $user, $order, $platformCommission);
                $order->update([
                    'gross_amount' => $voucher['final'], 'voucher_id' => $voucher['voucher']->id,
                    'voucher_code' => $voucher['voucher']->code, 'discount_amount' => $voucher['discount'],
                    'final_payable' => $voucher['final'], 'platform_revenue' => $platformCommission - $voucher['discount'],
                ]);
            }

            $remainingDiscount = $voucher['discount'] ?? 0;
            foreach ($photos as $photo) {
                $price = (int) $photo->harga;
                $originalPlatformAmount = Photo::platformAmount($price);
                $itemDiscount = min($originalPlatformAmount, $remainingDiscount);
                $remainingDiscount -= $itemDiscount;
                Transaction::create([
                    'photo_order_id' => $order->id,
                    'order_number' => $orderId,
                    'pembeli_id' => $user->id,
                    'photo_id' => $photo->id,
                    'fotografer_id' => $photo->fotografer_id,
                    'harga_foto' => $price,
                    'tip_amount' => 0,
                    'total_bayar' => $price - $itemDiscount,
                    'photographer_amount' => Photo::photographerAmount($price),
                    'platform_amount' => $originalPlatformAmount - $itemDiscount,
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
        $paid = $status === 'settlement' || ($status === 'capture' && ($payload['fraud_status'] ?? null) === 'accept');
        $eventKey = hash('sha256', implode('|', ['doku', $payload['notification_id'] ?? '', $order->order_id, $payload['transaction_id'] ?? '', $status, $payload['status_code'] ?? '', $payload['settlement_time'] ?? $payload['transaction_time'] ?? '']));

        return DB::transaction(function () use ($order, $payload, $signatureValid, $status, $paid, $eventKey): string {
            $order = PhotoOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (PaymentEvent::where('event_key', $eventKey)->exists()) {
                return 'duplicate';
            }

            $amount = (int) round((float) ($payload['gross_amount'] ?? 0));
            $validAmount = $amount === (int) $order->gross_amount;
            $validCurrency = ($payload['currency'] ?? null) === $order->currency;
            $result = ! $signatureValid ? 'invalid_signature' : (! $validAmount ? 'invalid_amount' : (! $validCurrency ? 'invalid_currency' : 'recorded'));
            $event = PaymentEvent::create([
                'photo_order_id' => $order->id,
                'provider' => 'doku',
                'event_key' => $eventKey,
                'provider_transaction_id' => $payload['transaction_id'] ?? null,
                'provider_status' => $status,
                'gross_amount' => $amount,
                'signature_valid' => $signatureValid,
                'payload' => collect($payload)->except(['signature_key'])->only(['order_id', 'status_code', 'gross_amount', 'currency', 'transaction_status', 'transaction_id', 'external_id', 'payment_type', 'transaction_time'])->all(),
                'processing_result' => $result,
                'provider_event_at' => $this->providerTime($payload),
            ]);
            if (! $signatureValid || ! $validAmount || ! $validCurrency) {
                return $result;
            }
            $providerTime = $this->providerTime($payload);
            if ($paid && $providerTime?->isBefore($order->created_at->copy()->startOfSecond())) {
                $order->update(['status' => 'expired', 'provider_status' => $status]);
                $order->transactions()->where('payment_status', 'pending')->update(['status' => 'expired', 'payment_status' => 'expired']);
                $event->update(['processing_result' => 'expired']);

                return 'expired';
            }

            if (in_array($status, ['refund', 'partial_refund'], true)) {
                $order->update(['requires_review' => true, 'provider_status' => $status]);
                $event->update(['processing_result' => 'requires_review']);

                return 'requires_review';
            }

            if ($order->processed_at && $order->status === 'paid') {
                $event->update(['processing_result' => 'ignored_paid']);

                return 'ignored_paid';
            }

            if (in_array($order->status, ['cancelled', 'expired', 'failed'], true) && ! $paid) {
                $event->update(['processing_result' => 'ignored_terminal']);

                return 'ignored_terminal';
            }

            $providerValues = [
                'provider_status' => $status,
                'provider_transaction_id' => $payload['transaction_id'] ?? $order->provider_transaction_id,
                'payment_method' => $payload['payment_type'] ?? $order->payment_method,
            ];
            if ($paid) {
                $paidAt = $providerTime ?? now();
                $order->update($providerValues + ['status' => 'paid', 'provider_paid_at' => $paidAt]);
                $this->applyPaidOrder($order, $paidAt, (string) ($payload['transaction_id'] ?? ''));
                app(VoucherService::class)->redeem($order);
                $event->update(['processing_result' => 'paid']);

                return 'paid';
            }

            $mapped = match ($status) {
                'deny', 'failed' => 'failed',
                'cancel' => 'cancelled',
                'expire' => 'expired',
                default => 'pending',
            };
            $order->update($providerValues + ['status' => $mapped]);
            $order->transactions()->where('payment_status', 'pending')->update(['status' => $mapped, 'payment_status' => $mapped]);
            if (in_array($mapped, ['failed', 'cancelled'], true)) {
                app(VoucherService::class)->release($order);
            }
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

            $paidOrder = $this->applyPaidOrder($order, $paidAt, $reference);
            app(VoucherService::class)->redeem($order);

            return $paidOrder;
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
