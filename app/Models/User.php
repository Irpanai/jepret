<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
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
            'is_active' => 'boolean',
            'photographer_watermark_locked' => 'boolean',
            'verified_at' => 'datetime',
            'rejected_at' => 'datetime',
            'photographer_onboarded_at' => 'datetime',
        ];
    }

    public function hasRole(string $role): bool
    {
        return match ($role) {
            'pembeli' => in_array($this->role, ['pembeli', 'fotografer'], true),
            default => $this->role === $role,
        };
    }

    public function hasCompletedPhotographerOnboarding(): bool
    {
        return $this->photographer_onboarded_at !== null;
    }

    public static function normalizeWhatsapp(?string $number): ?string
    {
        if ($number === null || trim($number) === '') {
            return null;
        }

        $digits = (string) preg_replace('/\D+/', '', $number);

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        return $digits;
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function hasActiveSubscription(?CarbonInterface $now = null): bool
    {
        return $this->subscription?->isActive($now) ?? false;
    }

    /**
     * @return array{
     *     state: string,
     *     package_name: string,
     *     badge_label: string,
     *     status_heading: string,
     *     remaining_seconds: int,
     *     countdown: array{days: int, hours: int, minutes: int, seconds: int, show_days: bool, formatted: string},
     *     has_unlimited_duration: bool,
     *     ends_at_wita: ?string,
     *     ends_at_iso: ?string,
     *     ends_at_timestamp_ms: ?int,
     *     server_now_timestamp_ms: int,
     *     storage_quota_mb: float,
     *     storage_quota_bytes: int,
     *     storage_quota_formatted: string,
     *     storage_used_mb: float,
     *     storage_remaining_mb: float
     * }
     */
    public function subscriptionDashboardSummary(?CarbonInterface $now = null): array
    {
        $reference = $now ?? now();
        $subscription = $this->subscription;

        if ($subscription === null) {
            return [
                'state' => 'unsubscribed',
                'package_name' => 'Belum Berlangganan',
                'badge_label' => 'Belum Berlangganan',
                'status_heading' => 'Belum Berlangganan',
                'remaining_seconds' => 0,
                'countdown' => Subscription::breakdownSeconds(0),
                'has_unlimited_duration' => false,
                'ends_at_wita' => null,
                'ends_at_iso' => null,
                'ends_at_timestamp_ms' => null,
                'server_now_timestamp_ms' => $reference->getTimestamp() * 1000,
                'storage_quota_mb' => $this->effectiveQuotaMb(),
                'storage_quota_bytes' => $this->effectiveQuotaBytes(),
                'storage_quota_formatted' => $this->formattedQuota(),
                'storage_used_mb' => (float) $this->storage_terpakai_mb,
                'storage_remaining_mb' => max(0, $this->effectiveQuotaMb() - (float) $this->storage_terpakai_mb),
            ];
        }

        $state = $subscription->lifecycleState($reference);
        $remainingSeconds = $subscription->remainingSeconds($reference);
        $countdown = Subscription::breakdownSeconds($remainingSeconds);

        return [
            'state' => $state,
            'package_name' => $subscription->packageDisplayName($this->package),
            'badge_label' => match ($state) {
                'active' => 'Aktif',
                'expiring_soon' => 'Segera Berakhir',
                default => 'Kedaluwarsa',
            },
            'status_heading' => match ($state) {
                'expired' => 'Paket Kedaluwarsa',
                default => 'Sisa masa aktif',
            },
            'remaining_seconds' => $remainingSeconds,
            'countdown' => $countdown,
            'has_unlimited_duration' => $subscription->isActive($reference) && $subscription->ends_at === null,
            'ends_at_wita' => $subscription->formattedEndsAtWita(),
            'ends_at_iso' => $subscription->endsAtWita()?->toIso8601String(),
            'ends_at_timestamp_ms' => $subscription->ends_at !== null ? $subscription->ends_at->getTimestamp() * 1000 : null,
            'server_now_timestamp_ms' => $reference->getTimestamp() * 1000,
            'storage_quota_mb' => $this->effectiveQuotaMb(),
            'storage_quota_bytes' => $this->effectiveQuotaBytes(),
            'storage_quota_formatted' => $this->formattedQuota(),
            'storage_used_mb' => (float) $this->storage_terpakai_mb,
            'storage_remaining_mb' => max(0, $this->effectiveQuotaMb() - (float) $this->storage_terpakai_mb),
        ];
    }

    public function scopeWithActiveSubscription(Builder $query): void
    {
        $query->whereHas('subscription', fn (Builder $subscription) => $subscription
            ->where('status', 'active')
            ->where(fn (Builder $dates) => $dates->whereNull('ends_at')->orWhere('ends_at', '>', now())));
    }

    public function effectiveQuotaBytes(): int
    {
        if ($this->storage_quota_override_mb !== null) {
            return (int) round(((float) $this->storage_quota_override_mb) * 1048576);
        }

        $subscriptionQuotaBytes = $this->subscription?->entitlement_snapshot['storage_quota_bytes'] ?? null;
        if ($subscriptionQuotaBytes !== null) {
            return (int) $subscriptionQuotaBytes;
        }

        $subscriptionQuotaMb = $this->subscription?->entitlement_snapshot['kuota_storage_mb'] ?? null;
        if ($subscriptionQuotaMb !== null) {
            return (int) round(((float) $subscriptionQuotaMb) * 1048576);
        }

        if ($this->package?->storage_quota_bytes !== null) {
            return (int) $this->package->storage_quota_bytes;
        }

        if ($this->package?->kuota_storage_mb) {
            return (int) round(((float) $this->package->kuota_storage_mb) * 1048576);
        }

        return 5000 * 1048576;
    }

    public function effectiveQuotaMb(): float
    {
        return $this->effectiveQuotaBytes() / 1048576;
    }

    public static function formatStorageQuota(int|float $quotaMb): string
    {
        $gb = $quotaMb / 1024;
        if ($gb >= 0.5) {
            $formatted = fmod($gb, 1) === 0.0
                ? number_format($gb, 0, ',', '.')
                : rtrim(rtrim(number_format($gb, 2, ',', '.'), '0'), ',');

            return $formatted.' GB';
        }

        if ($quotaMb > 0) {
            $formatted = fmod($quotaMb, 1) === 0.0
                ? number_format($quotaMb, 0, ',', '.')
                : rtrim(rtrim(number_format($quotaMb, 2, ',', '.'), '0'), ',');

            return $formatted.' MB';
        }

        return '0 MB';
    }

    public function formattedQuota(): string
    {
        return self::formatStorageQuota($this->effectiveQuotaMb());
    }

    public function formattedUsedStorage(): string
    {
        return self::formatStorageQuota((float) $this->storage_terpakai_mb);
    }

    public function formattedRemainingStorage(): string
    {
        return self::formatStorageQuota(max(0, $this->effectiveQuotaMb() - (float) $this->storage_terpakai_mb));
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

    public function photoOrders(): HasMany
    {
        return $this->hasMany(PhotoOrder::class);
    }
}
