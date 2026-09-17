<?php

namespace App\Models;

use Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Photo extends Model
{
    /** @use HasFactory<PhotoFactory> */
    use HasFactory;

    public const PHOTOGRAPHER_SHARE_PERCENT = 90;

    public const PLATFORM_SHARE_PERCENT = 10;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'taken_at' => 'datetime',
            'file_size_mb' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function fotografer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fotografer_id');
    }

    public function camera(): BelongsTo
    {
        return $this->belongsTo(Camera::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getNetHargaAttribute(): int
    {
        return self::photographerAmount((int) $this->harga);
    }

    public function getPlatformFeeAttribute(): int
    {
        return self::platformAmount((int) $this->harga);
    }

    public static function photographerAmount(int $price, int $tipAmount = 0): int
    {
        return intdiv($price * self::PHOTOGRAPHER_SHARE_PERCENT, 100) + $tipAmount;
    }

    public static function platformAmount(int $price): int
    {
        return $price - intdiv($price * self::PHOTOGRAPHER_SHARE_PERCENT, 100);
    }
}
