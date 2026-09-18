<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    public static function photographerSql(): string
    {
        return 'CASE WHEN revenue_share_snapshot IS NULL AND photographer_amount = 0 THEN '.self::legacyPhotographerAmountSql().' + tip_amount ELSE photographer_amount END';
    }

    public static function platformSql(): string
    {
        return 'CASE WHEN revenue_share_snapshot IS NULL AND platform_amount = 0 THEN harga_foto - '.self::legacyPhotographerAmountSql().' ELSE platform_amount END';
    }

    private static function legacyPhotographerAmountSql(): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? 'CAST(harga_foto * 0.9 AS INTEGER)'
            : 'FLOOR(harga_foto * 0.9)';
    }

    public function scopePaid(Builder $query): void
    {
        $query->where('transactions.status', 'paid')->where(function ($query): void {
            $query->where('payment_status', 'paid')->orWhere(function ($legacy): void {
                $legacy->whereNull('order_number')->whereNull('revenue_share_snapshot')->where('payment_status', 'pending');
            });
        });
    }

    public function scopeForPhotographer(Builder $query, int $id): void
    {
        $query->where(function ($query) use ($id): void {
            $query->where('transactions.fotografer_id', $id)->orWhere(function ($legacy) use ($id): void {
                $legacy->whereNull('transactions.fotografer_id')->whereHas('photo', fn ($photo) => $photo->where('fotografer_id', $id));
            });
        });
    }

    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'revenue_share_snapshot' => 'array',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    public function pembeli(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembeli_id');
    }

    public function fotografer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fotografer_id');
    }

    public function getJumlahFotograferAttribute(): int
    {
        if ($this->revenue_share_snapshot !== null || $this->photographer_amount > 0) {
            return (int) $this->photographer_amount;
        }

        return Photo::photographerAmount((int) $this->harga_foto, (int) $this->tip_amount);
    }

    public function getJumlahPlatformAttribute(): int
    {
        if ($this->revenue_share_snapshot !== null || $this->platform_amount > 0) {
            return (int) $this->platform_amount;
        }

        return Photo::platformAmount((int) $this->harga_foto);
    }
}
