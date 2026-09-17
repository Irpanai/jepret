<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalTransactions = Transaction::count();
        $totalFotografer = User::where('role', 'fotografer')->count();
        $totalGmv = Transaction::sum('total_bayar');
        $paidTransactions = Transaction::where('status', 'paid')->get();
        $platformShare = $paidTransactions->sum(fn ($transaction) => $transaction->jumlah_platform);
        $photographerShare = $paidTransactions->sum(fn ($transaction) => $transaction->jumlah_fotografer);
        $totalWithdrawal = Withdrawal::where('status', 'success')->sum('jumlah_tarik');

        // Data for charts or recent
        $recentTransactions = Transaction::with(['pembeli', 'photo.fotografer'])->latest()->take(5)->get();

        // Pending queues for dashboard
        $pendingUsers = User::where('role', 'fotografer')->where('is_verified', false)->latest()->take(3)->get();
        $pendingUsersCount = User::where('role', 'fotografer')->where('is_verified', false)->count();

        $pendingWithdrawals = Withdrawal::with('fotografer')->where('status', 'pending')->latest()->take(3)->get();
        $pendingWithdrawalsCount = Withdrawal::where('status', 'pending')->count();
        $pendingWithdrawalsAmount = Withdrawal::where('status', 'pending')->sum('jumlah_tarik');

        return view('superadmin.dashboard', compact(
            'totalTransactions', 'totalFotografer', 'totalGmv', 'totalWithdrawal', 'recentTransactions',
            'pendingUsers', 'pendingUsersCount', 'pendingWithdrawals', 'pendingWithdrawalsCount', 'pendingWithdrawalsAmount',
            'platformShare', 'photographerShare'
        ));
    }

    public function compliance()
    {
        $pendingUsers = User::where('role', 'fotografer')->where('is_verified', false)->latest()->paginate(10);

        return view('superadmin.compliance', compact('pendingUsers'));
    }

    public function ledger()
    {
        $transactions = Transaction::with(['pembeli', 'photo.fotografer', 'photo.event'])->latest()->paginate(15);

        $totalTransactionsCount = Transaction::count();
        $totalGmv = Transaction::sum('total_bayar');

        $paidTransactions = Transaction::where('status', 'paid')->get();
        $hakFotografer = $paidTransactions->sum(fn ($transaction) => $transaction->jumlah_fotografer);
        $pendapatanPlatform = $paidTransactions->sum(fn ($transaction) => $transaction->jumlah_platform);
        $totalMdr = 0;

        return view('superadmin.ledger', compact('transactions', 'totalTransactionsCount', 'totalGmv', 'hakFotografer', 'pendapatanPlatform', 'totalMdr'));
    }

    public function withdrawal()
    {
        $withdrawals = Withdrawal::with('fotografer')->latest()->paginate(15);
        $pendingCount = Withdrawal::where('status', 'pending')->count();
        $pendingAmount = Withdrawal::where('status', 'pending')->sum('jumlah_tarik');

        $totalDisbursed = Withdrawal::where('status', 'success')->sum('jumlah_tarik');
        $successCount = Withdrawal::where('status', 'success')->count();

        $recentSuccess = Withdrawal::with('fotografer')->where('status', 'success')->latest()->take(3)->get();

        return view('superadmin.withdrawal', compact('withdrawals', 'pendingCount', 'pendingAmount', 'totalDisbursed', 'successCount', 'recentSuccess'));
    }

    public function storage()
    {
        $totalStorage = User::sum('storage_terpakai_mb');

        return view('superadmin.storage', compact('totalStorage'));
    }

    public function settings()
    {
        return view('superadmin.settings');
    }

    public function approveWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['msg' => 'Hanya penarikan pending yang dapat disetujui.']);
        }

        $withdrawal->update(['status' => 'success']);

        return back()->with('success', 'Penarikan dana berhasil disetujui.');
    }

    public function verifyFotografer(Request $request, $id)
    {
        $user = User::where('role', 'fotografer')->findOrFail($id);
        $user->update(['is_verified' => true]);

        return back()->with('success', 'Fotografer verified.');
    }
}
