<x-marketplace-layout>
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500">
                <a href="#" class="hover:text-gray-900 transition">Event CFD</a>
                <span>/</span>
                <a href="#" class="hover:text-gray-900 transition">{{ $photo->event->nama_event ?? 'Event' }} ({{ $photo->event->lokasi ?? 'Lokasi' }})</a>
                <span>/</span>
                <span class="text-black font-bold uppercase">ASSET #CFD-2409-{{ str_pad($photo->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-50 text-green-700 text-[10px] font-bold tracking-widest border border-green-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Original Raw Stored (S3-Sync)
                </span>
                <span class="text-[10px] text-gray-400 font-medium">Diupload {{ $photo->created_at ? $photo->created_at->diffForHumans() : 'baru saja' }}</span>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Column: Photo Preview -->
            <div class="lg:col-span-8 flex flex-col gap-4">
                
                <!-- Main Photo -->
                <div class="relative bg-gray-100 rounded-lg overflow-hidden border border-gray-200 aspect-[3/2] flex items-center justify-center cursor-zoom-in group">
                    <img src="{{ Storage::url($photo->file_watermark) }}" alt="Preview" class="w-full h-full object-cover">
                    
                    <!-- Overlay Badges -->
                    <div class="absolute top-4 left-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-white/90 backdrop-blur-sm border border-white/20 shadow-sm text-xs font-bold text-gray-900">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Pratinjau Terproteksi • Resolusi Rendah
                    </div>
                    
                    <div class="absolute top-4 right-4 inline-flex items-center px-3 py-1.5 rounded-md bg-black/80 backdrop-blur-sm text-white text-[10px] font-bold tracking-widest">
                        3:2 RATIO • PREVIEW RES 1600x1066
                    </div>
                    
                    <div class="absolute bottom-4 right-4 inline-flex items-center gap-2 px-3 py-2 rounded-md bg-white/90 backdrop-blur-sm shadow-sm text-xs font-bold text-gray-900 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        Klik icon pembesar untuk simulasi zoom
                    </div>
                </div>

                <!-- Actions Bar -->
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <button class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Zoom Pratinjau
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            Simpan
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                            Bagikan
                        </button>
                    </div>
                    <div class="flex items-center gap-4 text-xs font-medium text-gray-400">
                        <span>ID: IMG_MURJANI_{{ str_pad($photo->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <a href="#" class="hover:text-gray-900 transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                            Laporkan
                        </a>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-gray-50 rounded-lg p-4 flex gap-4 border border-gray-100">
                    <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 mb-1">Teknologi Perlindungan Aset Jepret</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Pratinjau di layar dikompresi sebesar 82% dan disematkan watermark kriptis untuk melindungi hak cipta fotografer komunitas kami. Foto asli tanpa watermark beresolusi 24 Megapixel siap diunduh otomatis dalam hitungan detik setelah transaksi.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Right Column: Info & Purchase -->
            <div class="lg:col-span-4 flex flex-col gap-4">
                
                <!-- Photographer Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col gap-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-gray-900 flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($photo->fotografer->name ?? 'F', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 flex items-center gap-1.5">
                                    {{ $photo->fotografer->name ?? 'Fotografer' }}
                                    <svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </h3>
                                <div class="flex items-center gap-1 text-xs text-gray-500 font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Banjarbaru, Indonesia
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-center border border-gray-100 bg-gray-50 rounded-lg px-3 py-1.5">
                            <svg class="w-4 h-4 text-yellow-400 mb-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span class="text-xs font-bold text-gray-900">4.95</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2 text-xs font-medium text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            142 Foto di Event ini
                        </div>
                        <a href="#" class="text-xs font-bold text-black hover:underline flex items-center gap-1">
                            Lihat Profil <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Purchase Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">LISENSI PERSONAL DIGITAL</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-green-50 text-green-700 text-[10px] font-bold border border-green-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Siap Unduh
                        </span>
                    </div>
                    
                    <div class="flex items-baseline gap-2 mb-3">
                        <h2 class="text-4xl font-black text-gray-900 tracking-tight">Rp{{ number_format($photo->harga, 0, ',', '.') }}</h2>
                        <span class="text-xs font-semibold text-gray-500">/ file original</span>
                    </div>
                    
                    <p class="text-xs text-gray-500 leading-relaxed mb-5">
                        Sudah termasuk semua biaya admin & gateway pembayaran (QRIS, GoPay, OVO, ShopeePay, Transfer Bank).
                    </p>

                    @auth
                        @if(auth()->user()->role === 'pembeli')
                            <form method="POST" action="{{ route('cart.store') }}" class="mb-4">
                                @csrf
                                <input type="hidden" name="photo_id" value="{{ $photo->id }}">
                                <button class="w-full bg-black text-white text-center rounded-xl py-3.5 px-4 font-bold text-sm hover:bg-gray-800 transition flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    Tambah ke Keranjang
                                </button>
                            </form>
                        @else
                            <a href="{{ route('dashboard') }}" class="block w-full bg-black text-white text-center rounded-xl py-3.5 px-4 font-bold text-sm hover:bg-gray-800 transition mb-4">Buka Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full bg-black text-white text-center rounded-xl py-3.5 px-4 font-bold text-sm hover:bg-gray-800 transition mb-4">Login untuk Membeli</a>
                    @endauth

                    <div class="text-[10px] text-gray-400 text-center mb-5 pb-5 border-b border-gray-100">
                        Verifikasi kilat via Google • Tanpa password & tanpa antrean
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Pengiriman Instan:</h4>
                                <p class="text-xs text-gray-500">Tautan unduhan master JPEG resolusi 24MP tanpa watermark aktif otomatis seketika setelah pembayaran.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Akses Arsip Permanen:</h4>
                                <p class="text-xs text-gray-500">Tautan unduhan tersimpan di menu Pembelian Saya setelah pembayaran lunas.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Dukungan instan QRIS Nasional:</h4>
                                <p class="text-xs text-gray-500">(BCA, Mandiri, BRI, BNI, DANA, LinkAja).</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EXIF Table -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            Spesifikasi Berkas & EXIF
                        </h3>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">RAW METADATA</span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-xs text-gray-500">Event</span>
                            <span class="text-xs font-bold text-gray-900">{{ $photo->event->nama_event ?? 'Event' }} — CFD</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-xs text-gray-500">Tanggal & Waktu</span>
                            <span class="text-xs font-bold text-gray-900">{{ $photo->event->tanggal_event ? \Carbon\Carbon::parse($photo->event->tanggal_event)->format('l, d M Y') : 'Unknown' }} • {{ $photo->created_at ? $photo->created_at->format('H:i') : '' }} WITA</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-xs text-gray-500">Lokasi / Lintasan</span>
                            <span class="text-xs font-bold text-gray-900">{{ $photo->event->lokasi ?? 'Unknown' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-xs text-gray-500">Resolusi Asli</span>
                            <span class="text-xs font-bold text-gray-900">{{ $photo->resolusi ?? '6000 x 4000 px (24.0 MP)' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-xs text-gray-500">Format Pengiriman</span>
                            <span class="text-xs font-bold text-gray-900">JPEG Original (Uncompressed)</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-xs text-gray-500">Kamera Body</span>
                            <span class="text-xs font-bold text-gray-900">{{ $photo->kamera_body ?? 'Sony Alpha 7 IV (ILCE-7M4)' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-xs text-gray-500">Lensa</span>
                            <span class="text-xs font-bold text-gray-900">{{ $photo->lensa ?? 'FE 70-200mm f/2.8 GM OSS II' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-xs text-gray-500">Eksposur / F-Stop</span>
                            <span class="text-xs font-bold text-gray-900">{{ $photo->eksposur ?? '1/1250s • f/2.8 • ISO 200' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Focal Length</span>
                            <span class="text-xs font-bold text-gray-900">{{ $photo->focal_length ?? '135mm' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Related Photos -->
        <div class="mt-16 pt-8 border-t border-gray-200">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">KURASI LANJUTAN</span>
                    <h2 class="text-2xl font-black text-gray-900">Foto Lain dari {{ $photo->fotografer->name ?? 'Fotografer' }} di Event yang Sama</h2>
                    <p class="text-sm text-gray-500 mt-1">Semua peserta tertangkap lensa di {{ $photo->event->lokasi ?? 'Lokasi' }} dalam radius jam yang sama (06:45 - 08:30 WITA).</p>
                </div>
                <a href="#" class="text-sm font-bold text-black hover:underline flex items-center gap-1">
                    Lihat 141 Foto Lainnya <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($relatedPhotos as $related)
                <div class="bg-white rounded-xl overflow-hidden border border-gray-200 shadow-sm group cursor-pointer hover:shadow-md transition">
                    <div class="relative aspect-[3/4] bg-gray-100 overflow-hidden">
                        <img src="{{ Storage::url($related->file_watermark) }}" alt="Related" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute top-3 left-3 bg-black/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded">
                            {{ $related->created_at ? $related->created_at->format('H:i') : '' }} WITA
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition bg-black/20 backdrop-blur-[2px]">
                            <a href="{{ route('marketplace.show', $related->id) }}" class="bg-white text-black font-bold text-sm px-4 py-2 rounded-lg shadow-lg transform translate-y-2 group-hover:translate-y-0 transition">Lihat Detail</a>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-bold text-gray-400">#CFD-2409-{{ str_pad($related->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-[10px] font-medium text-gray-400">Sony A7 IV</span>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 truncate mb-3">Pesepeda Roadbike Sprint</h4>
                        <div class="flex items-center justify-between">
                            <span class="text-base font-black text-gray-900">Rp{{ number_format($related->harga, 0, ',', '.') }}</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-12 text-center text-gray-500">
                    Tidak ada foto lain di event ini.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-marketplace-layout>
