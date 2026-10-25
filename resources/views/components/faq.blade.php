@props([
    'categories' => config('faq.categories', []),
])

@php
    $categories = !empty($categories) ? $categories : [
        'buy' => [
            'id' => 'buy',
            'label' => 'Untuk pembeli',
            'items' => [
                ['question' => 'Bagaimana cara membeli foto?', 'answer' => 'Buka halaman galeri foto yang Anda inginkan, lalu klik “TAMBAH KE KERANJANG”. Periksa pesanan, klik “BUAT ORDER PEMBAYARAN”, lalu bayar dengan QRIS. Setelah itu, foto original siap diunduh.'],
                ['question' => 'Kenapa foto di galeri ada watermark-nya?', 'answer' => 'Foto di galeri hanyalah preview publik yang diberi watermark untuk melindungi karya fotografer. File original tanpa watermark terbuka setelah pembayaran berhasil.'],
                ['question' => 'Metode pembayaran apa yang tersedia?', 'answer' => 'Pembayaran memakai QRIS. Pindai kode QR yang tampil dengan aplikasi mobile banking atau dompet digital Anda sebelum batas waktu habis.'],
                ['question' => 'Di mana saya mengunduh foto setelah membayar?', 'answer' => 'Buka menu pesanan. Setelah pembayaran berhasil, file foto original tanpa watermark akan tersedia di sana untuk diunduh.'],
                ['question' => 'Apakah saya bisa membagikan foto yang sudah dibeli?', 'answer' => 'Bisa. File original tanpa watermark siap diunduh atau dibagikan dari menu pesanan.'],
                ['question' => 'Apakah ada voucher atau diskon?', 'answer' => 'Jika Anda punya voucher, masukkan kodenya di tahap konfirmasi pembelian sebelum membuat order pembayaran.'],
            ],
        ],
        'pro' => [
            'id' => 'pro',
            'label' => 'Untuk fotografer',
            'items' => [
                ['question' => 'Bagaimana cara mulai menjual foto di Jepret?', 'answer' => 'Daftar sebagai fotografer, lengkapi profil publik Anda, lalu unggah puluhan foto hasil shoot sekaligus. Anda bebas mengatur nama event, lokasi, jam pengambilan, dan harga jual.'],
                ['question' => 'Berapa pendapatan yang saya terima?', 'answer' => 'Setiap pesanan yang lunas dicatat otomatis dengan nominal pendapatan bersih 90% untuk Anda, lengkap dengan nama pembeli dan harga awal.'],
                ['question' => 'Berapa minimum penarikan dana?', 'answer' => 'Saldo bisa dicairkan langsung ke rekening bank dengan batas penarikan minimum Rp25.000.'],
                ['question' => 'Bisakah memakai watermark dengan logo sendiri?', 'answer' => 'Bisa. Fitur watermark batch memakai logo transparan milik Anda, dan ukuran serta tingkat transparansinya (opacity) bisa diatur penuh.'],
                ['question' => 'Apakah saya bisa memantau penjualan?', 'answer' => 'Bisa. Dasbor menampilkan total kunjungan foto, rasio konversi pembelian, grafik penjualan, dan akumulasi pendapatan bersih. Ledger transaksi juga bisa diunduh sebagai CSV kapan saja.'],
                ['question' => 'Apa saja yang tampil di profil fotografer?', 'answer' => 'Foto profil, bio singkat, spesialisasi, tautan WhatsApp dan Instagram, detail kamera yang dipakai, serta statistik performa portofolio Anda.'],
            ],
        ],
    ];

    $whatsappMessage = 'Halo Tim Jepret, saya ingin bertanya seputar pertanyaan atau kendala layanan di Jepret.';
    $contactUrl = 'https://wa.me/6285156767900?text=' . urlencode($whatsappMessage);
@endphp

<section
    class="faq"
    id="faq"
    aria-labelledby="faq-title"
    x-data="{
        activeTab: 'buy',
        openIndex: 0,
        isSwapping: false,
        setTab(tab) {
            if (this.activeTab === tab) return;
            this.activeTab = tab;
            this.openIndex = 0;
            this.isSwapping = true;
            this.$nextTick(() => {
                setTimeout(() => { this.isSwapping = false; }, 500);
            });
        },
        toggle(index) {
            this.openIndex = (this.openIndex === index) ? null : index;
        }
    }"
>
    <div class="faq-grid">
        <div class="aside">
            <p class="eyebrow">FAQ</p>
            <h2 id="faq-title">Pertanyaan yang sering <em>ditanyakan.</em></h2>

            <div class="tabs" role="tablist" aria-label="Kategori pertanyaan">
                <button
                    type="button"
                    role="tab"
                    id="tab-buy"
                    :aria-selected="activeTab === 'buy' ? 'true' : 'false'"
                    aria-controls="faq-list-buy"
                    :tabindex="activeTab === 'buy' ? 0 : -1"
                    @click="setTab('buy')"
                    @keydown.arrow-right.prevent="setTab('pro'); $nextTick(() => document.getElementById('tab-pro')?.focus())"
                    @keydown.arrow-down.prevent="setTab('pro'); $nextTick(() => document.getElementById('tab-pro')?.focus())"
                >
                    {{ $categories['buy']['label'] ?? 'Untuk pembeli' }}
                    <small id="n-buy">{{ count($categories['buy']['items'] ?? []) }}</small>
                </button>

                <button
                    type="button"
                    role="tab"
                    id="tab-pro"
                    :aria-selected="activeTab === 'pro' ? 'true' : 'false'"
                    aria-controls="faq-list-pro"
                    :tabindex="activeTab === 'pro' ? 0 : -1"
                    @click="setTab('pro')"
                    @keydown.arrow-left.prevent="setTab('buy'); $nextTick(() => document.getElementById('tab-buy')?.focus())"
                    @keydown.arrow-up.prevent="setTab('buy'); $nextTick(() => document.getElementById('tab-buy')?.focus())"
                >
                    {{ $categories['pro']['label'] ?? 'Untuk fotografer' }}
                    <small id="n-pro">{{ count($categories['pro']['items'] ?? []) }}</small>
                </button>
            </div>

            <div class="help">
                <p>Masih ada pertanyaan? Tim Jepret siap membantu.</p>
                <a href="{{ $contactUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Hubungi kami melalui WhatsApp">
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <span>Hubungi kami</span>
                </a>
            </div>
        </div>

        <div>
            <div :class="{ 'list': true, 'swap': isSwapping }">
                @foreach($categories as $catKey => $category)
                    <div
                        id="faq-list-{{ $catKey }}"
                        role="tabpanel"
                        aria-labelledby="tab-{{ $catKey }}"
                        x-show="activeTab === '{{ $catKey }}'"
                        x-cloak
                    >
                        @foreach($category['items'] as $index => $item)
                            @php
                                $number = sprintf('%02d', $index + 1);
                            @endphp
                            <div class="item">
                                <button
                                    type="button"
                                    class="q"
                                    id="q-{{ $catKey }}-{{ $index }}"
                                    :aria-expanded="openIndex === {{ $index }} ? 'true' : 'false'"
                                    aria-controls="a-{{ $catKey }}-{{ $index }}"
                                    @click="toggle({{ $index }})"
                                >
                                    <span class="no">{{ $number }}</span>
                                    <span class="t">{{ $item['question'] }}</span>
                                    <span class="plus" aria-hidden="true"></span>
                                </button>
                                <div
                                    class="a"
                                    id="a-{{ $catKey }}-{{ $index }}"
                                    role="region"
                                    aria-labelledby="q-{{ $catKey }}-{{ $index }}"
                                >
                                    <div>
                                        <p>{{ $item['answer'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="help m">
                <p>Masih ada pertanyaan? Tim Jepret siap membantu.</p>
                <a href="{{ $contactUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Hubungi kami melalui WhatsApp">
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <span>Hubungi kami</span>
                </a>
            </div>
        </div>
    </div>
</section>
