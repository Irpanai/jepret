<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use App\Models\PartnerLogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SuperAdminPartnerLogoController extends Controller
{
    public function index(): View
    {
        $logos = PartnerLogo::query()->ordered()->get();

        return view('superadmin.partner-logos.index', compact('logos'));
    }

    public function create(): View
    {
        return view('superadmin.partner-logos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('logo')->store('partner-logos', 'public');

        $logo = PartnerLogo::create([
            'name' => $validated['name'],
            'logo_path' => $path,
            'url' => $validated['url'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        AdminAuditLog::record($request->user(), 'partner_logo.created', $logo, ['after' => $logo->toArray()]);

        return redirect()->route('superadmin.partner-logos.index')->with('success', 'Logo partner berhasil ditambahkan.');
    }

    public function edit(PartnerLogo $partnerLogo): View
    {
        return view('superadmin.partner-logos.edit', compact('partnerLogo'));
    }

    public function update(Request $request, PartnerLogo $partnerLogo): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $before = $partnerLogo->toArray();

        if ($request->hasFile('logo')) {
            if ($partnerLogo->logo_path && Storage::disk('public')->exists($partnerLogo->logo_path)) {
                Storage::disk('public')->delete($partnerLogo->logo_path);
            }
            $partnerLogo->logo_path = $request->file('logo')->store('partner-logos', 'public');
        }

        $partnerLogo->name = $validated['name'];
        $partnerLogo->url = $validated['url'] ?? null;
        $partnerLogo->sort_order = (int) ($validated['sort_order'] ?? 0);
        $partnerLogo->is_active = $request->boolean('is_active');
        $partnerLogo->save();

        AdminAuditLog::record($request->user(), 'partner_logo.updated', $partnerLogo, [
            'before' => $before,
            'after' => $partnerLogo->fresh()->toArray(),
        ]);

        return redirect()->route('superadmin.partner-logos.index')->with('success', 'Logo partner berhasil diperbarui.');
    }

    public function destroy(Request $request, PartnerLogo $partnerLogo): RedirectResponse
    {
        $before = $partnerLogo->toArray();

        if ($partnerLogo->logo_path && Storage::disk('public')->exists($partnerLogo->logo_path)) {
            Storage::disk('public')->delete($partnerLogo->logo_path);
        }

        $partnerLogo->delete();

        AdminAuditLog::record($request->user(), 'partner_logo.deleted', $partnerLogo, ['before' => $before]);

        return redirect()->route('superadmin.partner-logos.index')->with('success', 'Logo partner berhasil dihapus.');
    }
}
