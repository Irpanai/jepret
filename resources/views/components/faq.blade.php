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
    class="faq border-t border-public-line bg-white"
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
                setTimeout(() => { this.isSwapping = false; }, 400);
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
            <h2 id="faq-title" class="font-heading">
                Pertanyaan yang sering <em class="font-display italic">ditanyakan.</em>
            </h2>

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
                    <small id="n-buy" class="font-sans">{{ count($categories['buy']['items'] ?? []) }}</small>
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
                    <small id="n-pro" class="font-sans">{{ count($categories['pro']['items'] ?? []) }}</small>
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
                        @if($catKey !== 'buy') x-cloak @endif
                    >
                        @foreach($category['items'] as $index => $item)
                            @php
                                $number = sprintf('%02d', $index + 1);
                            @endphp
                            <div class="item">
                                <button
                                    type="button"
                                    class="q font-heading"
                                    id="q-{{ $catKey }}-{{ $index }}"
                                    :aria-expanded="openIndex === {{ $index }} ? 'true' : 'false'"
                                    aria-controls="a-{{ $catKey }}-{{ $index }}"
                                    @click="toggle({{ $index }})"
                                >
                                    <span class="no font-sans">{{ $number }}</span>
                                    <span class="t">{{ $item['question'] }}</span>
                                    <span class="plus" aria-hidden="true"></span>
                                </button>
                                <div
                                    class="a"
                                    id="a-{{ $catKey }}-{{ $index }}"
                                    role="region"
                                    aria-labelledby="q-{{ $catKey }}-{{ $index }}"
                                    :aria-hidden="openIndex !== {{ $index }} ? 'true' : 'false'"
                                    :inert="openIndex !== {{ $index }}"
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

    <style>
    .faq {
        padding: clamp(72px, 10vw, 150px) clamp(20px, 5.2vw, 82px);
        background: #ffffff;
        color: #111;
    }
    .faq-grid {
        max-width: 1440px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: minmax(0, 0.8fr) minmax(0, 1.2fr);
        gap: clamp(36px, 7vw, 120px);
        align-items: start;
    }
    .faq .aside {
        position: sticky;
        top: clamp(24px, 5vh, 64px);
    }
    .faq .eyebrow {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 24px;
        color: #777773;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1.7px;
        text-transform: uppercase;
    }
    .faq .eyebrow::before {
        content: "";
        width: 23px;
        height: 1px;
        background: #111;
    }
    .faq h2 {
        margin: 0;
        font-size: clamp(38px, 5.2vw, 84px);
        font-weight: 600;
        line-height: 0.97;
        letter-spacing: -0.065em;
    }
    .faq h2 em {
        font-style: italic;
        font-weight: 400;
        font-size: 1.08em;
        line-height: 0.9;
        letter-spacing: -0.03em;
    }
    .faq .tabs {
        display: flex;
        gap: 26px;
        margin-top: clamp(28px, 4vw, 52px);
    }
    .faq .tabs button {
        display: flex;
        align-items: baseline;
        gap: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #777773;
        background: none;
        border: 0;
        border-bottom: 1.5px solid transparent;
        padding: 4px 0 9px;
        cursor: pointer;
        transition: color 0.2s, border-color 0.2s;
    }
    .faq .tabs button small {
        font-size: 10px;
        font-weight: 500;
    }
    .faq .tabs button:hover {
        color: #111;
    }
    .faq .tabs button[aria-selected="true"] {
        color: #111;
        border-color: #111;
    }
    .faq .help {
        margin-top: clamp(28px, 4vw, 52px);
        padding-top: 20px;
        border-top: 1px solid rgba(17, 17, 17, 0.14);
        max-width: 340px;
    }
    .faq .help p {
        margin: 0 0 16px;
        color: #777773;
        font-size: 13px;
        line-height: 1.7;
    }
    .faq .help a {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        min-height: 44px;
        padding: 0 18px;
        background: #111;
        color: #fff;
        border: 1px solid #111;
        font-size: 12px;
        font-weight: 600;
        transition: background 0.25s, color 0.25s;
    }
    .faq .help a:hover {
        background: transparent;
        color: #111;
    }
    .faq .list {
        border-top: 1px solid #111;
    }
    .faq .item {
        border-bottom: 1px solid rgba(17, 17, 17, 0.14);
    }
    .faq .q {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        text-align: left;
        background: none;
        border: 0;
        color: #111;
        padding: clamp(20px, 2.4vw, 30px) 0;
        cursor: pointer;
        font-size: clamp(18px, 1.9vw, 25px);
        font-weight: 500;
        line-height: 1.25;
        letter-spacing: -0.03em;
    }
    .faq .q .no {
        flex: none;
        width: 34px;
        color: #777773;
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0;
    }
    .faq .q .t {
        flex: 1;
        transition: transform 0.35s cubic-bezier(0.2, 0.7, 0.2, 1);
    }
    .faq .item:hover .t {
        transform: translateX(4px);
    }
    .faq .plus {
        flex: none;
        position: relative;
        width: 18px;
        height: 18px;
    }
    .faq .plus::before,
    .faq .plus::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 50%;
        height: 1.5px;
        background: currentColor;
        transition: transform 0.4s cubic-bezier(0.2, 0.7, 0.2, 1);
    }
    .faq .plus::after {
        transform: rotate(90deg);
    }
    .faq .q[aria-expanded="true"] .plus::before {
        transform: rotate(45deg);
    }
    .faq .q[aria-expanded="true"] .plus::after {
        transform: rotate(-45deg);
    }
    .faq .a {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.45s cubic-bezier(0.2, 0.7, 0.2, 1);
    }
    .faq .a > div {
        overflow: hidden;
    }
    .faq .a p {
        margin: 0;
        padding: 0 clamp(0px, 4vw, 60px) clamp(22px, 2.6vw, 32px) 34px;
        max-width: 62ch;
        color: #777773;
        font-size: 14.5px;
        line-height: 1.85;
    }
    .faq .q[aria-expanded="true"] + .a {
        grid-template-rows: 1fr;
    }
    .faq .list.swap {
        animation: swap 0.5s cubic-bezier(0.2, 0.7, 0.2, 1);
    }
    @keyframes swap {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: none; }
    }
    @media (max-width: 900px) {
        .faq-grid { grid-template-columns: 1fr; }
        .faq .aside { position: static; }
        .faq .help { display: none; }
        .faq .help.m { display: block; margin-top: 40px; }
    }
    @media (min-width: 901px) {
        .faq .help.m { display: none; }
    }
    @media (max-width: 520px) {
        .faq .q .no { display: none; }
        .faq .a p { padding-left: 0; }
    }
    @media (hover: none) and (pointer: coarse) {
        .faq .a {
            transition-duration: 0.3s;
        }
        .faq .list.swap {
            animation-duration: 0.32s;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .faq *, .faq *::before, .faq *::after {
            animation-duration: 0.01ms !important;
            transition-duration: 0.01ms !important;
        }
    }
    </style>
</section>
