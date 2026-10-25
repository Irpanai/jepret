<x-superadmin-layout>
    <div class="max-w-2xl space-y-6">
        <div>
            <a href="{{ route('superadmin.partner-logos.index') }}" class="text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-black">
                &larr; Kembali ke Daftar Logo
            </a>
            <h1 class="mt-2 text-2xl font-black text-black">Edit Logo Partner: {{ $partnerLogo->name }}</h1>
            <p class="helper mt-1">Perbarui logo, tautan, dan urutan tampilan pada marquee landing page.</p>
        </div>

        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-800 border border-red-200">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.partner-logos.update', $partnerLogo) }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-bold text-gray-900">Nama Partner / Komunitas / Brand <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $partnerLogo->name) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-black focus:ring-black text-sm font-semibold">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-900">Logo Saat Ini</label>
                <div class="flex h-16 w-36 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 p-2">
                    <img src="{{ $partnerLogo->logoUrl() }}" alt="{{ $partnerLogo->name }}" class="max-h-full max-w-full object-contain filter grayscale contrast-125">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="logo" class="block text-sm font-bold text-gray-900">Ganti File Logo (Opsional)</label>
                <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:uppercase file:tracking-wider file:bg-black file:text-white hover:file:bg-neutral-800">
                <p class="text-xs text-gray-400">Kosongkan jika tidak ingin mengganti logo yang sudah ada.</p>
            </div>

            <div class="space-y-1.5">
                <label for="url" class="block text-sm font-bold text-gray-900">Website / Tautan Resmi (Opsional)</label>
                <input type="url" id="url" name="url" value="{{ old('url', $partnerLogo->url) }}" placeholder="https://example.com" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-black focus:ring-black text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="sort_order" class="block text-sm font-bold text-gray-900">Urutan Tampil (Sort Order)</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $partnerLogo->sort_order) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-black focus:ring-black text-sm font-semibold">
                </div>

                <div class="flex items-center pt-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $partnerLogo->is_active ? '1' : '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                        <span class="ml-3 text-sm font-bold text-gray-900">Aktifkan di Marquee</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.partner-logos.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-xs font-extrabold uppercase tracking-wider text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-black px-6 py-2.5 text-xs font-extrabold uppercase tracking-wider text-white shadow hover:bg-neutral-800">
                    Perbarui Logo
                </button>
            </div>
        </form>
    </div>
</x-superadmin-layout>
