<?php

namespace App\Models;

use Database\Factories\PhotoOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['order_id', 'user_id', 'gross_amount', 'original_subtotal', 'voucher_id', 'voucher_code', 'discount_amount', 'final_payable', 'photographer_amount', 'platform_commission', 'platform_revenue', 'currency', 'provider', 'status', 'provider_status', 'provider_transaction_id', 'provider_external_id', 'payment_method', 'snap_token', 'snap_redirect_url', 'qr_content', 'requires_review', 'provider_paid_at', 'expires_at', 'processed_at'])]
class PhotoOrder extends Model
{
    /** @use HasFactory<PhotoOrderFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'requires_review' => 'boolean',
            'provider_paid_at' => 'datetime',
            'expires_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function voucherRedemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class, 'order_id')->where('order_type', $this->getMorphClass());
    }
}
