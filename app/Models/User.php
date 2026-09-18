<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'google_id', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'photographer_watermark_locked' => 'boolean',
            'verified_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function effectiveQuotaMb(): float
    {
        return (float) ($this->storage_quota_override_mb ?? $this->package?->kuota_storage_mb ?? 5000);
    }

    public function verificationState(): string
    {
        return $this->is_verified ? 'approved' : ($this->verification_rejection_reason ? 'rejected' : 'pending');
    }

    public function profilePhotoUrl(): string
    {
        if ($this->profile_photo_path) {
            return route('media.profile', $this);
        }

        return $this->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=050505&color=fff';
    }

    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class, 'fotografer_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'fotografer_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'fotografer_id');
    }

    public function featuredPhoto(): HasOne
    {
        return $this->hasOne(Photo::class, 'fotografer_id')
            ->where('status', 'active')
            ->latestOfMany('published_at');
    }

    public function photographerTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'fotografer_id');
    }
}
