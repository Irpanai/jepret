@php
    $editing = isset($package);
    $features = old('features_text', $editing ? implode(PHP_EOL, $package->features ?? []) : '');
    $storageMb = old('storage_quota_mb', $editing ? $package->storageQuotaMb() : null);
@endphp

@if($errors->any())
    <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>
@endif

<div class="grid gap-5 rounded-2xl border bg-white p-6 md:grid-cols-2">
    <div>
        <label for="display_name">Nama paket</label>
        <input id="display_name" class="mt-1 w-full rounded-lg border-gray-300" name="display_name" value="{{ old('display_name', $package->display_name ?? '') }}" required @error('display_name') aria-invalid="true" aria-describedby="display_name_error" @enderror>
        @error('display_name')<p id="display_name_error" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="code">Kode stabil <span class="font-normal text-gray-500">(opsional)</span></label>
        <input id="code" class="mt-1 w-full rounded-lg border-gray-300" name="code" value="{{ old('code', $package->code ?? '') }}" placeholder="Otomatis dari nama paket" @readonly($package->is_legacy ?? false) @error('code') aria-invalid="true" aria-describedby="code_error" @enderror>
        <p class="mt-1 text-xs text-gray-500">Huruf kecil dan tanda hubung akan dibuat otomatis.</p>
        @error('code')<p id="code_error" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="description">Deskripsi</label>
        <textarea id="description" class="mt-1 w-full rounded-lg border-gray-300" name="description" rows="3">{{ old('description', $package->description ?? '') }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label for="features_text">Fitur, satu per baris</label>
        <textarea id="features_text" class="mt-1 w-full rounded-lg border-gray-300" name="features_text" rows="6">{{ $features }}</textarea>
    </div>
    <div>
        <label for="harga">Harga</label>
        <input id="harga" class="mt-1 w-full rounded-lg border-gray-300" type="number" min="0" name="harga" value="{{ old('harga', $package->harga ?? 0) }}" @error('harga') aria-invalid="true" aria-describedby="harga_error" @enderror>
        @error('harga')<p id="harga_error" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="currency">Mata uang</label>
        <input id="currency" class="mt-1 w-full rounded-lg border-gray-300 uppercase" name="currency" maxlength="3" value="{{ old('currency', $package->currency ?? 'IDR') }}" required @error('currency') aria-invalid="true" aria-describedby="currency_error" @enderror>
        @error('currency')<p id="currency_error" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="duration_days">Durasi (hari)</label>
        <input id="duration_days" class="mt-1 w-full rounded-lg border-gray-300" type="number" min="1" name="duration_days" value="{{ old('duration_days', $package->duration_days ?? '') }}" @error('duration_days') aria-invalid="true" aria-describedby="duration_days_error" @enderror>
        @error('duration_days')<p id="duration_days_error" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="storage_quota_mb">Storage (MB)</label>
        <input id="storage_quota_mb" class="mt-1 w-full rounded-lg border-gray-300" type="number" min="0" step="0.01" name="storage_quota_mb" value="{{ $storageMb }}" @error('storage_quota_mb') aria-invalid="true" aria-describedby="storage_quota_mb_error" @enderror>
        @error('storage_quota_mb')<p id="storage_quota_mb_error" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="sort_order">Urutan</label>
        <input id="sort_order" class="mt-1 w-full rounded-lg border-gray-300" type="number" min="0" name="sort_order" value="{{ old('sort_order', $package->sort_order ?? 0) }}" required @error('sort_order') aria-invalid="true" aria-describedby="sort_order_error" @enderror>
        @error('sort_order')<p id="sort_order_error" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
    </div>
    <div class="flex flex-wrap items-center gap-5 pt-6">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $package->is_active ?? true))> Aktif</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_trial" value="1" @checked(old('is_trial', $package->is_trial ?? false))> Trial</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_custom" value="1" @checked(old('is_custom', $package->is_custom ?? false))> Custom</label>
        @if($package->is_legacy ?? false)
            <p class="w-full rounded-lg bg-amber-50 p-3 text-sm font-medium leading-6 text-amber-900">
                Paket legacy. Mengaktifkannya akan mengembalikan paket ini ke katalog publik dan checkout baru.
            </p>
        @endif
    </div>
</div>

<div class="flex gap-3">
    <button class="rounded-lg bg-black px-5 py-3 text-sm font-semibold text-white">Simpan paket</button>
    <a href="{{ route('superadmin.packages.index') }}" class="rounded-lg border bg-white px-5 py-3 text-sm font-semibold">Batal</a>
</div>
