<?php

namespace App\Models;

use Database\Factories\SubscriptionRefundFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['subscription_order_id', 'requested_by', 'reviewed_by', 'amount', 'reason', 'status', 'review_notes', 'provider_reference', 'reviewed_at'])]
class SubscriptionRefund extends Model
{
    /** @use HasFactory<SubscriptionRefundFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }
}
