<x-superadmin-layout>
    <!-- Header Area -->
    <div class="mb-6 flex flex-col xl:flex-row xl:items-start justify-between gap-4">
        <div class="flex-1">
            <div class="text-[10px] font-mono text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                CONSOLE / SUPER ADMIN / <span class="text-black font-bold">SYSTEM ROOT</span>
            </div>
            
            <div class="flex items-center gap-3 mb-2">
                <div class="bg-gray-100 text-gray-600 text-[9px] font-mono font-bold px-2 py-1 rounded uppercase tracking-wider flex items-center gap-1.5">
                    MASTER CONTROL
                </div>
                <div class="text-gray-400 text-[9px] font-mono font-bold uppercase tracking-wider flex items-center gap-1.5">
                    /
                    <span class="text-black ml-1">POLICY DAEMON</span>
                    <span class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded ml-1 border border-gray-200">v4.18.0-PROD</span>
                </div>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-black tracking-tight mb-2">Pengaturan Platform & Tata Kelola<br>Ekosistem</h1>
            <p class="text-xs sm:text-sm text-gray-600 font-medium max-w-2xl leading-relaxed">
                Konfigurasi global model bagi hasil (split komisi), proteksi watermark dinamis, gerbang pembayaran QRIS, dan aturan privasi metadata EXIF foto.
            </p>
        </div>
        
        <div class="flex flex-col gap-2 shrink-0 w-full xl:w-auto pt-2 xl:pt-0">
            <button class="bg-white border border-gray-200 text-black text-[11px] font-bold px-4 py-2.5 rounded hover:bg-gray-50 transition shadow-sm flex items-center justify-center xl:justify-start gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Riwayat Perubahan Konfigurasi
            </button>
            <button class="bg-black text-white text-[11px] font-bold px-4 py-2.5 rounded hover:bg-gray-800 transition shadow-sm flex items-center justify-center xl:justify-start gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Simpan Semua Kebijakan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Left Column: Main Settings (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Card 1: Fee Structure -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center shrink-0 border border-gray-200">
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-black">Model Bagi Hasil & Keuangan (Fee Structure)</h3>
                            <p class="text-[10px] text-gray-500 mt-1">Distribusi otomatis pendapatan penjualan foto publik saat checkout QRIS</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-700 text-[8px] font-mono font-bold px-2 py-1 rounded shrink-0">SETTLEMENT<br>LIVE</span>
                </div>

                <div class="space-y-6">
                    <!-- Split Slider -->
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label class="text-[11px] font-bold text-black">Split Komisi Default Transaksi</label>
                            <span class="text-[9px] font-mono font-bold text-black">90% Kreator : 10% Platform</span>
                        </div>
                        <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden flex relative mb-4">
                            <div class="h-full bg-black" style="width: 90%"></div>
                            <div class="absolute inset-y-0 left-[90%] -ml-2 w-4 h-4 rounded-full bg-black border-2 border-white top-1/2 -translate-y-1/2 shadow cursor-pointer"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-[9px] font-mono text-gray-400 uppercase tracking-widest mb-1">PORSI FOTOGRAFER (KREATOR)</div>
                                <div class="text-xl font-black text-green-600 mb-1">90%</div>
                                <div class="text-[9px] text-gray-500">Diterima bersih ke saldo dompet kreator</div>
                            </div>
                            <div>
                                <div class="text-[9px] font-mono text-gray-400 uppercase tracking-widest mb-1">PLATFORM TAKE-RATE</div>
                                <div class="text-xl font-black text-black mb-1">10%</div>
                                <div class="text-[9px] text-gray-500">Alokasi CDN, storage, dan pemeliharaan</div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h4 class="text-[11px] font-bold text-black">MDR QRIS Policy Ditanggung Platform</h4>
                                <p class="text-[10px] text-gray-500 mt-0.5 max-w-sm">Biaya QRIS 0.7% ditanggung dari porsi 10% marketplace agar kreator tidak mengalami potongan tambahan.</p>
                            </div>
                            <!-- Toggle On -->
                            <div class="w-10 h-6 bg-black rounded-full p-1 flex items-center justify-end shrink-0 cursor-pointer">
                                <div class="w-4 h-4 bg-white rounded-full shadow-sm"></div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-black mb-2">Batas Penarikan Minimum (IDR)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[10px] font-mono text-gray-400">Rp</div>
                                <input type="text" value="100.000" class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-8 pr-3 py-2 text-[11px] font-mono font-bold text-black focus:ring-1 focus:ring-black">
                            </div>
                            <p class="text-[9px] text-gray-400 mt-1.5">Ambang batas aktivasi tombol payout fotografer</p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-black mb-2">Jadwal Kliring BI-FAST Harian</label>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="bg-gray-100 text-gray-600 font-mono font-bold text-[10px] px-2.5 py-1.5 rounded border border-gray-200">11:00 WIB</span>
                                <span class="text-[10px] text-gray-400 font-bold">&amp;</span>
                                <span class="bg-gray-100 text-gray-600 font-mono font-bold text-[10px] px-2.5 py-1.5 rounded border border-gray-200">17:00 WIB</span>
                            </div>
                            <p class="text-[9px] text-gray-400 mt-1">Eksekusi otomatis antrean payout bank batch 1 & batch 2</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Watermark -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center shrink-0 border border-gray-200">
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-black">Watermark Dynamic Engine & Perlindungan Hak Cipta</h3>
                            <p class="text-[10px] text-gray-500 mt-1">Renderer on-the-fly Cloudflare Workers untuk memblokir pencurian foto preview</p>
                        </div>
                    </div>
                    <span class="bg-gray-100 text-gray-600 text-[8px] font-mono font-bold px-2 py-1 rounded shrink-0 border border-gray-200">WASM<br>RUNTIME</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[11px] font-bold text-black mb-2">Pola Watermark Preview</label>
                            <select class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[11px] text-black font-medium focus:ring-1 focus:ring-black">
                                <option>Diagonal Repetitive Stamp 'JEPRETCFD PREV...'</option>
                                <option>Center Large Logo Overlay</option>
                                <option>Grid Tiled Pattern</option>
                            </select>
                        </div>
                        
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <label class="text-[11px] font-bold text-black">Tingkat Transparansi (Opacity)</label>
                                <span class="text-[10px] font-mono font-bold text-black">18%</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-200 rounded-full relative mb-2">
                                <div class="absolute inset-y-0 left-0 bg-black rounded-full" style="width: 18%"></div>
                                <div class="absolute top-1/2 left-[18%] -translate-y-1/2 -ml-1.5 w-3 h-3 bg-black rounded-full shadow"></div>
                            </div>
                            <div class="flex justify-between text-[8px] font-mono text-gray-400">
                                <span>5% (Halus)</span>
                                <span>18% (Rekomendasi)</span>
                                <span>50% (Agresif)</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 flex items-center justify-between gap-4">
                            <div>
                                <h4 class="text-[11px] font-bold text-black">Micro-Tiling Identitas Fotografer</h4>
                                <p class="text-[10px] text-gray-500 mt-0.5">Sisipkan hash NIK & ID Studio di sudut foto pratinjau</p>
                            </div>
                            <!-- Toggle On -->
                            <div class="w-10 h-6 bg-black rounded-full p-1 flex items-center justify-end shrink-0 cursor-pointer">
                                <div class="w-4 h-4 bg-white rounded-full shadow-sm"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[11px] font-bold text-black mb-2">Real-Time Canvas Simulation</label>
                        <div class="w-full aspect-[4/3] bg-gray-100 rounded-lg border border-gray-200 overflow-hidden relative">
                            <!-- Simulated image placeholder -->
                            <img src="https://images.unsplash.com/photo-1552674605-db6aea4bc09c?q=80&w=600&auto=format&fit=crop" alt="Runner Preview" class="w-full h-full object-cover opacity-80 grayscale">
                            <div class="absolute inset-0 flex items-center justify-center opacity-30 select-none pointer-events-none transform -rotate-12">
                                <div class="text-[10px] sm:text-xs font-black tracking-[0.2em] text-white break-words w-[150%] text-center leading-loose">
                                    JEPRETCFD PREVIEW JEPRETCFD PREVIEW<br>JEPRETCFD PREVIEW JEPRETCFD PREVIEW<br>JEPRETCFD PREVIEW JEPRETCFD PREVIEW
                                </div>
                            </div>
                            <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[7px] font-mono px-1.5 py-0.5 rounded">
                                STUDIO: #0042 RAW - HASH: 9fa2c1
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-3.5 flex items-start gap-3">
                    <svg class="w-4 h-4 text-gray-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <div>
                        <h4 class="text-[11px] font-bold text-black">Enkripsi Resolusi Asli (Original Raw Lossless)</h4>
                        <p class="text-[9px] text-gray-500 mt-0.5 leading-relaxed">File master JPEG 6000x4000 dienkripsi AES-256 pada edge S3 bucket. URL unduhan bertanda tangan (Presigned URL) hanya diterbitkan setelah webhook status invoice QRIS = LUNAS.</p>
                    </div>
                </div>
            </div>

            <!-- Card 3: Privacy & EXIF -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 md:p-6">
                <div class="flex items-start gap-3 mb-5">
                    <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center shrink-0 border border-gray-200">
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-black">Kebijakan Privasi & Sanitasi Metadata EXIF</h3>
                        <p class="text-[10px] text-gray-500 mt-1">Sanitasi data telemetri kamera demi kepatuhan UU PDP (Pelindungan Data Pribadi)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50/50 rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <h4 class="text-[11px] font-bold text-black">Strip GPS Coordinates</h4>
                            <!-- Toggle On -->
                            <div class="w-10 h-6 bg-black rounded-full p-1 flex items-center justify-end shrink-0 cursor-pointer">
                                <div class="w-4 h-4 bg-white rounded-full shadow-sm"></div>
                            </div>
                        </div>
                        <p class="text-[9px] text-gray-500 mb-3 leading-relaxed">Otomatis menghapus tag latitude, longitude, dan altitude dari file pratinjau publik demi melindungi privasi rute pelari CFD.</p>
                        <div class="text-[9px] text-green-600 font-mono flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Sanitasi aktif sebelum ingest Cloudflare
                        </div>
                    </div>

                    <div class="bg-gray-50/50 rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <h4 class="text-[11px] font-bold text-black">Retain Camera Optics & Exposure</h4>
                            <!-- Toggle On -->
                            <div class="w-10 h-6 bg-black rounded-full p-1 flex items-center justify-end shrink-0 cursor-pointer">
                                <div class="w-4 h-4 bg-white rounded-full shadow-sm"></div>
                            </div>
                        </div>
                        <p class="text-[9px] text-gray-500 mb-3 leading-relaxed">Pertahankan tipe kamera, focal length lensa, aperture, shutter speed, dan ISO untuk kebutuhan kurasi galeri dan kredibilitas gear fotografer.</p>
                        <div class="text-[9px] text-gray-400 font-mono flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tampil di drawer EXIF aset buyer
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Sidebar Settings (1/3 width) -->
        <div class="space-y-6">
            
            <!-- Card 4: Payment & BI-FAST -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        <div>
                            <h3 class="text-[11px] font-bold text-black">Payment & BI-FAST</h3>
                            <div class="text-[9px] text-gray-400">Gateway status & webhook</div>
                        </div>
                    </div>
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                </div>

                <div class="space-y-3 mb-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-[10px] text-black font-bold">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            BCA QRIS Core
                        </div>
                        <span class="bg-green-100 text-green-700 text-[8px] font-mono font-bold px-1.5 py-0.5 rounded">ACTIVE (0.7%)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-[10px] text-gray-600 font-bold">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Midtrans Snap Pay
                        </div>
                        <span class="bg-green-50 text-green-600 border border-green-100 text-[8px] font-mono font-bold px-1.5 py-0.5 rounded text-center leading-tight">STANDBY<br>FALLBACK</span>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 mb-4">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] text-gray-600 font-bold">Auto-Reconciliation Ping</span>
                        <span class="text-[9px] font-mono text-green-600 font-bold text-right leading-tight">Sinkron<br>(48ms)</span>
                    </div>
                    <div class="text-[9px] font-mono text-gray-500 mb-2">Interval Toleransi Webhook: <strong class="text-black">60 Detik</strong></div>
                    <div class="w-full h-1 bg-gray-200 rounded-full overflow-hidden mb-1.5">
                        <div class="h-full bg-green-500" style="width: 99.82%"></div>
                    </div>
                    <div class="text-[8px] text-gray-400 text-center">99.82% Berhasil tanpa intervensi manual</div>
                </div>

                <div class="space-y-2">
                    <button class="w-full bg-white border border-gray-200 text-black text-[10px] font-bold py-2 rounded hover:bg-gray-50 transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Tes Webhook Ping
                    </button>
                    <button class="w-full bg-gray-50 border border-gray-200 text-gray-600 text-[10px] font-bold py-2 rounded hover:bg-gray-100 transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 14l-3 3m0 0l-3-3m3 3V10m7-4a2 2 0 11-4 0 2 2 0 014 0zM7 4a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Rotasi Kunci API Produksi
                    </button>
                </div>
            </div>

            <!-- Card 5: Security & Ops -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center shrink-0 border border-gray-200">
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-[11px] font-bold text-black">Root Security & Ops Access</h3>
                        <p class="text-[9px] text-gray-400 mt-0.5">Akses operator level kernel</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h4 class="text-[10px] font-bold text-black">Mode Maintenance Darurat</h4>
                            <p class="text-[8px] text-gray-500 mt-0.5 max-w-[120px]">Tutup akses publik, alihkan transaksi ke halaman pemeliharaan</p>
                        </div>
                        <!-- Toggle Off -->
                        <div class="w-8 h-5 bg-gray-200 rounded-full p-0.5 flex items-center shrink-0 cursor-pointer">
                            <div class="w-4 h-4 bg-white rounded-full shadow-sm"></div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h4 class="text-[10px] font-bold text-black">Wajibkan 2FA Super Admin</h4>
                            <p class="text-[8px] text-gray-500 mt-0.5 max-w-[120px]">Otentikasi TOTP hardware key (YubiKey/Google Authenticator)</p>
                        </div>
                        <!-- Toggle On -->
                        <div class="w-8 h-5 bg-black rounded-full p-0.5 flex items-center justify-end shrink-0 cursor-pointer">
                            <div class="w-4 h-4 bg-white rounded-full shadow-sm"></div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-bold text-black mb-1.5">Sesi Kedaluwarsa Otomatis (Inactivity Timeout)</h4>
                        <div class="relative">
                            <select class="w-full bg-gray-50 border border-gray-200 rounded px-2.5 py-2 text-[10px] text-black font-medium focus:ring-1 focus:ring-black appearance-none pr-8">
                                <option>12 Jam (Default Shift Operasional)</option>
                                <option>4 Jam</option>
                                <option>1 Jam (Strict)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none">
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        <p class="text-[8px] text-gray-400 mt-1">Token JWT root di-revoke saat sesi timeout tercapai</p>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-gray-100 bg-gray-50 rounded p-2 flex justify-between items-center text-[8px] font-mono uppercase tracking-widest text-gray-500">
                    <span>LAST ROOT AUDIT LOG</span>
                    <span class="font-bold text-black text-right leading-tight">14:02 WIB by<br>#001</span>
                </div>
            </div>

        </div>
    </div>

</x-superadmin-layout>
