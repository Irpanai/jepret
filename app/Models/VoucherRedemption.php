<?php

namespace App\Models;

use Database\Factories\VoucherRedemptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['voucher_id', 'user_id', 'order_type', 'order_id', 'discount_amount', 'status', 'reservation_expires_at', 'redeemed_at'])]
class VoucherRedemption extends Model
{
    /** @use HasFactory<VoucherRedemptionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['reservation_expires_at' => 'datetime', 'redeemed_at' => 'datetime'];
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): MorphTo
    {
        return $this->morphTo();
    }
}
