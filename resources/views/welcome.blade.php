<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JEPRET CFD - Temukan fotomu. Simpan momen.</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #FFFFFF; color: #111827; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased text-gray-900 bg-white">

    <!-- Navbar -->
    <x-navbar />

    <!-- Section 1: Hero Galeri Clean -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
        <h1 class="text-4xl md:text-6xl font-black text-black mb-4 tracking-tight">
            Temukan foto. Simpan momen.
        </h1>
        <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-3xl mx-auto font-medium">
            JepretCFD menghubungkan photographer dan buyer dalam satu platform sederhana. 
        </p>

        <!-- 4 Photos Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
            <div class="aspect-[4/5] bg-gray-100 rounded-2xl overflow-hidden">
                <img src="https://images.unsplash.com/photo-1552674605-15c2145e9ca4?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Runner">
            </div>
            <div class="aspect-[4/5] bg-gray-100 rounded-2xl overflow-hidden mt-6 md:mt-8">
                <img src="https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Runner 2">
            </div>
            <div class="aspect-[4/5] bg-gray-100 rounded-2xl overflow-hidden">
                <img src="https://images.unsplash.com/photo-1517649763962-0c623066013b?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Cyclist">
            </div>
            <div class="aspect-[4/5] bg-gray-100 rounded-2xl overflow-hidden mt-6 md:mt-8">
                <img src="https://images.unsplash.com/photo-1571008887538-b36bb32f4571?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover" alt="Runner 3">
            </div>
        </div>

        <a href="{{ route('galeri') }}" class="inline-flex items-center justify-center bg-black hover:bg-gray-800 text-white font-bold text-lg px-10 py-4 rounded-xl transition">
            Jelajahi Foto
        </a>
    </div>

    <!-- Section 2: Tentang & Cara Kerja -->
    <div id="tentang" class="bg-gray-50 border-y border-gray-100 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 max-w-3xl mx-auto">
                <h2 class="text-3xl md:text-5xl font-black text-black mb-6">Tentang JepretCFD</h2>
                <p class="text-lg text-gray-700 leading-relaxed font-medium mb-6">
                    Photographer dapat mengunggah, mengelola, dan menjual karya, sementara buyer dapat menemukan momen mereka dengan lebih mudah.
                </p>
                <div class="flex items-center justify-center gap-4 text-sm font-bold text-gray-500 uppercase tracking-widest">
                    <span>Cari</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    <span>Beli</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    <span>Download</span>
                </div>
            </div>

            <h3 class="text-2xl font-black text-black text-center mb-10">Cara Kerja</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Card 1: Buyer -->
                <div class="bg-white p-10 rounded-3xl border border-gray-200 shadow-sm flex flex-col h-full">
                    <h4 class="text-2xl font-bold text-black mb-2">Untuk Buyer</h4>
                    <p class="text-xl font-medium text-gray-900 mb-6">Temukan momennya. Dapatkan fotonya.</p>
                    <p class="text-base text-gray-600 leading-relaxed mb-8 flex-1">
                        Cari foto berdasarkan event, lokasi, tanggal, atau photographer. Pilih foto yang kamu inginkan, selesaikan pembayaran, lalu akses file original setelah transaksi berhasil.
                    </p>
                    <div class="text-sm font-bold text-gray-500 mb-8 flex flex-wrap items-center gap-2">
                        <span>Cari Foto</span> &rarr; <span>Pilih</span> &rarr; <span>Bayar</span> &rarr; <span>Download</span>
                    </div>
                    <a href="{{ route('galeri') }}" class="w-full bg-black text-white text-center font-bold py-4 rounded-xl text-lg hover:bg-gray-800 transition">
                        Jelajahi Foto
                    </a>
                </div>

                <!-- Card 2: Photographer -->
                <div class="bg-white p-10 rounded-3xl border border-gray-200 shadow-sm flex flex-col h-full">
                    <h4 class="text-2xl font-bold text-black mb-2">Untuk Photographer</h4>
                    <p class="text-xl font-medium text-gray-900 mb-6">Upload karya. Jual lebih mudah.</p>
                    <p class="text-base text-gray-600 leading-relaxed mb-8 flex-1">
                        Unggah hasil shoot, lengkapi detail foto, tentukan harga, lalu publikasikan ke marketplace JepretCFD. Pantau penjualan, transaksi, storage, dan pendapatan langsung dari dashboard.
                    </p>
                    <div class="text-sm font-bold text-gray-500 mb-8 flex flex-wrap items-center gap-2">
                        <span>Upload</span> &rarr; <span>Atur Harga</span> &rarr; <span>Publish</span> &rarr; <span>Terjual</span> &rarr; <span>Kelola Pendapatan</span>
                    </div>
                    <a href="{{ route('register', ['role' => 'fotografer']) }}" class="w-full bg-white border-2 border-black text-black text-center font-bold py-4 rounded-xl text-lg hover:bg-gray-50 transition">
                        Mulai sebagai Photographer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Pricing -->
    <div id="pricing" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-black mb-6">Paket Photographer</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto font-medium leading-relaxed">
                    Pilih ruang untuk setiap karya. Simpan, kelola, dan jual foto melalui JepretCFD. Pilih kapasitas yang sesuai dengan aktivitas fotografimu.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
                <!-- Trial -->
                <div class="border border-gray-200 rounded-3xl p-8 flex flex-col hover:shadow-lg transition bg-white">
                    <h3 class="text-2xl font-black text-black mb-2">Trial</h3>
                    <div class="mb-2">
                        <span class="text-3xl font-black text-black">Gratis</span>
                    </div>
                    <div class="text-lg font-bold text-gray-900 mb-6">500 MB</div>
                    <p class="text-sm text-gray-600 font-medium mb-8 flex-1">Untuk mencoba JepretCFD selama 7 hari.</p>
                    <ul class="space-y-4 mb-8 text-sm font-medium text-gray-700">
                        <li>&bull; 500 MB Cloud Storage</li>
                        <li>&bull; Upload & kelola foto</li>
                        <li>&bull; Protected preview</li>
                        <li>&bull; Marketplace access</li>
                        <li>&bull; Atur harga foto</li>
                    </ul>
                    <a href="{{ route('register') }}" class="w-full block text-center border-2 border-gray-200 text-black font-bold py-3 rounded-xl hover:border-black transition">Mulai Gratis</a>
                </div>

                <!-- Starter -->
                <div class="border border-gray-200 rounded-3xl p-8 flex flex-col hover:shadow-lg transition bg-white">
                    <h3 class="text-2xl font-black text-black mb-2">Starter</h3>
                    <div class="mb-2">
                        <span class="text-3xl font-black text-black">Rp29.000</span> <span class="text-gray-500 text-sm">/ 7hari</span>
                    </div>
                    <div class="text-lg font-bold text-gray-900 mb-6">5 GB</div>
                    <p class="text-sm text-gray-600 font-medium mb-8 flex-1">Untuk photographer yang mulai aktif menjual.</p>
                    <ul class="space-y-4 mb-8 text-sm font-medium text-gray-700">
                        <li>&bull; 5 GB Cloud Storage</li>
                        <li>&bull; Semua fitur utama JepretCFD</li>
                        <li>&bull; Protected preview + watermark</li>
                        <li>&bull; Dashboard photographer</li>
                        <li>&bull; Transaksi realtime</li>
                    </ul>
                    <button class="w-full border-2 border-black bg-white text-black font-bold py-3 rounded-xl hover:bg-gray-50 transition">Pilih Starter</button>
                </div>

                <!-- Creator -->
                <div class="border-2 border-black rounded-3xl p-8 flex flex-col shadow-xl bg-black text-white relative transform lg:-translate-y-4">
                    <h3 class="text-2xl font-black text-white mb-2">Creator</h3>
                    <div class="mb-2">
                        <span class="text-3xl font-black text-white">Rp59.000</span> <span class="text-gray-400 text-sm">/ bulan</span>
                    </div>
                    <div class="text-lg font-bold text-gray-200 mb-6">20 GB</div>
                    <p class="text-sm text-gray-400 font-medium mb-8 flex-1">Untuk photographer dengan aktivitas dan koleksi lebih besar.</p>
                    <ul class="space-y-4 mb-8 text-sm font-medium text-gray-300">
                        <li>&bull; 20 GB Cloud Storage</li>
                        <li>&bull; Semua fitur Starter</li>
                        <li>&bull; Dashboard & monitoring penjualan</li>
                        <li>&bull; Statistik transaksi & pendapatan</li>
                        <li>&bull; Pengelolaan storage</li>
                    </ul>
                    <button class="w-full bg-white text-black font-bold py-3 rounded-xl hover:bg-gray-100 transition">Pilih Creator</button>
                </div>

                <!-- Studio -->
                <div class="border border-gray-200 rounded-3xl p-8 flex flex-col hover:shadow-lg transition bg-white">
                    <h3 class="text-2xl font-black text-black mb-2">Studio</h3>
                    <div class="mb-2">
                        <span class="text-3xl font-black text-black">Custom</span>
                    </div>
                    <div class="text-lg font-bold text-gray-900 mb-6">Custom Storage</div>
                    <p class="text-sm text-gray-600 font-medium mb-8 flex-1">Untuk studio, tim, dan kebutuhan skala besar.</p>
                    <ul class="space-y-4 mb-8 text-sm font-medium text-gray-700">
                        <li>&bull; Kapasitas sesuai kebutuhan</li>
                        <li>&bull; Semua fitur Creator</li>
                        <li>&bull; Kebutuhan operasional custom</li>
                        <li>&bull; Dukungan kebutuhan tim</li>
                        <li>&bull; Konfigurasi fleksibel</li>
                    </ul>
                    <button class="w-full border-2 border-gray-200 text-black font-bold py-3 rounded-xl hover:border-black transition">Hubungi Kami</button>
                </div>
            </div>
            
            <div class="mt-16 text-center max-w-2xl mx-auto">
                <p class="text-lg text-gray-900 font-medium mb-8">
                    Tampilkan karya terbaikmu, jangkau lebih banyak buyer, dan kelola penjualan melalui satu platform.
                </p>
                <a href="{{ route('register', ['role' => 'fotografer']) }}" class="inline-block bg-black text-white font-bold text-lg px-8 py-4 rounded-xl hover:bg-gray-800 transition">
                    Mulai sebagai Photographer
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <div>
                    <span class="font-black text-2xl tracking-tighter text-white flex items-center gap-1 mb-4">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"></path><circle cx="12" cy="13" r="3"></circle></svg>
                        JEPRET CFD
                    </span>
                    <p class="text-gray-400 font-medium">Temukan foto. Simpan momen.</p>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#tentang" class="text-gray-400 hover:text-white transition font-medium">Tentang</a></li>
                        <li><a href="{{ route('galeri') }}" class="text-gray-400 hover:text-white transition font-medium">Galeri</a></li>
                        <li><a href="{{ route('photographers.index') }}" class="text-gray-400 hover:text-white transition font-medium">Photographers</a></li>
                        <li><a href="#pricing" class="text-gray-400 hover:text-white transition font-medium">Pricing</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Hubungi Kami</h4>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-2 text-gray-400 font-medium">
                            <span class="font-bold">WhatsApp:</span> 085156767900
                        </li>
                        <li class="flex items-center gap-2 text-gray-400 font-medium">
                            <span class="font-bold">Instagram:</span> @jepret.cfd
                        </li>
                        <li class="flex items-center gap-2 text-gray-400 font-medium">
                            <span class="font-bold">Email:</span> jepretccfdd@gmail.com
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm font-medium text-gray-500">
                <p>&copy; 2024 JEPRET CFD. All rights reserved.</p>
                <p>Platform Fotografi Event & Lintasan.</p>
            </div>
        </div>
    </footer>

</body>
</html>
