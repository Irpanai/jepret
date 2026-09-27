<?php

namespace App;

use App\Models\Package;
use App\Models\PaymentEvent;
use App\Models\Subscription;
use App\Models\SubscriptionOrder;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SubscriptionBilling
{
    public function createOrder(User $user, Package $package): SubscriptionOrder
    {
        return DB::transaction(function () use ($user, $package): SubscriptionOrder {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $subscription = Subscription::where('user_id', $user->id)->lockForUpdate()->first();
            $intent = $subscription?->isActive()
                ? ($subscription->package_id === $package->id ? 'renewal' : 'plan_change')
                : 'activation';

            $pending = SubscriptionOrder::where('user_id', $user->id)->where('package_id', $package->id)
                ->where('intent', $intent)->where('status', 'pending')->where('expires_at', '>', now())->latest()->first();
            if ($pending) {
                return $pending;
            }

            return SubscriptionOrder::create([
                'order_id' => 'JPR-SUB-'.now()->format('YmdHis').'-'.Str::upper(Str::random(8)),
                'user_id' => $user->id,
                'package_id' => $package->id,
                'intent' => $intent,
                'package_snapshot' => $this->snapshot($package),
                'gross_amount' => $package->harga,
                'currency' => $package->currency,
                'status' => 'pending',
                'source_subscription_version' => $subscription?->version ?? 0,
                'expires_at' => now()->addDay(),
            ]);
        });
    }

    public function activateTrial(User $user, Package $package): SubscriptionOrder
    {
        return DB::transaction(function () use ($user, $package): SubscriptionOrder {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $subscription = Subscription::where('user_id', $user->id)->lockForUpdate()->first();
            if ($subscription?->trial_used_at) {
                throw ValidationException::withMessages(['trial' => 'Trial hanya dapat digunakan satu kali per akun.']);
            }

            $order = SubscriptionOrder::create([
                'order_id' => 'JPR-TRIAL-'.now()->format('YmdHis').'-'.Str::upper(Str::random(8)),
                'user_id' => $user->id,
                'package_id' => $package->id,
                'intent' => 'trial',
                'package_snapshot' => $this->snapshot($package),
                'gross_amount' => 0,
                'currency' => $package->currency,
                'provider' => 'internal',
                'status' => 'paid',
                'provider_status' => 'trial_activated',
                'source_subscription_version' => $subscription?->version ?? 0,
                'provider_paid_at' => now(),
            ]);

            return $this->applyPaidOrder($order, now());
        });
    }

    /** @param array<string, mixed> $payload */
    public function processNotification(SubscriptionOrder $order, array $payload, bool $signatureValid): string
    {
        $status = (string) ($payload['transaction_status'] ?? '');
        $paid = $status === 'settlement' || ($status === 'capture' && ($payload['fraud_status'] ?? null) === 'accept');
        $eventKey = hash('sha256', implode('|', [$order->order_id, $payload['transaction_id'] ?? '', $status, $payload['status_code'] ?? '', $payload['settlement_time'] ?? $payload['transaction_time'] ?? '', $payload['signature_key'] ?? '']));

        return DB::transaction(function () use ($order, $payload, $signatureValid, $status, $paid, $eventKey): string {
            $order = SubscriptionOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (PaymentEvent::where('event_key', $eventKey)->exists()) {
                return 'duplicate';
            }

            $amount = (int) round((float) ($payload['gross_amount'] ?? 0));
            $validAmount = $amount === $order->gross_amount;
            $result = ! $signatureValid ? 'invalid_signature' : (! $validAmount ? 'invalid_amount' : 'recorded');
            PaymentEvent::create([
                'subscription_order_id' => $order->id,
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

            $order->update(['provider_status' => $status, 'provider_transaction_id' => $payload['transaction_id'] ?? $order->provider_transaction_id]);
            if ($paid) {
                $paidAt = $this->providerTime($payload) ?? now();
                $order->update(['status' => 'paid', 'provider_paid_at' => $paidAt]);
                $this->applyPaidOrder($order, $paidAt);

                return 'paid';
            }

            $mapped = match ($status) {
                'deny' => 'failed', 'cancel' => 'cancelled', 'expire' => 'expired', 'refund', 'partial_refund' => 'refunded', default => 'pending',
            };
            $order->update(['status' => $mapped]);

            return $mapped;
        });
    }

    public function applyPaidOrder(SubscriptionOrder $order, Carbon $paidAt): SubscriptionOrder
    {
        if ($order->processed_at) {
            return $order;
        }

        $subscription = Subscription::where('user_id', $order->user_id)->lockForUpdate()->first();
        $isOlderPlanChange = $subscription?->last_payment_at && (
            $paidAt->lt($subscription->last_payment_at)
            || ($paidAt->equalTo($subscription->last_payment_at) && strcmp($order->order_id, (string) $subscription->last_order_id) < 0)
        );
        if ($isOlderPlanChange && $order->intent !== 'renewal') {
            $order->update(['processed_at' => now(), 'requires_review' => true]);

            return $order;
        }
        if ($order->intent === 'renewal' && $subscription && $subscription->package_id !== $order->package_id) {
            $order->update(['processed_at' => now(), 'requires_review' => true]);

            return $order;
        }

        $duration = (int) ($order->package_snapshot['duration_days'] ?? 0);
        $active = $subscription?->isActive() ?? false;
        $startsAt = $active ? $subscription->starts_at : $paidAt;
        $endsAt = match ($order->intent) {
            'renewal' => ($active ? $subscription->ends_at : $paidAt)->copy()->addDays($duration),
            'plan_change' => $active ? $subscription->ends_at : $paidAt->copy()->addDays($duration),
            default => $paidAt->copy()->addDays($duration),
        };

        $values = [
            'package_id' => $order->package_id, 'status' => 'active', 'entitlement_snapshot' => $order->package_snapshot,
            'starts_at' => $startsAt, 'ends_at' => $endsAt, 'last_payment_at' => $paidAt, 'last_order_id' => $order->order_id,
            'version' => ($subscription?->version ?? 0) + 1, 'is_legacy_transition' => false,
        ];
        if ($order->intent === 'trial') {
            $values['trial_used_at'] = $paidAt;
        }
        Subscription::updateOrCreate(['user_id' => $order->user_id], $values);
        User::whereKey($order->user_id)->update(['package_id' => $order->package_id]);
        $order->update(['processed_at' => now()]);

        return $order;
    }

    /** @return array<string, mixed> */
    private function snapshot(Package $package): array
    {
        return ['code' => $package->code, 'name' => $package->display_name, 'revision' => $package->revision, 'price' => $package->harga, 'currency' => $package->currency, 'duration_days' => $package->duration_days, 'storage_quota_bytes' => $package->storage_quota_bytes, 'features' => $package->features];
    }

    /** @param array<string, mixed> $payload */
    private function providerTime(array $payload): ?Carbon
    {
        $value = $payload['settlement_time'] ?? $payload['transaction_time'] ?? null;

        return $value ? Carbon::parse($value, 'Asia/Jakarta')->utc() : null;
    }
}
