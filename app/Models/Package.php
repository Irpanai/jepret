<?php

namespace App\Models;

use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'nama_paket',
    'display_name',
    'description',
    'features',
    'harga',
    'currency',
    'duration_days',
    'kuota_storage_mb',
    'storage_quota_bytes',
    'bisa_custom_watermark',
    'bisa_broadcast_lokasi',
    'billing_period',
    'is_custom',
    'is_active',
    'is_trial',
    'is_legacy',
    'sort_order',
    'revision',
])]
class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'is_trial' => 'boolean',
            'is_legacy' => 'boolean',
            'bisa_custom_watermark' => 'boolean',
            'bisa_broadcast_lokasi' => 'boolean',
            'storage_quota_bytes' => 'integer',
        ];
    }

    public function scopePubliclyAvailable(Builder $query): void
    {
        $query->where('is_active', true)->where('is_legacy', false);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function storageQuotaMb(): ?float
    {
        if ($this->storage_quota_bytes === null) {
            return null;
        }

        return $this->storage_quota_bytes / 1048576;
    }
}
