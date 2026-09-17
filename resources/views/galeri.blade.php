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
        <form method="GET" action="{{ route('galeri') }}" class="mb-8 border-b border-gray-100 pb-6">
            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_auto] gap-4 items-start">
                <div class="relative">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari event, fotografer, lokasi, tag, atau judul foto..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-base focus:ring-black focus:border-black transition shadow-sm">
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select name="event" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl px-4 py-3 focus:ring-black focus:border-black font-semibold">
                        <option value="">Semua Event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" @selected((string) request('event') === (string) $event->id)>{{ $event->nama_event }}</option>
                        @endforeach
                    </select>
                    <select name="photographer" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl px-4 py-3 focus:ring-black focus:border-black font-semibold">
                        <option value="">Semua Photographer</option>
                        @foreach($photographers as $photographer)
                            <option value="{{ $photographer->id }}" @selected((string) request('photographer') === (string) $photographer->id)>{{ $photographer->studio_name ?: $photographer->name }}</option>
                        @endforeach
                    </select>
                    <select name="category" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl px-4 py-3 focus:ring-black focus:border-black font-semibold">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    <select name="daypart" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl px-4 py-3 focus:ring-black focus:border-black font-semibold">
                        <option value="">Semua Waktu</option>
                        <option value="morning" @selected(request('daypart') === 'morning')>Pagi</option>
                        <option value="afternoon" @selected(request('daypart') === 'afternoon')>Siang</option>
                        <option value="evening" @selected(request('daypart') === 'evening')>Sore</option>
                        <option value="night" @selected(request('daypart') === 'night')>Malam</option>
                    </select>
                    <input type="date" name="date" value="{{ request('date') }}" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl px-4 py-3 focus:ring-black focus:border-black font-semibold">
                    <button class="bg-black text-white text-sm font-bold px-6 py-3 rounded-xl hover:bg-gray-800 transition">Filter</button>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-10">
            @forelse($photos as $photo)
                <div class="bg-white group flex flex-col h-full border border-transparent hover:border-gray-200 hover:shadow-lg rounded-2xl transition p-2">
                    <a href="{{ route('marketplace.show', $photo) }}" class="block relative aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden mb-3">
                        <img src="{{ $photo->file_watermark ? Storage::url($photo->file_watermark) : 'https://placehold.co/600x800/f3f4f6/111827?text=JEPRET' }}" alt="{{ $photo->title ?: 'Foto marketplace' }}" class="w-full h-full object-cover blur-[2px] scale-105 group-hover:scale-110 transition duration-700">

                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-40">
                            <span class="text-3xl font-black text-white tracking-widest uppercase transform -rotate-45 select-none" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">JEPRET</span>
                        </div>

                        <div class="absolute top-3 left-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] font-bold text-white uppercase tracking-wider">
                            Preview
                        </div>
                    </a>

                    <div class="px-2 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-full overflow-hidden bg-gray-200 shrink-0">
                                <img src="{{ $photo->fotografer?->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($photo->fotografer?->name ?? 'Photographer').'&background=000&color=fff' }}" class="w-full h-full object-cover" alt="">
                            </div>
                            <span class="text-xs font-bold text-gray-700 truncate">{{ $photo->fotografer?->studio_name ?: ($photo->fotografer?->name ?? 'Photographer') }}</span>
                        </div>

                        <a href="{{ route('marketplace.show', $photo) }}" class="text-sm font-bold text-black mb-1 line-clamp-2 leading-tight hover:underline">
                            {{ $photo->title ?: ($photo->event->nama_event ?? 'Foto Jepret') }}
                        </a>

                        <p class="text-xs text-gray-500 font-medium mb-3">
                            {{ $photo->event->lokasi ?? 'Lokasi belum diisi' }} &bull; {{ optional($photo->taken_at ?? $photo->created_at)->format('d M Y') }}
                        </p>

                        <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                            <p class="text-base font-black text-black">Rp {{ number_format($photo->harga, 0, ',', '.') }}</p>

                            @auth
                                @if(auth()->user()->role === 'pembeli')
                                    <form method="POST" action="{{ route('cart.store') }}">
                                        @csrf
                                        <input type="hidden" name="photo_id" value="{{ $photo->id }}">
                                        <button class="bg-gray-100 hover:bg-black hover:text-white text-black text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                            + Keranjang
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                    Login
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-lg font-bold text-gray-500">Belum ada foto yang tersedia.</p>
                    <p class="text-sm text-gray-400 mt-1">Coba ubah filter atau kembali lagi setelah fotografer mempublikasikan foto.</p>
                </div>
            @endforelse
        </div>

        <div class="pb-8">
            {{ $photos->withQueryString()->links() }}
        </div>
    </div>

</body>
</html>
