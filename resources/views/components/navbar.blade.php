@php
    $isPublicUser = ! auth()->check() || auth()->user()->role === 'pembeli';
    $cartCount = $isPublicUser ? count(session('cart', [])) : 0;
@endphp

<nav class="sticky top-0 z-50 border-b border-public-line bg-white/95">
    <div class="public-container">
        <div class="flex min-h-16 items-center justify-between gap-5">
            <a href="{{ route('landing') }}" class="flex items-center gap-3 text-sm font-extrabold uppercase tracking-normal text-public-ink" aria-label="JepretCFD home">
                <img src="{{ asset('images/jepret.png') }}" alt="" class="h-11 w-11 shrink-0 object-contain">
                JepretCFD
            </a>

            @if($isPublicUser)
                <div class="hidden items-center gap-7 text-xs font-extrabold uppercase text-public-muted md:flex">
                    <a href="{{ url('/#tentang') }}" class="hover:text-public-ink">Tentang</a>
                    <a href="{{ route('galeri') }}" class="{{ request()->routeIs('galeri') || request()->routeIs('marketplace.*') ? 'text-public-ink' : 'hover:text-public-ink' }}">Galeri</a>
                    <a href="{{ route('photographers.index') }}" class="{{ request()->routeIs('photographers.*') ? 'text-public-ink' : 'hover:text-public-ink' }}">Photographers</a>
                    <a href="{{ url('/#pricing') }}" class="hover:text-public-ink">Pricing</a>
                </div>
            @endif

            <div class="hidden items-center gap-3 md:flex">
                @auth
                    @if(auth()->user()->role === 'pembeli')
                        <a href="{{ route('cart.index') }}" class="relative public-button public-button-secondary min-h-10 px-3 py-2">
                            Cart
                            @if($cartCount > 0)
                                <span class="ml-2 grid h-5 min-w-5 place-items-center bg-public-ink px-1 text-[10px] text-white">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = ! open" @click.outside="open = false" class="flex h-10 items-center gap-2 border border-public-line bg-white px-2 text-xs font-extrabold uppercase" aria-haspopup="menu" :aria-expanded="open.toString()">
                                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=050505&color=fff' }}" alt="" class="h-6 w-6 object-cover">
                                Profile
                            </button>
                            <div x-cloak x-show="open" class="absolute right-0 mt-2 w-56 border border-public-line bg-white p-2 shadow-sm" role="menu">
                                <div class="border-b border-public-line px-3 py-3">
                                    <p class="truncate text-sm font-bold text-public-ink">{{ Auth::user()->name }}</p>
                                    <p class="truncate text-xs text-public-muted">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('purchases.index') }}" class="block px-3 py-2 text-sm font-bold hover:bg-public-bone" role="menuitem">Pembelian Saya</a>
                                <a href="{{ route('register', ['role' => 'fotografer']) }}" class="block px-3 py-2 text-sm font-bold hover:bg-public-bone" role="menuitem">Daftar Photographer</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 text-left text-sm font-bold text-red-700 hover:bg-public-bone" role="menuitem">Log out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('dashboard') }}" class="public-button min-h-10 px-4 py-2">Dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="public-button public-button-secondary min-h-10 px-4 py-2">Masuk</a>
                    <a href="{{ route('register') }}" class="public-button min-h-10 px-4 py-2">Daftar</a>
                @endauth
            </div>

            <button type="button" class="grid h-10 w-10 place-items-center border border-public-line md:hidden" @click="mobileMenuOpen = ! mobileMenuOpen" aria-label="Toggle navigation" :aria-expanded="mobileMenuOpen.toString()">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"></path></svg>
            </button>
        </div>

        <div x-cloak x-show="mobileMenuOpen" class="border-t border-public-line py-4 md:hidden">
            <div class="grid gap-2 text-sm font-extrabold uppercase">
                @if($isPublicUser)
                    <a href="{{ url('/#tentang') }}" class="py-2">Tentang</a>
                    <a href="{{ route('galeri') }}" class="py-2">Galeri</a>
                    <a href="{{ route('photographers.index') }}" class="py-2">Photographers</a>
                    <a href="{{ url('/#pricing') }}" class="py-2">Pricing</a>
                @endif

                @auth
                    @if(auth()->user()->role === 'pembeli')
                        <a href="{{ route('cart.index') }}" class="py-2">Cart @if($cartCount > 0) ({{ $cartCount }}) @endif</a>
                        <a href="{{ route('purchases.index') }}" class="py-2">Pembelian Saya</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="py-2">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="py-2 text-left text-red-700">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="py-2">Masuk</a>
                    <a href="{{ route('register') }}" class="py-2">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
