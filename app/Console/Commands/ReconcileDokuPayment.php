<?php

namespace App\Console\Commands;

use App\DokuPaymentGateway;
use App\DokuPaymentReconciler;
use App\Models\PhotoOrder;
use App\Models\SubscriptionOrder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('payments:reconcile {invoice : Jepret order ID} {--dry-run : Query DOKU without changing local payment data}')]
#[Description('Reconcile one DOKU payment order with the authoritative provider status')]
class ReconcileDokuPayment extends Command
{
    public function handle(DokuPaymentGateway $gateway, DokuPaymentReconciler $reconciler): int
    {
        $invoice = (string) $this->argument('invoice');
        $order = PhotoOrder::where('order_id', $invoice)->first()
            ?? SubscriptionOrder::where('order_id', $invoice)->first();

        if (! $order) {
            $this->error('Payment order was not found.');

            return self::FAILURE;
        }

        if ($order->provider !== 'doku' || ! $order->provider_transaction_id || ! $order->provider_external_id) {
            $this->error('The order does not have complete DOKU references.');

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            return $this->inspect($order, $gateway, $reconciler);
        }

        if ($order->status === 'paid' && $order->processed_at) {
            $this->info("{$invoice} is already paid and fulfilled.");

            return self::SUCCESS;
        }

        $result = $reconciler->reconcile($order);
        $order->refresh();

        if ($result === null) {
            $this->error('DOKU reconciliation failed or is temporarily throttled. Check the application log.');

            return self::FAILURE;
        }

        $this->info("{$invoice}: {$result} (local status: {$order->status}).");

        return self::SUCCESS;
    }

    private function inspect(PhotoOrder|SubscriptionOrder $order, DokuPaymentGateway $gateway, DokuPaymentReconciler $reconciler): int
    {
        try {
            $response = $order instanceof PhotoOrder
                ? $gateway->queryPhotoOrder($order)
                : $gateway->querySubscriptionOrder($order);
        } catch (Throwable $exception) {
            report($exception);
            $this->error('DOKU status query failed. Check the application log.');

            return self::FAILURE;
        }

        $providerStatus = (string) $response['latestTransactionStatus'];
        $mappedStatus = $reconciler->status($providerStatus);
        $this->line("{$order->order_id}: DOKU {$providerStatus} ({$mappedStatus}); local {$order->status}. No data changed.");

        return self::SUCCESS;
    }
}
