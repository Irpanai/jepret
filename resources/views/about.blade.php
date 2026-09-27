<x-marketplace-layout
    title="Tentang Jepret — Every Moment Has a Story"
    description="Jepret menghubungkan fotografer event dengan orang-orang yang ingin menemukan, membeli, dan menyimpan momen mereka."
>
    <article class="about-page">
        <section class="about-hero relative min-h-[34rem] overflow-hidden border-b border-public-line sm:min-h-[38rem] lg:min-h-[42rem]" aria-labelledby="about-hero-title" data-hero-reveal>
            <div class="about-hero-shape" aria-hidden="true"></div>
            <div class="public-container relative z-10 grid min-h-[34rem] content-between gap-10 py-10 sm:min-h-[38rem] sm:py-12 lg:min-h-[42rem] lg:py-14">
                <p class="public-kicker" data-hero-copy style="--reveal-index: 0">About Jepret</p>

                <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
                    <h1 id="about-hero-title" class="about-display lg:col-span-9">
                        <span class="hero-reveal-line"><span data-hero-word style="--reveal-index: 0">Every Moment</span></span>
                        <span class="hero-reveal-line"><span class="public-serif" data-hero-word style="--reveal-index: 1">Has a Story.</span></span>
                    </h1>
                    <p class="max-w-xl text-base font-semibold leading-7 text-public-muted sm:text-lg lg:col-span-3 lg:pb-2" data-hero-copy style="--reveal-index: 2">
                        Jepret adalah marketplace fotografi event yang menghubungkan fotografer dengan peserta event. Temukan, beli, dan simpan setiap momen berharga melalui satu platform digital.
                    </p>
                </div>
            </div>
        </section>

        <section class="bg-public-bone py-14 sm:py-18 lg:py-20" aria-labelledby="how-title">
            <div class="public-container">
                <div class="grid gap-8 lg:grid-cols-12">
                    <header class="lg:col-span-5" data-reveal>
                        <p class="public-kicker">Cara kerja</p>
                        <h2 id="how-title" class="public-heading mt-5 max-w-2xl">Dari lintasan ke <span class="public-serif">arsip pribadi.</span></h2>
                    </header>

                    <ol class="grid border-l border-t border-public-line sm:grid-cols-3 lg:col-span-7">
                        @foreach([
                            ['01', 'Cari', 'Temukan foto berdasarkan event, lokasi, tanggal, fotografer, atau kata kunci.'],
                            ['02', 'Beli', 'Pilih foto favorit, masukkan ke keranjang, lalu selesaikan pembayaran.'],
                            ['03', 'Download', 'Unduh versi pembelian setelah transaksi berhasil dikonfirmasi.'],
                        ] as $index => [$number, $title, $copy])
                            <li class="about-step group flex min-h-52 flex-col justify-between border-b border-r border-public-line bg-white p-5 sm:min-h-60 sm:p-6" data-reveal style="--about-delay: {{ $index * 80 }}ms">
                                <span class="text-xs font-extrabold text-public-muted">{{ $number }}</span>
                                <div>
                                    <h3 class="text-2xl font-extrabold sm:text-3xl">{{ $title }}</h3>
                                    <p class="mt-3 text-sm font-semibold leading-6 text-public-muted">{{ $copy }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>

        <section class="py-14 sm:py-18 lg:py-20" aria-labelledby="sides-title">
            <div class="public-container">
                <header class="grid gap-5 border-b border-public-line pb-7 lg:grid-cols-12 lg:items-end" data-reveal>
                    <p class="public-kicker lg:col-span-3">Two sides, one platform</p>
                    <h2 id="sides-title" class="public-heading lg:col-span-9">Built for Photographers.<br><span class="public-serif">Made for Everyone.</span></h2>
                </header>

                <div class="grid lg:grid-cols-2">
                    <article class="border-b border-public-line py-8 lg:border-r lg:pr-8" data-reveal>
                        <div class="about-image-reveal aspect-[4/3] overflow-hidden bg-public-bone">
                            <img src="{{ asset('images/photographers.jpg') }}" alt="Fotografer memotret dalam sebuah sesi" width="720" height="400" loading="lazy" decoding="async" class="h-full w-full object-cover grayscale">
                        </div>
                        <div class="grid gap-5 pt-7 sm:grid-cols-[auto_1fr] sm:gap-8">
                            <span class="public-kicker">01 / Photographer</span>
                            <div>
                                <h3 class="text-3xl font-extrabold">Karya bertemu audiensnya.</h3>
                                <p class="mt-4 max-w-xl font-semibold leading-7 text-public-muted">Bangun portofolio, publikasikan karya, kelola koleksi event, dan jangkau pelanggan melalui satu ruang kerja.</p>
                            </div>
                        </div>
                    </article>

                    <article class="border-b border-public-line py-8 lg:pl-8 lg:pt-16" data-reveal style="--about-delay: 100ms">
                        <div class="about-image-reveal aspect-[4/3] overflow-hidden bg-public-bone">
                            <img src="{{ asset('images/herophotographer.jpg') }}" alt="Peserta event di depan kamera fotografer" width="736" height="981" loading="lazy" decoding="async" class="h-full w-full object-cover grayscale">
                        </div>
                        <div class="grid gap-5 pt-7 sm:grid-cols-[auto_1fr] sm:gap-8">
                            <span class="public-kicker">02 / Buyer</span>
                            <div>
                                <h3 class="text-3xl font-extrabold">Momen kembali ke pemiliknya.</h3>
                                <p class="mt-4 max-w-xl font-semibold leading-7 text-public-muted">Temukan foto event, pilih momen favorit, lakukan pembelian, dan unduh foto sesuai akses transaksi.</p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="border-y border-public-line bg-public-bone py-14 sm:py-18 lg:py-20" aria-labelledby="why-title">
            <div class="public-container">
                <div class="grid gap-8 lg:grid-cols-12">
                    <header class="lg:col-span-5" data-reveal>
                        <p class="public-kicker">Why Jepret</p>
                        <h2 id="why-title" class="public-heading mt-5">Made for <span class="public-serif">Every Moment.</span></h2>
                    </header>

                    <div class="grid border-l border-t border-public-line sm:grid-cols-2 lg:col-span-7">
                        @foreach([
                            ['search', 'Easy Discovery', 'Cari koleksi berdasarkan event, lokasi, tanggal, fotografer, dan kategori.'],
                            ['shield', 'Protected Preview', 'Preview publik dilindungi watermark sebelum transaksi selesai.'],
                            ['cart', 'Simple Checkout', 'Keranjang menyatukan foto pilihan dalam alur pembayaran yang ringkas.'],
                            ['frame', 'Photographer Portfolio', 'Profil publik menampilkan karya aktif dan koleksi fotografer.'],
                        ] as $index => [$icon, $title, $copy])
                            <article class="about-feature border-b border-r border-public-line p-6 sm:min-h-52" data-reveal style="--about-delay: {{ ($index % 2) * 80 }}ms">
                                <div class="flex h-full flex-col justify-between gap-8">
                                    @if($icon === 'search')
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
                                    @elseif($icon === 'shield')
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3 5 6v5c0 4.6 2.8 8.1 7 10 4.2-1.9 7-5.4 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>
                                    @elseif($icon === 'cart')
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.9-1.4L21 8H6"></path><circle cx="10" cy="20" r="1"></circle><circle cx="18" cy="20" r="1"></circle></svg>
                                    @else
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="4" width="18" height="16"></rect><circle cx="9" cy="10" r="2"></circle><path d="m5 18 5-5 3 3 2-2 4 4"></path></svg>
                                    @endif
                                    <div>
                                        <h3 class="text-xl font-extrabold">{{ $title }}</h3>
                                        <p class="mt-3 text-sm font-semibold leading-6 text-public-muted">{{ $copy }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="py-14 sm:py-18 lg:py-24" aria-labelledby="vision-title">
            <div class="public-container">
                <div class="grid gap-8 lg:grid-cols-12" data-reveal>
                    <p class="public-kicker lg:col-span-3">Our vision</p>
                    <div class="lg:col-span-9">
                        <h2 id="vision-title" class="about-vision">Connecting Moments.<br><span class="public-serif">Empowering Creators.</span></h2>
                        <div class="mt-8 grid gap-5 border-t border-public-line pt-5 sm:grid-cols-2 lg:ml-auto lg:max-w-3xl">
                            <span class="public-kicker">Visi Jepret</span>
                            <p class="text-base font-semibold leading-7 text-public-muted sm:text-lg">Membangun ekosistem fotografi digital yang mempertemukan karya fotografer dengan orang-orang yang menghargai setiap momen, sekaligus membuka peluang ekonomi bagi kreator fotografi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-public-ink py-14 text-white sm:py-18 lg:py-20" aria-labelledby="cta-title">
            <div class="public-container" data-reveal>
                <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-8">
                        <p class="text-[0.68rem] font-extrabold uppercase text-white/50">Start your story</p>
                        <h2 id="cta-title" class="about-cta mt-5">Your Next Moment <span class="public-serif">Starts Here.</span></h2>
                    </div>
                    <div class="lg:col-span-4">
                        <p class="max-w-lg font-semibold leading-7 text-white/65">Jelajahi koleksi foto dari berbagai event atau bergabung sebagai fotografer untuk mulai membagikan karyamu.</p>
                        <div class="mt-7 flex flex-col gap-3 sm:flex-row lg:flex-col xl:flex-row">
                            <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center border border-white bg-white px-5 text-xs font-extrabold uppercase text-public-ink transition-colors duration-200 hover:bg-public-bone">Join as Photographer</a>
                            <a href="{{ route('galeri') }}" class="inline-flex min-h-12 items-center justify-center border border-white/40 px-5 text-xs font-extrabold uppercase text-white transition-colors duration-200 hover:border-white hover:bg-white hover:text-public-ink">Explore Jepret</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </article>
</x-marketplace-layout>
