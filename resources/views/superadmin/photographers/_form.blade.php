@php($editing = isset($photographer))

<div class="grid gap-5 lg:grid-cols-2">
    <div><label for="name">Nama</label><input id="name" name="name" value="{{ old('name', $photographer->name ?? '') }}" required class="mt-1 w-full rounded-lg border-gray-300"></div>
    <div><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $photographer->email ?? '') }}" required class="mt-1 w-full rounded-lg border-gray-300"></div>
    <div><label for="studio_name">Nama studio</label><input id="studio_name" name="studio_name" value="{{ old('studio_name', $photographer->studio_name ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300"></div>
    <div><label for="slug">Slug publik</label><input id="slug" name="slug" value="{{ old('slug', $photographer->slug ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="dibuat otomatis jika kosong"></div>
    <div><label for="location">Lokasi</label><input id="location" name="location" value="{{ old('location', $photographer->location ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300"></div>
    <div><label for="category">Kategori</label><input id="category" name="category" value="{{ old('category', $photographer->category ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300"></div>
    <div><label for="whatsapp">WhatsApp</label><input id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $photographer->whatsapp ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300"></div>
    <div><label for="instagram_username">Instagram</label><div class="mt-1 flex rounded-lg border border-gray-300 bg-white"><span class="grid w-11 place-items-center border-r text-gray-500">@</span><input id="instagram_username" name="instagram_username" value="{{ old('instagram_username', $photographer->instagram_username ?? '') }}" class="w-full border-0 rounded-r-lg focus:ring-black"></div></div>
    <div><label for="package_id">Paket</label><select id="package_id" name="package_id" class="mt-1 w-full rounded-lg border-gray-300"><option value="">Tanpa paket</option>@foreach($packages as $package)<option value="{{ $package->id }}" @selected((string) old('package_id', $photographer->package_id ?? '') === (string) $package->id)>{{ $package->display_name ?? $package->nama_paket }}</option>@endforeach</select></div>
    <div></div>
    <div><label for="password">{{ $editing ? 'Password baru' : 'Password' }}</label><input id="password" type="password" name="password" {{ $editing ? '' : 'required' }} class="mt-1 w-full rounded-lg border-gray-300"><p class="helper mt-1">{{ $editing ? 'Kosongkan jika tidak diubah.' : 'Minimum 8 karakter.' }}</p></div>
    <div><label for="password_confirmation">Konfirmasi password</label><input id="password_confirmation" type="password" name="password_confirmation" {{ $editing ? '' : 'required' }} class="mt-1 w-full rounded-lg border-gray-300"></div>
    <div class="lg:col-span-2"><label for="bio">Bio</label><textarea id="bio" name="bio" rows="4" class="mt-1 w-full rounded-lg border-gray-300">{{ old('bio', $photographer->bio ?? '') }}</textarea></div>
</div>

<fieldset class="mt-7 rounded-xl border bg-gray-50 p-5">
    <legend class="px-2 text-sm font-extrabold">Informasi pembayaran</legend>
    <div class="grid gap-5 md:grid-cols-3">
        <div><label for="bank_name">Bank / e-wallet</label><input id="bank_name" name="bank_name" value="{{ old('bank_name', $photographer->bank_name ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300"></div>
        <div><label for="bank_account_name">Nama pemilik</label><input id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $photographer->bank_account_name ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300"></div>
        <div><label for="bank_account_number">Nomor rekening</label><input id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $photographer->bank_account_number ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300"></div>
    </div>
</fieldset>

@unless($editing)
    <div class="mt-6 flex flex-wrap gap-5 rounded-xl border bg-gray-50 p-5 text-sm font-semibold">
        <label class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-black focus:ring-black" @checked(old('is_active', true))> Akun aktif</label>
        <label class="flex items-center gap-2"><input type="hidden" name="is_verified" value="0"><input type="checkbox" name="is_verified" value="1" class="rounded border-gray-300 text-black focus:ring-black" @checked(old('is_verified', false))> Langsung approved</label>
    </div>
@endunless

