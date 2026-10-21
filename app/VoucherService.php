<?php

namespace App;

use App\Models\Package;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    /** @return array{voucher: Voucher, discount: int, final: int} */
    public function preview(?string $code, string $scope, int $subtotal, User $user, ?int $platformCommission = null, ?Package $package = null): array
    {
        $voucher = Voucher::where('code', Voucher::normalizeCode((string) $code))->first();
        $this->validate($voucher, $scope, $subtotal, $user, $package);
        $discount = $this->discount($voucher, $subtotal);

        if ($scope === 'photo' && $discount > (int) $platformCommission) {
            throw ValidationException::withMessages(['voucher_code' => 'Diskon voucher melebihi komisi platform untuk pesanan ini.']);
        }
        if ($subtotal - $discount < 1) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher tidak boleh membuat total pembayaran menjadi Rp0.']);
        }

        return ['voucher' => $voucher, 'discount' => $discount, 'final' => $subtotal - $discount];
    }

    /** @return array{voucher: Voucher, discount: int, final: int} */
    public function reserve(string $code, string $scope, int $subtotal, User $user, Model $order, ?int $platformCommission = null, ?Package $package = null): array
    {
        return DB::transaction(function () use ($code, $scope, $subtotal, $user, $order, $platformCommission, $package): array {
            $voucher = Voucher::where('code', Voucher::normalizeCode($code))->lockForUpdate()->first();
            $this->validate($voucher, $scope, $subtotal, $user, $package);
            $discount = $this->discount($voucher, $subtotal);

            if ($scope === 'photo' && $discount > (int) $platformCommission) {
                throw ValidationException::withMessages(['voucher_code' => 'Diskon voucher melebihi komisi platform untuk pesanan ini.']);
            }
            if ($subtotal - $discount < 1) {
                throw ValidationException::withMessages(['voucher_code' => 'Voucher tidak boleh membuat total pembayaran menjadi Rp0.']);
            }

            VoucherRedemption::create([
                'voucher_id' => $voucher->id,
                'user_id' => $user->id,
                'order_type' => $order->getMorphClass(),
                'order_id' => $order->getKey(),
                'discount_amount' => $discount,
                'status' => 'reserved',
                'reservation_expires_at' => $order->expires_at,
            ]);

            return ['voucher' => $voucher, 'discount' => $discount, 'final' => $subtotal - $discount];
        });
    }

    public function redeem(Model $order): void
    {
        VoucherRedemption::where('order_type', $order->getMorphClass())->where('order_id', $order->getKey())
            ->whereIn('status', ['reserved', 'released'])->update(['status' => 'redeemed', 'redeemed_at' => now()]);
    }

    public function release(Model $order): void
    {
        VoucherRedemption::where('order_type', $order->getMorphClass())->where('order_id', $order->getKey())
            ->where('status', 'reserved')->update(['status' => 'released']);
    }

    private function validate(?Voucher $voucher, string $scope, int $subtotal, User $user, ?Package $package = null): void
    {
        if (! $voucher) {
            throw ValidationException::withMessages(['voucher_code' => 'Kode voucher tidak valid.']);
        }
        if (! $voucher->is_active) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher sedang tidak aktif.']);
        }
        if ($voucher->scope !== $scope) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher tidak berlaku untuk transaksi ini.']);
        }
        if ($scope === 'subscription' && ! $voucher->applies_to_all_subscription_packages
            && ($package === null || ! $voucher->subscriptionPackages()->whereKey($package->id)->exists())) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher ini tidak berlaku untuk paket yang dipilih.']);
        }
        if ($voucher->starts_at?->isFuture()) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher belum mulai berlaku.']);
        }
        if ($voucher->expires_at?->isPast()) {
            throw ValidationException::withMessages(['voucher_code' => 'Voucher sudah kedaluwarsa.']);
        }
        if ($subtotal < $voucher->minimum_order_amount) {
            throw ValidationException::withMessages(['voucher_code' => 'Minimum transaksi voucher belum terpenuhi.']);
        }

        $activeStatuses = ['reserved', 'redeemed'];
        if ($voucher->usage_limit !== null && $voucher->redemptions()->whereIn('status', $activeStatuses)->count() >= $voucher->usage_limit) {
            throw ValidationException::withMessages(['voucher_code' => 'Kuota voucher sudah habis.']);
        }
        if ($voucher->per_user_limit !== null && $voucher->redemptions()->where('user_id', $user->id)->whereIn('status', $activeStatuses)->count() >= $voucher->per_user_limit) {
            throw ValidationException::withMessages(['voucher_code' => 'Batas penggunaan voucher untuk akun ini sudah tercapai.']);
        }
    }

    private function discount(Voucher $voucher, int $subtotal): int
    {
        $discount = $voucher->discount_type === 'percentage'
            ? intdiv($subtotal * (int) $voucher->discount_value, 100)
            : (int) $voucher->discount_value;

        if ($voucher->discount_type === 'percentage' && $voucher->maximum_discount !== null) {
            $discount = min($discount, (int) $voucher->maximum_discount);
        }

        return min($discount, $subtotal);
    }
}
