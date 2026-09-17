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

                <dl class="mt-10 grid gap-5 border-t border-public-line pt-5 text-xs sm:grid-cols-3" data-hero-copy style="--reveal-index: 5">
                    <div class="flex gap-3"><span class="font-extrabold text-public-ink">01</span><div><dt class="font-extrabold uppercase text-public-muted">Website</dt><dd class="mt-1 font-bold text-public-ink">jepretcfd.id</dd></div></div>
                    <div class="flex gap-3"><span class="font-extrabold text-public-ink">02</span><div><dt class="font-extrabold uppercase text-public-muted">WhatsApp</dt><dd class="mt-1 font-bold text-public-ink">0851 5676 7900</dd></div></div>
                    <div class="flex gap-3"><span class="font-extrabold text-public-ink">03</span><div><dt class="font-extrabold uppercase text-public-muted">Coverage</dt><dd class="mt-1 font-bold text-public-ink">Event Indonesia</dd></div></div>
                </dl>
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

    <section id="tentang" class="bg-public-bone py-16 sm:py-24">
        <div class="public-container" data-reveal>
            <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-end">
                <div>
                    <p class="public-kicker">Cara kerja</p>
                    <h2 class="public-heading mt-4">Dari lintasan ke arsip pribadi.</h2>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach([
                        ['01', 'Cari', 'Temukan foto berdasarkan event, lokasi, tanggal, photographer, atau tag visual.'],
                        ['02', 'Beli', 'Masukkan foto ke cart, lanjut checkout, lalu selesaikan pembayaran.'],
                        ['03', 'Download', 'File original tanpa watermark terbuka untuk pembelian yang sudah paid.'],
                    ] as [$number, $title, $copy])
                        <div class="border border-public-line bg-white p-5">
                            <p class="public-kicker">{{ $number }}</p>
                            <h3 class="mt-8 text-2xl font-extrabold">{{ $title }}</h3>
                            <p class="mt-3 text-sm font-semibold leading-6 text-public-muted">{{ $copy }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

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

    <section id="pricing" class="border-y border-public-line bg-public-bone py-16 sm:py-24">
        <div class="public-container" data-reveal>
            @php
                $pricingPlans = [
                    [
                        'name' => 'Trial',
                        'price' => 'Gratis',
                        'period' => '7 hari',
                        'storage' => '500 MB',
                        'description' => 'Untuk mencoba JepretCFD selama 7 hari.',
                        'features' => ['500 MB Cloud Storage', 'Upload & kelola foto', 'Protected preview', 'Marketplace access', 'Atur harga foto'],
                        'cta' => 'Mulai Gratis',
                    ],
                    [
                        'name' => 'Starter',
                        'price' => 'Rp29.000',
                        'period' => '7 hari',
                        'storage' => '5 GB',
                        'description' => 'Untuk photographer yang mulai aktif menjual.',
                        'features' => ['5 GB Cloud Storage', 'Semua fitur utama JepretCFD', 'Protected preview + watermark', 'Dashboard photographer', 'Transaksi realtime'],
                        'cta' => 'Pilih Starter',
                    ],
                    [
                        'name' => 'Creator',
                        'price' => 'Rp59.000',
                        'period' => 'bulan',
                        'storage' => '20 GB',
                        'description' => 'Untuk photographer dengan aktivitas dan koleksi lebih besar.',
                        'features' => ['20 GB Cloud Storage', 'Semua fitur Starter', 'Dashboard & monitoring penjualan', 'Statistik transaksi & pendapatan', 'Pengelolaan storage'],
                        'cta' => 'Pilih Creator',
                        'featured' => true,
                    ],
                    [
                        'name' => 'Studio',
                        'price' => 'Custom',
                        'period' => null,
                        'storage' => 'Custom Storage',
                        'description' => 'Untuk studio, tim, dan kebutuhan skala besar.',
                        'features' => ['Kapasitas sesuai kebutuhan', 'Semua fitur Creator', 'Kebutuhan operasional custom', 'Dukungan kebutuhan tim', 'Konfigurasi fleksibel'],
                        'cta' => 'Hubungi Kami',
                    ],
                ];
            @endphp

            <div class="grid gap-8 border-b border-public-line pb-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
                <div>
                    <p class="public-kicker">Pricing Photographer</p>
                    <h2 class="public-heading mt-4">Pilih ruang untuk setiap karya.</h2>
                </div>
                <p class="max-w-xl text-sm font-semibold leading-6 text-public-muted lg:justify-self-end">
                    Simpan, kelola, dan jual foto melalui JepretCFD. Pilih kapasitas yang sesuai dengan aktivitas fotografimu.
                </p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($pricingPlans as $plan)
                    @php($isFeatured = $plan['featured'] ?? false)
                    <article class="flex min-w-0 flex-col border p-5 sm:p-6 {{ $isFeatured ? 'border-public-ink bg-public-ink text-white' : 'border-public-line bg-white text-public-ink' }}">
                        <div class="flex min-h-7 items-start justify-between gap-3">
                            <h3 class="text-xl font-extrabold">{{ $plan['name'] }}</h3>
                            @if($isFeatured)
                                <span class="border border-white/25 px-2 py-1 text-[10px] font-extrabold uppercase text-white/75">Pilihan Utama</span>
                            @endif
                        </div>

                        <div class="mt-8 flex min-h-12 flex-wrap items-baseline gap-x-1">
                            <p class="text-3xl font-extrabold sm:text-4xl">{{ $plan['price'] }}</p>
                            @if($plan['period'])
                                <span class="text-xs font-bold {{ $isFeatured ? 'text-white/55' : 'text-public-muted' }}">/ {{ $plan['period'] }}</span>
                            @endif
                        </div>

                        <p class="mt-3 text-xs font-extrabold uppercase {{ $isFeatured ? 'text-white/55' : 'text-public-muted' }}">{{ $plan['storage'] }}</p>
                        <p class="mt-5 min-h-[4.5rem] text-sm font-semibold leading-6 {{ $isFeatured ? 'text-white/68' : 'text-public-muted' }}">{{ $plan['description'] }}</p>

                        <div class="mt-6 border-t pt-5 {{ $isFeatured ? 'border-white/20' : 'border-public-line' }}">
                            <p class="text-xs font-extrabold uppercase {{ $isFeatured ? 'text-white/55' : 'text-public-muted' }}">Termasuk</p>
                            <ul class="mt-4 grid gap-3">
                                @foreach($plan['features'] as $feature)
                                    <li class="grid grid-cols-[18px_minmax(0,1fr)] gap-2 text-sm font-semibold leading-5">
                                        <span class="grid h-[18px] w-[18px] place-items-center border text-[10px] {{ $isFeatured ? 'border-white/35 text-white' : 'border-public-ink text-public-ink' }}" aria-hidden="true">+</span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <a
                            href="{{ $plan['name'] === 'Studio' ? 'mailto:jepretccfdd@gmail.com?subject=Paket%20Studio%20JepretCFD' : route('register', ['role' => 'fotografer']) }}"
                            class="mt-8 inline-flex min-h-12 w-full items-center justify-center border px-4 text-center text-xs font-extrabold uppercase {{ $isFeatured ? 'border-white bg-white text-public-ink hover:bg-public-bone' : 'border-public-ink bg-public-ink text-white hover:bg-neutral-800' }}"
                        >
                            {{ $plan['cta'] }}
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 flex flex-col gap-4 border-t border-public-line pt-7 sm:flex-row sm:items-center sm:justify-between">
                <p class="max-w-3xl text-sm font-extrabold leading-6 text-public-ink">Tampilkan karya terbaikmu, jangkau lebih banyak buyer, dan kelola penjualan melalui satu platform.</p>
                <a href="{{ route('register', ['role' => 'fotografer']) }}" class="shrink-0 text-xs font-extrabold uppercase text-public-ink hover:underline">Mulai sebagai Photographer →</a>
            </div>
        </div>
    </section>

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
