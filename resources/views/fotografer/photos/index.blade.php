<x-fg-layout>
    <div class="space-y-7">
        <div>
            <h1>Foto</h1>
            <p class="helper mt-2">Upload banyak file, kelola publikasi, harga, dan watermark pribadi.</p>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <section class="rounded-2xl border bg-white p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2>Photographer Watermark</h2>
                    <p class="helper mt-1">
                        Status: <strong>{{ auth()->user()->photographer_watermark_locked ? 'Locked' : 'Unlocked' }}</strong>.
                        {{ auth()->user()->photographer_watermark_locked ? 'Watermark tersimpan dipakai untuk upload baru.' : 'Upload berikutnya wajib menyertakan watermark.' }}
                    </p>
                </div>
                <form class="flex flex-wrap gap-2" method="POST" enctype="multipart/form-data" action="{{ route('fotografer.watermark.update') }}">
                    @csrf
                    @method('PATCH')
                    <input class="text-sm" type="file" name="watermark" accept="image/png,image/webp">
                    <button class="rounded border px-4 py-2" name="locked" value="1">Upload / Lock</button>
                    <button class="rounded border px-4 py-2" name="locked" value="0">Unlock</button>
                </form>
            </div>
        </section>

        <form
            class="grid gap-6 rounded-2xl border bg-white p-6 lg:grid-cols-3"
            method="POST"
            enctype="multipart/form-data"
            action="{{ route('fotografer.photos.store') }}"
            x-data="photoWatermarkEditor({{ Js::from(auth()->user()->custom_watermark_path ? route('fotografer.watermark.preview') : null) }})"
        >
            @csrf

            <div class="space-y-5 lg:col-span-2">
                <x-photographer-photo-editor />

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="event_id">Event</label>
                        <select id="event_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm" name="event_id">
                            <option value="">Pilih event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="new_folder">Atau event baru</label>
                        <input id="new_folder" class="mt-1 w-full rounded-lg border-gray-300 text-sm" name="new_folder">
                    </div>
                    <div>
                        <label for="camera_id">Kamera</label>
                        <select id="camera_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm" name="camera_id">
                            <option value="">Tanpa kamera</option>
                            @foreach($cameras as $camera)
                                <option value="{{ $camera->id }}">{{ $camera->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="harga">Harga</label>
                        <input id="harga" class="mt-1 w-full rounded-lg border-gray-300 text-sm" type="number" name="harga" min="0" value="{{ old('harga', 20000) }}" required>
                    </div>
                    <div>
                        <label for="category">Kategori</label>
                        <input id="category" class="mt-1 w-full rounded-lg border-gray-300 text-sm" name="category">
                    </div>
                    <div>
                        <label for="taken_at">Waktu ambil</label>
                        <input id="taken_at" class="mt-1 w-full rounded-lg border-gray-300 text-sm" type="datetime-local" name="taken_at">
                    </div>
                </div>
            </div>

            <aside class="space-y-4">
                <h2>Watermark batch</h2>
                <p class="helper">PNG/WEBP transparan. Kosongkan bila watermark tersimpan sudah locked.</p>
                <input class="w-full text-sm" type="file" name="watermark" accept="image/png,image/webp" @change="selectWatermark($event)">
                <p class="border-l-4 border-amber-500 bg-white p-3 text-sm text-gray-700" x-show="!watermarkUrl">Pilih watermark agar hasilnya terlihat di editor.</p>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="lock_watermark" value="1">
                    Simpan dan lock untuk upload berikutnya
                </label>
                <button class="w-full rounded-lg bg-black px-5 py-3 text-white">Upload &amp; publish</button>
            </aside>
        </form>

        <section class="space-y-5" aria-labelledby="catalog-title">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 id="catalog-title">Katalog</h2>
                    <p class="helper mt-1">{{ $photos->total() }} foto ditemukan</p>
                </div>
                @if(request()->hasAny(['q', 'event', 'camera', 'category', 'status', 'sort']))
                    <a href="{{ route('fotografer.photos.index') }}" class="text-sm font-bold text-gray-600 underline decoration-gray-300 underline-offset-4 hover:text-black">Reset pencarian</a>
                @endif
            </div>

            <form method="GET" action="{{ route('fotografer.photos.index') }}" class="rounded-2xl border bg-white p-4 sm:p-5" role="search">
                <div class="grid gap-3 lg:grid-cols-[minmax(16rem,2fr)_repeat(3,minmax(0,1fr))]">
                    <div class="relative">
                        <label class="sr-only" for="catalog-search">Cari katalog</label>
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
                        <input id="catalog-search" name="q" value="{{ request('q') }}" class="h-11 w-full rounded-lg border-gray-300 pl-10 text-sm" type="search" placeholder="Cari nama foto, file, tag, event, atau kamera">
                    </div>

                    <div>
                        <label class="sr-only" for="catalog-event">Filter event</label>
                        <select id="catalog-event" name="event" class="h-11 w-full rounded-lg border-gray-300 text-sm">
                            <option value="">Semua event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" @selected((string) request('event') === (string) $event->id)>{{ $event->nama_event }} ({{ $event->photos_count }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="sr-only" for="catalog-camera">Filter kamera</label>
                        <select id="catalog-camera" name="camera" class="h-11 w-full rounded-lg border-gray-300 text-sm">
                            <option value="">Semua kamera</option>
                            @foreach($cameras as $camera)
                                <option value="{{ $camera->id }}" @selected((string) request('camera') === (string) $camera->id)>{{ $camera->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="sr-only" for="catalog-category">Filter kategori</label>
                        <select id="catalog-category" name="category" class="h-11 w-full rounded-lg border-gray-300 text-sm">
                            <option value="">Semua kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto]">
                    <div>
                        <label class="sr-only" for="catalog-status">Filter status</label>
                        <select id="catalog-status" name="status" class="h-11 w-full rounded-lg border-gray-300 text-sm">
                            <option value="">Semua status</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="sr-only" for="catalog-sort">Urutkan katalog</label>
                        <select id="catalog-sort" name="sort" class="h-11 w-full rounded-lg border-gray-300 text-sm">
                            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Terbaru</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
                            <option value="title" @selected(request('sort') === 'title')>Nama A–Z</option>
                            <option value="price_low" @selected(request('sort') === 'price_low')>Harga terendah</option>
                            <option value="price_high" @selected(request('sort') === 'price_high')>Harga tertinggi</option>
                        </select>
                    </div>

                    <button class="h-11 rounded-lg bg-black px-6 text-sm font-bold text-white hover:bg-gray-800">Terapkan</button>
                </div>
            </form>

            <div class="mt-4 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($photos as $photo)
                    <article class="overflow-hidden rounded-xl border bg-white">
                        <img class="aspect-video w-full object-cover" src="{{ route('media.preview', $photo) }}" alt="{{ $photo->title }}">
                        <div class="space-y-3 p-4">
                            <div>
                                <h3>{{ $photo->title }}</h3>
                                <p class="helper">{{ $photo->event?->nama_event }} · {{ number_format(($photo->storage_bytes ?? 0) / 1048576, 2) }} MB</p>
                            </div>
                            <form class="grid grid-cols-2 gap-2" method="POST" action="{{ route('fotografer.photos.update', $photo) }}">
                                @csrf
                                @method('PUT')
                                <input class="rounded border-gray-300 text-sm" name="title" value="{{ $photo->title }}">
                                <input class="rounded border-gray-300 text-sm" type="number" name="harga" value="{{ $photo->harga }}">
                                <select class="rounded border-gray-300 text-sm" name="status">
                                    <option @selected($photo->status === 'active') value="active">Active</option>
                                    <option @selected($photo->status === 'inactive') value="inactive">Inactive</option>
                                </select>
                                <button class="rounded bg-black px-3 py-2 text-white">Simpan</button>
                            </form>
                            <form method="POST" action="{{ route('fotografer.photos.destroy', $photo) }}" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-700">Hapus</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed bg-white p-8 sm:col-span-2 xl:col-span-3">
                        <p class="font-bold">Tidak ada foto yang cocok.</p>
                        <p class="helper mt-1">Ubah kata pencarian atau reset filter katalog.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">{{ $photos->links() }}</div>
        </section>
    </div>
</x-fg-layout>
