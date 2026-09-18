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
                        <img :src="previewUrl" alt="Foto profil" 
                             class="absolute object-cover" 
                             :style="`width: ${zoom}%; height: ${zoom}%; left: -${(zoom - 100) * x / 100}%; top: -${(zoom - 100) * y / 100}%; max-width: none;`">
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
            @foreach(['name'=>'Nama','studio_name'=>'Studio','slug'=>'Slug publik','whatsapp'=>'WhatsApp','location'=>'Lokasi','category'=>'Kategori'] as $field=>$label)
                <div><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm" name="{{ $field }}" value="{{ old($field,$user->$field) }}"></div>
            @endforeach
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
