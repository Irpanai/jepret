<?php

namespace App\Models;

use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'package_id', 'status', 'entitlement_snapshot', 'starts_at', 'ends_at', 'trial_used_at', 'last_payment_at', 'last_order_id', 'version', 'is_legacy_transition'])]
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['entitlement_snapshot' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'trial_used_at' => 'datetime', 'last_payment_at' => 'datetime', 'is_legacy_transition' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->ends_at === null || $this->ends_at->isFuture());
    }
}
