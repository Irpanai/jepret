<x-fg-layout>
    <div class="space-y-7">
        <div>
            <h1>Foto</h1>
            <p class="helper mt-2">Upload banyak file, kelola publikasi, harga, dan watermark pribadi.</p>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm font-semibold text-green-800">{{ session('success') }}</div>
        @endif

        <div x-data="{ clientSuccess: sessionStorage.getItem('flash_success') }" x-init="if (clientSuccess) { sessionStorage.removeItem('flash_success'); setTimeout(() => clientSuccess = null, 5000); }" x-show="clientSuccess" x-cloak class="rounded-lg bg-green-50 p-4 text-sm font-semibold text-green-800" role="status">
            <span x-text="clientSuccess"></span>
        </div>

        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-4 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
        @endif

        <form
            class="grid gap-6 rounded-2xl border bg-white p-6 lg:grid-cols-3"
            method="POST"
            enctype="multipart/form-data"
            action="{{ route('fotografer.photos.store') }}"
            x-data="photoWatermarkEditor({{ Js::from([
                'watermarkUrl' => auth()->user()->custom_watermark_path ? route('fotografer.watermark.preview') : null,
                'usedBytes' => $storageUsage['used_bytes'],
                'quotaBytes' => $storageUsage['quota_bytes'],
            ]) }})"
            @submit.prevent="uploadAll($event)"
        >
            @csrf

            <div class="space-y-5 lg:col-span-2">
                <x-photographer-photo-editor :accepted-photo-extensions="$acceptedPhotoExtensions" />

                <div class="grid gap-4 sm:grid-cols-2">
                    <div x-data="{ eventMode: '{{ count($events) > 0 ? 'existing' : 'new' }}' }" class="sm:col-span-2 rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2.5">
                            <label class="text-sm font-bold text-gray-900">Event Foto <span class="text-red-500">*</span></label>
                            <div class="inline-flex rounded-lg bg-gray-200/80 p-0.5 text-xs font-semibold">
                                <button type="button" @click="eventMode = 'existing'; $refs.newFolderInput.value = ''" :class="eventMode === 'existing' ? 'bg-black text-white shadow-sm' : 'text-gray-600 hover:text-black'" class="rounded-md px-3 py-1.5 transition">Pilih Event yang Ada</button>
                                <button type="button" @click="eventMode = 'new'; $refs.existingSelect.value = ''" :class="eventMode === 'new' ? 'bg-black text-white shadow-sm' : 'text-gray-600 hover:text-black'" class="rounded-md px-3 py-1.5 transition">+ Buat Event Baru</button>
                            </div>
                        </div>

                        <div x-show="eventMode === 'existing'">
                            <select id="event_id" name="event_id" x-ref="existingSelect" class="w-full rounded-lg border-gray-300 bg-white text-sm focus:border-black focus:ring-black">
                                <option value="">-- Pilih event yang sudah ada --</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" @selected(old('event_id') == $event->id)>{{ $event->nama_event }} ({{ $event->photos_count }} foto)</option>
                                @endforeach
                            </select>
                            @if(count($events) === 0)
                                <p class="mt-1.5 text-xs text-amber-700">Belum ada event tersimpan. Silakan pilih tab <strong>+ Buat Event Baru</strong>.</p>
                            @endif
                        </div>

                        <div x-show="eventMode === 'new'" x-cloak>
                            <input id="new_folder" name="new_folder" x-ref="newFolderInput" class="w-full rounded-lg border-gray-300 bg-white text-sm focus:border-black focus:ring-black" placeholder="Contoh: Makassar Half Marathon 2026" value="{{ old('new_folder') }}">
                            <p class="mt-1 text-xs text-gray-500">Event baru akan otomatis dibuatkan folder untuk foto-foto ini.</p>
                        </div>
                    </div>

                    <div>
                        <label for="camera_id" class="text-sm font-semibold text-gray-700">Kamera</label>
                        <select id="camera_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" name="camera_id">
                            <option value="">Tanpa kamera</option>
                            @foreach($cameras as $camera)
                                <option value="{{ $camera->id }}" @selected(old('camera_id') == $camera->id)>{{ $camera->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="harga" class="text-sm font-semibold text-gray-700">Harga (Rp)</label>
                        <input id="harga" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" type="number" name="harga" min="0" value="{{ old('harga', 20000) }}" required>
                    </div>

                    <div>
                        <label for="category" class="text-sm font-semibold text-gray-700">Kategori</label>
                        <input id="category" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" name="category" placeholder="Contoh: Sports, Wedding, Portrait" value="{{ old('category') }}">
                    </div>

                    <div>
                        <label for="taken_at" class="text-sm font-semibold text-gray-700">Waktu ambil (WITA)</label>
                        <input id="taken_at" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" type="datetime-local" name="taken_at" value="{{ old('taken_at') }}">
                    </div>
                </div>
            </div>

            <aside class="space-y-4">
                <div class="space-y-2 border-b border-gray-200 pb-4" aria-live="polite">
                    <div class="flex justify-between gap-3 text-sm"><span class="text-gray-500">Batas penyimpanan</span><strong x-text="formatBytes(quotaBytes)"></strong></div>
                    <div class="flex justify-between gap-3 text-sm"><span class="text-gray-500">Terpakai aktual</span><strong x-text="formatBytes(usedBytes)"></strong></div>
                    <div class="flex justify-between gap-3 text-sm"><span class="text-gray-500">Tersisa</span><strong x-text="formatBytes(remainingBytes)"></strong></div>
                    <div class="flex justify-between gap-3 text-sm"><span class="text-gray-500">Dipilih</span><strong><span x-text="files.length"></span> file · <span x-text="formatBytes(selectedBytes)"></span></strong></div>
                    <div class="flex justify-between gap-3 text-sm"><span class="text-gray-500">Proyeksi sumber</span><strong x-text="formatBytes(projectedBytes)"></strong></div>
                    <p class="text-xs leading-5 text-gray-500">Pemakaian aktual diperbarui setelah original dan seluruh variannya selesai diproses.</p>
                </div>

                @php($currentUser = auth()->user())
                <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-gray-900">Pengaturan Watermark</h2>
                        @if($currentUser->custom_watermark_path && $currentUser->photographer_watermark_locked)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                Terkunci
                            </span>
                        @elseif($currentUser->custom_watermark_path)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                Tersimpan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                Wajib Upload
                            </span>
                        @endif
                    </div>

                    @if($currentUser->custom_watermark_path)
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Watermark profil Anda aktif. Pilih file di bawah jika ingin menggantinya untuk upload ini.
                        </p>
                    @else
                        <p class="text-xs text-amber-800 leading-relaxed">
                            Unggah watermark PNG atau WEBP transparan untuk melindungi preview foto Anda.
                        </p>
                    @endif

                    <div>
                        <input class="w-full text-xs text-gray-600 file:mr-2.5 file:rounded-lg file:border-0 file:bg-gray-200 file:px-3 file:py-1.5 file:text-xs file:font-semibold hover:file:bg-gray-300 cursor-pointer" type="file" name="watermark" accept="image/png,image/webp" @change="selectWatermark($event)">
                    </div>

                    <p class="border-l-4 border-amber-500 bg-white p-3 text-xs text-gray-700" x-show="!watermarkUrl" x-cloak>
                        Pilih watermark agar hasilnya terlihat di preview editor.
                    </p>

                    <label class="flex items-center gap-2.5 text-xs font-medium text-gray-700 cursor-pointer select-none">
                        <input type="checkbox" name="lock_watermark" value="1" @checked($currentUser->photographer_watermark_locked) class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black">
                        <span>Kunci & simpan watermark untuk upload berikutnya</span>
                    </label>
                </div>

                <label class="flex min-h-11 items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 cursor-pointer select-none hover:border-gray-300 transition">
                    <input type="hidden" name="sell_without_watermark" value="0">
                    <input type="checkbox" name="sell_without_watermark" value="1" checked class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black">
                    <span>
                        <strong class="block text-sm text-gray-900">Jual Foto Tanpa Watermark — Aktif</strong>
                        <span class="block text-xs font-normal text-gray-500 leading-relaxed">Pembeli menerima file original. Preview publik tetap terlindungi watermark.</span>
                    </span>
                </label>

                <p class="rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-700" x-show="exceedsQuota" x-cloak>
                    Penyimpanan Anda habis. Ukuran foto yang dipilih melebihi sisa kapasitas sebesar <span x-text="formatBytes(pendingBytes - remainingBytes)"></span>.
                </p>

                <p class="rounded-lg bg-red-50 px-3 py-2.5 text-sm font-semibold text-red-700 border border-red-200" x-show="uploadError" x-text="uploadError" x-cloak role="alert"></p>

                <div>
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-black px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-neutral-800 disabled:cursor-not-allowed disabled:bg-gray-300 flex items-center justify-center gap-2"
                        :disabled="uploading || !hasPendingFiles || exceedsQuota"
                    >
                        <template x-if="!uploading">
                            <span>Upload & publish</span>
                        </template>
                        <template x-if="uploading">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Memproses upload...</span>
                            </span>
                        </template>
                    </button>
                    <p class="mt-2 text-center text-xs text-gray-500" x-show="!hasPendingFiles && !uploading">
                        Pilih setidaknya 1 foto di atas untuk mulai mengunggah.
                    </p>
                </div>

                <div class="space-y-2 pt-1" x-show="uploading || uploadProgress > 0" x-cloak>
                    <div class="flex items-center justify-between text-xs font-semibold text-gray-600">
                        <span>Progress upload</span>
                        <span x-text="`${uploadProgress}%`"></span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-200" role="progressbar" aria-label="Progress upload foto" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="uploadProgress">
                        <span class="block h-full w-full origin-left rounded-full bg-black transition-transform duration-200" :style="`transform: scaleX(${Math.max(0, Math.min(100, uploadProgress)) / 100})`"></span>
                    </div>
                </div>
            </aside>
        </form>
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
