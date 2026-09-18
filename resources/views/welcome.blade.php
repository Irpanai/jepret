@php
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'JepretCFD',
        'url' => route('landing'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => route('galeri').'?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
@endphp

<x-marketplace-layout
    title="JepretCFD - Temukan Foto Event & Photographer"
    description="Temukan foto event, simpan momen terbaik, dan beli file original dari photographer JepretCFD dengan preview terlindungi."
    :structured-data="$structuredData"
>
    <section class="border-b border-public-line bg-white">
        <div class="grid min-h-[calc(100vh-6rem)] lg:grid-cols-[minmax(0,3fr)_minmax(420px,2fr)]" data-hero-reveal>
            <div class="flex min-w-0 flex-col justify-between px-4 py-8 sm:px-8 sm:py-10 lg:px-[max(3rem,calc((100vw-1600px)/2))] lg:py-8 lg:pr-12">
                <div>
                    <div class="mt-4 sm:mt-8 lg:mt-10 xl:mt-12">
                        <p class="public-kicker" data-hero-copy style="--reveal-index: 1">Marketplace fotografi event</p>
                        <h1 class="mt-5 max-w-5xl text-[clamp(3.2rem,7vw,8rem)] font-extrabold leading-[0.86]">
                            <span class="hero-reveal-line"><span data-hero-word style="--reveal-index: 0">Temukan</span></span><br>
                            <span class="hero-reveal-line"><span data-hero-word style="--reveal-index: 1">momennya.</span></span>
                        </h1>
                        <div class="mt-7 h-1 w-20 bg-public-ink" data-hero-copy style="--reveal-index: 2"></div>
                        <p class="mt-6 max-w-lg text-base font-semibold leading-7 text-public-muted" data-hero-copy style="--reveal-index: 3">
                            Cari foto dari event dan photographer pilihanmu. Preview tetap terlindungi, sementara file original tersimpan privat sampai pembayaran selesai.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center gap-x-7 gap-y-4" data-hero-copy style="--reveal-index: 4">
                            <a href="{{ route('galeri') }}" class="text-sm font-extrabold uppercase text-public-ink hover:underline">Jelajahi Galeri →</a>
                            <a href="{{ route('photographers.index') }}" class="text-sm font-extrabold uppercase text-public-muted hover:text-public-ink">Lihat Photographers</a>
                        </div>
                    </div>
                </div>

                <div class="mt-10 border-t border-public-line pt-6" data-hero-copy style="--reveal-index: 5">
                    <dl class="grid grid-cols-3 gap-3 sm:gap-5">
                        <div class="flex flex-col gap-2">
                            <dt class="text-xs font-semibold text-public-muted sm:text-sm">Foto Terjual</dt>
                            <dd class="order-first text-3xl font-extrabold leading-none text-public-ink sm:text-4xl">200+</dd>
                        </div>
                        <div class="flex flex-col gap-2">
                            <dt class="text-xs font-semibold text-public-muted sm:text-sm">Photographers</dt>
                            <dd class="order-first text-3xl font-extrabold leading-none text-public-ink sm:text-4xl">25+</dd>
                        </div>
                        <div class="flex flex-col gap-2">
                            <dt class="text-xs font-semibold text-public-muted sm:text-sm">Foto Tersedia</dt>
                            <dd class="order-first text-3xl font-extrabold leading-none text-public-ink sm:text-4xl">500+</dd>
                        </div>
                    </dl>
                    <p class="mt-5 max-w-lg text-xs font-semibold leading-5 text-public-muted">Dipercaya photographer dan komunitas untuk menemukan, menjual, dan menyimpan momen terbaik.</p>
                </div>
            </div>

            <a href="{{ route('register', ['role' => 'fotografer']) }}" class="relative min-h-[430px] overflow-hidden bg-public-mist lg:min-h-full" data-hero-media>
                <img src="{{ asset('images/herophotographer.jpg') }}" alt="Photographer JepretCFD sedang mengabadikan momen" class="h-full w-full object-cover">
                <div class="absolute inset-x-0 bottom-0 z-10 bg-public-ink p-5 text-white sm:p-6">
                    <p class="text-[10px] font-extrabold uppercase text-white/55">Berkarya bersama JepretCFD</p>
                    <div class="mt-2 flex items-end justify-between gap-4">
                        <div>
                            <p class="max-w-md text-xl font-extrabold leading-tight sm:text-2xl">Ubah setiap jepretan menjadi peluang.</p>
                            <p class="mt-2 max-w-md text-xs font-bold leading-5 text-white/60">Bangun portofolio, jangkau lebih banyak buyer, dan kelola penjualan dalam satu ruang.</p>
                        </div>
                        <span class="shrink-0 text-xs font-extrabold uppercase">Daftar Photographer →</span>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <x-home.about />


    <section class="bg-white py-16 sm:py-24">
        <div class="public-container" data-reveal>
            <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="public-kicker">Galeri terbaru</p>
                    <h2 class="public-heading mt-4">Momen pilihan dari berbagai event.</h2>
                </div>
                <div class="flex max-w-md flex-col items-start gap-4 sm:items-end">
                    <p class="text-sm font-semibold leading-6 text-public-muted sm:text-right">
                        Jelajahi foto terbaru yang tersedia di galeri JepretCFD dan temukan momen terbaikmu.
                    </p>
                    <a href="{{ route('login') }}" class="public-button public-button-secondary">Lihat Semua</a>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($galleryPhotos as $photo)
                    <x-public.photo-card :photo="$photo" />
                @empty
                    <div class="col-span-full border border-public-line bg-public-bone p-8 text-center">
                        <p class="text-xl font-extrabold text-public-ink">Belum ada foto aktif.</p>
                        <p class="mt-2 text-sm font-semibold text-public-muted">Foto terbaru dari galeri akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <x-home.pricing />

    <section class="bg-white py-16 sm:py-24">
        <div class="public-container grid gap-8 border border-public-line bg-public-bone p-6 sm:p-10 lg:grid-cols-[1fr_auto] lg:items-center" data-reveal>
            <div>
                <p class="public-kicker">Untuk photographer</p>
                <h2 class="mt-4 text-4xl font-extrabold leading-none sm:text-6xl">Upload karya. Jual lebih mudah.</h2>
                <p class="mt-5 max-w-2xl text-sm font-semibold leading-6 text-public-muted">
                    Kelola event, unggah foto, lindungi preview dengan watermark, lalu jual file original dengan skema revenue 90% photographer dan 10% platform.
                </p>
            </div>
            <a href="{{ route('register', ['role' => 'fotografer']) }}" class="public-button">Mulai sebagai Photographer</a>
        </div>
    </section>
</x-marketplace-layout>
