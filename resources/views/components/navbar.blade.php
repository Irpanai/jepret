@php
    $cartCount = auth()->check() ? count(session('cart', [])) : 0;
    $userRole = auth()->user()?->role;
@endphp

<nav class="sticky top-0 z-50 border-b border-public-line bg-white/95" x-data="{ cartCount: {{ $cartCount }} }" @cart:updated.window="cartCount = $event.detail.count">
    <div class="public-container">
        <div class="flex min-h-16 items-center justify-between gap-5">
            <a href="{{ route('landing') }}" class="flex items-center gap-3 text-sm font-extrabold uppercase tracking-normal text-public-ink" aria-label="JepretCFD home">
                <img src="{{ asset('images/jepret.png') }}" alt="" class="h-11 w-11 shrink-0 object-contain">
                JepretCFD
            </a>

            <div class="hidden items-center gap-7 text-xs font-extrabold uppercase text-public-muted md:flex">
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-public-ink' : 'hover:text-public-ink' }}">Tentang</a>
                <a href="{{ route('galeri') }}" class="{{ request()->routeIs('galeri') || request()->routeIs('marketplace.*') ? 'text-public-ink' : 'hover:text-public-ink' }}">Galeri</a>
                <a href="{{ route('photographers.index') }}" class="{{ request()->routeIs('photographers.*') ? 'text-public-ink' : 'hover:text-public-ink' }}">Photographers</a>
                <a href="{{ route('pricing') }}" class="{{ request()->routeIs('pricing') ? 'text-public-ink' : 'hover:text-public-ink' }}">Pricing</a>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                @auth
                    <a href="{{ route('cart.index') }}" class="group relative grid h-10 w-12 place-items-center border border-public-line bg-white text-public-ink transition-colors hover:bg-public-ink hover:text-white" title="Keranjang" :aria-label="`Keranjang belanja${cartCount > 0 ? ' berisi '+cartCount+' item' : ''}`">
                        <svg class="h-6 w-6 transition-transform duration-200 group-hover:-rotate-3 group-hover:scale-105" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3.5 5.5h3.2l2.5 14.2a2.4 2.4 0 0 0 2.4 2h11.8a2.4 2.4 0 0 0 2.3-1.8L28 10H8.1"/>
                            <path d="M11 14h14.8M12 18h12.8" opacity=".45"/>
                            <circle cx="12.5" cy="26.2" r="1.7"/>
                            <circle cx="23.5" cy="26.2" r="1.7"/>
                        </svg>
                        <span x-cloak x-show="cartCount > 0" x-text="cartCount" class="absolute -right-2 -top-2 grid h-5 min-w-5 place-items-center rounded-full bg-public-ink px-1 text-[10px] font-extrabold text-white ring-2 ring-white group-hover:bg-white group-hover:text-public-ink"></span>
                    </a>

                    {{-- Profile dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = ! open" @click.outside="open = false" class="flex h-10 items-center gap-2 border border-public-line bg-white px-2 text-xs font-extrabold uppercase" aria-haspopup="menu" :aria-expanded="open.toString()">
                            <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="" class="h-6 w-6 object-cover rounded-full">
                            Profile
                        </button>
                        <div x-cloak x-show="open" class="absolute right-0 mt-2 w-56 border border-public-line bg-white p-2 shadow-sm" role="menu">
                            <div class="border-b border-public-line px-3 py-3">
                                <p class="truncate text-sm font-bold text-public-ink">{{ Auth::user()->name }}</p>
                                <p class="truncate text-xs text-public-muted">{{ Auth::user()->email }}</p>
                                <p class="mt-1 text-[10px] font-bold uppercase text-public-muted">{{ ucfirst($userRole) }}</p>
                            </div>

                            @if($userRole === 'pembeli')
                                <a href="{{ route('purchases.index') }}" class="block px-3 py-2 text-sm font-bold hover:bg-public-bone" role="menuitem">Pembelian Saya</a>
                                <form method="POST" action="{{ route('logout.register') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 text-left text-sm font-bold hover:bg-public-bone" role="menuitem">Daftar Photographer</button>
                                </form>
                            @elseif($userRole === 'fotografer')
                                <a href="{{ route('fotografer.dashboard') }}" class="block px-3 py-2 text-sm font-bold hover:bg-public-bone" role="menuitem">Halaman Saya</a>
                                <a href="{{ route('purchases.index') }}" class="block px-3 py-2 text-sm font-bold hover:bg-public-bone" role="menuitem">Pembelian Saya</a>
                            @elseif($userRole === 'superadmin')
                                <a href="{{ route('superadmin.dashboard') }}" class="block px-3 py-2 text-sm font-bold hover:bg-public-bone" role="menuitem">Halaman Saya</a>
                                <a href="{{ route('purchases.index') }}" class="block px-3 py-2 text-sm font-bold hover:bg-public-bone" role="menuitem">Pembelian Saya</a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 text-left text-sm font-bold text-red-700 hover:bg-public-bone" role="menuitem">Log out</button>
                            </form>
                        </div>
                    </div>
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
                <a href="{{ route('about') }}" class="py-2 {{ request()->routeIs('about') ? 'text-public-ink' : '' }}">Tentang</a>
                <a href="{{ route('galeri') }}" class="py-2 {{ request()->routeIs('galeri') || request()->routeIs('marketplace.*') ? 'text-public-ink' : '' }}">Galeri</a>
                <a href="{{ route('photographers.index') }}" class="py-2 {{ request()->routeIs('photographers.*') ? 'text-public-ink' : '' }}">Photographers</a>
                <a href="{{ route('pricing') }}" class="py-2 {{ request()->routeIs('pricing') ? 'text-public-ink' : '' }}">Pricing</a>

                @auth
                    <a href="{{ route('cart.index') }}" class="flex items-center gap-3 py-2">
                        <svg class="h-6 w-6" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.5 5.5h3.2l2.5 14.2a2.4 2.4 0 0 0 2.4 2h11.8a2.4 2.4 0 0 0 2.3-1.8L28 10H8.1"/><path d="M11 14h14.8M12 18h12.8" opacity=".45"/><circle cx="12.5" cy="26.2" r="1.7"/><circle cx="23.5" cy="26.2" r="1.7"/></svg>
                        <span>Keranjang <span x-cloak x-show="cartCount > 0">(<span x-text="cartCount"></span>)</span></span>
                    </a>
                    <a href="{{ route('purchases.index') }}" class="py-2">Pembelian Saya</a>
                    @if($userRole === 'fotografer')
                        <a href="{{ route('fotografer.dashboard') }}" class="py-2">Halaman Saya</a>
                    @elseif($userRole === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}" class="py-2">Halaman Saya</a>
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
