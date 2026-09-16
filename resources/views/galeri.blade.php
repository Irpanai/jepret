<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galeri - JEPRET CFD</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #FFFFFF; color: #111827; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased text-gray-900 bg-white">

    <x-navbar />

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Search & Filter Controls -->
        <div class="mb-8 flex flex-col md:flex-row gap-4 items-center justify-between border-b border-gray-100 pb-6">
            <div class="w-full md:w-1/2 relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" placeholder="Cari foto berdasarkan event, photographer, atau kategori..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-base focus:ring-black focus:border-black transition shadow-sm">
            </div>
            
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <select class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl px-4 py-3 focus:ring-black focus:border-black font-semibold">
                    <option>Semua Event</option>
                    <option>CFD Sudirman</option>
                    <option>Banjarmasin Marathon</option>
                </select>
                <select class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl px-4 py-3 focus:ring-black focus:border-black font-semibold">
                    <option>Kapan Saja</option>
                    <option>Hari Ini</option>
                    <option>Minggu Ini</option>
                </select>
                <button class="bg-black text-white text-sm font-bold px-6 py-3 rounded-xl hover:bg-gray-800 transition">Filter</button>
            </div>
        </div>

        <!-- Gallery Grid (5 cols on Desktop) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-16">
            
            @forelse($photos as $photo)
            <div class="bg-white group cursor-pointer flex flex-col h-full border border-transparent hover:border-gray-200 hover:shadow-lg rounded-2xl transition p-2">
                <!-- Image Wrapper with Protection -->
                <div class="relative aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden mb-3">
                    <img src="{{ $photo->url ?? 'https://images.unsplash.com/photo-1552674605-15c2145e9ca4?q=80&w=600&auto=format&fit=crop' }}" alt="Photo" class="w-full h-full object-cover blur-[2px] scale-105 group-hover:scale-110 transition duration-700">
                    
                    <!-- Watermark -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-40">
                        <span class="text-3xl font-black text-white tracking-widest uppercase transform -rotate-45 select-none" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">JEPRET</span>
                    </div>

                    <!-- Protected Badge -->
                    <div class="absolute top-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] font-bold text-white uppercase tracking-wider">
                        Protected
                    </div>
                </div>
                
                <!-- Info -->
                <div class="px-2 flex-1 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-full overflow-hidden bg-gray-200 shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($photo->fotografer->user->name ?? 'Photographer') }}&background=000&color=fff" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xs font-bold text-gray-700 truncate">{{ $photo->fotografer->user->name ?? 'Dwi Visual' }}</span>
                    </div>

                    <h3 class="text-sm font-bold text-black mb-1 line-clamp-2 leading-tight">
                        {{ $photo->event->name ?? 'Sunday Morning Run CFD' }}
                    </h3>
                    
                    <p class="text-xs text-gray-500 font-medium mb-3">
                        {{ $photo->event->location ?? 'Sudirman, Jakarta' }} &bull; {{ $photo->created_at ? $photo->created_at->format('d M Y') : '12 Sep 2024' }}
                    </p>
                    
                    <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                        <p class="text-base font-black text-black">Rp {{ number_format($photo->price ?? 20000, 0, ',', '.') }}</p>
                        
                        @auth
                            @if(auth()->user()->role === 'pembeli')
                            <button class="bg-gray-100 hover:bg-black hover:text-white text-black text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                + Keranjang
                            </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                Login to Buy
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-lg font-bold text-gray-500">Belum ada foto yang tersedia.</p>
            </div>
            @endforelse

            <!-- Dummy items for visual if empty (since DB might not be seeded) -->
            @if($photos->isEmpty())
                @for($i=0; $i<10; $i++)
                <div class="bg-white group cursor-pointer flex flex-col h-full border border-transparent hover:border-gray-200 hover:shadow-lg rounded-2xl transition p-2">
                    <!-- Image Wrapper with Protection -->
                    <div class="relative aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden mb-3">
                        <img src="https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?q=80&w=600&auto=format&fit=crop" alt="Photo" class="w-full h-full object-cover blur-[2px] scale-105 group-hover:scale-110 transition duration-700">
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-40">
                            <span class="text-3xl font-black text-white tracking-widest uppercase transform -rotate-45 select-none" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">JEPRET</span>
                        </div>
                        <div class="absolute top-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] font-bold text-white uppercase tracking-wider">
                            Protected
                        </div>
                    </div>
                    
                    <div class="px-2 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-full overflow-hidden bg-gray-200 shrink-0">
                                <img src="https://ui-avatars.com/api/?name=Studio+Kreatif&background=000&color=fff" class="w-full h-full object-cover">
                            </div>
                            <span class="text-xs font-bold text-gray-700 truncate">Studio Kreatif</span>
                        </div>
                        <h3 class="text-sm font-bold text-black mb-1 line-clamp-2 leading-tight">Gowes Pagi Rute Jend. Sudirman</h3>
                        <p class="text-xs text-gray-500 font-medium mb-3">Jakarta Pusat &bull; 10 Sep 2024</p>
                        <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                            <p class="text-base font-black text-black">Rp 25.000</p>
                            @auth
                                @if(auth()->user()->role === 'pembeli')
                                <button class="bg-gray-100 hover:bg-black hover:text-white text-black text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                    + Keranjang
                                </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                    Login to Buy
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endfor
            @endif

        </div>
        
        <!-- Infinite Scroll loader mockup -->
        <div class="py-8 flex justify-center items-center">
            <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-500 font-bold text-sm uppercase tracking-widest">Memuat lebih banyak...</span>
        </div>

    </div>

</body>
</html>
