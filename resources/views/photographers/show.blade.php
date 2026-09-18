@php
    $displayName = $photographer->studio_name ?: $photographer->name;
    $heroImage = $featuredCamera?->photo_path ? route('media.camera', $featuredCamera) : asset('images/login-bg.jpg');
    $whatsappNumber = preg_replace('/\D+/', '', $photographer->whatsapp ?? '');
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
    title="{{ $displayName }} - Photographer | Jepret"
    description="Profil {{ $displayName }} di Jepret. Lihat karya pilihan dan foto event yang tersedia untuk dibeli."
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
                    {{ $photographer->bio ?: 'Photographer Jepret dengan koleksi foto event aktif yang siap ditemukan dan dibeli oleh buyer.' }}
                </p>

                @if($whatsappNumber || $photographer->instagram_username)
                    <div class="mt-6 flex flex-wrap gap-3" aria-label="Social links {{ $displayName }}">
                        @if($whatsappNumber)
                            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center gap-2.5 border border-public-line bg-white px-4 text-sm font-bold text-public-ink transition-colors hover:border-public-ink hover:bg-public-ink hover:text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a9.75 9.75 0 0 0-8.42 14.65L2.25 21.5l4.96-1.3A9.75 9.75 0 1 0 12 2Zm5.68 13.96c-.24.68-1.4 1.3-1.94 1.38-.5.08-1.12.11-1.81-.11-.42-.14-.96-.32-1.65-.62-2.9-1.25-4.79-4.18-4.94-4.37-.14-.2-1.18-1.57-1.18-3 0-1.42.74-2.12 1.01-2.41.26-.29.57-.36.76-.36h.55c.18 0 .41-.07.64.49.24.58.81 1.99.88 2.13.07.15.12.32.02.51-.09.2-.14.32-.28.49-.14.17-.3.38-.43.51-.14.15-.29.3-.12.59.16.29.73 1.2 1.57 1.94 1.08.96 1.99 1.26 2.27 1.4.28.15.45.13.62-.07.16-.19.71-.83.9-1.12.19-.29.38-.24.64-.14.26.09 1.66.78 1.94.93.29.14.48.21.55.33.07.12.07.68-.17 1.36Z"/></svg>
                                <span>{{ $photographer->whatsapp }}</span>
                            </a>
                        @endif
                        @if($photographer->instagram_username)
                            <a href="https://www.instagram.com/{{ $photographer->instagram_username }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center gap-2.5 border border-public-line bg-white px-4 text-sm font-bold text-public-ink transition-colors hover:border-public-ink hover:bg-public-ink hover:text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.4" cy="6.7" r="1" fill="currentColor" stroke="none"/></svg>
                                <span>{{ '@'.$photographer->instagram_username }}</span>
                            </a>
                        @endif
                    </div>
                @endif

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
                <img src="{{ $heroImage }}" alt="Kamera milik {{ $displayName }}" class="h-full w-full object-cover">
                <div class="absolute bottom-4 left-4 right-4 bg-white p-4">
                    <p class="public-kicker">Kamera Photographer</p>
                    <p class="mt-2 text-xl font-extrabold">{{ $featuredCamera?->brand_model ?: 'Peralatan belum ditambahkan' }}</p>
                    @if($featuredCamera?->lens)
                        <p class="mt-1 text-sm font-semibold text-public-muted">{{ $featuredCamera->lens }}</p>
                    @endif
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
                        <img src="{{ route('media.preview', $photo) }}" alt="{{ $photo->title ?: 'Karya Jepret' }}" class="h-full w-full object-cover">
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
