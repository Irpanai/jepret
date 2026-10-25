@php
    $steps = [
        [
            'num' => '01',
            'label' => 'Tahap 01',
            'title' => 'Pilih Foto & Keranjang',
            'description' => 'Buka halaman galeri foto yang Anda inginkan, lalu klik tombol "TAMBAH KE KERANJANG".',
            'image' => asset('images/modulsection/user1.png'),
            'alt' => 'Tangkapan layar halaman galeri foto dan tombol Tambah ke Keranjang',
            'badge' => 'Eksplorasi Galeri',
            'path_label' => 'galeri/detail-foto',
            'action_highlight' => 'Pilih foto di galeri dan klik tombol "TAMBAH KE KERANJANG"',
        ],
        [
            'num' => '02',
            'label' => 'Tahap 02',
            'title' => 'Konfirmasi Pembelian',
            'description' => 'Periksa kembali identitas pembeli dan pesanan. Masukkan voucher jika ada, lalu klik "BUAT ORDER PEMBAYARAN".',
            'image' => asset('images/modulsection/user2.png'),
            'alt' => 'Tangkapan layar halaman konfirmasi pembelian dan ringkasan pesanan',
            'badge' => 'Ringkasan & Promo',
            'path_label' => 'checkout/konfirmasi',
            'action_highlight' => 'Periksa data pembeli, terapkan voucher diskon, dan buat order pembayaran',
        ],
        [
            'num' => '03',
            'label' => 'Tahap 03',
            'title' => 'Pembayaran QRIS',
            'description' => 'Sistem akan menampilkan kode QR. Pindai barcode menggunakan aplikasi mobile banking atau dompet digital sebelum batas waktu habis.',
            'image' => asset('images/modulsection/user3.png'),
            'alt' => 'Tangkapan layar kode QRIS dinamis untuk pembayaran otomatis',
            'badge' => 'QRIS Realtime',
            'path_label' => 'pembayaran/qris-instant',
            'action_highlight' => 'Pindai kode QRIS dengan Mobile Banking atau E-wallet lainnya',
        ],
        [
            'num' => '04',
            'label' => 'Tahap 04',
            'title' => 'Unduh Foto Original',
            'description' => 'Setelah pembayaran berhasil, buka menu pesanan. File foto original tanpa watermark kini siap diunduh atau dibagikan.',
            'image' => asset('images/modulsection/user4.png'),
            'alt' => 'Tangkapan layar file foto original resolusi tinggi tanpa watermark siap diunduh',
            'badge' => 'Bebas Watermark',
            'path_label' => 'pesanan/download-original',
            'action_highlight' => 'Buka riwayat pesanan dan unduh file original resolusi penuh tanpa watermark',
        ],
    ];
@endphp

<section class="border-b border-public-line bg-public-bone py-16 sm:py-24" aria-labelledby="how-it-works-title" x-data="{
        activeTab: 0,
        autoplay: true,
        inView: false,
        progress: 0,
        timer: null,
        duration: 5000,
        totalSteps: {{ count($steps) }},
        steps: {{ Js::from($steps) }},
        zoomModal: false,
        activeImage: '',
        activeTitle: '',
        activeDesc: '',
        init() {
            const observer = new IntersectionObserver(([entry]) => {
                this.inView = entry.isIntersecting;
            }, { threshold: 0.25 });
            observer.observe(this.$el);
            this.startTimer();
        },
        destroy() {
            if (this.timer) clearInterval(this.timer);
        },
        startTimer() {
            if (this.timer) clearInterval(this.timer);
            this.progress = 0;
            const tick = 50;
            const stepInc = (tick / this.duration) * 100;
            this.timer = setInterval(() => {
                if (!this.autoplay || !this.inView || this.zoomModal || document.hidden) return;
                this.progress += stepInc;
                if (this.progress >= 100) {
                    this.nextTab();
                }
            }, tick);
        },
        pauseAutoplay() {
            this.autoplay = false;
        },
        resumeAutoplay() {
            this.autoplay = true;
        },
        toggleAutoplay() {
            this.autoplay = !this.autoplay;
        },
        selectTab(idx) {
            this.activeTab = idx;
            this.startTimer();
        },
        nextTab() {
            this.activeTab = (this.activeTab + 1) % this.totalSteps;
            this.progress = 0;
        },
        prevTab() {
            this.activeTab = (this.activeTab - 1 + this.totalSteps) % this.totalSteps;
            this.progress = 0;
        },
        openZoom(img, title, desc) {
            this.activeImage = img;
            this.activeTitle = title;
            this.activeDesc = desc;
            this.zoomModal = true;
        }
    }" @keydown.left.prevent="prevTab()" @keydown.right.prevent="nextTab()">
    <div class="public-container">
        {{-- 1. Header (Tengah Atas / Center-Aligned) --}}
        <div class="mx-auto max-w-3xl text-center" data-reveal>
            <p class="public-kicker">Cara Kerja Jepret</p>
            <h2 id="how-it-works-title" class="landing-heading mt-3 text-public-ink">
                Temukan, Bayar, dan <span class="font-display italic">Simpan Fotomu!</span>
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-sm font-semibold leading-relaxed text-public-muted sm:text-base">
                Kami tahu lelahnya mencari foto sendiri di ribuan file Google Drive dan harus chat fotografer satu per
                satu. Lewat sistem otomatis Jepret, cukup ikuti 4 tahap sederhana untuk menemukan momen terbaik Anda dan
                mengunduh foto original tanpa watermark. </p>
        </div>

        {{-- 2. Minimalist Text Tabs (Tanpa Box / Border Kasar & Responsif Penuh) --}}
        <div class="mx-auto mt-8 w-full max-w-4xl px-4 sm:mt-12 sm:px-6 lg:max-w-5xl" data-reveal>
            {{-- Tab Indicators (Grid 4 Kolom di Mobile & Desktop) --}}
            <div class="grid w-full grid-cols-4 gap-2.5 sm:gap-4 md:gap-6 lg:gap-8" role="tablist"
                aria-label="Tahapan Transaksi Jepret">
                @foreach($steps as $index => $step)
                    <button type="button" x-ref="tab_{{ $index }}" @click="selectTab({{ $index }})"
                        class="group relative flex w-full cursor-pointer flex-col justify-start text-left focus:outline-none transition-all duration-300"
                        role="tab" :aria-selected="activeTab === {{ $index }}" :id="'tab-button-' + {{ $index }}"
                        :aria-controls="'tab-panel-' + {{ $index }}">
                        {{-- Progress Bar Indicator (Garis Tipis di Atas Tab) --}}
                        <div class="relative h-[2px] w-full overflow-hidden rounded-full bg-public-line/60"
                            aria-hidden="true">
                            <div class="h-full bg-public-ink transition-all duration-75"
                                :style="activeTab === {{ $index }} ? `width: ${progress}%` : (activeTab > {{ $index }} ? 'width: 100%' : 'width: 0%')">
                            </div>
                        </div>

                        {{-- Step Numeral & Kicker --}}
                        <div class="mt-2 flex items-center gap-1.5 sm:mt-3 sm:gap-2">
                            <span
                                class="font-mono text-xs font-bold tracking-wider transition-colors duration-200 sm:text-xs md:text-sm"
                                :class="activeTab === {{ $index }} ? 'text-public-ink font-black' : 'text-public-muted/50 group-hover:text-public-muted'">
                                {{ $step['num'] }}
                            </span>
                            <span class="text-public-line hidden sm:inline" aria-hidden="true">&bull;</span>
                            <span
                                class="font-mono text-[9px] font-extrabold uppercase tracking-widest transition-colors duration-200 hidden sm:inline sm:text-[10px]"
                                :class="activeTab === {{ $index }} ? 'text-public-ink' : 'text-public-muted/50 group-hover:text-public-muted'">
                                {{ $step['label'] }}
                            </span>
                        </div>

                        {{-- Desktop Only: Title & Description inside each column --}}
                        <div class="hidden w-full md:block">
                            <h3 class="mt-2 text-base font-extrabold tracking-tight transition-colors duration-200 leading-snug whitespace-normal break-words"
                                :class="activeTab === {{ $index }} ? 'text-public-ink' : 'text-public-muted/60 group-hover:text-public-ink/80'">
                                {{ $step['title'] }}
                            </h3>
                            <p class="mt-1 text-sm font-medium leading-relaxed transition-colors duration-200 whitespace-normal break-words"
                                :class="activeTab === {{ $index }} ? 'text-public-muted' : 'text-public-muted/40'">
                                {{ $step['description'] }}
                            </p>
                        </div>
                    </button>
                @endforeach
            </div>

            {{-- Mobile Only: Active Step Content (Full Screen Width w-full, No Truncation, Wraps Naturally) --}}
            <div class="mt-4 w-full md:hidden" aria-live="polite">
                @foreach($steps as $index => $step)
                    <div x-show="activeTab === {{ $index }}" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0" class="w-full text-left">
                        <div class="mb-1 flex items-center gap-2">
                            <span
                                class="rounded bg-public-ink/10 px-2 py-0.5 font-mono text-[10px] font-extrabold uppercase tracking-widest text-public-ink">
                                {{ $step['label'] }}
                            </span>
                            <span class="font-mono text-[10px] font-bold text-public-muted"
                                x-text="'Langkah ' + (activeTab + 1) + ' dari 4'"></span>
                        </div>
                        <h3
                            class="text-base font-extrabold tracking-tight text-public-ink leading-snug whitespace-normal break-words">
                            {{ $step['title'] }}
                        </h3>
                        <p class="mt-1 text-sm font-medium leading-relaxed text-public-muted whitespace-normal break-words">
                            {{ $step['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 3. Image Showcase Container (Bawah - Floating Studio Frame dengan Batas Ukuran & Ruang Putih) --}}
        <div class="mx-auto mt-8 sm:mt-10 max-w-4xl lg:max-w-5xl px-0 sm:px-4" data-reveal>
            <div class="group relative overflow-hidden rounded-2xl border border-public-line/80 bg-neutral-950 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.22)] ring-1 ring-black/10 transition-all duration-300"
                @mouseenter="pauseAutoplay()" @mouseleave="resumeAutoplay()">
                {{-- Browser / Studio Mockup Header --}}
                <div
                    class="flex items-center justify-between border-b border-white/10 bg-neutral-900/95 px-4 py-3 backdrop-blur-md sm:px-5">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5" aria-hidden="true">
                            <span class="h-2.5 w-2.5 rounded-full bg-[#ff5f57] shadow-sm"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-[#febc2e] shadow-sm"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-[#28c840] shadow-sm"></span>
                        </div>
                        <div
                            class="ml-2 hidden items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-0.5 font-mono text-[10px] text-white/50 md:inline-flex">
                            <svg class="h-3 w-3 text-white/40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <span class="text-white/30">https://</span>
                            <span>jepret.web.id/</span><span class="text-white/80"
                                x-text="steps[activeTab].path_label"></span>
                        </div>
                    </div>

                    {{-- Controls: Autoplay status & Navigation arrows --}}
                    <div class="flex items-center gap-2">
                        <button type="button" @click="toggleAutoplay()"
                            class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/5 px-2.5 py-1 font-mono text-[9px] font-extrabold uppercase tracking-wider text-white/70 hover:border-white/30 hover:bg-white/10 hover:text-white transition-colors"
                            :title="autoplay ? 'Jeda pergantian otomatis' : 'Mulai pergantian otomatis'">
                            <span class="inline-block h-1.5 w-1.5 rounded-full"
                                :class="autoplay ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400'"></span>
                            <span x-text="autoplay ? 'Auto (5s)' : 'Dijeda'"></span>
                        </button>

                        <div class="h-3 w-px bg-white/15 mx-0.5"></div>

                        <button type="button" @click="prevTab()"
                            class="grid h-7 w-7 place-items-center rounded-full border border-white/15 bg-white/5 text-white/70 hover:border-white/30 hover:bg-white/10 hover:text-white transition-colors"
                            title="Tahap sebelumnya" aria-label="Tahap sebelumnya">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M10 12l-4-4 4-4" />
                            </svg>
                        </button>
                        <button type="button" @click="nextTab()"
                            class="grid h-7 w-7 place-items-center rounded-full border border-white/15 bg-white/5 text-white/70 hover:border-white/30 hover:bg-white/10 hover:text-white transition-colors"
                            title="Tahap berikutnya" aria-label="Tahap berikutnya">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M6 4l4 4-4 4" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Main Viewport: Smooth cross-fade transition --}}
                <div class="relative aspect-[16/10] sm:aspect-[16/9] w-full overflow-hidden bg-neutral-900">
                    @foreach($steps as $index => $step)
                        <div x-show="activeTab === {{ $index }}"
                            x-transition:enter="transition-opacity ease-out duration-400"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity ease-in duration-300 absolute inset-0"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="h-full w-full"
                            role="tabpanel" :id="'tab-panel-' + {{ $index }}"
                            :aria-labelledby="'tab-button-' + {{ $index }}">
                            <img src="{{ $step['image'] }}" alt="{{ $step['alt'] }}"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}" decoding="async"
                                class="h-full w-full object-cover object-top">
                        </div>
                    @endforeach

                    {{-- Click Overlay to trigger full preview modal --}}
                    <button type="button"
                        @click="openZoom(steps[activeTab].image, steps[activeTab].title, steps[activeTab].description)"
                        class="group/zoom absolute inset-0 flex items-end justify-end p-4 sm:p-5 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300 focus:opacity-100 focus:outline-none"
                        title="Klik untuk melihat tangkapan layar ukuran penuh">
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-public-ink/90 px-3.5 py-1.5 font-mono text-[10px] font-extrabold uppercase tracking-widest text-white backdrop-blur-md shadow-lg group-hover/zoom:border-white transition-colors">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                                <path d="M11 8v6M8 11h6" />
                            </svg>
                            <span>Perbesar Screenshot</span>
                        </span>
                    </button>
                </div>

                {{-- Bottom Status Strip --}}
                <div
                    class="flex flex-col gap-3 border-t border-white/10 bg-neutral-900/95 px-4 py-3 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex h-5 items-center rounded-full border border-white/15 bg-white/10 px-2 font-mono text-[9px] font-extrabold uppercase tracking-widest text-white"
                            x-text="'Tahap ' + steps[activeTab].num"></span>
                        <p class="text-xs font-semibold text-white/80" x-text="steps[activeTab].action_highlight"></p>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <button type="button"
                            @click="openZoom(steps[activeTab].image, steps[activeTab].title, steps[activeTab].description)"
                            class="inline-flex min-h-8 items-center gap-1.5 rounded-full border border-white/20 bg-white/5 px-3 text-[10px] font-extrabold uppercase tracking-wider text-white transition-all hover:border-white hover:bg-white hover:text-public-ink">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                            <span>Perbesar</span>
                        </button>
                        <a href="{{ route('galeri') }}"
                            class="inline-flex min-h-8 items-center gap-1.5 rounded-full border border-white bg-white px-3.5 text-[10px] font-extrabold uppercase tracking-wider text-public-ink transition-all hover:bg-neutral-200">
                            <span>Buka Galeri</span>
                            <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 8h11M9 4l4 4-4 4" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lightbox Zoom Modal (Resolusi Penuh) --}}
    <template x-teleport="body">
        <div x-cloak x-show="zoomModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-public-ink/80 p-4 backdrop-blur-sm sm:p-6"
            @keydown.escape.window="zoomModal = false" role="dialog" aria-modal="true" :aria-label="activeTitle">
            <div class="fixed inset-0" @click="zoomModal = false" aria-hidden="true"></div>

            <div x-show="zoomModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-public-line bg-white shadow-2xl"
                @click.stop>
                <div class="flex items-center justify-between border-b border-public-line bg-public-bone px-5 py-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-public-ink"></span>
                        <h4 class="text-sm font-extrabold uppercase tracking-wider text-public-ink"
                            x-text="activeTitle"></h4>
                    </div>
                    <button type="button" @click="zoomModal = false"
                        class="grid h-8 w-8 place-items-center rounded-full border border-public-line bg-white text-public-ink transition-colors hover:border-public-ink hover:bg-public-ink hover:text-white"
                        aria-label="Tutup preview">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-auto bg-neutral-950 p-2 sm:p-4 text-center">
                    <img :src="activeImage" :alt="activeTitle"
                        class="mx-auto h-auto max-h-[72vh] w-auto max-w-full object-contain shadow-md rounded-lg">
                </div>

                <div class="border-t border-public-line bg-white px-5 py-3">
                    <p class="text-xs font-semibold text-public-muted" x-text="activeDesc"></p>
                </div>
            </div>
        </div>
    </template>
</section>