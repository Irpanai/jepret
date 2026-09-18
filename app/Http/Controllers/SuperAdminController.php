<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Models\Withdrawal;
use App\TransactionReporting;
use App\WithdrawalWorkflow;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuperAdminController extends Controller
{
    public function dashboard(Request $request, TransactionReporting $reporting): View
    {
        $query = $reporting->query($request);
        $metrics = $reporting->totals($query);
        $daily = $reporting->daily((clone $query)->where('transactions.created_at', '>=', now()->subDays(29)->startOfDay()));
        $locations = (clone $query)->paid()->join('photos', 'transactions.photo_id', '=', 'photos.id')->join('events', 'photos.event_id', '=', 'events.id')->selectRaw('events.lokasi, COUNT(*) AS sales')->groupBy('events.lokasi')->orderByDesc('sales')->limit(8)->get();
        $pendingUsers = User::where('role', 'fotografer')->where('is_verified', false)->whereNull('verification_rejection_reason')->latest()->limit(5)->get();
        $pendingWithdrawals = Withdrawal::with('fotografer')->where('status', 'pending')->latest()->limit(5)->get();
        $storage = ['used_mb' => (float) User::where('role', 'fotografer')->sum('storage_terpakai_mb'), 'quota_mb' => (float) User::where('role', 'fotografer')->with('package')->get()->sum(fn ($user) => $user->effectiveQuotaMb())];
        $integrations = ['database' => DB::connection()->getDatabaseName(), 'payment' => config('midtrans.server_key') ? 'Midtrans configured' : 'Simulated / not configured', 'storage' => config('filesystems.default'), 'queue' => config('queue.default'), 'mail' => config('mail.default')];

        return view('superadmin.dashboard', compact('metrics', 'daily', 'locations', 'pendingUsers', 'pendingWithdrawals', 'storage', 'integrations'));
    }

    public function executiveReport(Request $request, TransactionReporting $reporting): Response
    {
        $query = $reporting->query($request);
        $data = ['metrics' => $reporting->totals($query), 'daily' => $reporting->daily($query), 'from' => $request->input('from'), 'to' => $request->input('to'), 'photographers' => User::where('role', 'fotografer')->count(), 'pendingWithdrawal' => Withdrawal::where('status', 'pending')->sum('jumlah_tarik'), 'successfulWithdrawal' => Withdrawal::where('status', 'success')->sum('jumlah_tarik')];
        AdminAuditLog::record($request->user(), 'report.exported', $request->user(), ['from' => $data['from'], 'to' => $data['to']]);

        return Pdf::loadView('superadmin.report', $data)->setPaper('a4')->download('jepret-executive-report.pdf');
    }

    public function compliance(Request $request): View
    {
        $request->validate(['q' => ['nullable', 'string', 'max:200'], 'status' => ['nullable', 'in:pending,approved,rejected']]);
        $status = $request->input('status', 'pending');
        $users = User::where('role', 'fotografer')->with(['package', 'cameras', 'photos' => fn ($query) => $query->latest()->limit(4)])->withCount(['photos', 'events', 'photographerTransactions as paid_sales_count' => fn ($query) => $query->paid()])
            ->when($status === 'pending', fn ($query) => $query->where('is_verified', false)->whereNull('verification_rejection_reason'))
            ->when($status === 'approved', fn ($query) => $query->where('is_verified', true))
            ->when($status === 'rejected', fn ($query) => $query->whereNotNull('verification_rejection_reason'))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($query) => $query->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('studio_name', 'like', $term)->orWhere('category', 'like', $term));
            })
            ->latest()->paginate(10)->withQueryString();
        $counts = ['pending' => User::where('role', 'fotografer')->where('is_verified', false)->whereNull('verification_rejection_reason')->count(), 'approved' => User::where('role', 'fotografer')->where('is_verified', true)->count(), 'rejected' => User::where('role', 'fotografer')->whereNotNull('verification_rejection_reason')->count()];

        return view('superadmin.compliance', compact('users', 'counts', 'status'));
    }

    public function reviewPhotographer(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'fotografer', 404);
        if ($user->is_verified) {
            return back()->withErrors(['decision' => 'Photographer ini sudah approved dan tidak memerlukan approval ulang.']);
        }
        $data = $request->validate(['decision' => ['required', Rule::in(['approve', 'reject'])], 'reason' => ['nullable', 'required_if:decision,reject', 'string', 'max:1000']]);
        $before = $user->verificationState();
        $approved = $data['decision'] === 'approve';
        $user->forceFill(['is_verified' => $approved, 'verified_at' => $approved ? now() : null, 'verification_rejection_reason' => $approved ? null : $data['reason'], 'rejected_at' => $approved ? null : now(), 'reviewed_by' => $request->user()->id])->save();
        AdminAuditLog::record($request->user(), 'photographer.'.($approved ? 'approved' : 'rejected'), $user, ['before' => $before, 'after' => $user->verificationState(), 'reason' => $data['reason'] ?? null]);

        return back()->with('success', 'Status fotografer diperbarui.');
    }

    public function ledger(Request $request, TransactionReporting $reporting): View
    {
        $query = $reporting->query($request);
        $metrics = $reporting->totals($query);
        $transactions = $query->latest('transactions.created_at')->paginate(15)->withQueryString();

        return view('superadmin.ledger', compact('transactions', 'metrics'));
    }

    public function exportLedger(Request $request, TransactionReporting $reporting): StreamedResponse
    {
        AdminAuditLog::record($request->user(), 'ledger.exported', $request->user(), $request->only(['q', 'from', 'to']));

        return $reporting->csv($reporting->query($request), 'jepret-ledger-'.now()->format('Ymd').'.csv');
    }

    public function withdrawal(Request $request): View
    {
        $request->validate(['status' => ['nullable', 'in:all,pending,held,success,rejected']]);
        $status = $request->input('status', 'all');
        $withdrawals = Withdrawal::with('fotografer')->when($status !== 'all', fn ($query) => $query->where('status', $status))->latest()->paginate(15)->withQueryString();
        $metrics = ['pending_count' => Withdrawal::where('status', 'pending')->count(), 'pending_amount' => Withdrawal::where('status', 'pending')->sum('jumlah_tarik'), 'held' => Withdrawal::where('status', 'held')->count(), 'success_amount' => Withdrawal::where('status', 'success')->sum('jumlah_tarik')];

        return view('superadmin.withdrawal', compact('withdrawals', 'metrics', 'status'));
    }

    public function reviewWithdrawal(Request $request, WithdrawalWorkflow $workflow, int $id): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:held,success,rejected'], 'reason' => ['nullable', 'required_if:status,rejected', 'string', 'max:1000']]);
        $workflow->review($request->user(), $id, $data['status'], $data['reason'] ?? null);

        return back()->with('success', 'Penarikan diperbarui.');
    }

    public function batchWithdrawals(Request $request, WithdrawalWorkflow $workflow): RedirectResponse
    {
        $data = $request->validate(['withdrawal_ids' => ['required', 'array', 'min:1'], 'withdrawal_ids.*' => ['integer', 'exists:withdrawals,id']]);
        $workflow->approveBatch($request->user(), $data['withdrawal_ids']);

        return back()->with('success', count($data['withdrawal_ids']).' penarikan disetujui.');
    }

    public function storage(): View
    {
        $photographers = User::where('role', 'fotografer')->with('package')->withCount('photos')->withMax('photos', 'created_at')->orderBy('name')->paginate(15);
        $totalStorage = User::where('role', 'fotografer')->sum('storage_terpakai_mb');

        return view('superadmin.storage', compact('photographers', 'totalStorage'));
    }

    public function updateQuota(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'fotografer', 404);
        $data = $request->validate(['quota_mb' => ['nullable', 'numeric', 'min:0']]);
        if ($data['quota_mb'] !== null && (float) $data['quota_mb'] < (float) $user->storage_terpakai_mb) {
            return back()->withErrors(['quota_mb' => 'Kuota tidak boleh lebih kecil dari storage yang sudah digunakan.']);
        }
        $before = $user->storage_quota_override_mb;
        $user->forceFill(['storage_quota_override_mb' => $data['quota_mb']])->save();
        AdminAuditLog::record($request->user(), 'storage.quota_changed', $user, ['before' => $before, 'after' => $data['quota_mb']]);

        return back()->with('success', 'Kuota efektif diperbarui.');
    }

    public function settings(): View
    {
        $settings = PlatformSetting::values();
        $history = AdminAuditLog::with('admin')->where('action', 'settings.updated')->latest()->limit(10)->get();

        return view('superadmin.settings', compact('settings', 'history'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate(['minimum_withdrawal' => ['required', 'integer', 'min:1'], 'watermark_opacity' => ['required', 'integer', 'between:5,100'], 'watermark_scale' => ['required', 'integer', 'between:10,80'], 'watermark_position' => ['required', 'in:center,top-left,top-right,bottom-left,bottom-right'], 'strip_gps' => ['nullable', 'boolean'], 'retain_camera_metadata' => ['nullable', 'boolean']]);
        $data['strip_gps'] = $request->boolean('strip_gps');
        $data['retain_camera_metadata'] = $request->boolean('retain_camera_metadata');
        $before = PlatformSetting::values();
        foreach ($data as $key => $value) {
            PlatformSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        AdminAuditLog::record($request->user(), 'settings.updated', $request->user(), ['before' => $before, 'after' => $data]);

        return back()->with('success', 'Pengaturan platform disimpan.');
    }
}
