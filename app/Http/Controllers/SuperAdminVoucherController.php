<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveVoucherRequest;
use App\Models\AdminAuditLog;
use App\Models\Package;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SuperAdminVoucherController extends Controller
{
    public function index(): View
    {
        $query = Voucher::query()->withCount(['redemptions as redeemed_count' => fn ($query) => $query->where('status', 'redeemed')]);
        $query->when(request('q'), fn ($query, $value) => $query->where(fn ($query) => $query->where('code', 'like', '%'.$value.'%')->orWhere('name', 'like', '%'.$value.'%')))
            ->when(request('scope'), fn ($query, $value) => $query->where('scope', $value))
            ->when(request('status') !== null && request('status') !== '', fn ($query) => $query->where('is_active', request('status') === 'active'));
        $vouchers = $query->latest()->paginate(15)->withQueryString();
        $metrics = ['total' => Voucher::count(), 'active' => Voucher::where('is_active', true)->count(), 'redeemed' => VoucherRedemption::where('status', 'redeemed')->count(), 'discount' => VoucherRedemption::where('status', 'redeemed')->sum('discount_amount')];

        return view('superadmin.vouchers.index', compact('vouchers', 'metrics'));
    }

    public function create(): View
    {
        return view('superadmin.vouchers.create', ['packages' => $this->subscriptionPackages()]);
    }

    public function store(SaveVoucherRequest $request): RedirectResponse
    {
        $voucher = DB::transaction(function () use ($request): Voucher {
            $voucher = Voucher::create($request->voucherData() + ['created_by' => $request->user()->id]);
            $voucher->subscriptionPackages()->sync($request->selectedPackageIds());

            return $voucher;
        });
        AdminAuditLog::record($request->user(), 'voucher.created', $voucher, ['after' => $voucher->toArray()]);

        return redirect()->route('superadmin.vouchers.index')->with('success', 'Voucher berhasil dibuat.');
    }

    public function show(Voucher $voucher): View
    {
        $redemptions = $voucher->redemptions()->with('user')->latest()->paginate(20);

        return view('superadmin.vouchers.show', compact('voucher', 'redemptions'));
    }

    public function edit(Voucher $voucher): View
    {
        $voucher->load('subscriptionPackages:id');

        return view('superadmin.vouchers.edit', ['voucher' => $voucher, 'packages' => $this->subscriptionPackages($voucher)]);
    }

    public function update(SaveVoucherRequest $request, Voucher $voucher): RedirectResponse
    {
        $before = $voucher->toArray();
        DB::transaction(function () use ($request, $voucher): void {
            $voucher->update($request->voucherData());
            $voucher->subscriptionPackages()->sync($request->selectedPackageIds());
        });
        AdminAuditLog::record($request->user(), 'voucher.updated', $voucher, ['before' => $before, 'after' => $voucher->fresh()->toArray()]);

        return redirect()->route('superadmin.vouchers.index')->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => ! $voucher->is_active]);

        return back()->with('success', $voucher->is_active ? 'Voucher diaktifkan.' : 'Voucher dinonaktifkan.');
    }

    /** @return Collection<int, Package> */
    private function subscriptionPackages(?Voucher $voucher = null): Collection
    {
        $selectedIds = $voucher?->subscriptionPackages->modelKeys() ?? [];

        return Package::query()
            ->where('is_legacy', false)
            ->where('is_custom', false)
            ->where('is_trial', false)
            ->where(fn ($query) => $query->where('is_active', true)->orWhereKey($selectedIds))
            ->ordered()
            ->get(['id', 'display_name', 'code', 'is_active']);
    }
}
