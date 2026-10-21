<?php

namespace App\Models;

use Database\Factories\VoucherFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['code', 'name', 'description', 'scope', 'applies_to_all_subscription_packages', 'discount_type', 'discount_value', 'maximum_discount', 'minimum_order_amount', 'usage_limit', 'per_user_limit', 'starts_at', 'expires_at', 'is_active', 'created_by'])]
class Voucher extends Model
{
    /** @use HasFactory<VoucherFactory> */
    use HasFactory;

    protected $attributes = [
        'applies_to_all_subscription_packages' => true,
    ];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'expires_at' => 'datetime', 'is_active' => 'boolean', 'applies_to_all_subscription_packages' => 'boolean'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class);
    }

    public function subscriptionPackages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'voucher_subscription_packages');
    }

    public static function normalizeCode(string $code): string
    {
        return Str::upper(trim($code));
    }
}
