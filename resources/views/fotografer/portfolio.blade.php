<x-fg-layout>
<div class="space-y-7">
    <div><h1>Profil & Portofolio</h1><p class="helper mt-2">Profil publik dan karya terbaru dari data akun Anda.</p></div>
    @if(session('success'))<div class="rounded-lg bg-green-50 p-4 text-sm">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <div class="grid gap-6 lg:grid-cols-2">
        <form class="space-y-4 rounded-2xl border bg-white p-6" method="POST" enctype="multipart/form-data" action="{{ route('fotografer.portfolio.update') }}">
            @csrf @method('PATCH')
            <h2>Edit profil</h2>
            <div x-data="{
                    previewUrl: '{{ $user->profilePhotoUrl() }}',
                    hasFile: false,
                    zoom: 100,
                    x: 50,
                    y: 50,
                    fileSelected(e) {
                        if (e.target.files.length > 0) {
                            this.previewUrl = URL.createObjectURL(e.target.files[0]);
                            this.hasFile = true;
                            this.zoom = 100;
                            this.x = 50;
                            this.y = 50;
                        }
                    }
                }">
                <div class="flex items-center gap-4 rounded-xl bg-gray-50 p-4">
                    <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-full bg-gray-200">
                        <img :src="previewUrl" alt="Foto profil" class="absolute object-cover" :style="`width: ${zoom}%; height: ${zoom}%; left: -${(zoom - 100) * x / 100}%; top: -${(zoom - 100) * y / 100}%; max-width: none;`">
                    </div>
                    <div class="min-w-0 flex-1">
                        <label for="profile_photo">Foto profil</label>
                        <input id="profile_photo" class="mt-2 block w-full text-sm" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" @change="fileSelected">
                        <p class="helper mt-2">JPG, PNG, atau WEBP. Maksimum 5 MB.</p>
                    </div>
                </div>
                <div x-show="hasFile" class="mt-4 space-y-4 rounded-xl bg-gray-50 p-4" style="display: none;">
                    <div>
                        <label class="text-xs font-bold text-gray-500">Zoom (<span x-text="zoom"></span>%)</label>
                        <input type="range" name="profile_photo_zoom" x-model="zoom" min="100" max="200" class="mt-2 w-full">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500">Geser Horizontal (<span x-text="x"></span>%)</label>
                            <input type="range" name="profile_photo_x" x-model="x" min="0" max="100" class="mt-2 w-full">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500">Geser Vertikal (<span x-text="y"></span>%)</label>
                            <input type="range" name="profile_photo_y" x-model="y" min="0" max="100" class="mt-2 w-full">
                        </div>
                    </div>
                </div>
            </div>
            @foreach(['name'=>'Nama','studio_name'=>'Studio','slug'=>'Slug publik','location'=>'Lokasi','category'=>'Kategori'] as $field=>$label)
                <div><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm" name="{{ $field }}" value="{{ old($field,$user->$field) }}"></div>
            @endforeach
            <fieldset class="space-y-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <legend class="px-2 text-sm font-extrabold text-black">Social links</legend>
                <div>
                    <label for="whatsapp">WhatsApp</label>
                    <div class="mt-1 flex rounded-lg border border-gray-300 bg-white focus-within:border-black">
                        <span class="grid w-12 shrink-0 place-items-center border-r border-gray-200 text-green-600" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a9.75 9.75 0 0 0-8.42 14.65L2.25 21.5l4.96-1.3A9.75 9.75 0 1 0 12 2Zm5.68 13.96c-.24.68-1.4 1.3-1.94 1.38-.5.08-1.12.11-1.81-.11-.42-.14-.96-.32-1.65-.62-2.9-1.25-4.79-4.18-4.94-4.37-.14-.2-1.18-1.57-1.18-3 0-1.42.74-2.12 1.01-2.41.26-.29.57-.36.76-.36h.55c.18 0 .41-.07.64.49.24.58.81 1.99.88 2.13.07.15.12.32.02.51-.09.2-.14.32-.28.49-.14.17-.3.38-.43.51-.14.15-.29.3-.12.59.16.29.73 1.2 1.57 1.94 1.08.96 1.99 1.26 2.27 1.4.28.15.45.13.62-.07.16-.19.71-.83.9-1.12.19-.29.38-.24.64-.14.26.09 1.66.78 1.94.93.29.14.48.21.55.33.07.12.07.68-.17 1.36Z"/></svg>
                        </span>
                        <input id="whatsapp" class="w-full border-0 bg-transparent text-sm focus:ring-0" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" placeholder="Contoh: 628123456789">
                    </div>
                    <p class="helper mt-1">Gunakan nomor aktif dengan kode negara.</p>
                </div>
                <div>
                    <label for="instagram_username">Instagram</label>
                    <div class="mt-1 flex rounded-lg border border-gray-300 bg-white focus-within:border-black">
                        <span class="grid w-12 shrink-0 place-items-center border-r border-gray-200 text-base font-bold text-gray-500" aria-hidden="true">@</span>
                        <input id="instagram_username" class="w-full border-0 bg-transparent text-sm focus:ring-0" name="instagram_username" value="{{ old('instagram_username', $user->instagram_username) }}" placeholder="winantioo">
                    </div>
                    <p class="helper mt-1">Masukkan username tanpa URL Instagram.</p>
                </div>
            </fieldset>
            <div><label for="bio">Bio</label><textarea id="bio" class="mt-1 w-full rounded-lg border-gray-300 text-sm" name="bio" rows="5">{{ old('bio',$user->bio) }}</textarea></div>
            <button class="rounded-lg bg-black px-5 py-3 text-white">Simpan profil</button>
        </form>
        <section class="rounded-2xl border bg-white p-6">
            <div class="flex items-center gap-4"><img src="{{ $user->profilePhotoUrl() }}" alt="Foto profil {{ $user->name }}" class="h-20 w-20 rounded-full object-cover"><div><h2>{{ $user->studio_name ?: $user->name }}</h2><p class="mt-1 text-sm text-gray-600">{{ $user->category ?: 'Photographer' }}</p></div></div>
            <p class="mt-5 text-sm text-gray-600">{{ $user->bio ?: 'Bio belum diisi.' }}</p>
            <dl class="mt-6 grid grid-cols-2 gap-4 text-sm"><div><dt class="text-gray-500">Lokasi</dt><dd class="font-bold">{{ $user->location ?: '—' }}</dd></div><div><dt class="text-gray-500">Verifikasi</dt><dd class="font-bold uppercase">{{ $user->verificationState() }}</dd></div></dl>
            @if($user->is_verified)<a class="button mt-6 inline-block rounded-lg border px-4 py-2" href="{{ route('photographers.show',$user->slug ?: $user->id) }}">Lihat profil publik</a>@else<p class="helper mt-6">Profil publik tersedia setelah akun disetujui Super Admin.</p>@endif
        </section>
    </div>
    <section><h2>Karya terbaru</h2><div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">@forelse($photos as $photo)<article class="overflow-hidden rounded-xl border bg-white"><img class="aspect-[4/3] w-full object-cover" src="{{ route('media.preview',$photo) }}" alt="{{ $photo->title }}"><div class="p-4"><h3>{{ $photo->title }}</h3><p class="helper">{{ $photo->event?->nama_event }} · {{ ($photo->taken_at ?? $photo->created_at)->format('d M Y') }}</p></div></article>@empty<p class="helper">Belum ada karya.</p>@endforelse</div><div class="mt-4">{{ $photos->links() }}</div></section>
</div>
</x-fg-layout>
