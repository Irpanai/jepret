<?php

namespace App;

use App\Models\PhotoOrder;
use App\Models\SubscriptionOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class DokuPaymentReconciler
{
    public function __construct(
        private DokuPaymentGateway $gateway,
        private PhotoOrderBilling $photoBilling,
        private SubscriptionBilling $subscriptionBilling,
    ) {}

    public function reconcile(PhotoOrder|SubscriptionOrder $order): void
    {
        if (! in_array($order->status, ['pending', 'expired'], true)
            || ! $order->provider_transaction_id
            || ! $order->provider_external_id
            || ! Cache::add('doku.query.'.$order::class.'.'.$order->getKey(), true, 15)) {
            return;
        }

        try {
            $response = $order instanceof PhotoOrder
                ? $this->gateway->queryPhotoOrder($order)
                : $this->gateway->querySubscriptionOrder($order);
            $payload = [
                'order_id' => $response['originalPartnerReferenceNo'],
                'transaction_id' => $response['originalReferenceNo'],
                'external_id' => $order->provider_external_id,
                'transaction_status' => $this->status((string) $response['latestTransactionStatus']),
                'status_code' => (string) $response['latestTransactionStatus'],
                'gross_amount' => data_get($response, 'amount.value'),
                'currency' => data_get($response, 'amount.currency'),
                'payment_type' => 'qris',
                'transaction_time' => $response['paidTime'] ?? null,
            ];

            if ($order instanceof PhotoOrder) {
                $this->photoBilling->processNotification($order, $payload, true);
            } else {
                $this->subscriptionBilling->processNotification($order, $payload, true);
            }
        } catch (Throwable $exception) {
            Log::warning('DOKU QRIS reconciliation failed.', [
                'order_id' => $order->order_id,
                'exception' => $exception::class,
            ]);
        }
    }

    public function status(string $status): string
    {
        return match ($status) {
            '00' => 'settlement',
            '04' => 'refund',
            '05' => 'cancel',
            '06' => 'failed',
            default => 'pending',
        };
    }
}
