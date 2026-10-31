<?php

namespace App;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;

class PhotographerNotificationFeed
{
    /**
     * @return list<array{
     *     id: string,
     *     type: string,
     *     status: string,
     *     title: string,
     *     message: string,
     *     time: string,
     *     timestamp: int,
     *     url: string,
     *     badge_label: string,
     *     badge_class: string,
     *     icon: string
     * }>
     */
    public static function forUser(User $user): array
    {
        $items = collect();

        // 1. Penjualan Foto Terbayar (Transactions)
        $sales = Transaction::query()
            ->forPhotographer($user->id)
            ->paid()
            ->with(['photo', 'pembeli'])
            ->latest('paid_at')
            ->limit(10)
            ->get();

        foreach ($sales as $sale) {
            $time = $sale->paid_at ?? $sale->created_at;
            $photoTitle = $sale->photo?->title ?? ('Foto #'.$sale->photo_id);
            $buyerName = $sale->pembeli?->name ?? 'Pembeli';
            $amount = number_format($sale->jumlah_fotografer, 0, ',', '.');

            $items->push([
                'id' => 'sale_'.$sale->id,
                'type' => 'sale',
                'status' => 'success',
                'title' => 'Foto Terjual',
                'message' => "{$photoTitle} dibeli oleh {$buyerName}. Bagian Anda: +Rp{$amount}",
                'time' => $time ? $time->timezone(config('app.display_timezone'))->format('d M H:i') : 'Baru saja',
                'timestamp' => $time ? $time->timestamp : now()->timestamp,
                'url' => route('fotografer.orders'),
                'badge_label' => 'Penjualan',
                'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'icon' => 'sale',
            ]);
        }

        // 2. Status Penarikan Dana (Withdrawals)
        $withdrawals = Withdrawal::query()
            ->where('fotografer_id', $user->id)
            ->latest('created_at')
            ->limit(10)
            ->get();

        foreach ($withdrawals as $wd) {
            $time = $wd->processed_at ?? $wd->updated_at ?? $wd->created_at;
            $amount = number_format($wd->jumlah_tarik, 0, ',', '.');
            $method = $wd->metode_pembayaran ?: 'rekening tujuan';

            [$title, $message, $status, $badgeLabel, $badgeClass, $icon] = match ($wd->status) {
                'success' => [
                    "Penarikan #WD-{$wd->id} Berhasil",
                    "Dana Rp{$amount} telah ditransfer ke {$method}.",
                    'success',
                    'Berhasil',
                    'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'withdrawal_success',
                ],
                'rejected' => [
                    "Penarikan #WD-{$wd->id} Ditolak",
                    "Penarikan Rp{$amount} ditolak".($wd->admin_notes ? ": {$wd->admin_notes}." : '.').' Saldo telah dikembalikan.',
                    'rejected',
                    'Ditolak',
                    'bg-red-50 text-red-700 border-red-200',
                    'withdrawal_rejected',
                ],
                'held' => [
                    "Penarikan #WD-{$wd->id} Ditahan",
                    "Penarikan Rp{$amount} sedang ditahan oleh admin untuk verifikasi.",
                    'held',
                    'Ditahan',
                    'bg-blue-50 text-blue-700 border-blue-200',
                    'withdrawal_held',
                ],
                default => [
                    "Pengajuan Penarikan #WD-{$wd->id}",
                    "Pengajuan penarikan Rp{$amount} ke {$method} sedang menunggu review.",
                    'pending',
                    'Menunggu',
                    'bg-amber-50 text-amber-700 border-amber-200',
                    'withdrawal_pending',
                ],
            };

            $items->push([
                'id' => 'wd_'.$wd->id.'_'.$wd->status,
                'type' => 'withdrawal',
                'status' => $status,
                'title' => $title,
                'message' => $message,
                'time' => $time ? $time->timezone(config('app.display_timezone'))->format('d M H:i') : 'Baru saja',
                'timestamp' => $time ? $time->timestamp : now()->timestamp,
                'url' => route('fotografer.dashboard').'#riwayat-penarikan',
                'badge_label' => $badgeLabel,
                'badge_class' => $badgeClass,
                'icon' => $icon,
            ]);
        }

        // 3. Verifikasi Akun
        if ($user->is_verified && $user->verified_at) {
            $items->push([
                'id' => 'verify_'.$user->id,
                'type' => 'verification',
                'status' => 'success',
                'title' => 'Profil Terverifikasi',
                'message' => 'Selamat! Akun fotografer Anda telah disetujui. Anda dapat mengunggah dan menjual foto.',
                'time' => $user->verified_at->timezone(config('app.display_timezone'))->format('d M H:i'),
                'timestamp' => $user->verified_at->timestamp,
                'url' => route('fotografer.photos.index'),
                'badge_label' => 'Verifikasi',
                'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'icon' => 'verification',
            ]);
        } elseif (! $user->is_verified && $user->verification_rejection_reason) {
            $time = $user->rejected_at ?? $user->updated_at;
            $items->push([
                'id' => 'verify_reject_'.$user->id,
                'type' => 'verification',
                'status' => 'rejected',
                'title' => 'Verifikasi Ditolak',
                'message' => 'Alasan: '.$user->verification_rejection_reason.'. Silakan perbarui profil Anda.',
                'time' => $time ? $time->timezone(config('app.display_timezone'))->format('d M H:i') : 'Baru saja',
                'timestamp' => $time ? $time->timestamp : now()->timestamp,
                'url' => route('profile.edit'),
                'badge_label' => 'Perlu Aksi',
                'badge_class' => 'bg-red-50 text-red-700 border-red-200',
                'icon' => 'verification_rejected',
            ]);
        }

        // 4. Subscription State
        if ($user->subscription) {
            $subSummary = $user->subscriptionDashboardSummary();
            if ($subSummary['state'] === 'expiring_soon') {
                $items->push([
                    'id' => 'sub_warning_'.$user->subscription->id,
                    'type' => 'subscription',
                    'status' => 'warning',
                    'title' => 'Masa Aktif Paket Segera Berakhir',
                    'message' => "Paket {$subSummary['package_name']} tersisa {$subSummary['countdown']['formatted']}. Perpanjang agar foto tetap aktif.",
                    'time' => now()->timezone(config('app.display_timezone'))->format('d M H:i'),
                    'timestamp' => now()->timestamp,
                    'url' => route('subscriptions.plans'),
                    'badge_label' => 'Langganan',
                    'badge_class' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'icon' => 'subscription',
                ]);
            } elseif ($subSummary['state'] === 'expired') {
                $time = $user->subscription->ends_at;
                $items->push([
                    'id' => 'sub_expired_'.$user->subscription->id,
                    'type' => 'subscription',
                    'status' => 'rejected',
                    'title' => 'Paket Berlangganan Berakhir',
                    'message' => "Masa aktif {$subSummary['package_name']} telah habis. Perpanjang untuk mengaktifkan kembali karya Anda.",
                    'time' => $time ? $time->timezone(config('app.display_timezone'))->format('d M H:i') : 'Baru saja',
                    'timestamp' => $time ? $time->timestamp : now()->timestamp,
                    'url' => route('subscriptions.plans'),
                    'badge_label' => 'Kedaluwarsa',
                    'badge_class' => 'bg-red-50 text-red-700 border-red-200',
                    'icon' => 'subscription',
                ]);
            }
        }

        return $items->sortByDesc('timestamp')->values()->all();
    }
}

