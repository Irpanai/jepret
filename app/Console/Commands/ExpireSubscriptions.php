<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('subscriptions:expire')]
#[Description('Mark elapsed Photographer subscriptions as expired')]
class ExpireSubscriptions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = Subscription::where('status', 'active')->whereNotNull('ends_at')->where('ends_at', '<=', now())->update(['status' => 'expired']);
        $this->info("Expired {$count} subscription(s).");

        return self::SUCCESS;
    }
}
