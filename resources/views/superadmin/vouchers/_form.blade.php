@csrf
@if(isset($voucher)) @method('PUT') @endif
@php
    $selectedPackageIds = collect(old('package_ids', isset($voucher) ? $voucher->subscriptionPackages->modelKeys() : []))->map(fn ($id) => (int) $id)->all();
    $allPackages = session()->hasOldInput('scope')
        ? old('applies_to_all_packages') !== null
        : (isset($voucher) ? $voucher->applies_to_all_subscription_packages : true);
@endphp
<div x-data="{ scope: @js(old('scope', $voucher->scope ?? 'photo')), allPackages: @js((bool) $allPackages) }">
<div class="grid gap-5 md:grid-cols-2">
    <label class="grid gap-2 text-sm font-bold">Kode<input name="code" value="{{ old('code', $voucher->code ?? '') }}" class="rounded-lg border-gray-300 uppercase" required maxlength="50"></label>
    <label class="grid gap-2 text-sm font-bold">Nama<input name="name" value="{{ old('name', $voucher->name ?? '') }}" class="rounded-lg border-gray-300" required></label>
    <label class="grid gap-2 text-sm font-bold">Scope<select name="scope" x-model="scope" class="rounded-lg border-gray-300"><option value="photo">Photo</option><option value="subscription">Subscription</option></select></label>
    <label class="grid gap-2 text-sm font-bold">Jenis diskon<select name="discount_type" class="rounded-lg border-gray-300"><option value="fixed" @selected(old('discount_type', $voucher->discount_type ?? '') === 'fixed')>Nominal</option><option value="percentage" @selected(old('discount_type', $voucher->discount_type ?? '') === 'percentage')>Persentase</option></select></label>
    @foreach([['discount_value','Nilai diskon'],['maximum_discount','Maksimum Diskon (Rp)'],['minimum_order_amount','Minimum transaksi (Rp)'],['usage_limit','Kuota global'],['per_user_limit','Batas per akun']] as [$field,$label])
        <label class="grid gap-2 text-sm font-bold">{{ $label }}<input type="number" min="0" name="{{ $field }}" value="{{ old($field, $voucher->{$field} ?? ($field === 'minimum_order_amount' ? 0 : '')) }}" class="rounded-lg border-gray-300" {{ $field === 'discount_value' ? 'required' : '' }}></label>
    @endforeach
    <label class="grid gap-2 text-sm font-bold">Mulai (WITA)<input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($voucher) && $voucher->starts_at ? $voucher->starts_at->timezone(config('app.display_timezone'))->format('Y-m-d\TH:i') : '') }}" class="rounded-lg border-gray-300"></label>
    <label class="grid gap-2 text-sm font-bold">Berakhir (WITA)<input type="datetime-local" name="expires_at" value="{{ old('expires_at', isset($voucher) && $voucher->expires_at ? $voucher->expires_at->timezone(config('app.display_timezone'))->format('Y-m-d\TH:i') : '') }}" class="rounded-lg border-gray-300"></label>
</div>
<fieldset x-cloak x-show="scope === 'subscription'" class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4 sm:p-5">
    <legend class="px-1 text-sm font-bold text-gray-900">Berlaku untuk Paket</legend>
    <p class="mt-1 text-sm text-gray-500">Pilih semua paket atau batasi voucher ke paket subscription tertentu.</p>
    <label class="mt-4 flex min-h-11 cursor-pointer items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 text-sm font-bold">
        <input type="checkbox" name="applies_to_all_packages" value="1" x-model="allPackages" class="rounded border-gray-300">
        Semua Paket
    </label>
    <div x-show="! allPackages" class="mt-3 grid gap-2 sm:grid-cols-2">
        @forelse($packages as $package)
            <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 text-sm font-semibold">
                <input type="checkbox" name="package_ids[]" value="{{ $package->id }}" @checked(in_array($package->id, $selectedPackageIds, true)) class="rounded border-gray-300">
                <span class="min-w-0"><span class="block truncate">{{ $package->display_name }}</span><span class="block text-xs font-normal text-gray-500">{{ $package->code }}{{ $package->is_active ? '' : ' · Nonaktif' }}</span></span>
            </label>
        @empty
            <p class="rounded-lg border border-dashed border-gray-300 bg-white p-4 text-sm text-gray-500 sm:col-span-2">Belum ada paket subscription yang dapat dipilih.</p>
        @endforelse
    </div>
    @error('package_ids')<p class="mt-3 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>@enderror
</fieldset>
<label class="mt-5 grid gap-2 text-sm font-bold">Deskripsi<textarea name="description" class="rounded-lg border-gray-300" rows="3">{{ old('description', $voucher->description ?? '') }}</textarea></label>
<p class="helper mt-2">Untuk diskon persentase, batas maksimum selalu berupa nominal Rupiah. Waktu mulai dan berakhir dibaca sebagai WITA.</p>
<label class="mt-5 flex min-h-11 items-center gap-3 text-sm font-bold"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $voucher->is_active ?? true)) class="rounded border-gray-300"> Aktifkan voucher segera</label>
@if($errors->any())<div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">{{ $errors->first() }}</div>@endif
<div class="mt-6 flex gap-3"><button class="rounded-lg bg-black px-5 py-3 text-sm font-bold text-white">Simpan voucher</button><a href="{{ route('superadmin.vouchers.index') }}" class="rounded-lg border bg-white px-5 py-3 text-sm font-bold">Batal</a></div>
</div>
