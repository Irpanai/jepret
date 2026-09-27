<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SuperAdminPackageController extends Controller
{
    public function index(): View
    {
        $packages = Package::query()->ordered()->get();

        return view('superadmin.packages.index', compact('packages'));
    }

    public function create(): View
    {
        return view('superadmin.packages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $package = Package::create($this->validatedData($request));
        AdminAuditLog::record($request->user(), 'package.created', $package, ['after' => $package->toArray()]);

        return redirect()->route('superadmin.packages.index')->with('success', 'Paket berhasil dibuat.');
    }

    public function edit(Package $package): View
    {
        return view('superadmin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $before = $package->toArray();
        $data = $this->validatedData($request, $package);

        if ($package->is_legacy) {
            $data['is_active'] = false;
            $data['is_legacy'] = true;
        }

        $data['revision'] = $package->revision + 1;
        $package->update($data);
        AdminAuditLog::record($request->user(), 'package.updated', $package, ['before' => $before, 'after' => $package->fresh()->toArray()]);

        return redirect()->route('superadmin.packages.index')->with('success', 'Paket berhasil diperbarui.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?Package $package = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:50', Rule::unique('packages')->ignore($package)],
            'display_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'features_text' => ['nullable', 'string', 'max:5000'],
            'harga' => ['nullable', 'integer', 'min:0', 'max:2000000000'],
            'currency' => ['required', 'string', 'size:3'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'storage_quota_mb' => ['nullable', 'numeric', 'min:0', 'max:1048576'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
            'is_trial' => ['nullable', 'boolean'],
            'is_custom' => ['nullable', 'boolean'],
        ]);

        $isCustom = $request->boolean('is_custom');
        if (! $isCustom && ($data['duration_days'] ?? null) === null) {
            throw ValidationException::withMessages(['duration_days' => 'Durasi wajib diisi untuk paket non-custom.']);
        }
        if (! $isCustom && ($data['storage_quota_mb'] ?? null) === null) {
            throw ValidationException::withMessages(['storage_quota_mb' => 'Kuota storage wajib diisi untuk paket non-custom.']);
        }

        $storageMb = $data['storage_quota_mb'] ?? null;

        return [
            'code' => Str::lower($data['code']),
            'nama_paket' => $data['display_name'],
            'display_name' => $data['display_name'],
            'description' => $data['description'] ?? null,
            'features' => collect(preg_split('/\r\n|\r|\n/', $data['features_text'] ?? ''))
                ->map(fn (string $feature): string => trim($feature))->filter()->values()->all(),
            'harga' => $isCustom ? 0 : (int) ($data['harga'] ?? 0),
            'currency' => Str::upper($data['currency']),
            'duration_days' => $isCustom ? null : (int) $data['duration_days'],
            'billing_period' => $isCustom ? null : $data['duration_days'].' hari',
            'kuota_storage_mb' => $isCustom ? 0 : (int) round((float) $storageMb),
            'storage_quota_bytes' => $isCustom ? null : (int) round((float) $storageMb * 1048576),
            'is_active' => $request->boolean('is_active'),
            'is_trial' => $request->boolean('is_trial'),
            'is_custom' => $isCustom,
            'is_legacy' => false,
            'sort_order' => (int) $data['sort_order'],
            'revision' => $package?->revision ?? 1,
            'bisa_custom_watermark' => true,
            'bisa_broadcast_lokasi' => false,
        ];
    }
}
