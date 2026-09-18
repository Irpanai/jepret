@php
    $displayName = $photographer->studio_name ?: $photographer->name;
    $heroPhoto = $featuredPhotos->first();
    $heroImage = $heroPhoto ? route('media.preview', $heroPhoto) : asset('images/login-bg.jpg');
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $photographer->name,
        'url' => route('photographers.show', $photographer->slug ?: $photographer->id),
        'image' => $photographer->profilePhotoUrl(),
        'workLocation' => $photographer->location,
    ];
@endphp

<x-marketplace-layout
    title="{{ $displayName }} - Photographer | JepretCFD"
    description="Profil {{ $displayName }} di JepretCFD. Lihat karya pilihan dan foto event yang tersedia untuk dibeli."
    :og-image="$heroImage"
    :structured-data="$structuredData"
>
    <section class="border-b border-public-line bg-white py-10 sm:py-14">
        <div class="public-container grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
            <div>
                <nav aria-label="Breadcrumb" class="mb-8 flex flex-wrap items-center gap-2 text-xs font-extrabold uppercase text-public-muted">
                    <a href="{{ route('photographers.index') }}" class="hover:text-public-ink">Photographers</a>
                    <span>/</span>
                    <span>{{ $displayName }}</span>
                </nav>

                <div class="flex items-start gap-4">
                    <img src="{{ $photographer->profilePhotoUrl() }}" alt="Foto profil {{ $photographer->name }}" class="h-20 w-20 shrink-0 rounded-full border border-public-line object-cover sm:h-24 sm:w-24">
                    <div>
                        <p class="public-kicker">{{ $photographer->category ?: 'Photographer' }}</p>
                        <h1 class="mt-3 text-5xl font-extrabold leading-none text-public-ink sm:text-7xl">{{ $displayName }}</h1>
                    </div>
                </div>

                <div class="mt-6 grid gap-2 text-sm font-semibold leading-6 text-public-muted">
                    @if($photographer->studio_name)
                        <p>Photographer: <span class="font-extrabold text-public-ink">{{ $photographer->name }}</span></p>
                    @endif
                    <p>Lokasi: <span class="font-extrabold text-public-ink">{{ $photographer->location ?: 'Indonesia' }}</span></p>
                </div>

                <p class="mt-6 max-w-2xl text-base font-semibold leading-7 text-public-muted">
                    {{ $photographer->bio ?: 'Photographer JepretCFD dengan koleksi foto event aktif yang siap ditemukan dan dibeli oleh buyer.' }}
                </p>

                <div class="mt-8 grid grid-cols-3 border-y border-public-line py-5 text-sm">
                    <div>
                        <p class="text-2xl font-extrabold">{{ number_format($photographer->active_photos_count ?? 0) }}</p>
                        <p class="font-bold uppercase text-public-muted">Foto Aktif</p>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold">{{ number_format($photographer->events_count ?? 0) }}</p>
                        <p class="font-bold uppercase text-public-muted">Event</p>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold">{{ number_format($photographer->paid_sales_count ?? 0) }}</p>
                        <p class="font-bold uppercase text-public-muted">Terjual</p>
                    </div>
                </div>
            </div>

            <div class="relative aspect-[5/4] overflow-hidden border border-public-line bg-public-mist">
                <img src="{{ $heroImage }}" alt="Karya dari {{ $displayName }}" class="h-full w-full object-cover">
                <div class="absolute bottom-4 left-4 right-4 bg-white p-4">
                    <p class="public-kicker">{{ $heroPhoto?->event?->nama_event ?? 'JepretCFD archive' }}</p>
                    <p class="mt-2 text-xl font-extrabold">{{ $heroPhoto?->title ?: 'Karya pilihan' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-public-bone py-12 sm:py-16">
        <div class="public-container">
            <div class="mb-6">
                <p class="public-kicker">Karya Pilihan</p>
                <h2 class="mt-2 text-3xl font-extrabold text-public-ink">Portfolio/editorial identity.</h2>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                @forelse($featuredPhotos as $index => $photo)
                    <a href="{{ route('marketplace.show', $photo) }}" class="relative overflow-hidden border border-public-line bg-white {{ $index === 0 ? 'md:col-span-2 aspect-[16/10]' : 'aspect-[4/5]' }}">
                        <img src="{{ route('media.preview', $photo) }}" alt="{{ $photo->title ?: 'Karya JepretCFD' }}" class="h-full w-full object-cover">
                        <span class="absolute bottom-3 left-3 bg-white px-3 py-2 text-xs font-extrabold uppercase text-public-ink">{{ $photo->event?->nama_event ?? 'Event' }}</span>
                    </a>
                @empty
                    <div class="col-span-full border border-public-line bg-white p-8 text-center">
                        <p class="text-sm font-bold text-public-muted">Belum ada karya pilihan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="marketplace" class="bg-white py-12 sm:py-16">
        <div class="public-container">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="public-kicker">Foto Tersedia</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-public-ink">Marketplace images.</h2>
                </div>
                <a href="{{ route('galeri', ['photographer' => $photographer->id]) }}" class="text-xs font-extrabold uppercase text-public-muted underline">Filter di galeri</a>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-5">
                @forelse($photos as $photo)
                    <x-public.photo-card :photo="$photo" />
                @empty
                    <div class="col-span-full border border-public-line bg-public-bone p-8 text-center">
                        <p class="text-sm font-bold text-public-muted">Belum ada foto aktif untuk dibeli.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $photos->withQueryString()->links() }}
            </div>
        </div>
    </section>
</x-marketplace-layout>
