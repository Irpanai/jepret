<?php

namespace App;

use App\Models\AdminAuditLog;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawalWorkflow
{
    /** @param array{jumlah_tarik:int, metode_pembayaran:string, nomor_tujuan:string, idempotency_key:string, destination_type:string, account_holder?:string|null} $data */
    public function submit(User $photographer, array $data): Withdrawal
    {
        return DB::transaction(function () use ($photographer, $data): Withdrawal {
            $user = User::whereKey($photographer->id)->lockForUpdate()->firstOrFail();
            $existing = Withdrawal::where('fotografer_id', $user->id)->where('idempotency_key', $data['idempotency_key'])->first();
            if ($existing) {
                return $existing;
            }
            if ($data['jumlah_tarik'] < PlatformSetting::minimumWithdrawal() || $data['jumlah_tarik'] > $user->saldo) {
                throw ValidationException::withMessages(['jumlah_tarik' => 'Nominal harus memenuhi minimum penarikan dan tidak melebihi saldo tersedia.']);
            }
            $withdrawal = Withdrawal::create($data + ['fotografer_id' => $user->id, 'status' => 'pending']);
            $user->decrement('saldo', $data['jumlah_tarik']);

            return $withdrawal;
        }, 3);
    }

    public function review(User $admin, int $id, string $status, ?string $reason = null): Withdrawal
    {
        abort_unless($admin->role === 'superadmin', 403);

        return DB::transaction(function () use ($admin, $id, $status, $reason): Withdrawal {
            $withdrawal = Withdrawal::whereKey($id)->lockForUpdate()->firstOrFail();
            if (! in_array($withdrawal->status, ['pending', 'held'], true)) {
                throw ValidationException::withMessages(['withdrawal' => 'Penarikan ini sudah selesai diproses.']);
            }
            if (! in_array($status, ['held', 'success', 'rejected'], true) || ($status === 'rejected' && ! trim((string) $reason))) {
                throw ValidationException::withMessages(['reason' => 'Alasan penolakan wajib diisi.']);
            }
            $before = $withdrawal->status;
            if ($status === 'rejected') {
                $user = User::whereKey($withdrawal->fotografer_id)->lockForUpdate()->firstOrFail();
                $user->increment('saldo', $withdrawal->jumlah_tarik);
            }
            $withdrawal->update(['status' => $status, 'review_status' => $status, 'admin_notes' => $reason, 'reviewed_by' => $admin->id, 'processed_at' => now()]);
            AdminAuditLog::record($admin, 'withdrawal.'.$status, $withdrawal, ['before' => $before, 'after' => $status, 'reason' => $reason]);

            return $withdrawal;
        }, 3);
    }

    /** @param list<int> $ids */
    public function approveBatch(User $admin, array $ids): void
    {
        sort($ids);
        DB::transaction(function () use ($admin, $ids): void {
            foreach (array_unique($ids) as $id) {
                $this->review($admin, (int) $id, 'success');
            }
            AdminAuditLog::record($admin, 'withdrawal.batch_approved', $admin, ['withdrawal_ids' => $ids]);
        }, 3);
    }
}
