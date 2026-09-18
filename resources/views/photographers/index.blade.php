@php
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => 'Photographers Jepret',
        'url' => route('photographers.index'),
    ];
@endphp

<x-marketplace-layout
    title="Photographers | Jepret"
    description="Temukan photographer Jepret berdasarkan nama, studio, kategori, dan lokasi. Lihat profil dan foto event yang tersedia untuk dibeli."
    :structured-data="$structuredData"
>
    <section class="relative overflow-hidden border-b border-public-line bg-public-ink py-10 text-white sm:py-14">
        <img src="{{ asset('images/photographers.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover" aria-hidden="true">
        <div class="absolute inset-0 bg-black/55" aria-hidden="true"></div>
        <div class="public-container relative z-10">
            <p class="public-kicker">Directory</p>
            <div class="mt-4 grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <h1 class="public-heading text-white">Photographers.</h1>
                    <p class="mt-4 max-w-2xl text-sm font-semibold leading-6 text-white/85">
                        Direktori photographer event yang aktif mempublikasikan karya di Jepret. Cari berdasarkan studio, kota, atau kategori.
                    </p>
                </div>
                <p class="text-sm font-extrabold uppercase text-white/85">{{ number_format($photographers->total()) }} photographer</p>
            </div>
        </div>
    </section>

    <section class="bg-public-bone py-5">
        <div class="public-container">
            <form method="GET" action="{{ route('photographers.index') }}" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_220px_auto]">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, studio, lokasi, atau kategori" class="public-input min-h-12 px-4">
                <select name="location" class="public-input min-h-12 px-3">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $location)
                        <option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>
                    @endforeach
                </select>
                <select name="category" class="public-input min-h-12 px-3">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                <button class="public-button min-h-12">Filter</button>
            </form>
        </div>
    </section>

    @if($featuredPhotographers->isNotEmpty())
        <section class="bg-white py-12 sm:py-16">
            <div class="public-container">
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="public-kicker">Featured archive</p>
                        <h2 class="mt-2 text-3xl font-extrabold text-public-ink">Photographer dengan karya aktif.</h2>
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-3">
                    @foreach($featuredPhotographers as $photographer)
                        <x-public.photographer-card :photographer="$photographer" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="bg-public-bone py-12 sm:py-16">
        <div class="public-container">
            <div class="mb-6 border-b border-public-line pb-4">
                <p class="public-kicker">Roster</p>
                <h2 class="mt-2 text-3xl font-extrabold text-public-ink">Semua photographer.</h2>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($photographers as $photographer)
                    <x-public.photographer-card :photographer="$photographer" />
                @empty
                    <div class="col-span-full border border-public-line bg-white p-10 text-center">
                        <h2 class="text-2xl font-extrabold text-public-ink">Photographer tidak ditemukan.</h2>
                        <p class="mt-2 text-sm font-semibold text-public-muted">Coba ubah kata pencarian atau hapus filter.</p>
                        <a href="{{ route('photographers.index') }}" class="public-button public-button-secondary mt-6">Reset Filter</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $photographers->withQueryString()->links() }}
            </div>
        </div>
    </section>
</x-marketplace-layout>
