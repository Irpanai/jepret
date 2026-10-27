<?php

namespace App\Models;

use Carbon\CarbonInterface;
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

    public const EXPIRING_SOON_THRESHOLD_SECONDS = 86400;

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

    public function isActive(?CarbonInterface $now = null): bool
    {
        $reference = $now ?? now();

        return $this->status === 'active' && ($this->ends_at === null || $this->ends_at->gt($reference));
    }

    public function isExpired(?CarbonInterface $now = null): bool
    {
        return ! $this->isActive($now);
    }

    public function remainingSeconds(?CarbonInterface $now = null): int
    {
        if (! $this->isActive($now) || $this->ends_at === null) {
            return 0;
        }

        $reference = $now ?? now();

        return max(0, $this->ends_at->getTimestamp() - $reference->getTimestamp());
    }

    public function isExpiringSoon(?CarbonInterface $now = null): bool
    {
        if (! $this->isActive($now) || $this->ends_at === null) {
            return false;
        }

        $remaining = $this->remainingSeconds($now);

        return $remaining > 0 && $remaining < self::EXPIRING_SOON_THRESHOLD_SECONDS;
    }

    public function lifecycleState(?CarbonInterface $now = null): string
    {
        if (! $this->isActive($now)) {
            return 'expired';
        }

        if ($this->isExpiringSoon($now)) {
            return 'expiring_soon';
        }

        return 'active';
    }

    public function packageDisplayName(?Package $fallbackPackage = null): string
    {
        $rawName = trim((string) (
            $this->package?->display_name
            ?? $this->package?->nama_paket
            ?? ($this->entitlement_snapshot['name'] ?? null)
            ?? $fallbackPackage?->display_name
            ?? $fallbackPackage?->nama_paket
            ?? 'Photographer'
        ));

        if ($rawName === '') {
            $rawName = 'Photographer';
        }

        return str_starts_with(strtolower($rawName), 'paket ')
            ? $rawName
            : 'Paket '.$rawName;
    }

    public function startsAtWita(): ?CarbonInterface
    {
        if ($this->starts_at === null) {
            return null;
        }

        return $this->starts_at->copy()->timezone(config('app.display_timezone', 'Asia/Makassar'));
    }

    public function endsAtWita(): ?CarbonInterface
    {
        if ($this->ends_at === null) {
            return null;
        }

        return $this->ends_at->copy()->timezone(config('app.display_timezone', 'Asia/Makassar'));
    }

    public function formattedEndsAtWita(): ?string
    {
        $endsAt = $this->endsAtWita();

        if ($endsAt === null) {
            return null;
        }

        return $endsAt->locale('id')->translatedFormat('j F Y, H:i').' WITA';
    }

    /**
     * @return array{days: int, hours: int, minutes: int, seconds: int, show_days: bool, formatted: string}
     */
    public static function breakdownSeconds(int $totalSeconds): array
    {
        $clamped = max(0, $totalSeconds);
        $days = intdiv($clamped, 86400);
        $hours = intdiv($clamped % 86400, 3600);
        $minutes = intdiv($clamped % 3600, 60);
        $seconds = $clamped % 60;
        $showDays = $clamped >= self::EXPIRING_SOON_THRESHOLD_SECONDS;

        $formatted = $showDays
            ? sprintf('%02d Hari : %02d Jam : %02d Menit : %02d Detik', $days, $hours, $minutes, $seconds)
            : sprintf('%02d Jam : %02d Menit : %02d Detik', $hours, $minutes, $seconds);

        return [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'show_days' => $showDays,
            'formatted' => $formatted,
        ];
    }
}
