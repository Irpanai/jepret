@php
    $title = ($photo->title ?: ($photo->event?->nama_event ?? 'Foto JepretCFD')).' by '.($photo->fotografer?->studio_name ?: $photo->fotografer?->name ?? 'Photographer').' | JepretCFD';
    $description = 'Preview terlindungi untuk '.($photo->title ?: $photo->event?->nama_event ?? 'foto event').' dari JepretCFD. Beli untuk mengakses file original tanpa watermark.';
    $previewUrl = $photo->file_watermark ? url(Storage::url($photo->file_watermark)) : asset('images/login-bg.jpg');
    $photoDate = $photo->taken_at ?: ($photo->event?->tanggal_event ? \Illuminate\Support\Carbon::parse($photo->event->tanggal_event) : null);
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'ImageObject',
        'name' => $photo->title ?: ($photo->event?->nama_event ?? 'Foto JepretCFD'),
        'contentUrl' => $previewUrl,
        'creator' => [
            '@type' => 'Person',
            'name' => $photo->fotografer?->name ?? 'Photographer JepretCFD',
        ],
        'creditText' => $photo->fotografer?->studio_name ?: $photo->fotografer?->name,
    ];
@endphp

<x-marketplace-layout :title="$title" :description="$description" :og-image="$previewUrl" :structured-data="$structuredData">
    <section class="bg-white py-6 sm:py-10">
        <div class="public-container">
            <nav aria-label="Breadcrumb" class="mb-6 flex flex-wrap items-center gap-2 text-xs font-extrabold uppercase text-public-muted">
                <a href="{{ route('galeri') }}" class="hover:text-public-ink">Galeri</a>
                <span>/</span>
                <span>{{ $photo->event?->nama_event ?? 'Foto' }}</span>
            </nav>

            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_420px] xl:grid-cols-[minmax(0,1fr)_480px]">
                <div>
                    <div class="protected-photo relative aspect-[4/3] overflow-hidden border border-public-line bg-public-mist">
                        <img src="{{ $previewUrl }}" alt="{{ $photo->title ?: 'Preview foto JepretCFD' }}" class="h-full w-full object-cover blur-[1.5px]">
                        <div class="absolute left-4 top-4 bg-white px-3 py-2 text-xs font-extrabold uppercase text-public-ink">
                            Preview terlindungi
                        </div>
                    </div>

                    <div class="mt-5 border border-public-line bg-public-bone p-4">
                        <p class="text-sm font-semibold leading-6 text-public-muted">
                            Preview dikompresi dan diberi watermark untuk menjaga hak cipta photographer. File original tetap privat dan hanya tersedia setelah pembayaran lunas.
                        </p>
                    </div>
                </div>

                <aside class="grid content-start gap-5">
                    <div class="border border-public-line bg-white p-5">
                        <p class="public-kicker">{{ $photo->event?->lokasi ?? 'Event JepretCFD' }}</p>
                        <h1 class="mt-4 text-4xl font-extrabold leading-none text-public-ink sm:text-5xl">
                            {{ $photo->title ?: ($photo->event?->nama_event ?? 'Foto JepretCFD') }}
                        </h1>
                        <p class="mt-4 text-sm font-semibold leading-6 text-public-muted">
                            {{ $photo->event?->nama_event ?? 'Event belum dicatat' }}
                            @if($photoDate)
                                / {{ $photoDate->format('d M Y') }}
                            @endif
                        </p>

                        <div class="mt-6 flex items-center gap-3 border-y border-public-line py-4">
                            <img src="{{ $photo->fotografer?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($photo->fotografer?->name ?? 'Photographer').'&background=050505&color=fff' }}" alt="" class="h-12 w-12 object-cover">
                            <div class="min-w-0">
                                <p class="text-xs font-extrabold uppercase text-public-muted">Photographer</p>
                                <a href="{{ $photo->fotografer ? route('photographers.show', $photo->fotografer->slug ?: $photo->fotografer->id) : '#' }}" class="text-lg font-extrabold text-public-ink hover:underline">
                                    {{ $photo->fotografer?->studio_name ?: ($photo->fotografer?->name ?? 'Photographer') }}
                                </a>
                            </div>
                        </div>

                        <div class="mt-6 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs font-extrabold uppercase text-public-muted">Harga original</p>
                                <p class="mt-1 text-4xl font-extrabold text-public-ink">Rp{{ number_format($photo->harga, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            @auth
                                @if(auth()->user()->role === 'pembeli')
                                    <form method="POST" action="{{ route('cart.store') }}">
                                        @csrf
                                        <input type="hidden" name="photo_id" value="{{ $photo->id }}">
                                        <button class="public-button w-full">Tambah ke Cart</button>
                                    </form>
                                @else
                                    <a href="{{ route('dashboard') }}" class="public-button w-full">Buka Dashboard</a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="public-button w-full">Login untuk Membeli</a>
                            @endauth
                        </div>
                    </div>

                    <div class="border border-public-line bg-white p-5">
                        <h2 class="text-sm font-extrabold uppercase text-public-ink">Metadata</h2>
                        <dl class="mt-4 grid gap-3 text-sm">
                            @foreach([
                                'Event' => $photo->event?->nama_event,
                                'Lokasi' => $photo->event?->lokasi,
                                'Kategori' => $photo->category,
                                'Waktu' => optional($photo->taken_at ?? $photo->created_at)->format('d M Y, H:i'),
                                'Kamera' => $photo->kamera_body,
                                'Lensa' => $photo->lensa,
                                'Resolusi' => $photo->resolusi,
                            ] as $label => $value)
                                <div class="flex justify-between gap-4 border-b border-public-line pb-2 last:border-b-0 last:pb-0">
                                    <dt class="font-bold text-public-muted">{{ $label }}</dt>
                                    <dd class="max-w-[65%] text-right font-extrabold text-public-ink">{{ $value ?: 'Belum dicatat' }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="bg-public-bone py-12 sm:py-16">
        <div class="public-container">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="public-kicker">Related</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-public-ink">Foto lain dari event yang sama.</h2>
                </div>
                <a href="{{ route('galeri', ['event' => $photo->event_id]) }}" class="text-xs font-extrabold uppercase text-public-muted underline">Lihat semua</a>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4">
                @forelse($relatedPhotos as $related)
                    <x-public.photo-card :photo="$related" />
                @empty
                    <div class="col-span-full border border-public-line bg-white p-8 text-center">
                        <p class="text-sm font-bold text-public-muted">Belum ada foto terkait.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-marketplace-layout>
