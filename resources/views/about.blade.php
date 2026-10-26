@php
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'AboutPage',
        'name' => 'Tentang Jepret — Every Moment. One Place.',
        'description' => 'Jepret mempertemukan orang yang mencari momen dengan fotografer yang menangkapnya dalam satu marketplace fotografi digital.',
        'url' => route('about'),
    ];
@endphp

<x-marketplace-layout
    title="Tentang Jepret — Every Moment. One Place."
    description="Jepret mempertemukan orang yang mencari momen dengan fotografer yang menangkapnya dalam satu marketplace fotografi digital."
    :canonical="route('about')"
    :og-image="asset('images/galeri.jpeg')"
    :structured-data="$structuredData"
>
    <article class="about-page text-public-ink">
        {{-- 1. HERO SECTION --}}
        <section
            class="relative overflow-hidden border-b border-public-line bg-white pt-10 pb-16 sm:pt-14 sm:pb-24 lg:pt-20 lg:pb-28"
            aria-labelledby="about-hero-title"
            data-hero-reveal
        >
            <div class="public-container">
                {{-- Editorial Metadata Bar --}}
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-public-line pb-5" data-hero-copy style="--reveal-index: 0">
                    <p class="about-eyebrow">00 / About Jepret</p>
                    <p class="font-sans text-[11px] font-bold uppercase tracking-widest text-public-muted">
                        Digital Photography Marketplace &middot; Indonesia
                    </p>
                </div>

                {{-- Expressive Display Headline --}}
                <div class="mt-10 sm:mt-14 lg:mt-16">
                    <h1 id="about-hero-title" class="about-display max-w-5xl text-public-ink">
                        <span class="hero-reveal-line">
                            <span data-hero-word style="--reveal-index: 0">Every moment.</span>
                        </span>
                        <br>
                        <span class="hero-reveal-line">
                            <span class="font-display italic font-normal" data-hero-word style="--reveal-index: 1">One place.</span>
                        </span>
                    </h1>
                </div>

                {{-- Asymmetric Hero Composition: Lead Copy + Curated Photography Frames --}}
                <div class="mt-10 grid gap-10 lg:mt-16 lg:grid-cols-12 lg:items-end lg:gap-12">
                    <div class="flex flex-col justify-between gap-8 lg:col-span-5" data-hero-copy style="--reveal-index: 2">
                        <div>
                            <p class="max-w-md text-lg font-semibold leading-relaxed text-public-ink sm:text-xl">
                                Jepret mempertemukan orang yang mencari momen dengan fotografer yang menangkapnya.
                            </p>
                            <p class="mt-4 max-w-md text-sm font-medium leading-relaxed text-public-muted">
                                Ruang arsip visual tempat ribuan dokumentasi olahraga, panggung, dan perayaan dikurasi secara terstruktur, dilindungi preview aman, dan siap dimiliki dalam resolusi penuh.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-6 border-t border-public-line pt-6">
                            <a href="{{ route('galeri') }}" class="public-button px-6">
                                <span>Jelajahi Galeri</span>
                            </a>
                            <a href="{{ route('photographers.index') }}" class="about-link text-public-ink">
                                <span>Direktori Fotografer</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>

                    {{-- Asymmetric Editorial Imagery --}}
                    <div class="grid gap-5 sm:grid-cols-12 sm:items-end lg:col-span-7">
                        <figure class="sm:col-span-8" data-hero-photo style="--reveal-index: 1">
                            <div class="about-image-reveal aspect-[16/11] overflow-hidden border border-public-line bg-public-mist">
                                <img
                                    src="{{ asset('images/galeri.jpeg') }}"
                                    alt="Suasana dokumentasi event dan pencarian momen di galeri Jepret"
                                    width="960"
                                    height="660"
                                    fetchpriority="high"
                                    decoding="async"
                                    class="h-full w-full object-cover grayscale"
                                >
                            </div>
                            <figcaption class="mt-2.5 flex items-center justify-between font-sans text-[10px] font-bold uppercase tracking-widest text-public-muted">
                                <span>FIG. 01 &mdash; EVENT ARCHIVE</span>
                                <span>PROTECTED PREVIEW</span>
                            </figcaption>
                        </figure>

                        <figure class="hidden sm:col-span-4 sm:block" data-hero-photo style="--reveal-index: 2">
                            <div class="about-image-reveal aspect-[3/4] overflow-hidden border border-public-line bg-public-mist">
                                <img
                                    src="{{ asset('images/photographers.jpg') }}"
                                    alt="Fotografer profesional sedang mengabadikan momen di lapangan"
                                    width="480"
                                    height="640"
                                    loading="eager"
                                    decoding="async"
                                    class="h-full w-full object-cover grayscale"
                                >
                            </div>
                            <figcaption class="mt-2.5 flex items-center justify-between font-sans text-[10px] font-bold uppercase tracking-widest text-public-muted">
                                <span>FIG. 02 &mdash; CREATOR</span>
                                <span>ORIGINAL FILE</span>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. ABOUT JEPRET --}}
        <section
            class="border-b border-public-line bg-white py-20 sm:py-28 lg:py-32"
            aria-labelledby="about-story-title"
        >
            <div class="public-container">
                <div class="grid gap-10 lg:grid-cols-12 lg:gap-12" data-reveal>
                    <div class="lg:col-span-4">
                        <p class="about-eyebrow">01 / About Jepret</p>
                        <p class="mt-6 hidden font-sans text-xs uppercase leading-relaxed tracking-widest text-public-muted lg:block">
                            ARSIP TERSTRUKTUR<br>
                            PREVIEW BER-WATERMARK<br>
                            UNDUHAN RESOLUSI PENUH
                        </p>
                    </div>

                    <div class="lg:col-span-8">
                        <h2 id="about-story-title" class="public-heading max-w-3xl text-public-ink">
                            The best moments are already out there. <span class="font-display italic font-normal">We make them easier to find.</span>
                        </h2>

                        <div class="mt-10 grid gap-8 border-t border-public-line pt-8 sm:grid-cols-2 sm:gap-10">
                            <p class="text-base font-medium leading-relaxed text-public-ink/90">
                                Jepret adalah marketplace fotografi digital yang menghubungkan fotografer dengan orang-orang yang ingin menemukan dan membeli foto dari berbagai event. Setiap karya diorganisir berdasarkan event, lokasi, tanggal, dan fotografer agar momen berharga tidak lagi hilang di ribuan folder penyimpanan mentah.
                            </p>
                            <p class="text-base font-medium leading-relaxed text-public-muted">
                                Melalui sistem preview ber-watermark dan pembayaran digital yang terintegrasi, Jepret menjaga hak cipta karya fotografer sekaligus memberikan akses unduhan file original beresolusi penuh secara instan kepada pembeli setelah transaksi selesai.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 3. HOW IT WORKS --}}
        <section
            class="border-b border-public-line bg-public-bone py-20 sm:py-28 lg:py-32"
            aria-labelledby="about-how-title"
        >
            <div class="public-container">
                <header class="grid gap-6 pb-12 lg:grid-cols-12 lg:items-end lg:pb-16" data-reveal>
                    <div class="lg:col-span-5">
                        <p class="about-eyebrow">02 / How It Works</p>
                        <h2 id="about-how-title" class="public-heading mt-4 text-public-ink">
                            Three steps from lens <span class="font-display italic font-normal">to archive.</span>
                        </h2>
                    </div>
                    <p class="max-w-md text-sm font-semibold leading-relaxed text-public-muted lg:col-span-7 lg:justify-self-end">
                        Alur sederhana tanpa proses manual yang berbelit—dirancang agar pencarian dan kepemilikan foto berlangsung cepat serta transparan.
                    </p>
                </header>

                @php
                    $stages = [
                        [
                            'number' => '01',
                            'title' => 'Discover',
                            'lead' => 'Temukan foto dari event atau galeri fotografer.',
                            'detail' => 'Telusuri katalog visual menggunakan filter event, lokasi, kategori, tanggal pengambilan, atau nama fotografer tanpa perlu membuka ratusan tautan terpisah.',
                            'meta' => 'SEARCH & FILTER',
                        ],
                        [
                            'number' => '02',
                            'title' => 'Choose',
                            'lead' => 'Pilih foto yang ingin dibeli melalui proses yang mudah dipahami.',
                            'detail' => 'Tinjau hasil foto melalui preview publik yang terlindungi watermark, masukkan frame pilihan ke keranjang belanja, dan selesaikan pembayaran QRIS secara praktis.',
                            'meta' => 'CART & QRIS CHECKOUT',
                        ],
                        [
                            'number' => '03',
                            'title' => 'Keep',
                            'lead' => 'Setelah pembayaran berhasil, akses dan unduh foto yang sudah dibeli.',
                            'detail' => 'Begitu transaksi terkonfirmasi, file foto original beresolusi penuh tanpa watermark langsung tersedia di halaman pembelian untuk diunduh dan disimpan.',
                            'meta' => 'ORIGINAL DOWNLOAD',
                        ],
                    ];
                @endphp

                <ol class="border-t border-public-line">
                    @foreach($stages as $index => $stage)
                        <li
                            class="about-step-row group grid gap-6 border-b border-public-line py-8 sm:py-10 lg:grid-cols-12 lg:items-baseline lg:gap-8 lg:py-12"
                            data-reveal
                            style="--about-delay: {{ $index * 90 }}ms"
                        >
                            <div class="flex items-baseline gap-4 lg:col-span-3">
                                <span class="about-step-num font-display italic text-public-muted">
                                    {{ $stage['number'] }}
                                </span>
                                <span class="font-sans text-[11px] font-bold uppercase tracking-widest text-public-muted">
                                    &mdash; {{ $stage['meta'] }}
                                </span>
                            </div>

                            <div class="lg:col-span-4">
                                <h3 class="text-2xl font-extrabold tracking-tight text-public-ink sm:text-3xl">
                                    {{ $stage['title'] }}
                                </h3>
                                <p class="mt-2 text-base font-bold text-public-ink/85">
                                    {{ $stage['lead'] }}
                                </p>
                            </div>

                            <p class="max-w-xl text-sm font-medium leading-relaxed text-public-muted lg:col-span-5">
                                {{ $stage['detail'] }}
                            </p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- 4. FOR PHOTOGRAPHERS & PHOTO SEEKERS --}}
        <section
            class="border-b border-public-line bg-white py-20 sm:py-28 lg:py-36"
            aria-labelledby="about-ecosystem-title"
        >
            <div class="public-container">
                <header class="grid gap-6 border-b border-public-line pb-10 lg:grid-cols-12 lg:items-end lg:pb-14" data-reveal>
                    <div class="lg:col-span-5">
                        <p class="about-eyebrow">03 / Ecosystem</p>
                        <h2 id="about-ecosystem-title" class="public-heading mt-4 text-public-ink">
                            Two perspectives. <span class="font-display italic font-normal">Shared value.</span>
                        </h2>
                    </div>
                    <p class="max-w-md text-sm font-semibold leading-relaxed text-public-muted lg:col-span-7 lg:justify-self-end">
                        Dirancang seimbang untuk mendukung kreator yang memproduksi karya di lapangan dan peserta event yang ingin mengabadikan momennya.
                    </p>
                </header>

                {{-- Staggered Asymmetric Two-Column Editorial Layout --}}
                <div class="mt-12 grid gap-16 lg:mt-16 lg:grid-cols-12 lg:gap-12">
                    {{-- Left Column: For Photographers --}}
                    <article class="lg:col-span-6 lg:pr-6" data-reveal>
                        <div class="flex items-center justify-between border-b border-public-line pb-3">
                            <span class="font-sans text-xs font-extrabold uppercase tracking-widest text-public-ink">
                                01 / For Photographers
                            </span>
                            <span class="font-sans text-[11px] font-semibold uppercase tracking-widest text-public-muted">
                                CREATOR WORKSPACE
                            </span>
                        </div>

                        <h3 class="mt-6 text-3xl font-extrabold tracking-tight text-public-ink sm:text-4xl">
                            Your work deserves to be <span class="font-display italic font-normal">discovered.</span>
                        </h3>

                        <figure class="mt-8">
                            <div class="about-image-reveal aspect-[4/5] overflow-hidden border border-public-line bg-public-mist">
                                <img
                                    src="{{ asset('images/photographers.jpg') }}"
                                    alt="Fotografer mengabadikan momen event menggunakan kamera profesional"
                                    width="720"
                                    height="900"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-full w-full object-cover grayscale"
                                >
                            </div>
                        </figure>

                        <p class="mt-7 text-base font-medium leading-relaxed text-public-muted">
                            Publikasikan hasil liputan event dalam galeri profesional tanpa perlu mengirim file satu per satu. Jepret membantu fotografer mengunggah foto, mengelola koleksi per event, memasang watermark kustom otomatis pada preview publik, serta menjual file original dengan pembagian pendapatan 90% untuk fotografer.
                        </p>

                        <dl class="mt-6 divide-y divide-public-line border-y border-public-line text-xs">
                            <div class="flex items-center justify-between py-3">
                                <dt class="font-bold uppercase tracking-wider text-public-muted">Proteksi Karya</dt>
                                <dd class="font-extrabold text-public-ink">Watermark Kustom &amp; Preview Terkunci</dd>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <dt class="font-bold uppercase tracking-wider text-public-muted">Manajemen &amp; Portofolio</dt>
                                <dd class="font-extrabold text-public-ink">Katalog Event, Profil Publik &amp; Kamera</dd>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <dt class="font-bold uppercase tracking-wider text-public-muted">Monetisasi Transparan</dt>
                                <dd class="font-extrabold text-public-ink">90% Revenue Share &amp; Tarik Dana</dd>
                            </div>
                        </dl>

                        <div class="mt-7 flex flex-wrap items-center gap-6">
                            <a href="{{ route('pricing') }}" class="about-link text-public-ink">
                                <span>Lihat Paket Fotografer</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                            <a href="{{ route('photographers.index') }}" class="about-link text-public-muted hover:text-public-ink">
                                <span>Eksplorasi Kreator</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </article>

                    {{-- Right Column: For Photo Seekers (Vertically Offset on Desktop) --}}
                    <article
                        class="lg:col-span-6 lg:mt-24 lg:border-l lg:border-public-line lg:pl-12"
                        data-reveal
                        style="--about-delay: 120ms"
                    >
                        <div class="flex items-center justify-between border-b border-public-line pb-3">
                            <span class="font-sans text-xs font-extrabold uppercase tracking-widest text-public-ink">
                                02 / For Photo Seekers
                            </span>
                            <span class="font-sans text-[11px] font-semibold uppercase tracking-widest text-public-muted">
                                EVENT PARTICIPANTS
                            </span>
                        </div>

                        <h3 class="mt-6 text-3xl font-extrabold tracking-tight text-public-ink sm:text-4xl">
                            Your moment is worth <span class="font-display italic font-normal">keeping.</span>
                        </h3>

                        <figure class="mt-8">
                            <div class="about-image-reveal aspect-[4/3] overflow-hidden border border-public-line bg-public-mist">
                                <img
                                    src="{{ asset('images/herophotographer.jpg') }}"
                                    alt="Peserta event diabadikan oleh lensa fotografer"
                                    width="736"
                                    height="552"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-full w-full object-cover grayscale"
                                >
                            </div>
                        </figure>

                        <p class="mt-7 text-base font-medium leading-relaxed text-public-muted">
                            Setiap garis finis, panggung, dan perayaan menyimpan ekspresi yang tidak terulang. Di Jepret, Anda dapat menemukan foto berdasarkan event atau lokasi, memilih frame terbaik melalui preview yang jernih, menyelesaikan pembelian via QRIS, dan langsung mengunduh file original berkualitas tinggi.
                        </p>

                        <dl class="mt-6 divide-y divide-public-line border-y border-public-line text-xs">
                            <div class="flex items-center justify-between py-3">
                                <dt class="font-bold uppercase tracking-wider text-public-muted">Eksplorasi Cepat</dt>
                                <dd class="font-extrabold text-public-ink">Filter Event, Lokasi, Tanggal &amp; Waktu</dd>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <dt class="font-bold uppercase tracking-wider text-public-muted">Transaksi Instan</dt>
                                <dd class="font-extrabold text-public-ink">Keranjang Ringkas, Voucher &amp; QRIS</dd>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <dt class="font-bold uppercase tracking-wider text-public-muted">Kualitas Arsip</dt>
                                <dd class="font-extrabold text-public-ink">File Original Tanpa Watermark</dd>
                            </div>
                        </dl>

                        <div class="mt-7">
                            <a href="{{ route('galeri') }}" class="about-link text-public-ink">
                                <span>Cari Momen di Galeri</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- 5. BRAND STATEMENT --}}
        <section
            class="border-b border-public-line bg-public-bone py-24 sm:py-32 lg:py-40"
            aria-labelledby="about-statement-title"
        >
            <div class="public-container">
                <div class="grid gap-8 lg:grid-cols-12" data-reveal>
                    <div class="lg:col-span-3">
                        <p class="about-eyebrow">04 / Philosophy</p>
                    </div>

                    <div class="lg:col-span-9">
                        <blockquote id="about-statement-title" class="about-statement text-public-ink">
                            &ldquo;A photograph is more than an image. <span class="font-display italic font-normal">It&rsquo;s a moment that found its way back to you.&rdquo;</span>
                        </blockquote>

                        <div class="mt-10 flex items-center gap-4 border-t border-public-line pt-6">
                            <span class="h-1.5 w-1.5 bg-public-ink" aria-hidden="true"></span>
                            <p class="font-sans text-xs font-bold uppercase tracking-widest text-public-muted">
                                JEPRET EDITORIAL ARCHIVE &mdash; EVERY FRAME MATTERS
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 6. FINAL CTA --}}
        <section
            class="border-b border-white/15 bg-public-ink py-20 text-white sm:py-28 lg:py-32"
            aria-labelledby="about-cta-title"
        >
            <div class="public-container">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-end" data-reveal>
                    <div class="lg:col-span-8">
                        <p class="about-eyebrow !text-white/60">05 / Start Exploring</p>
                        <h2 id="about-cta-title" class="about-cta mt-6 text-white">
                            The moment happened. <span class="font-display italic font-normal">Now find it.</span>
                        </h2>
                        <p class="mt-6 max-w-xl text-base font-medium leading-relaxed text-white/70">
                            Jelajahi galeri foto event dari berbagai kota atau mulai publikasikan karya fotografi Anda di ekosistem Jepret hari ini.
                        </p>
                    </div>

                    <div class="flex flex-col items-stretch gap-4 sm:flex-row sm:items-center lg:col-span-4 lg:justify-end">
                        <a
                            href="{{ route('galeri') }}"
                            class="inline-flex min-h-12 items-center justify-center gap-3 border border-white bg-white px-7 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-all duration-200 hover:-translate-y-0.5 hover:bg-public-bone"
                        >
                            <span>Jelajahi Galeri</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>

                        @auth
                            @if(auth()->user()->role === 'fotografer')
                                <a
                                    href="{{ route('fotografer.dashboard') }}"
                                    class="inline-flex min-h-12 items-center justify-center border border-white/30 bg-transparent px-6 text-xs font-extrabold uppercase tracking-wider text-white transition-all duration-200 hover:-translate-y-0.5 hover:border-white hover:bg-white/10"
                                >
                                    Dashboard Fotografer
                                </a>
                            @else
                                <a
                                    href="{{ route('photographers.index') }}"
                                    class="inline-flex min-h-12 items-center justify-center border border-white/30 bg-transparent px-6 text-xs font-extrabold uppercase tracking-wider text-white transition-all duration-200 hover:-translate-y-0.5 hover:border-white hover:bg-white/10"
                                >
                                    Lihat Fotografer
                                </a>
                            @endif
                        @else
                            <a
                                href="{{ route('register', ['role' => 'fotografer']) }}"
                                class="inline-flex min-h-12 items-center justify-center border border-white/30 bg-transparent px-6 text-xs font-extrabold uppercase tracking-wider text-white transition-all duration-200 hover:-translate-y-0.5 hover:border-white hover:bg-white/10"
                            >
                                Gabung Fotografer
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        <style>
            .about-page {
                background: var(--public-paper, #ffffff);
            }
            .about-eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 0.75rem;
                font-size: 0.75rem;
                font-weight: 800;
                line-height: 1.3;
                letter-spacing: 0.1em !important;
                text-transform: uppercase;
                color: var(--public-muted, #5f6875);
            }
            .about-eyebrow::before {
                content: "";
                display: inline-block;
                width: 1.75rem;
                height: 1px;
                background: currentColor;
            }
            .about-display {
                max-width: none;
                font-size: clamp(3rem, 7.2vw, 6.75rem);
                font-weight: 700;
                line-height: 0.92;
                letter-spacing: -0.055em !important;
                text-wrap: balance;
            }
            .about-statement {
                font-size: clamp(2.15rem, 4.6vw, 4.5rem);
                font-weight: 700;
                line-height: 1.04;
                letter-spacing: -0.045em !important;
                text-wrap: balance;
            }
            .about-cta {
                max-width: none;
                font-size: clamp(2.5rem, 5.4vw, 5.25rem);
                font-weight: 700;
                line-height: 0.95;
                letter-spacing: -0.05em !important;
                text-wrap: balance;
            }
            .about-step-row {
                transition: background-color 280ms ease, transform 320ms cubic-bezier(0.22, 1, 0.36, 1);
            }
            .about-step-num {
                font-size: clamp(2.25rem, 3.8vw, 3.5rem);
                line-height: 0.9;
                letter-spacing: -0.04em !important;
                transition: transform 320ms cubic-bezier(0.22, 1, 0.36, 1), color 280ms ease;
            }
            .about-link {
                display: inline-flex;
                align-items: center;
                gap: 0.625rem;
                font-size: 0.75rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.08em !important;
                padding-bottom: 0.25rem;
                border-bottom: 1px solid currentColor;
                transition: gap 240ms cubic-bezier(0.22, 1, 0.36, 1), opacity 200ms ease;
            }
            @media (hover: hover) and (pointer: fine) {
                .about-step-row:hover {
                    background-color: rgba(16, 17, 20, 0.025);
                }
                .about-step-row:hover .about-step-num {
                    color: var(--public-ink, #101114);
                    transform: translateX(6px);
                }
                .about-link:hover {
                    gap: 0.95rem;
                }
            }
            @media (prefers-reduced-motion: reduce) {
                .about-step-row,
                .about-step-num,
                .about-link {
                    transition: none;
                }
            }
        </style>
    </article>
</x-marketplace-layout>
