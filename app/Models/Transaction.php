<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
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
        if ($this->photographer_amount > 0) {
            return (int) $this->photographer_amount;
        }

        return $this->photo ? Photo::photographerAmount((int) $this->harga_foto, (int) $this->tip_amount) : 0;
    }

    public function getJumlahPlatformAttribute(): int
    {
        if ($this->platform_amount > 0) {
            return (int) $this->platform_amount;
        }

        return $this->photo ? Photo::platformAmount((int) $this->harga_foto) : 0;
    }
}
