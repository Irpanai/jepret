@php
    $features = [
        [
            'id' => 1,
            'title' => 'Profil Profesional yang Meyakinkan',
            'desc' => 'Tampil lebih tepercaya di mata klien. Setiap fotografer dibekali halaman profil publik yang interaktif. Anda dapat menampilkan foto profil, bio singkat, spesialisasi, tautan kontak langsung (WhatsApp & Instagram), detail kamera yang dipakai, hingga statistik performa portofolio Anda.',
            'image' => asset('images/modulsection/fotografer0.png'),
        ],
        [
            'id' => 2,
            'title' => 'Dashboard Analitik Terpusat',
            'desc' => 'Ketahui persis seberapa diminati karya Anda. Dasbor utama menyajikan data metrik real-time, mulai dari total kunjungan foto, rasio konversi pembelian, grafik penjualan, hingga akumulasi pendapatan bersih Anda.',
            'image' => asset('images/modulsection/fotografer1.png'),
        ],
        [
            'id' => 3,
            'title' => 'Manajemen Inventaris Kamera',
            'desc' => 'Data dan kelompokkan Kamera yang Anda pakai. Tambahkan nama body kamera, jenis lensa, dan catatan operasional agar pembeli tahu kualitas alat yang Anda gunakan di lapangan.',
            'image' => asset('images/modulsection/fotografer2.png'),
        ],
        [
            'id' => 4,
            'title' => 'Upload Cepat & Proteksi Watermark Kustom',
            'desc' => 'Unggah puluhan konten foto hasil shoot sekaligus tanpa repot. Lindungi karya Anda sebelum terjual dengan fitur watermark batch menggunakan logo transparan milik Anda sendiri. Anda memiliki kontrol penuh untuk mengatur ukuran dan tingkat transparansi (opacity).',
            'image' => asset('images/modulsection/fotografer3.png'),
        ],
        [
            'id' => 5,
            'title' => 'Pembukuan Transparan & Tarik Dana Instan',
            'desc' => 'Pantau ledger (buku besar) transaksi secara mendetail. Setiap pesanan lunas akan tercatat otomatis lengkap dengan nama pembeli, harga awal, dan nominal pendapatan bersih (90%). Nikmati kemudahan mencairkan saldo ke rekening bank dengan penarikan minimum hanya Rp25.000.',
            'image' => asset('images/modulsection/fotografer5.png'),
        ],
    ];
@endphp

<section id="fotografer" class="border-b border-public-line bg-white py-16 sm:py-24" aria-labelledby="photographer-features-title"
    x-data="{
        active: 1,
        previewModal: false,
        modalTitle: '',
        modalDesc: '',
        modalImage: '',
        openPreview(image, title, desc) {
            this.modalImage = image;
            this.modalTitle = title;
            this.modalDesc = desc;
            this.previewModal = true;
        }
    }">
    <div class="mx-auto w-full max-w-[1520px] px-4 sm:px-6 lg:px-10">
        {{-- Header --}}
        <div class="mx-auto max-w-3xl text-center" data-reveal>
            <p class="text-sm font-semibold tracking-wider uppercase text-public-muted">Cara kerja Photographer</p>
            <h2 id="photographer-features-title"
                class="mt-3 text-3xl font-bold tracking-tight text-public-ink sm:text-5xl">
                Photographer
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-public-muted sm:text-lg">
                Tingkatkan personal branding Anda dan kelola semuanya dalam satu platform di Jepret mulai dari Profile
                pribadi, proteksi foto, analitik penjualan, hingga kemudahan tarik dana instan.
            </p>
        </div>

        {{-- Accordion Widescreen --}}
        <div class="mt-12 w-full border-t border-public-line sm:mt-16" data-reveal>
            @foreach ($features as $feature)
                <div class="relative overflow-hidden border-b border-public-line">
                    <h3>
                        <button type="button" id="photographer-feature-trigger-{{ $feature['id'] }}"
                            @click="active = active === {{ $feature['id'] }} ? null : {{ $feature['id'] }}"
                            :aria-expanded="(active === {{ $feature['id'] }}).toString()"
                            aria-controls="photographer-feature-panel-{{ $feature['id'] }}"
                            class="group flex w-full items-center gap-4 py-6 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-public-ink focus-visible:ring-offset-2 sm:gap-6 sm:py-8">
                            <span
                                class="font-sans text-sm font-bold tracking-wider transition-colors duration-200 lg:text-base"
                                :class="active === {{ $feature['id'] }} ? 'text-public-ink' : 'text-public-muted/60'">
                                {{ str_pad($feature['id'], 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <span
                                class="flex-1 text-xl font-semibold leading-snug tracking-tight transition-colors duration-200 group-hover:text-public-ink lg:text-3xl"
                                :class="active === {{ $feature['id'] }} ? 'text-public-ink font-bold' : 'text-public-ink/75'">
                                {{ $feature['title'] }}
                            </span>
                            <span
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-full border transition-all duration-200 sm:h-12 sm:w-12"
                                :class="active === {{ $feature['id'] }} ? 'border-public-ink bg-public-ink text-white shadow-sm' : 'border-public-line text-public-ink group-hover:border-public-ink'">
                                <svg class="h-4 w-4 transition-transform duration-300 sm:h-5 sm:w-5"
                                    :class="{ 'rotate-180': active === {{ $feature['id'] }} }" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </span>
                        </button>
                    </h3>

                    <div id="photographer-feature-panel-{{ $feature['id'] }}" role="region"
                        aria-labelledby="photographer-feature-trigger-{{ $feature['id'] }}"
                        x-show="active === {{ $feature['id'] }}" @if ($feature['id'] !== 1) x-cloak @endif
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <div class="mb-6 rounded-2xl bg-gray-50/70 p-5 sm:mb-8 sm:p-6 lg:p-8">
                            <div class="grid items-center gap-6 lg:grid-cols-12 lg:gap-10">
                                {{-- Kolom Kiri: Teks (4 Kolom) --}}
                                <div class="lg:col-span-4">
                                    <p
                                        class="border-l-2 border-public-ink pl-5 text-base leading-relaxed text-public-muted sm:text-lg lg:text-xl">
                                        {{ $feature['desc'] }}
                                    </p>
                                </div>

                                {{-- Kolom Kanan: Gambar Ukuran Besar (8 Kolom) --}}
                                <div class="lg:col-span-8">
                                    <button type="button"
                                        @click="openPreview(@js($feature['image']), @js($feature['title']), @js($feature['desc']))"
                                        class="group relative block w-full overflow-hidden rounded-2xl border border-public-line/80 bg-neutral-900/5 text-left shadow-xl transition-all duration-300 hover:border-public-ink hover:shadow-2xl focus:outline-none focus-visible:ring-2 focus-visible:ring-public-ink"
                                        title="Klik untuk melihat preview utuh">
                                        <img src="{{ $feature['image'] }}"
                                            alt="Tangkapan layar fitur {{ $feature['title'] }}"
                                            loading="{{ $feature['id'] === 1 ? 'eager' : 'lazy' }}" decoding="async"
                                            class="h-auto w-full rounded-2xl object-contain transition-transform duration-300 group-hover:scale-[1.01]">

                                        {{-- Floating Hint Pill --}}
                                        <div
                                            class="absolute bottom-4 right-4 inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-public-ink/90 px-3.5 py-1.5 font-sans text-xs font-semibold text-white shadow-lg backdrop-blur-sm transition-opacity duration-200 sm:opacity-0 sm:group-hover:opacity-100">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5">
                                                <circle cx="11" cy="11" r="8" />
                                                <path d="m21 21-4.3-4.3" />
                                                <path d="M11 8v6M8 11h6" />
                                            </svg>
                                            <span>Lihat Preview</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Simple Preview Card Modal --}}
    <template x-teleport="body">
        <div x-cloak x-show="previewModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-public-ink/80 p-4 backdrop-blur-sm sm:p-6"
            @keydown.escape.window="previewModal = false" role="dialog" aria-modal="true" :aria-label="modalTitle">
            {{-- Backdrop Click --}}
            <div class="fixed inset-0" @click="previewModal = false" aria-hidden="true"></div>

            {{-- Card Container --}}
            <div x-show="previewModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-public-line bg-white shadow-2xl"
                @click.stop>
                {{-- Card Header: Title & Close Button --}}
                <div
                    class="flex items-center justify-between border-b border-public-line bg-public-bone px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-public-ink"></span>
                        <h4 class="text-base font-bold text-public-ink sm:text-lg" x-text="modalTitle"></h4>
                    </div>
                    <button type="button" @click="previewModal = false"
                        class="grid h-8 w-8 place-items-center rounded-full border border-public-line bg-white text-public-ink transition-colors hover:border-public-ink hover:bg-public-ink hover:text-white"
                        aria-label="Tutup preview">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Card Body: Full Uncropped Image --}}
                <div class="overflow-auto bg-neutral-950 p-3 text-center sm:p-6">
                    <img :src="modalImage" :alt="modalTitle"
                        class="mx-auto h-auto max-h-[65vh] w-auto max-w-full rounded-lg object-contain shadow-md">
                </div>

                {{-- Card Footer: Description --}}
                <div class="border-t border-public-line bg-white px-5 py-4 sm:px-6">
                    <p class="text-sm font-medium leading-relaxed text-public-muted sm:text-base" x-text="modalDesc">
                    </p>
                </div>
            </div>
        </div>
    </template>
</section>