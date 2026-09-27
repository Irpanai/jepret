<?php

namespace App\Models;

use Database\Factories\SubscriptionOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'user_id', 'package_id', 'intent', 'package_snapshot', 'gross_amount', 'currency', 'provider', 'status', 'provider_status', 'provider_transaction_id', 'snap_token', 'snap_redirect_url', 'source_subscription_version', 'requires_review', 'provider_paid_at', 'expires_at', 'processed_at'])]
class SubscriptionOrder extends Model
{
    /** @use HasFactory<SubscriptionOrderFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['package_snapshot' => 'array', 'requires_review' => 'boolean', 'provider_paid_at' => 'datetime', 'expires_at' => 'datetime', 'processed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
