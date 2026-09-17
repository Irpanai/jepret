@php
    $cartPhotoIds = auth()->check() && auth()->user()->role === 'pembeli'
        ? array_values(session('cart', []))
        : [];

    $cartPhotos = collect();

    if ($cartPhotoIds !== []) {
        $cartPhotos = \App\Models\Photo::query()
            ->with(['event', 'fotografer'])
            ->whereIn('id', $cartPhotoIds)
            ->get()
            ->sortBy(fn ($photo) => array_search($photo->id, $cartPhotoIds, true));
    }

    $cartTotal = $cartPhotos->sum('harga');
@endphp

<nav class="border-b border-gray-100 bg-white sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-2 cursor-pointer shrink-0">
                <span class="font-black text-xl tracking-tighter text-black flex items-center gap-1">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"></path><circle cx="12" cy="13" r="3"></circle></svg>
                    JEPRET CFD
                </span>
            </a>

            <!-- Center Links -->
            <div class="hidden md:flex items-center space-x-6 ml-10">
                @if(!auth()->check() || auth()->user()->role === 'pembeli')
                    @if(!auth()->check())
                        <a href="{{ url('/#tentang') }}" class="text-base font-semibold text-gray-500 hover:text-black transition">Tentang</a>
                    @else
                        <a href="{{ url('/#tentang') }}" class="text-base font-semibold text-gray-500 hover:text-black transition">Tentang</a>
                        <a href="{{ route('galeri') }}" class="text-base font-semibold {{ request()->routeIs('galeri') ? 'text-black' : 'text-gray-500 hover:text-black' }} transition">Galeri</a>
                        <a href="{{ route('photographers.index') }}" class="text-base font-semibold {{ request()->routeIs('photographers.*') ? 'text-black' : 'text-gray-500 hover:text-black' }} transition">Photographers</a>
                    @endif
                    <a href="{{ url('/#pricing') }}" class="text-base font-semibold text-gray-500 hover:text-black transition">Pricing</a>
                @endif
            </div>

            <!-- Right Actions -->
            <div class="flex items-center space-x-4 ml-auto">
                
                @auth
                    @if(auth()->user()->role === 'pembeli')
                        <!-- Cart Icon -->
                        <div class="flex items-center relative" x-data="{ cartOpen: false }">
                            <button @click="cartOpen = !cartOpen" @click.away="cartOpen = false" class="text-gray-500 hover:text-black transition relative p-2" title="Keranjang">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                @if($cartPhotos->isNotEmpty())
                                    <span class="absolute -top-0.5 -right-0.5 min-w-5 h-5 px-1 bg-red-500 rounded-full ring-2 ring-white text-white text-[10px] font-black flex items-center justify-center">{{ $cartPhotos->count() }}</span>
                                @endif
                            </button>

                            <!-- Cart Dropdown -->
                            <div x-show="cartOpen"
                                 x-transition.opacity.duration.200ms
                                 style="display: none;"
                                 class="absolute top-full right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50">
                                
                                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                    <span class="font-black text-sm text-gray-900">Keranjang ({{ $cartPhotos->count() }})</span>
                                    <span class="text-[10px] font-bold text-gray-500">Rp{{ number_format($cartTotal, 0, ',', '.') }}</span>
                                </div>

                                <div class="p-3 space-y-3 max-h-64 overflow-y-auto">
                                    @forelse($cartPhotos as $cartPhoto)
                                        <div class="flex gap-3">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-xs font-bold text-gray-900 truncate">{{ $cartPhoto->event->nama_event ?? $cartPhoto->title ?? 'Foto Jepret' }}</h4>
                                                <p class="text-[9px] text-gray-500 mb-1">{{ $cartPhoto->fotografer->name ?? 'Photographer' }}</p>
                                                <div class="font-black text-xs text-gray-900 flex items-center gap-1">
                                                    Rp{{ number_format($cartPhoto->harga, 0, ',', '.') }}
                                                    <form method="POST" action="{{ route('cart.destroy') }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="photo_id" value="{{ $cartPhoto->id }}">
                                                        <button type="submit" class="text-red-500 hover:underline font-bold">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="py-6 text-center">
                                            <p class="text-xs font-bold text-gray-500">Keranjang masih kosong.</p>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="p-3 border-t border-gray-100 bg-white">
                                    <a href="{{ route('cart.index') }}" class="block w-full bg-black text-white text-center text-sm font-bold py-3 rounded-lg hover:bg-gray-800 transition-colors shadow-sm">
                                        Lanjut ke Checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Profile Dropdown -->
                    <div class="relative ml-2" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-200 hover:border-black transition focus:outline-none">
                            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=000&color=fff' }}" alt="Avatar" class="w-full h-full object-cover">
                        </button>
                        
                        <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            
                            @if(Auth::user()->role === 'pembeli')
                                <a href="{{ route('purchases.index') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-black">Pembelian Saya</a>
                                <a href="{{ route('register', ['role' => 'fotografer']) }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-black">Daftar Sebagai Photographer</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-black">Dashboard</a>
                            @endif
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 mt-1 border-t border-gray-100">
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Guest -->
                    <a href="{{ route('login') }}" class="text-base font-semibold text-gray-900 hover:text-gray-600 transition px-2">Masuk/Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
