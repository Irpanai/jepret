<section class="border-b border-public-line bg-public-ink text-white" aria-labelledby="photographer-cta-title">
    <div class="public-container py-16 sm:py-24">
        <div class="grid items-stretch gap-10 lg:grid-cols-12" data-reveal>
            {{-- Visual Documentary Column --}}
            <div class="relative min-h-[320px] overflow-hidden border border-white/20 bg-neutral-900 sm:min-h-[420px] lg:col-span-5 lg:min-h-full">
                <img
                    src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?auto=format&fit=crop&w=1200&q=85"
                    alt="Fotografer profesional memfokuskan lensa kamera dalam liputan event"
                    loading="lazy"
                    decoding="async"
                    class="h-full w-full object-cover object-center grayscale contrast-125 transition-transform duration-700 hover:scale-105"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-public-ink via-transparent to-transparent opacity-80 lg:hidden"></div>
                <div class="absolute bottom-4 left-4 border border-white/20 bg-public-ink/90 px-3 py-1.5 font-sans text-[10px] font-extrabold uppercase tracking-widest text-white backdrop-blur-sm">
                    KEMITRAAN KREATOR &middot; 90% BAGI HASIL
                </div>
            </div>

            {{-- Editorial Value & CTA Column --}}
            <div class="flex flex-col justify-between lg:col-span-7 lg:pl-6">
                <div>
                    <p class="font-sans text-xs font-extrabold uppercase tracking-widest text-white/50">
                        PROGRAM KEMITRAAN PHOTOGRAPHER
                    </p>
                    <h2 id="photographer-cta-title" class="landing-heading mt-4 text-white">
                        Jepret Momen. Jual Foto. <span class="font-display italic">Hasilkan Uang.</span>
                    </h2>
                    <p class="mt-5 max-w-xl text-sm font-semibold leading-relaxed text-white/70">
                        Bangun portofolio profesional dan distribusikan foto event tanpa khawatir pembajakan. Jepret menangani proteksi preview, payment gateway otomatis, serta pengiriman instan file original beresolusi tinggi langsung ke pembeli.
                    </p>

                    <dl class="mt-8 divide-y divide-white/15 border-y border-white/15 text-sm">
                        <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                            <dt class="font-extrabold text-white">Unggah Per Event</dt>
                            <dd class="text-xs font-semibold text-white/70 sm:col-span-2">Organisir ribuan foto berdasarkan nama event, tanggal, dan lokasi secara rapi.</dd>
                        </div>
                        <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                            <dt class="font-extrabold text-white">Protected Preview</dt>
                            <dd class="text-xs font-semibold text-white/70 sm:col-span-2">Watermark sistem otomatis mengunci preview publik. File resolusi penuh tersimpan privat.</dd>
                        </div>
                        <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                            <dt class="font-extrabold text-white">Bagi Hasil Adil</dt>
                            <dd class="text-xs font-semibold text-white/70 sm:col-span-2">Skema pendapatan transparan 90% untuk fotografer dan 10% untuk operasional platform.</dd>
                        </div>
                    </dl>

                    {{-- 90/10 Split Visualizer --}}
                    <div class="mt-8 border border-white/20 bg-white/5 p-4 sm:p-5">
                        <div class="flex items-center justify-between text-xs font-extrabold uppercase tracking-wider">
                            <span class="text-white">90% Pendapatan Fotografer</span>
                            <span class="text-white/50">10% Platform</span>
                        </div>
                        <div class="mt-3 flex h-3.5 w-full overflow-hidden border border-white/30 bg-white/10" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" aria-label="Bagi hasil penjualan 90 persen fotografer">
                            <div class="h-full bg-white transition-all duration-700" style="width: 90%"></div>
                            <div class="h-full bg-transparent" style="width: 10%"></div>
                        </div>
                        <p class="mt-2 text-[11px] font-semibold text-white/60">
                            Tanpa potongan tersembunyi. Penarikan dana (withdrawal) dapat diajukan langsung dari dashboard fotografer kapan saja.
                        </p>
                    </div>
                </div>

                {{-- Action Links --}}
                <div class="mt-10 flex flex-wrap items-center gap-4">
                    @auth
                        @if(auth()->user()->role === 'fotografer')
                            <a href="{{ route('fotografer.dashboard') }}" class="landing-cta inline-flex min-h-12 items-center gap-3 border border-white bg-white px-7 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-all hover:bg-neutral-200">
                                <span>Buka Dashboard Fotografer</span>
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="landing-cta inline-flex min-h-12 items-center gap-3 border border-white bg-white px-7 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-all hover:bg-neutral-200">
                                <span>Daftar Sebagai Photographer</span>
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register', ['role' => 'fotografer']) }}" class="landing-cta inline-flex min-h-12 items-center gap-3 border border-white bg-white px-7 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-all hover:bg-neutral-200">
                            <span>Daftar Sebagai Photographer</span>
                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                        </a>
                        <a href="{{ route('pricing') }}" class="landing-link text-xs font-extrabold uppercase tracking-wider text-white/80 hover:text-white">
                            Pelajari Pilihan Paket & Kuota &rarr;
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>
