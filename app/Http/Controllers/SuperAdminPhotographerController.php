<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use App\Models\Package;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperAdminPhotographerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $photographers = User::query()
            ->where('role', 'fotografer')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = '%'.$request->string('q')->trim()->toString().'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('studio_name', 'like', $search));
            })
            ->when($request->filled('account_status'), fn ($query) => $query->where('is_active', $request->string('account_status')->toString() === 'active'))
            ->when($request->filled('approval_status'), function ($query) use ($request): void {
                match ($request->string('approval_status')->toString()) {
                    'approved' => $query->where('is_verified', true),
                    'pending' => $query->where('is_verified', false)->whereNull('verification_rejection_reason'),
                    'rejected' => $query->where('is_verified', false)->whereNotNull('verification_rejection_reason'),
                    default => null,
                };
            })
            ->with('package')
            ->withCount([
                'photos',
                'events',
                'cameras',
                'photographerTransactions as paid_sales_count' => fn ($query) => $query->where('payment_status', 'paid'),
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'total' => User::where('role', 'fotografer')->count(),
            'active' => User::where('role', 'fotografer')->where('is_active', true)->count(),
            'inactive' => User::where('role', 'fotografer')->where('is_active', false)->count(),
        ];

        return view('superadmin.photographers.index', compact('photographers', 'counts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $packages = Package::orderBy('nama_paket')->get();

        return view('superadmin.photographers.create', compact('packages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['role'] = 'fotografer';
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        $data['is_active'] = $request->boolean('is_active');
        $data['is_verified'] = $request->boolean('is_verified');
        $data['verified_at'] = $data['is_verified'] ? now() : null;
        $data['email_verified_at'] = now();

        $photographer = new User;
        $photographer->forceFill($data)->save();

        AdminAuditLog::record($request->user(), 'photographer.created', $photographer, ['email' => $photographer->email]);

        return redirect()->route('superadmin.photographers.show', $photographer)->with('success', 'Photographer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $photographer): View
    {
        $this->ensurePhotographer($photographer);
        $photographer->load(['package', 'cameras', 'events' => fn ($query) => $query->latest()->limit(8), 'photos' => fn ($query) => $query->latest()->limit(8)]);
        $photographer->loadCount(['photos', 'events', 'cameras', 'photographerTransactions as paid_sales_count' => fn ($query) => $query->where('payment_status', 'paid')]);
        $photographer->loadSum(['photographerTransactions as paid_revenue' => fn ($query) => $query->where('payment_status', 'paid')], 'photographer_amount');

        return view('superadmin.photographers.show', compact('photographer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $photographer): View
    {
        $this->ensurePhotographer($photographer);
        $packages = Package::orderBy('nama_paket')->get();

        return view('superadmin.photographers.edit', compact('photographer', 'packages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $photographer): RedirectResponse
    {
        $this->ensurePhotographer($photographer);
        $data = $this->validatedData($request, $photographer);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']).'-'.$photographer->id;

        if (! filled($data['password'] ?? null)) {
            unset($data['password']);
        }

        $before = $photographer->only(['name', 'email', 'studio_name', 'slug', 'package_id']);
        $photographer->forceFill($data)->save();
        AdminAuditLog::record($request->user(), 'photographer.updated', $photographer, ['before' => $before, 'after' => $photographer->only(array_keys($before))]);

        return redirect()->route('superadmin.photographers.show', $photographer)->with('success', 'Data photographer diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $photographer): RedirectResponse
    {
        $this->ensurePhotographer($photographer);

        if ($photographer->photos()->exists() || $photographer->events()->exists() || $photographer->cameras()->exists() || $photographer->photographerTransactions()->exists() || Withdrawal::where('fotografer_id', $photographer->id)->exists()) {
            return back()->withErrors(['photographer' => 'Photographer memiliki data terkait. Nonaktifkan akun untuk menjaga riwayat dan transaksi.']);
        }

        AdminAuditLog::record($request->user(), 'photographer.deleted', $photographer, ['email' => $photographer->email]);
        $photographer->delete();

        return redirect()->route('superadmin.photographers.index')->with('success', 'Photographer berhasil dihapus.');
    }

    public function status(Request $request, User $photographer): RedirectResponse
    {
        $this->ensurePhotographer($photographer);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $before = $photographer->is_active;
        $photographer->forceFill(['is_active' => (bool) $data['is_active']])->save();
        AdminAuditLog::record($request->user(), 'photographer.status_changed', $photographer, ['before' => $before, 'after' => $photographer->is_active]);

        return back()->with('success', $photographer->is_active ? 'Akun photographer diaktifkan.' : 'Akun photographer dinonaktifkan.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?User $photographer = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($photographer)],
            'password' => [$photographer ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'slug' => ['nullable', 'alpha_dash', 'max:255', Rule::unique('users')->ignore($photographer)],
            'studio_name' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'instagram_username' => ['nullable', 'regex:/^[A-Za-z0-9._]+$/', 'max:30'],
            'location' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'is_active' => $photographer ? ['prohibited'] : ['nullable', 'boolean'],
            'is_verified' => $photographer ? ['prohibited'] : ['nullable', 'boolean'],
        ]);
    }

    private function ensurePhotographer(User $photographer): void
    {
        abort_unless($photographer->role === 'fotografer', 404);
    }
}
