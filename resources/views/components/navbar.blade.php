@php
    $cartCount = auth()->check() ? count(session('cart', [])) : 0;
    $userRole = auth()->user()?->role;
    $navigationLinks = [
        ['label' => 'About', 'route' => 'about', 'active' => request()->routeIs('about')],
        ['label' => 'Galeri', 'route' => 'galeri', 'active' => request()->routeIs('galeri') || request()->routeIs('marketplace.*')],
        ['label' => 'Photographers', 'route' => 'photographers.index', 'active' => request()->routeIs('photographers.*')],
        ['label' => 'Pricing', 'route' => 'pricing', 'active' => request()->routeIs('pricing')],
    ];
@endphp

<nav
    class="site-nav sticky top-0 z-50 border-b border-public-line bg-white/95 backdrop-blur-md transition-all duration-200"
    :class="{ 'nav-scrolled': scrolled }"
    aria-label="Navigasi utama"
    x-data="{ cartCount: {{ $cartCount }}, scrolled: window.scrollY > 8, mobileMenuOpen: false }"
    @scroll.window.throttle.50ms="scrolled = window.scrollY > 8"
    @keydown.escape.window="mobileMenuOpen = false"
    @cart:updated.window="cartCount = $event.detail.count ?? Math.max(0, cartCount + ($event.detail.delta ?? 0))"
>
    <div class="public-container">
        <div class="flex min-h-16 items-center justify-between gap-5">
            <a href="{{ route('landing') }}" class="flex items-center gap-3 text-sm font-extrabold uppercase tracking-normal text-public-ink" aria-label="Jepret home">
                <img src="{{ asset('images/jepret.png') }}" alt="" width="44" height="44" class="h-11 w-11 shrink-0 object-contain">
                Jepret
            </a>

            <div class="hidden items-center gap-8 text-xs font-extrabold uppercase text-public-muted md:flex">
                @foreach($navigationLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        class="nav-link {{ $link['active'] ? 'text-public-ink' : 'hover:text-public-ink' }}"
                        @if($link['active']) aria-current="page" @endif
                    >{{ $link['label'] }}</a>
                @endforeach
            </div>

            <div class="hidden items-center gap-3 md:flex">
                @auth
                    <a href="{{ route('cart.index') }}" class="group relative grid h-10 w-12 place-items-center border border-public-line bg-white text-public-ink transition-colors duration-200 hover:border-public-ink hover:bg-public-ink hover:text-white" title="Keranjang" :aria-label="`Keranjang belanja${cartCount > 0 ? ' berisi '+cartCount+' item' : ''}`">
                        <svg class="h-6 w-6 transition-transform duration-200 group-hover:-rotate-3 group-hover:scale-105" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3.5 5.5h3.2l2.5 14.2a2.4 2.4 0 0 0 2.4 2h11.8a2.4 2.4 0 0 0 2.3-1.8L28 10H8.1"/>
                            <path d="M11 14h14.8M12 18h12.8" opacity=".45"/>
                            <circle cx="12.5" cy="26.2" r="1.7"/>
                            <circle cx="23.5" cy="26.2" r="1.7"/>
                        </svg>
                        <span x-cloak x-show="cartCount > 0" x-text="cartCount" class="absolute -right-2 -top-2 grid h-5 min-w-5 place-items-center rounded-full bg-public-ink px-1 text-[10px] font-extrabold text-white ring-2 ring-white group-hover:bg-white group-hover:text-public-ink"></span>
                    </a>

                    {{-- Profile dropdown --}}
                    <div class="relative" x-data="{ open: false }" @keydown.escape="open = false">
                        <button type="button" @click="open = ! open" @click.outside="open = false" class="flex h-10 items-center gap-2 border border-public-line bg-white px-2 text-xs font-extrabold uppercase transition-colors duration-200 hover:border-public-ink" aria-haspopup="menu" :aria-expanded="open.toString()">
                            <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="" width="24" height="24" class="h-6 w-6 rounded-full object-cover">
                            Profile
                            <svg class="h-3 w-3 transition-transform duration-200" :class="{ 'rotate-180': open }" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m3 4.5 3 3 3-3"/></svg>
                        </button>
                        <div
                            x-cloak
                            x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute right-0 mt-2 w-56 border border-public-line bg-white p-2 shadow-sm"
                            role="menu"
                        >
                            <div class="border-b border-public-line px-3 py-3">
                                <p class="truncate text-sm font-bold text-public-ink">{{ Auth::user()->name }}</p>
                                <p class="truncate text-xs text-public-muted">{{ Auth::user()->email }}</p>
                                <p class="mt-1 text-[10px] font-bold uppercase text-public-muted">{{ ucfirst($userRole) }}</p>
                            </div>

                            @if($userRole === 'pembeli')
                                <a href="{{ route('purchases.index') }}" class="block px-3 py-2 text-sm font-bold hover:bg-public-bone" role="menuitem">Pembelian Saya</a>
                                <a href="{{ route('register') }}" class="block px-3 py-2 text-sm font-bold hover:bg-public-bone" role="menuitem">Daftar Photographer</a>
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

            <div class="flex items-center gap-2 md:hidden">
                @auth
                    <a href="{{ route('cart.index') }}" class="relative grid h-11 w-11 place-items-center border border-public-line text-public-ink" :aria-label="`Keranjang belanja${cartCount > 0 ? ' berisi '+cartCount+' item' : ''}`">
                        <svg class="h-6 w-6" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.5 5.5h3.2l2.5 14.2a2.4 2.4 0 0 0 2.4 2h11.8a2.4 2.4 0 0 0 2.3-1.8L28 10H8.1"/><circle cx="12.5" cy="26.2" r="1.7"/><circle cx="23.5" cy="26.2" r="1.7"/></svg>
                        <span x-cloak x-show="cartCount > 0" x-text="cartCount" class="absolute -right-1.5 -top-1.5 grid h-5 min-w-5 place-items-center rounded-full bg-public-ink px-1 text-[10px] font-extrabold text-white ring-2 ring-white"></span>
                    </a>
                @endauth
                <button
                    type="button"
                    class="grid h-11 w-11 place-items-center border border-public-line text-public-ink"
                    @click="mobileMenuOpen = ! mobileMenuOpen"
                    aria-controls="mobile-navigation"
                    :aria-expanded="mobileMenuOpen.toString()"
                    :aria-label="mobileMenuOpen ? 'Tutup navigasi' : 'Buka navigasi'"
                >
                    <svg x-show="! mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"></path></svg>
                    <svg x-cloak x-show="mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"></path></svg>
                </button>
            </div>
        </div>

        <div
            id="mobile-navigation"
            x-cloak
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="max-h-[calc(100dvh-4rem)] overflow-y-auto border-t border-public-line pb-5 md:hidden"
        >
            <div class="grid text-sm font-extrabold uppercase">
                @foreach($navigationLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        @click="mobileMenuOpen = false"
                        class="flex min-h-12 items-center justify-between border-b border-public-line {{ $link['active'] ? 'text-public-ink' : 'text-public-muted' }}"
                        @if($link['active']) aria-current="page" @endif
                    >
                        {{ $link['label'] }}
                        @if($link['active'])
                            <span class="h-1.5 w-1.5 bg-public-ink" aria-hidden="true"></span>
                        @endif
                    </a>
                @endforeach

                @auth
                    <div class="mt-4 flex items-center gap-3 border-b border-public-line pb-4 normal-case">
                        <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="" width="36" height="36" class="h-9 w-9 rounded-full object-cover">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-public-ink">{{ Auth::user()->name }}</p>
                            <p class="truncate text-xs font-semibold text-public-muted">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('cart.index') }}" class="flex min-h-12 items-center border-b border-public-line">
                        <span>Keranjang <span x-cloak x-show="cartCount > 0">(<span x-text="cartCount"></span>)</span></span>
                    </a>
                    <a href="{{ route('purchases.index') }}" class="flex min-h-12 items-center border-b border-public-line">Pembelian Saya</a>
                    @if($userRole === 'pembeli')
                        <a href="{{ route('register') }}" class="flex min-h-12 items-center border-b border-public-line">Daftar Photographer</a>
                    @endif
                    @if(in_array($userRole, ['fotografer', 'superadmin'], true))
                        <a href="{{ route('dashboard') }}" class="flex min-h-12 items-center border-b border-public-line {{ request()->routeIs('fotografer.*') || request()->routeIs('superadmin.*') ? 'text-public-ink' : '' }}">Halaman Saya</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex min-h-12 w-full items-center text-left uppercase text-red-700">Log out</button>
                    </form>
                @else
                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <a href="{{ route('login') }}" class="public-button public-button-secondary">Masuk</a>
                        <a href="{{ route('register') }}" class="public-button">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
