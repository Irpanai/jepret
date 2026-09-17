<x-marketplace-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Stepper -->
        <div class="flex items-center justify-center mb-12">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block leading-none">Langkah 01</span>
                        <span class="text-xs font-bold text-gray-900 leading-none">Google Auth</span>
                    </div>
                </div>
                <div class="w-12 h-px bg-gray-200"></div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block leading-none">Langkah 02</span>
                        <span class="text-xs font-bold text-gray-900 leading-none">Rincian Foto</span>
                    </div>
                </div>
                <div class="w-12 h-px bg-gray-200"></div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center shrink-0 text-[10px] font-bold">
                        3
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-black uppercase tracking-widest block leading-none">Langkah 03</span>
                        <span class="text-xs font-black text-black leading-none">Bayar QRIS</span>
                    </div>
                </div>
                <div class="w-12 h-px bg-gray-200"></div>
                <div class="flex items-center gap-2 opacity-40">
                    <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center shrink-0 text-[10px] font-bold">
                        4
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block leading-none">Langkah 04</span>
                        <span class="text-xs font-bold text-gray-500 leading-none">Unduh Original</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Order Summary -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                
                <!-- Google Connection Info -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
                            <!-- Simple Google G icon SVG -->
                            <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                            TERSAMBUNG GOOGLE
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-[10px] font-bold border border-green-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Tersambung
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 rounded-lg mb-3">
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ Auth::user()->email ?? 'buyer.momen@gmail.com' }}</div>
                            <div class="text-[10px] text-gray-500 font-medium mt-0.5">Akses arsip digital permanen</div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <p class="text-xs text-gray-500">
                        Tautan download original & bukti lisensi otomatis dikirimkan ke email ini.
                    </p>
                </div>

                <!-- Item Info -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ITEM PEMBELIAN</span>
                        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-widest">REF: #CFD-{{ str_pad($photo->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="flex gap-4 pb-4 border-b border-gray-100 mb-4">
                        <div class="w-20 h-20 rounded-lg overflow-hidden shrink-0 bg-gray-100 border border-gray-200 relative">
                            <img src="{{ Storage::url($photo->file_watermark) }}" alt="Thumbnail" class="w-full h-full object-cover">
                            <!-- Overlay watermark tiny text -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-50 transform -rotate-12">
                                <span class="text-[6px] font-black text-white tracking-widest uppercase shadow-sm">JEPRET PREVIEW</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">{{ $photo->event->nama_event ?? 'Event CFD' }}</div>
                            <h3 class="text-sm font-bold text-gray-900 leading-tight mb-2">Batch #{{ str_pad($photo->id, 5, '0', STR_PAD_LEFT) }} Runner</h3>
                            <div class="text-xs text-gray-600 mb-2">Fotografer: <span class="font-bold text-gray-900">{{ $photo->fotografer->name ?? 'Dwi Visual' }}</span></div>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 text-[9px] font-bold">24.2 MP</span>
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 text-[9px] font-bold">RAW-JPEG</span>
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 text-[9px] font-bold">{{ $photo->resolusi ?? '6000 x 4000' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-600">Lisensi Digital Personal</span>
                            <span class="text-xs font-bold text-gray-900">Rp{{ number_format($photo->harga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-600">Biaya Platform (QRIS standard)</span>
                            <span class="text-xs font-bold text-green-600">Gratis (Rp0)</span>
                        </div>
                    </div>

                    <div class="flex items-end justify-between pt-4 border-t border-gray-100 mb-6">
                        <span class="text-sm font-black text-gray-900">Total Tagihan</span>
                        <span class="text-2xl font-black text-gray-900">Rp{{ number_format($photo->harga, 0, ',', '.') }}</span>
                    </div>

                    <!-- Auto Webhook banner -->
                    <div class="bg-gray-50 rounded-lg p-3 flex gap-3 border border-gray-100">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            <span class="font-bold text-gray-900">Auto-Webhook Sync:</span> File master tanpa watermark langsung terbuka dalam 3 detik setelah notifikasi settlement bank diterima.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Right Column: QRIS Area -->
            <div class="lg:col-span-7">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm flex flex-col items-center">
                    
                    <div class="w-full flex items-center justify-between mb-8">
                        <div class="flex items-center gap-2">
                            <div class="bg-black text-white px-2 py-0.5 rounded text-xs font-black tracking-wider">QRIS</div>
                            <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">GPN PEMBAYARAN DIGITAL</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs font-bold text-gray-500 bg-red-50 text-red-600 px-2 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                            14:29
                        </div>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 text-xs font-bold text-gray-600 mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                        Menunggu Pembayaran Transfer...
                    </div>

                    <h2 class="text-2xl md:text-3xl font-black text-gray-900 text-center mb-2 tracking-tight">Pindai QRIS untuk Membeli</h2>
                    <p class="text-sm text-gray-500 text-center max-w-sm mb-8">
                        Scan melalui BCA, GoPay, OVO, Dana, ShopeePay, Livin', atau aplikasi m-Banking apa pun.
                    </p>

                    <!-- The QR Code Box -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm w-full max-w-xs mb-8 flex flex-col items-center">
                        <div class="w-full flex items-center justify-between mb-4">
                            <span class="text-[9px] font-bold text-gray-400 tracking-widest">NMID: ID1020039102938</span>
                            <span class="text-[9px] font-bold text-gray-400 tracking-widest">A01 - JEPRET</span>
                        </div>
                        
                        <!-- Dummy QR Code UI (Black squares mimicking a real QR) -->
                        <div class="w-full aspect-square border-4 border-black rounded-lg p-3 relative bg-white flex flex-col gap-1 justify-between">
                            <!-- Just some geometric shapes to look like a QR code -->
                            <div class="flex justify-between w-full">
                                <div class="w-12 h-12 border-[6px] border-black flex items-center justify-center"><div class="w-5 h-5 bg-black"></div></div>
                                <div class="flex gap-1 items-start w-10 overflow-hidden"><div class="w-3 h-3 bg-black"></div><div class="w-2 h-4 bg-black"></div><div class="w-4 h-2 bg-black"></div></div>
                                <div class="w-12 h-12 border-[6px] border-black flex items-center justify-center"><div class="w-5 h-5 bg-black"></div></div>
                            </div>
                            
                            <div class="flex gap-1 px-1 h-10 w-full flex-wrap overflow-hidden">
                                <div class="w-4 h-4 bg-black"></div><div class="w-6 h-3 bg-black"></div><div class="w-2 h-5 bg-black"></div>
                                <div class="w-8 h-2 bg-black"></div><div class="w-3 h-3 bg-black"></div><div class="w-5 h-5 bg-black"></div>
                                <div class="w-2 h-6 bg-black"></div><div class="w-4 h-4 bg-black"></div><div class="w-7 h-2 bg-black"></div>
                            </div>

                            <!-- Center Logo -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-10 h-10 bg-white flex items-center justify-center">
                                    <svg class="w-8 h-8 text-black" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM11 19.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                </div>
                            </div>

                            <div class="flex justify-between w-full items-end">
                                <div class="w-12 h-12 border-[6px] border-black flex items-center justify-center"><div class="w-5 h-5 bg-black"></div></div>
                                <div class="flex gap-1 items-end w-12 h-12 overflow-hidden flex-wrap-reverse"><div class="w-3 h-5 bg-black"></div><div class="w-5 h-3 bg-black"></div><div class="w-2 h-6 bg-black"></div></div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col items-center">
                            <span class="text-[9px] font-bold text-gray-400 tracking-widest uppercase mb-1">TOTAL BAYAR PAS:</span>
                            <span class="text-xl font-black text-gray-900">Rp{{ number_format($photo->harga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Payment Guide Box -->
                    <div class="w-full bg-gray-50 border border-gray-100 rounded-xl p-5 mb-8">
                        <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">PANDUAN PEMBAYARAN CEPAT</h4>
                        <ol class="text-xs text-gray-600 space-y-2 list-decimal list-inside">
                            <li>Buka aplikasi e-Wallet (GoPay, OVO, Dana, ShopeePay) atau m-Banking pilihan Anda.</li>
                            <li>Pilih menu <span class="font-bold text-gray-900">Bayar / Scan QRIS</span> dan arahkan kamera ke barcode di atas.</li>
                            <li>Periksa nama penerima <span class="font-bold text-gray-900">JEPRET ARCHIVE</span>, nominal Rp{{ number_format($photo->harga, 0, ',', '.') }}, lalu masukkan PIN.</li>
                        </ol>
                    </div>

                    <!-- Actions -->
                    <div class="w-full flex flex-col sm:flex-row items-center gap-3">
                        <a href="{{ route('marketplace.show', $photo) }}" class="w-full sm:w-auto flex-1 bg-white border border-gray-200 text-gray-700 font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Kembali ke Detail
                        </a>
                        <form action="{{ route('cart.store') }}" method="POST" class="w-full sm:w-auto flex-1">
                            @csrf
                            <input type="hidden" name="photo_id" value="{{ $photo->id }}">
                            <button class="w-full bg-black text-white font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-gray-800 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Tambah ke Keranjang
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>

        <!-- Sub footer -->
        <div class="mt-12 pt-6 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Koneksi terenkripsi Bank Indonesia & Standar QRIS Nasional (ASPI).
            </div>
            <div class="text-xs text-gray-400 font-medium">
                BCA / Mandiri / BNI / BRI • GoPay / OVO / Dana / ShopeePay
            </div>
        </div>

    </div>
</x-marketplace-layout>
