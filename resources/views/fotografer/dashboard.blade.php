<x-fg-layout>
    <!-- Header Area -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">CREATOR CENTER // ID-8821</span>
                <span class="flex items-center gap-1 text-[10px] font-bold text-green-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Sync Live
                </span>
            </div>
            <h1 class="text-3xl font-black text-black tracking-tight mb-2">Selamat datang kembali, Dwi.</h1>
            <p class="text-sm text-gray-500 font-medium">Ringkasan performa penjualan foto, status sinkronisasi cloud, dan analitik pendapatan Anda.</p>
        </div>
        <div class="flex flex-col gap-2 shrink-0">
            <div class="flex items-center gap-2">
                <button class="bg-white border border-gray-200 text-black text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Kelola Harga Batch
                </button>
                <button class="bg-white border border-gray-200 text-black text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Tarik Saldo Payout
                </button>
            </div>
            <a href="/fotografer/photos" class="bg-black text-white text-xs font-bold px-4 py-2.5 rounded-lg shadow-sm hover:bg-gray-800 flex items-center justify-center gap-2 text-center w-full">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Upload Foto Baru
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Pendapatan -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">PENDAPATAN BERSIH</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-black">Rp4.820.000</h3>
                <p class="text-[10px] font-bold text-green-500 mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    +14.2% bulan ini
                </p>
            </div>
            <p class="text-[9px] text-gray-400 font-medium mt-2">Net creator earnings (90%)</p>
        </div>

        <!-- Card 2: Foto Terjual -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">FOTO TERJUAL</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-black">184 <span class="text-sm text-gray-400 font-bold">/ 420 item</span></h3>
                <p class="text-[10px] font-bold text-gray-500 mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    43.8% katalog terjual
                </p>
            </div>
            <p class="text-[9px] text-gray-400 font-medium mt-2">High-res direct watermark release</p>
        </div>

        <!-- Card 3: Total Kunjungan -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">TOTAL KUNJUNGAN</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-black">12.450 <span class="text-sm text-gray-400 font-bold">views</span></h3>
                <p class="text-[10px] font-bold text-green-500 mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    +890 dari pencarian wajah
                </p>
            </div>
            <p class="text-[9px] text-gray-400 font-medium mt-2">CFD & event participant indexing</p>
        </div>

        <!-- Card 4: Rasio Konversi -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">RASIO KONVERSI BELI</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-black">4.8%</h3>
                <p class="text-[10px] font-medium text-gray-500 mt-1 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    Benchmark platform: 3.5%
                </p>
            </div>
            <p class="text-[9px] text-gray-400 font-medium mt-2">Outperforming platform avg (+1.3%)</p>
        </div>
    </div>

    <!-- Chart & Right Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Main Chart Area -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-1">ANALITIK TRAJEKTORI</span>
                    <h3 class="text-lg font-bold text-black">Tren Penjualan 30 Hari Terakhir</h3>
                </div>
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <button class="bg-black text-white text-[10px] font-bold px-3 py-1.5 rounded shadow-sm">30 Hari</button>
                    <button class="text-gray-500 hover:text-black text-[10px] font-bold px-3 py-1.5 rounded transition">7 Hari</button>
                    <button class="text-gray-500 hover:text-black text-[10px] font-bold px-3 py-1.5 rounded transition">Tahun 2025</button>
                </div>
            </div>

            <!-- Chart Mockup -->
            <div class="relative flex-grow min-h-[200px] flex items-end justify-between px-2 pt-10 pb-6 border-b border-gray-100">
                <!-- Grid Lines -->
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                    <div class="border-b border-gray-100 w-full h-0"></div>
                    <div class="border-b border-gray-100 w-full h-0"></div>
                    <div class="border-b border-gray-100 w-full h-0"></div>
                    <div class="border-b border-gray-100 w-full h-0"></div>
                </div>
                
                <!-- Bars Mockup -->
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-12 relative z-0"></div>
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-16 relative z-0"></div>
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-24 relative z-0"></div>
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-32 relative z-0"></div>
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-28 relative z-0"></div>
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-40 relative z-0"></div>
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-48 relative z-0"></div>
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-44 relative z-0"></div>
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-48 relative z-0"></div>
                <div class="w-1/12 bg-gray-100 rounded-t-sm h-56 relative z-0 bg-black/10"></div>
                
                <!-- Line Graphic (Simplified SVG curve over bars) -->
                <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                    <path d="M 5 60 Q 15 50 25 70 T 45 40 T 65 30 T 85 40 T 95 20" fill="none" stroke="black" stroke-width="2" />
                    <!-- Point -->
                    <circle cx="95" cy="20" r="2" fill="white" stroke="black" stroke-width="1.5" />
                </svg>
            </div>
            
            <div class="flex justify-between items-center text-[9px] font-bold text-gray-400 mt-2 px-2 uppercase tracking-widest">
                <span>01 Feb</span>
                <span>07 Feb (Minggu CFD)</span>
                <span>14 Feb</span>
                <span>21 Feb (Banjarbaru 10K)</span>
                <span class="text-black">Hari Ini (Rp4.82M)</span>
            </div>

            <div class="mt-8 border-t border-gray-100 pt-6">
                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-3">TOP PERFORMING EVENT BUNDLES</span>
                <div class="flex flex-wrap gap-2">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        <span class="text-[10px] font-bold text-black">Lapangan Murjani CFD</span>
                        <span class="text-[10px] text-gray-500">(84 sales)</span>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        <span class="text-[10px] font-bold text-black">CFD Sudirman</span>
                        <span class="text-[10px] text-gray-500">(62 sales)</span>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-[10px] font-bold text-black">Banjarbaru 10K</span>
                        <span class="text-[10px] text-gray-500">(38 sales)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Panel -->
        <div class="flex flex-col gap-6">
            <!-- Saldo Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <span class="text-xs font-bold text-black">Saldo Siap Ditarik</span>
                        </div>
                        <span class="bg-green-50 text-green-600 text-[9px] font-bold px-2 py-1 rounded">Tersedia</span>
                    </div>
                    <h3 class="text-3xl font-black text-black mb-2">Rp1.420.000</h3>
                    <p class="text-[10px] text-gray-500 font-medium leading-relaxed">
                        Batas penarikan otomatis setiap hari Senin pukul 09:00 WIB.
                    </p>
                </div>

                <div class="mt-6">
                    <div class="flex justify-between items-center text-[10px] font-bold text-black mb-3 pb-3 border-b border-gray-100">
                        <span class="text-gray-500">Rekening Utama:</span>
                        <span>BCA ***4921</span>
                    </div>
                    <button class="w-full bg-black text-white text-xs font-bold py-3 rounded-xl hover:bg-gray-800 transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Tarik ke BCA ***4921
                    </button>
                </div>
            </div>

            <!-- Kapasitas Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                            <span class="text-xs font-bold text-black">Kapasitas PRO PLAN</span>
                        </div>
                        <span class="text-[10px] font-bold text-gray-500">64.8%</span>
                    </div>
                    
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-[10px] font-bold text-black">32.4 GB <span class="text-gray-400 font-medium">terpakai</span></span>
                        <span class="text-[10px] font-bold text-black">50 GB <span class="text-gray-400 font-medium">kuota</span></span>
                    </div>
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden mb-4">
                        <div class="h-full bg-black rounded-full" style="width: 64.8%;"></div>
                    </div>
                    
                    <p class="text-[10px] text-gray-500 font-medium leading-relaxed">
                        Estimasi tersisa ~1.200 file RAW/Full JPEG resolusi 45MP.
                    </p>
                </div>
                
                <div class="mt-4 flex justify-between items-center text-[10px]">
                    <span class="text-gray-500 font-medium">Butuh ruang lebih besar?</span>
                    <a href="#" class="font-bold text-black underline">Upgrade ke Studio Plan (200 GB)</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-200">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-black mb-0.5">Penjualan & Transaksi Terkini</h3>
                    <p class="text-[10px] text-gray-500 font-medium">Pemberitahuan instan via QRIS & verifikasi otomatis</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button class="text-xs font-bold text-gray-600 border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export CSV
                </button>
                <a href="#" class="text-xs font-bold text-black hover:underline px-2">Lihat Semua (184)</a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-100 text-[9px] uppercase tracking-widest text-gray-400 font-bold">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-lg">ID PESANAN</th>
                        <th class="px-6 py-4">FOTO PREVIEW</th>
                        <th class="px-6 py-4">JUDUL & EVENT</th>
                        <th class="px-6 py-4">PEMBELI</th>
                        <th class="px-6 py-4">HARGA</th>
                        <th class="px-6 py-4">PENDAPATAN (90%)</th>
                        <th class="px-6 py-4">PLATFORM (10%)</th>
                        <th class="px-6 py-4">STATUS</th>
                        <th class="px-6 py-4 text-right rounded-tr-lg">WAKTU</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-xs font-medium">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-black">#JCFD-1242</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-10 h-10 bg-gray-100 rounded border border-gray-200 overflow-hidden relative group">
                                <img src="https://images.unsplash.com/photo-1552674605-15c2145e9ca4?q=80&w=100&auto=format&fit=crop" class="w-full h-full object-cover grayscale" alt="Preview">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40"><span class="text-[6px] font-bold text-white">PROT</span></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-black mb-0.5">Pelari 5K Selebrasi Finish</p>
                            <p class="text-[10px] text-gray-400">Lapangan Murjani CFD</p>
                        </td>
                        <td class="px-6 py-4 text-gray-500">b***@gmail.com</td>
                        <td class="px-6 py-4">Rp20.000</td>
                        <td class="px-6 py-4 font-bold text-black">Rp14.000</td>
                        <td class="px-6 py-4 text-gray-400">Rp6.000</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-50 text-green-600 text-[9px] font-bold px-2 py-1 rounded border border-green-100">Lunas (QRIS)</span>
                        </td>
                        <td class="px-6 py-4 text-right text-[10px] text-gray-400 whitespace-nowrap">12 menit lalu</td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-black">#JCFD-1241</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-10 h-10 bg-gray-100 rounded border border-gray-200 overflow-hidden relative group">
                                <img src="https://images.unsplash.com/photo-1517649763962-0c623066013b?q=80&w=100&auto=format&fit=crop" class="w-full h-full object-cover grayscale" alt="Preview">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40"><span class="text-[6px] font-bold text-white">PROT</span></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-black mb-0.5">Pesepeda Roadbike Sprint</p>
                            <p class="text-[10px] text-gray-400">CFD Sudirman</p>
                        </td>
                        <td class="px-6 py-4 text-gray-500">m***@yahoo.com</td>
                        <td class="px-6 py-4">Rp25.000</td>
                        <td class="px-6 py-4 font-bold text-black">Rp17.500</td>
                        <td class="px-6 py-4 text-gray-400">Rp7.500</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-50 text-green-600 text-[9px] font-bold px-2 py-1 rounded border border-green-100">Lunas (QRIS)</span>
                        </td>
                        <td class="px-6 py-4 text-right text-[10px] text-gray-400 whitespace-nowrap">44 menit lalu</td>
                    </tr>
                    
                    <!-- Row 3 -->
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-black">#JCFD-1239</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-10 h-10 bg-gray-100 rounded border border-gray-200 overflow-hidden relative group">
                                <img src="https://images.unsplash.com/photo-1522163182402-834f871fd851?q=80&w=100&auto=format&fit=crop" class="w-full h-full object-cover grayscale" alt="Preview">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40"><span class="text-[6px] font-bold text-white">PROT</span></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-black mb-0.5">Jalan Santai Pagi Keluarga</p>
                            <p class="text-[10px] text-gray-400">Lapangan Murjani CFD</p>
                        </td>
                        <td class="px-6 py-4 text-gray-500">d***@icloud.com</td>
                        <td class="px-6 py-4">Rp20.000</td>
                        <td class="px-6 py-4 font-bold text-black">Rp14.000</td>
                        <td class="px-6 py-4 text-gray-400">Rp6.000</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-50 text-green-600 text-[9px] font-bold px-2 py-1 rounded border border-green-100">Lunas (QRIS)</span>
                        </td>
                        <td class="px-6 py-4 text-right text-[10px] text-gray-400 whitespace-nowrap">2 jam lalu</td>
                    </tr>
                    
                    <!-- Row 4 -->
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-black">#JCFD-1235</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-10 h-10 bg-gray-100 rounded border border-gray-200 overflow-hidden relative group">
                                <img src="https://images.unsplash.com/photo-1571008887538-b36bb32f4571?q=80&w=100&auto=format&fit=crop" class="w-full h-full object-cover grayscale" alt="Preview">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40"><span class="text-[6px] font-bold text-white">PROT</span></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-black mb-0.5">Fast Breakaway Lead Paceline</p>
                            <p class="text-[10px] text-gray-400">Banjarbaru 10K</p>
                        </td>
                        <td class="px-6 py-4 text-gray-500">r***@corp.id</td>
                        <td class="px-6 py-4">Rp25.000</td>
                        <td class="px-6 py-4 font-bold text-black">Rp17.500</td>
                        <td class="px-6 py-4 text-gray-400">Rp7.500</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-50 text-green-600 text-[9px] font-bold px-2 py-1 rounded border border-green-100">Lunas (QRIS)</span>
                        </td>
                        <td class="px-6 py-4 text-right text-[10px] text-gray-400 whitespace-nowrap">3 jam lalu</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-100 flex justify-between items-center text-[10px] text-gray-500 font-medium">
            <span>Menampilkan 4 dari 184 transaksi berhasil</span>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 border border-gray-200 rounded text-gray-400 cursor-not-allowed">Previous</button>
                <button class="px-3 py-1.5 border border-gray-200 rounded hover:bg-gray-50 text-black">Next</button>
            </div>
        </div>
    </div>
</x-fg-layout>
