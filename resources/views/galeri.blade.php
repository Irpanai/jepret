@php
    $structuredData = ['@context' => 'https://schema.org', '@type' => 'CollectionPage', 'name' => 'Galeri Foto Event Jepret', 'description' => 'Foto event, lokasi, dan photographer dari komunitas Jepret.', 'url' => route('galeri')];
    $hasFilters = request()->hasAny(['q', 'event', 'photographer', 'category', 'location', 'daypart', 'date', 'sort']);
@endphp

<x-marketplace-layout title="Galeri Foto Event | Jepret" description="Jelajahi foto event Jepret berdasarkan event, photographer, kategori, lokasi, tanggal, dan waktu. Preview terlindungi, original terbuka setelah pembelian." :canonical="route('galeri')" :og-image="asset('images/galeri.jpeg')" :structured-data="$structuredData">
    <div class="gallery-page" data-gallery-page>
        <div
            x-data="{ visible: false, message: '', timer: null }"
            @cart:added.window="message = $event.detail.message; visible = true; clearTimeout(timer); timer = setTimeout(() => visible = false, 2400)"
            x-cloak
            x-show="visible"
            x-transition
            class="fixed bottom-5 right-5 z-[70] flex items-center gap-3 bg-public-ink px-5 py-4 text-sm font-bold text-white shadow-xl"
            role="status"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
            <span x-text="message"></span>
        </div>
        @if(session('success'))
            <div class="border-b border-public-line bg-public-ink py-3 text-white">
                <div class="public-container flex items-center justify-between gap-4 text-xs font-bold">
                    <p>{{ session('success') }}</p>
                    <a href="{{ route('cart.index') }}" class="shrink-0 uppercase underline underline-offset-4">Lihat keranjang</a>
                </div>
            </div>
        @endif
        <section class="gallery-hero relative overflow-hidden bg-neutral-200 text-public-ink" data-gallery-hero>
            <div class="gallery-hero-media absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/galeri.jpeg') }}')" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-white/85 via-white/45 to-white/15" aria-hidden="true"></div>
            <div class="public-container relative flex min-h-[190px] items-center justify-between gap-8 py-7 sm:min-h-[225px] sm:py-8">
                <div class="max-w-xl">
                    <p class="gallery-hero-copy text-[10px] font-bold uppercase tracking-[0.22em] sm:text-xs">Momen, Orang, Kota</p>
                    <h1 class="gallery-title-mask mt-2"><span class="gallery-title public-heading block">Galeri.</span></h1>
                    <div class="gallery-hero-copy mt-3 max-w-md text-xs font-medium leading-5 sm:text-sm">
                        <p>Temukan cerita di balik setiap jepretan.</p>
                        <p>Foto dari berbagai event, lokasi, dan photographer di komunitas Jepret.</p>
                    </div>
                </div>
                <div class="gallery-hero-copy hidden items-stretch gap-4 pr-2 sm:flex" aria-hidden="true">
                    <span class="w-px bg-black/45"></span>
                    <p class="text-[9px] font-semibold uppercase leading-[1.7] tracking-[0.25em] sm:text-[10px]">People<br>Events<br>Stories<br>Forever</p>
                </div>
            </div>
        </section>

        <section class="relative z-10 border-b border-public-line bg-white" aria-label="Filter galeri">
            <div class="public-container py-3 sm:py-4">
                <form method="GET" action="{{ route('galeri') }}" class="gallery-filter" data-gallery-filter x-data="{ filtersOpen: false }">
                    <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-2 lg:grid-cols-[minmax(280px,2.5fr)_repeat(5,minmax(105px,1fr))]">
                        <label class="relative block">
                            <span class="sr-only">Cari foto</span>
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-public-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari event, lokasi, photographer, atau momen..." class="public-input h-11 rounded-md py-2 pl-10 pr-3 text-xs">
                        </label>
                        <button type="button" class="inline-flex h-11 items-center gap-2 rounded-md border border-public-line bg-white px-4 text-xs font-bold lg:hidden" @click="filtersOpen = ! filtersOpen" :aria-expanded="filtersOpen.toString()" aria-controls="gallery-mobile-filters">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>Filter
                        </button>

                        <select name="event" class="public-input hidden h-11 rounded-md px-3 text-xs lg:block" aria-label="Event"><option value="">Event</option>@foreach($events as $event)<option value="{{ $event->id }}" @selected((string) request('event') === (string) $event->id)>{{ $event->nama_event }}</option>@endforeach</select>
                        <select name="location" class="public-input hidden h-11 rounded-md px-3 text-xs lg:block" aria-label="Lokasi"><option value="">Lokasi</option>@foreach($locations as $location)<option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>@endforeach</select>
                        <select name="category" class="public-input hidden h-11 rounded-md px-3 text-xs lg:block" aria-label="Kategori"><option value="">Kategori</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
                        <select name="photographer" class="public-input hidden h-11 rounded-md px-3 text-xs lg:block" aria-label="Photographer"><option value="">Photographer</option>@foreach($photographers as $photographer)<option value="{{ $photographer->id }}" @selected((string) request('photographer') === (string) $photographer->id)>{{ $photographer->studio_name ?: $photographer->name }}</option>@endforeach</select>
                        <select name="sort" class="public-input hidden h-11 rounded-md px-3 text-xs lg:block" aria-label="Urutkan"><option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru</option><option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option><option value="price_low" @selected(request('sort') === 'price_low')>Harga Terendah</option><option value="price_high" @selected(request('sort') === 'price_high')>Harga Tertinggi</option></select>
                    </div>

                    <div id="gallery-mobile-filters" x-cloak x-show="filtersOpen" class="mt-3 grid gap-2 rounded-lg border border-public-line bg-public-bone p-3 lg:hidden">
                        <select name="event" class="public-input h-11 rounded-md px-3 text-xs"><option value="">Semua Event</option>@foreach($events as $event)<option value="{{ $event->id }}" @selected((string) request('event') === (string) $event->id)>{{ $event->nama_event }}</option>@endforeach</select>
                        <select name="location" class="public-input h-11 rounded-md px-3 text-xs"><option value="">Semua Lokasi</option>@foreach($locations as $location)<option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>@endforeach</select>
                        <select name="category" class="public-input h-11 rounded-md px-3 text-xs"><option value="">Semua Kategori</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
                        <select name="photographer" class="public-input h-11 rounded-md px-3 text-xs"><option value="">Semua Photographer</option>@foreach($photographers as $photographer)<option value="{{ $photographer->id }}" @selected((string) request('photographer') === (string) $photographer->id)>{{ $photographer->studio_name ?: $photographer->name }}</option>@endforeach</select>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" name="date" value="{{ request('date') }}" class="public-input h-11 rounded-md px-2 text-xs" aria-label="Tanggal">
                            <select name="daypart" class="public-input h-11 rounded-md px-2 text-xs" aria-label="Waktu"><option value="">Semua Waktu</option><option value="morning" @selected(request('daypart') === 'morning')>Pagi</option><option value="afternoon" @selected(request('daypart') === 'afternoon')>Siang</option><option value="evening" @selected(request('daypart') === 'evening')>Sore</option><option value="night" @selected(request('daypart') === 'night')>Malam</option></select>
                        </div>
                        <select name="sort" class="public-input h-11 rounded-md px-3 text-xs"><option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru</option><option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option><option value="price_low" @selected(request('sort') === 'price_low')>Harga Terendah</option><option value="price_high" @selected(request('sort') === 'price_high')>Harga Tertinggi</option></select>
                    </div>

                    <div class="mt-2 hidden items-center justify-between gap-3 lg:flex">
                        <div class="flex items-center gap-2">
                            <input type="date" name="date" value="{{ request('date') }}" class="public-input h-9 w-auto rounded-md px-2 text-[11px]" aria-label="Tanggal">
                            <select name="daypart" class="public-input h-9 w-auto rounded-md px-2 text-[11px]" aria-label="Waktu"><option value="">Semua Waktu</option><option value="morning" @selected(request('daypart') === 'morning')>Pagi</option><option value="afternoon" @selected(request('daypart') === 'afternoon')>Siang</option><option value="evening" @selected(request('daypart') === 'evening')>Sore</option><option value="night" @selected(request('daypart') === 'night')>Malam</option></select>
                        </div>
                        <div class="flex items-center gap-3">@if($hasFilters)<a href="{{ route('galeri') }}" class="text-[10px] font-bold uppercase tracking-wider text-public-muted underline">Reset</a>@endif<button class="rounded-md bg-public-ink px-5 py-2.5 text-[10px] font-bold uppercase tracking-wider text-white">Terapkan</button></div>
                    </div>
                    <div class="mt-3 flex items-center gap-3 lg:hidden">@if($hasFilters)<a href="{{ route('galeri') }}" class="text-[10px] font-bold uppercase tracking-wider text-public-muted underline">Reset</a>@endif<button class="ml-auto rounded-md bg-public-ink px-5 py-2.5 text-[10px] font-bold uppercase tracking-wider text-white">Terapkan</button></div>
                </form>
            </div>
        </section>

        <section class="bg-white pb-14 pt-5 sm:pt-7">
            <div class="public-container">
                <div class="mb-4 flex items-center justify-between text-[10px] font-semibold uppercase tracking-[0.12em] text-public-muted sm:text-xs">
                    <p><span data-gallery-count>{{ number_format($photos->total()) }}</span> foto ditemukan</p>
                    <svg class="h-4 w-4 text-public-ink" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>
                </div>

                @if($photos->isNotEmpty())
                    <div class="gallery-masonry" data-gallery-items>@foreach($photos as $photo)<x-public.photo-card :photo="$photo" />@endforeach</div>
                    <div class="mt-8 text-center" data-gallery-pagination data-next-page="{{ $photos->nextPageUrl() }}">
                        <p class="hidden text-xs font-semibold text-public-muted" data-gallery-loading>Memuat foto berikutnya…</p>
                        <p class="hidden text-xs font-semibold text-public-muted" data-gallery-error>Foto berikutnya gagal dimuat. <button type="button" class="underline" data-gallery-retry>Coba lagi</button></p>
                        <div class="gallery-pagination-fallback">{{ $photos->withQueryString()->links() }}</div>
                    </div>
                    <div class="h-px" data-gallery-sentinel aria-hidden="true"></div>
                @else
                    <div class="py-20 text-center">
                        <h2 class="public-serif text-3xl text-public-ink">Belum ada foto yang cocok.</h2>
                        <p class="mt-2 text-sm font-medium text-public-muted">Coba ubah filter atau reset pencarian.</p>
                        <a href="{{ route('galeri') }}" class="mt-5 inline-block border-b border-public-ink pb-1 text-xs font-bold uppercase tracking-wider">Reset Filter</a>
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-marketplace-layout>
