<x-fg-layout>
    <!-- Header Area -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">STUDIO KURASI FOTOGRAFER • ID-CFD-8820</span>
            </div>
            <h1 class="text-3xl font-black text-black tracking-tight mb-2">Manajemen Foto & Cloud Upload</h1>
            <p class="text-sm text-gray-500 font-medium max-w-xl">
                Kelola kurasi galeri, hak cipta watermark, dan harga jual per foto. Komisi fotografer terdistribusi otomatis 90%.
            </p>
        </div>
        <div class="flex items-center gap-4 shrink-0">
            <div class="bg-gray-50 border border-gray-200 text-gray-500 text-[10px] font-bold px-4 py-3 rounded-lg shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                <div class="flex flex-col items-start leading-tight">
                    <span>S3-EDGE-JAKARTA:</span>
                    <span class="text-black">SYNCED</span>
                </div>
            </div>
            <button class="bg-black text-white text-xs font-bold px-6 py-3.5 rounded-lg shadow-sm hover:bg-gray-800 flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Publikasikan Semua Draf
            </button>
        </div>
    </div>

    <!-- Upload & Calculator Section -->
    <form action="{{ route('fotografer.photos.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        @csrf
        <!-- Upload Box -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="text-xs font-bold text-black uppercase tracking-widest">UPLOAD STUDIO ENGINE</span>
                </div>
                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">RAW, JPEG, PNG • MAX 10MB</span>
            </div>

            <!-- Event Selection & Folder Creation -->
            <div class="mb-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="title" class="block text-[10px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Judul Foto</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-0 focus:border-black transition-colors" placeholder="Contoh: Finisher di KM 5">
                    </div>
                    <div>
                        <label for="camera_id" class="block text-[10px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Kamera</label>
                        <select name="camera_id" id="camera_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-0 focus:border-black transition-colors">
                            <option value="">-- Tanpa metadata kamera --</option>
                            @foreach($cameras as $camera)
                                <option value="{{ $camera->id }}" @selected(old('camera_id') == $camera->id)>{{ $camera->name }} - {{ $camera->brand_model }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="event_id" class="block text-[10px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Pilih Event/Folder (Opsional)</label>
                    <select name="event_id" id="event_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-0 focus:border-black transition-colors">
                        <option value="">-- Pilih Event yang Sudah Ada --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->nama_event }} - {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="relative flex items-center py-1">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 text-[9px] font-bold uppercase tracking-widest">ATAU BUAT FOLDER BARU</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>
                <div>
                    <label for="new_folder" class="block text-[10px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Nama Folder / Event Baru</label>
                    <input type="text" name="new_folder" id="new_folder" value="{{ old('new_folder') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-0 focus:border-black transition-colors" placeholder="Contoh: Jakarta Marathon 2026">
                    <p class="text-[9px] text-gray-400 mt-2 font-medium">Sistem otomatis membuatkan folder untuk foto Anda.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="category" class="block text-[10px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Kategori</label>
                        <input type="text" name="category" id="category" value="{{ old('category') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-0 focus:border-black transition-colors" placeholder="Run, bike, portrait">
                    </div>
                    <div>
                        <label for="taken_at" class="block text-[10px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Tanggal Ambil</label>
                        <input type="datetime-local" name="taken_at" id="taken_at" value="{{ old('taken_at') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-0 focus:border-black transition-colors">
                    </div>
                    <div>
                        <label for="daypart" class="block text-[10px] font-bold text-gray-500 mb-2 uppercase tracking-widest">Waktu</label>
                        <select name="daypart" id="daypart" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-0 focus:border-black transition-colors">
                            <option value="">Auto / Tidak diisi</option>
                            <option value="morning" @selected(old('daypart') === 'morning')>Pagi</option>
                            <option value="afternoon" @selected(old('daypart') === 'afternoon')>Siang</option>
                            <option value="evening" @selected(old('daypart') === 'evening')>Sore</option>
                            <option value="night" @selected(old('daypart') === 'night')>Malam</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dropzone (File Input) -->
            <div x-data="{ fileName: '', previewUrl: '' }" class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50/50 flex flex-col items-center justify-center py-8 hover:border-black hover:bg-gray-50 transition relative overflow-hidden group flex-grow mb-4" :class="{ 'border-black bg-gray-50': fileName }">
                <input type="file" name="photo" id="photo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/jpeg,image/png,image/jpg" required
                    @change="
                        if($event.target.files.length > 0) {
                            fileName = $event.target.files[0].name;
                            previewUrl = URL.createObjectURL($event.target.files[0]);
                        } else {
                            fileName = '';
                            previewUrl = '';
                        }
                    ">
                
                <!-- If no file is selected -->
                <div x-show="!fileName" class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center mb-4 group-hover:bg-black group-hover:border-black group-hover:text-white transition-colors">
                        <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-black mb-1">Pilih atau Tarik File Ke Sini</h3>
                    <p class="text-[10px] text-gray-500 font-medium mb-4">Maksimal ukuran file 10MB.</p>
                    <div class="flex gap-2">
                        <span class="px-2 py-1 bg-white border border-gray-200 rounded text-[9px] font-bold text-gray-400">JPG</span>
                        <span class="px-2 py-1 bg-white border border-gray-200 rounded text-[9px] font-bold text-gray-400">PNG</span>
                    </div>
                </div>

                <!-- If file is selected -->
                <div x-show="fileName" class="flex flex-col items-center" style="display: none;">
                    <img :src="previewUrl" class="w-32 h-32 object-cover rounded-lg shadow-sm mb-3">
                    <span class="text-xs font-bold text-black" x-text="fileName"></span>
                    <span class="text-[10px] text-green-600 font-bold mt-1">Siap diunggah!</span>
                </div>
            </div>
            
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2 text-green-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-xs font-bold">{{ session('success') }}</span>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-[10px] font-bold text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Calculator Box -->
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between" x-data="{ harga: 20000 }">
            <div>
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-2 text-black">
                        <span class="font-bold text-lg">%</span>
                        <h3 class="text-xs font-bold uppercase tracking-widest">KALKULATOR BAGI HASIL</h3>
                    </div>
                    <span class="bg-green-100 text-green-700 text-[9px] font-bold px-2 py-1 rounded">Skema 90/10</span>
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-gray-500 mb-2">Tentukan Harga Jual Foto (Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-xs font-bold">Rp</span>
                        </div>
                        <input type="number" name="harga" x-model="harga" required min="0" class="block w-full pl-8 pr-3 py-3 border border-gray-200 rounded-lg leading-5 bg-white text-black font-black text-lg focus:outline-none focus:ring-1 focus:ring-black focus:border-black transition">
                    </div>
                    <p class="text-[9px] font-medium text-gray-400 mt-2 leading-relaxed">Rekomendasi harga event CFD umum: Rp15.000 - Rp25.000</p>
                </div>

                <div class="border-t border-gray-200 pt-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] font-bold text-gray-500">Harga Jual Pembeli (100%)</span>
                        <span class="text-[10px] font-bold text-gray-400 line-through" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(harga)">Rp 20.000</span>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                            <span class="text-xs font-bold text-black">Pendapatan Bersih Anda (90%)</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-black block mb-0.5">Rp</span>
                            <span class="text-xl font-black text-black leading-none" x-text="new Intl.NumberFormat('id-ID').format(Math.floor(harga * 0.9))">18.000</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-3">
                        <span class="text-[10px] font-bold text-gray-500">Biaya Platform & Server CDN (10%)</span>
                        <span class="text-[10px] font-bold text-gray-400" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(Math.ceil(harga * 0.1))">Rp 2.000</span>
                    </div>
                    <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden mt-2 flex">
                        <div class="h-full bg-black rounded-l-full" style="width: 90%;"></div>
                        <div class="h-full bg-gray-300 rounded-r-full" style="width: 10%;"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4 border-t border-gray-200 pt-4 flex flex-col gap-3">
                <button type="submit" class="w-full bg-black text-white text-xs font-bold px-6 py-3.5 rounded-lg shadow-sm hover:bg-gray-800 flex items-center justify-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Unggah & Terbitkan Sekarang
                </button>
            </div>
        </div>
    </form>

    <!-- Catalog Section -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-bold text-black">Katalog Foto Acara Aktif</h2>
                <span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2 py-1 rounded">142 Item</span>
            </div>
            
            <div class="flex items-center gap-2">
                <button class="bg-white border border-gray-200 text-gray-600 hover:text-black text-[10px] font-bold px-3 py-2 rounded-lg shadow-sm transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Ubah Harga Batch
                </button>
                <button class="bg-white border border-gray-200 text-gray-600 hover:text-black text-[10px] font-bold px-3 py-2 rounded-lg shadow-sm transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Arsipkan
                </button>
                <button class="bg-white border border-gray-200 text-gray-600 hover:text-black text-[10px] font-bold px-3 py-2 rounded-lg shadow-sm transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Unduh Laporan
                </button>
                <button class="bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 text-[10px] font-bold px-3 py-2 rounded-lg shadow-sm transition flex items-center gap-1.5 ml-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus
                </button>
            </div>
        </div>
        
        <div class="px-6 pt-4 pb-2 border-b border-gray-100 flex justify-between items-center">
            <div class="flex gap-6">
                <button class="text-xs font-bold text-white bg-black px-4 py-1.5 rounded-full shadow-sm">Semua (142)</button>
                <button class="text-xs font-bold text-gray-500 hover:text-black py-1.5 transition">Dipublikasikan (128)</button>
                <button class="text-xs font-bold text-gray-500 hover:text-black py-1.5 transition">Terjual (86)</button>
                <button class="text-xs font-bold text-gray-500 hover:text-black py-1.5 transition">Draf (14)</button>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" class="rounded border-gray-300 text-black focus:ring-black">
                <label class="text-[10px] font-medium text-gray-500">Pilih Semua (142 Foto)</label>
            </div>
        </div>

        <!-- Photo Grid -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-gray-50/50">
            @forelse($photos as $photo)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col group relative">
                <div class="absolute top-3 left-3 z-10 bg-white rounded shadow-sm">
                    <input type="checkbox" class="m-2 rounded border-gray-300 text-black focus:ring-black cursor-pointer">
                </div>
                <div class="absolute top-3 right-3 z-10">
                    <span class="bg-green-100/90 backdrop-blur-sm text-green-700 text-[10px] font-bold px-2 py-1.5 rounded shadow-sm">Aktif di Marketplace</span>
                </div>
                
                <div class="relative w-full aspect-video bg-gray-100 overflow-hidden">
                    <img src="{{ Storage::url($photo->file_watermark) }}" class="w-full h-full object-cover" alt="Preview">
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20">
                        <!-- hover overlay if needed -->
                    </div>
                </div>
                
                <div class="p-4 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xs font-black text-black uppercase tracking-wider truncate" title="{{ basename($photo->file_asli) }}">{{ Str::limit(basename($photo->file_asli), 20) }}</h3>
                        <span class="text-[9px] font-bold text-gray-400">10.0 MB</span>
                    </div>
                    <p class="text-[9px] font-medium text-gray-500 mb-4 leading-relaxed">
                        Event: {{ $photo->event->nama_event ?? 'Tidak ada Event' }}
                        @if($photo->ai_tags)
                            <br>Tags: {{ implode(', ', json_decode($photo->ai_tags, true)) }}
                        @endif
                    </p>
                    
                    <div class="bg-gray-50 rounded-lg p-3 grid grid-cols-2 gap-4 mb-4 mt-auto">
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 block mb-0.5">Harga Satuan:</span>
                            <span class="text-xs font-bold text-black">Rp {{ number_format($photo->harga, 0, ',', '.') }} <span class="text-[9px] font-medium text-gray-400">(Net Rp {{ number_format($photo->net_harga, 0, ',', '.') }})</span></span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 block mb-0.5">Statistik Penjualan:</span>
                            <span class="text-[9px] font-medium text-gray-500">{{ number_format($photo->views_count ?? 0) }} tayangan • <span class="text-gray-400 font-bold">{{ $photo->transactions()->where('status', 'paid')->count() }} terjual</span></span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center pt-2">
                        <div class="flex gap-2 text-gray-400">
                            <button class="hover:text-black transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></button>
                            <button class="hover:text-black transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></button>
                        </div>
                        <button class="text-[10px] font-bold text-gray-600 border border-gray-200 bg-white hover:bg-gray-50 px-3 py-1.5 rounded shadow-sm transition">
                            Ubah Harga
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center flex flex-col items-center justify-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-sm font-bold text-gray-400">Belum ada foto yang diunggah.</p>
            </div>
            @endforelse
        </div>

        <div class="p-6 border-t border-gray-100 flex justify-between items-center">
            <span class="text-[10px] font-medium text-gray-400">Menampilkan 1-3 dari 142 berkas foto digital</span>
            <div class="flex items-center gap-1">
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-400 hover:bg-gray-50 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                <button class="w-8 h-8 flex items-center justify-center bg-black text-white font-bold text-xs rounded shadow-sm">1</button>
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 transition text-xs font-bold">2</button>
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 transition text-xs font-bold">3</button>
                <span class="w-6 text-center text-gray-400 text-xs">...</span>
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 transition text-xs font-bold">48</button>
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
            </div>
        </div>

    </div>
</x-fg-layout>
