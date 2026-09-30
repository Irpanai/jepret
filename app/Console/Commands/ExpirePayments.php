<?php

namespace App\Console\Commands;

use App\Models\PhotoOrder;
use App\Models\SubscriptionOrder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('payments:expire')]
#[Description('Expire pending payment orders whose QRIS validity has elapsed')]
class ExpirePayments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        DB::transaction(function (): void {
            PhotoOrder::query()
                ->where('status', 'pending')
                ->where('expires_at', '<=', now())
                ->lockForUpdate()
                ->each(function (PhotoOrder $order): void {
                    $order->update(['status' => 'expired', 'provider_status' => 'expired_locally']);
                    $order->transactions()->where('payment_status', 'pending')->update(['status' => 'expired', 'payment_status' => 'expired']);
                });

            SubscriptionOrder::query()
                ->where('status', 'pending')
                ->where('expires_at', '<=', now())
                ->lockForUpdate()
                ->update(['status' => 'expired', 'provider_status' => 'expired_locally']);
        });

        return self::SUCCESS;
    }
}
