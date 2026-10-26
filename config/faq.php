<?php

return [
    'categories' => [
        'buy' => [
            'id' => 'buy',
            'label' => 'Untuk pembeli',
            'items' => [
                [
                    'question' => 'Bagaimana cara membeli foto?',
                    'answer' => 'Buka halaman galeri foto yang Anda inginkan, lalu klik “TAMBAH KE KERANJANG”. Periksa pesanan, klik “BUAT ORDER PEMBAYARAN”, lalu bayar dengan QRIS. Setelah itu, foto original siap diunduh.',
                ],
                [
                    'question' => 'Kenapa foto di galeri ada watermark-nya?',
                    'answer' => 'Foto di galeri hanyalah preview publik yang diberi watermark untuk melindungi karya fotografer. File original tanpa watermark terbuka setelah pembayaran berhasil.',
                ],
                [
                    'question' => 'Metode pembayaran apa yang tersedia?',
                    'answer' => 'Pembayaran memakai QRIS. Pindai kode QR yang tampil dengan aplikasi mobile banking atau dompet digital Anda sebelum batas waktu habis.',
                ],
                [
                    'question' => 'Di mana saya mengunduh foto setelah membayar?',
                    'answer' => 'Buka menu pesanan. Setelah pembayaran berhasil, file foto original tanpa watermark akan tersedia di sana untuk diunduh.',
                ],
                [
                    'question' => 'Apakah saya bisa membagikan foto yang sudah dibeli?',
                    'answer' => 'Bisa. File original tanpa watermark siap diunduh atau dibagikan dari menu pesanan.',
                ],
                [
                    'question' => 'Apakah ada voucher atau diskon?',
                    'answer' => 'Jika Anda punya voucher, masukkan kodenya di tahap konfirmasi pembelian sebelum membuat order pembayaran.',
                ],
            ],
        ],
        'pro' => [
            'id' => 'pro',
            'label' => 'Untuk fotografer',
            'items' => [
                [
                    'question' => 'Bagaimana cara mulai menjual foto di Jepret?',
                    'answer' => 'Daftar sebagai fotografer, lengkapi profil publik Anda, lalu unggah puluhan foto hasil shoot sekaligus. Anda bebas mengatur nama event, lokasi, jam pengambilan, dan harga jual.',
                ],
                [
                    'question' => 'Berapa pendapatan yang saya terima?',
                    'answer' => 'Setiap pesanan yang lunas dicatat otomatis dengan nominal pendapatan bersih 90% untuk Anda, lengkap dengan nama pembeli dan harga awal.',
                ],
                [
                    'question' => 'Berapa minimum penarikan dana?',
                    'answer' => 'Saldo bisa dicairkan langsung ke rekening bank dengan batas penarikan minimum Rp25.000.',
                ],
                [
                    'question' => 'Bisakah memakai watermark dengan logo sendiri?',
                    'answer' => 'Bisa. Fitur watermark batch memakai logo transparan milik Anda, dan ukuran serta tingkat transparansinya (opacity) bisa diatur penuh.',
                ],
                [
                    'question' => 'Apakah saya bisa memantau penjualan?',
                    'answer' => 'Bisa. Dasbor menampilkan total kunjungan foto, rasio konversi pembelian, grafik penjualan, dan akumulasi pendapatan bersih. Ledger transaksi juga bisa diunduh sebagai CSV kapan saja.',
                ],
                [
                    'question' => 'Apa saja yang tampil di profil fotografer?',
                    'answer' => 'Foto profil, bio singkat, spesialisasi, tautan WhatsApp dan Instagram, detail kamera yang dipakai, serta statistik performa portofolio Anda.',
                ],
            ],
        ],
    ],
];
