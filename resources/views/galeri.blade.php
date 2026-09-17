@php
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => 'Galeri Foto Event JepretCFD',
        'url' => route('galeri'),
    ];
@endphp

<x-marketplace-layout
    title="Galeri Foto Event | JepretCFD"
    description="Jelajahi foto event JepretCFD berdasarkan event, photographer, kategori, tanggal, dan waktu. Preview terlindungi, original terbuka setelah pembelian."
    :structured-data="$structuredData"
>
    <section class="border-b border-public-line bg-white py-10 sm:py-14">
        <div class="public-container">
            <p class="public-kicker">Gallery</p>
            <div class="mt-4 grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <h1 class="public-heading">Galeri foto event.</h1>
                    <p class="mt-4 max-w-2xl text-sm font-semibold leading-6 text-public-muted">
                        Cari foto berdasarkan event, photographer, kategori, lokasi, tanggal, atau waktu pengambilan. Semua preview tetap ber-watermark.
                    </p>
                </div>
                <p class="text-sm font-extrabold uppercase text-public-muted">{{ number_format($photos->total()) }} foto aktif</p>
            </div>
        </div>
    </section>

    <section class="bg-public-bone py-5">
        <div class="public-container">
            <form method="GET" action="{{ route('galeri') }}" class="grid gap-3">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari event, photographer, lokasi, tag, atau judul foto" class="public-input min-h-12 px-4">

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                    <select name="event" class="public-input min-h-11 px-3">
                        <option value="">Semua Event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" @selected((string) request('event') === (string) $event->id)>{{ $event->nama_event }}</option>
                        @endforeach
                    </select>
                    <select name="photographer" class="public-input min-h-11 px-3">
                        <option value="">Semua Photographer</option>
                        @foreach($photographers as $photographer)
                            <option value="{{ $photographer->id }}" @selected((string) request('photographer') === (string) $photographer->id)>{{ $photographer->studio_name ?: $photographer->name }}</option>
                        @endforeach
                    </select>
                    <select name="category" class="public-input min-h-11 px-3">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    <select name="daypart" class="public-input min-h-11 px-3">
                        <option value="">Semua Waktu</option>
                        <option value="morning" @selected(request('daypart') === 'morning')>Pagi</option>
                        <option value="afternoon" @selected(request('daypart') === 'afternoon')>Siang</option>
                        <option value="evening" @selected(request('daypart') === 'evening')>Sore</option>
                        <option value="night" @selected(request('daypart') === 'night')>Malam</option>
                    </select>
                    <input type="date" name="date" value="{{ request('date') }}" class="public-input min-h-11 px-3">
                    <button class="public-button min-h-11">Filter</button>
                </div>

                @if(request()->hasAny(['q', 'event', 'photographer', 'category', 'daypart', 'date']))
                    <a href="{{ route('galeri') }}" class="w-max text-xs font-extrabold uppercase text-public-muted underline">Reset filter</a>
                @endif
            </form>
        </div>
    </section>

    <section class="bg-white py-8 sm:py-12">
        <div class="public-container">
            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-5">
                @forelse($photos as $photo)
                    <x-public.photo-card :photo="$photo" />
                @empty
                    <div class="col-span-full border border-public-line bg-public-bone p-10 text-center">
                        <h2 class="text-2xl font-extrabold text-public-ink">Belum ada foto yang cocok.</h2>
                        <p class="mt-2 text-sm font-semibold text-public-muted">Ubah filter atau kembali lagi setelah photographer mempublikasikan foto baru.</p>
                        <a href="{{ route('galeri') }}" class="public-button public-button-secondary mt-6">Reset Filter</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $photos->withQueryString()->links() }}
            </div>
        </div>
    </section>
</x-marketplace-layout>
