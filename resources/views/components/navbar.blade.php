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
    x-data="{
        cartCount: {{ $cartCount }},
        scrolled: window.scrollY > 8,
        mobileMenuOpen: false,
        hoveredNav: null
    }"
    @scroll.window.throttle.50ms.passive="scrolled = window.scrollY > 8"
    @keydown.escape.window="mobileMenuOpen = false"
    @cart:updated.window="cartCount = $event.detail.count ?? Math.max(0, cartCount + ($event.detail.delta ?? 0))"
>
    <div class="public-container">
        <div
            class="flex items-center justify-between gap-5 transition-[min-height] duration-200"
            :class="scrolled ? 'min-h-14 sm:min-h-16' : 'min-h-16 sm:min-h-[4.5rem]'"
        >
            {{-- Brand Identity --}}
            <div class="flex items-center gap-4">
                <a
                    href="{{ route('landing') }}"
                    class="group flex items-center gap-3 text-sm font-extrabold uppercase tracking-tight text-public-ink"
                    aria-label="Jepret home"
                >
                    <span class="grid h-10 w-10 shrink-0 place-items-center border border-transparent transition-transform duration-300 group-hover:-rotate-3">
                        <img src="{{ asset('images/jepret.png') }}" alt="" width="40" height="40" class="h-10 w-10 object-contain">
                    </span>
                    <span class="text-base font-extrabold tracking-tight">Jepret</span>
                </a>
            </div>

            {{-- Desktop Editorial Navigation --}}
            <div
                class="hidden items-center gap-7 md:flex lg:gap-9"
                @mouseleave="hoveredNav = null"
            >
                @foreach($navigationLinks as $idx => $link)
                    <a
                        href="{{ route($link['route']) }}"
                        @mouseenter="hoveredNav = {{ $idx }}"
                        @focus="hoveredNav = {{ $idx }}"
                        @blur="hoveredNav = null"
                        class="nav-link {{ $link['active'] ? 'text-public-ink' : 'text-public-muted hover:text-public-ink' }}"
                        :class="hoveredNav !== null && hoveredNav !== {{ $idx }} ? 'opacity-55' : 'opacity-100'"
                        @if($link['active']) aria-current="page" @endif
                    >
                        <span>{{ $link['label'] }}</span>
                        @if($link['active'])
                            <span class="ml-0.5 h-1 w-1 rounded-full bg-public-ink" aria-hidden="true"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- Desktop Right Actions --}}
            <div class="hidden items-center gap-3 md:flex">
                @auth
                    {{-- Cart Trigger --}}
                    <a
                        href="{{ route('cart.index') }}"
                        class="group relative inline-flex h-10 items-center justify-center gap-2 border border-public-line bg-white px-3 text-public-ink transition-all duration-200 hover:-translate-y-0.5 hover:border-public-ink hover:bg-public-ink hover:text-white"
                        title="Keranjang"
                        :aria-label="`Keranjang belanja${cartCount > 0 ? ' berisi '+cartCount+' item' : ''}`"
                    >
                        <svg class="h-5 w-5 transition-transform duration-200 group-hover:-rotate-3 group-hover:scale-105" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3.5 5.5h3.2l2.5 14.2a2.4 2.4 0 0 0 2.4 2h11.8a2.4 2.4 0 0 0 2.3-1.8L28 10H8.1"/>
                            <path d="M11 14h14.8M12 18h12.8" opacity=".45"/>
                            <circle cx="12.5" cy="26.2" r="1.7"/>
                            <circle cx="23.5" cy="26.2" r="1.7"/>
                        </svg>
                        <span
                            x-cloak
                            x-show="cartCount > 0"
                            x-text="cartCount"
                            class="grid h-5 min-w-5 place-items-center bg-public-ink px-1.5 font-sans text-[10px] font-extrabold text-white transition-colors duration-200 group-hover:bg-white group-hover:text-public-ink"
                        ></span>
                    </a>

                    {{-- Profile Dropdown --}}
                    <div class="relative" x-data="{ open: false }" @keydown.escape="open = false">
                        <button
                            type="button"
                            @click="open = ! open"
                            @click.outside="open = false"
                            class="group flex h-10 items-center gap-2.5 border border-public-line bg-white px-3 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-all duration-200 hover:border-public-ink"
                            :class="open ? 'border-public-ink bg-public-bone' : ''"
                            aria-haspopup="menu"
                            :aria-expanded="open.toString()"
                        >
                            <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="" width="24" height="24" class="h-6 w-6 rounded-full border border-public-line object-cover">
                            <span>Profile</span>
                            <svg class="h-3 w-3 text-public-muted transition-transform duration-200 group-hover:text-public-ink" :class="{ 'rotate-180 text-public-ink': open }" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="m3 4.5 3 3 3-3"/>
                            </svg>
                        </button>

                        <div
                            x-cloak
                            x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1.5"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute right-0 mt-2 w-60 border border-public-ink bg-white p-1.5 shadow-[4px_4px_0px_0px_rgba(16,17,20,0.08)]"
                            role="menu"
                        >
                            <div class="border-b border-public-line bg-public-bone/60 px-3 py-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm font-extrabold text-public-ink">{{ Auth::user()->name }}</p>
                                    <span class="shrink-0 border border-public-line bg-white px-1.5 py-0.5 font-sans text-[9px] font-extrabold uppercase tracking-wider text-public-ink">
                                        {{ ucfirst($userRole) }}
                                    </span>
                                </div>
                                <p class="mt-0.5 truncate text-xs font-medium text-public-muted">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-1">
                                @if($userRole === 'pembeli')
                                    <a href="{{ route('purchases.index') }}" class="group/item flex items-center justify-between px-3 py-2 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-colors hover:bg-public-bone" role="menuitem">
                                        <span>Pembelian Saya</span>
                                        <span class="font-sans text-public-muted transition-transform duration-200 group-hover/item:translate-x-0.5 group-hover/item:text-public-ink" aria-hidden="true">&rarr;</span>
                                    </a>
                                    <a href="{{ route('register') }}" class="group/item flex items-center justify-between px-3 py-2 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-colors hover:bg-public-bone" role="menuitem">
                                        <span>Daftar Photographer</span>
                                        <span class="font-sans text-public-muted transition-transform duration-200 group-hover/item:translate-x-0.5 group-hover/item:text-public-ink" aria-hidden="true">&rarr;</span>
                                    </a>
                                @elseif($userRole === 'fotografer')
                                    <a href="{{ route('fotografer.dashboard') }}" class="group/item flex items-center justify-between px-3 py-2 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-colors hover:bg-public-bone" role="menuitem">
                                        <span>Halaman Saya</span>
                                        <span class="font-sans text-public-muted transition-transform duration-200 group-hover/item:translate-x-0.5 group-hover/item:text-public-ink" aria-hidden="true">&rarr;</span>
                                    </a>
                                    <a href="{{ route('purchases.index') }}" class="group/item flex items-center justify-between px-3 py-2 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-colors hover:bg-public-bone" role="menuitem">
                                        <span>Pembelian Saya</span>
                                        <span class="font-sans text-public-muted transition-transform duration-200 group-hover/item:translate-x-0.5 group-hover/item:text-public-ink" aria-hidden="true">&rarr;</span>
                                    </a>
                                @elseif($userRole === 'superadmin')
                                    <a href="{{ route('superadmin.dashboard') }}" class="group/item flex items-center justify-between px-3 py-2 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-colors hover:bg-public-bone" role="menuitem">
                                        <span>Halaman Saya</span>
                                        <span class="font-sans text-public-muted transition-transform duration-200 group-hover/item:translate-x-0.5 group-hover/item:text-public-ink" aria-hidden="true">&rarr;</span>
                                    </a>
                                    <a href="{{ route('purchases.index') }}" class="group/item flex items-center justify-between px-3 py-2 text-xs font-extrabold uppercase tracking-wider text-public-ink transition-colors hover:bg-public-bone" role="menuitem">
                                        <span>Pembelian Saya</span>
                                        <span class="font-sans text-public-muted transition-transform duration-200 group-hover/item:translate-x-0.5 group-hover/item:text-public-ink" aria-hidden="true">&rarr;</span>
                                    </a>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="border-t border-public-line pt-1">
                                @csrf
                                <button type="submit" class="flex w-full items-center justify-between px-3 py-2 text-left text-xs font-extrabold uppercase tracking-wider text-red-700 transition-colors hover:bg-red-50" role="menuitem">
                                    <span>Log out</span>
                                    <span class="font-sans text-[10px]" aria-hidden="true">&times;</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="public-button public-button-secondary min-h-10 px-4 py-2">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="public-button group min-h-10 gap-2 px-5 py-2">
                        <span>Daftar</span>
                        <span class="transition-transform duration-200 group-hover:translate-x-0.5" aria-hidden="true">&rarr;</span>
                    </a>
                @endauth
            </div>

            {{-- Mobile Top Bar Controls --}}
            <div class="flex items-center gap-2 md:hidden">
                @auth
                    <a
                        href="{{ route('cart.index') }}"
                        class="relative grid h-11 w-11 place-items-center border border-public-line bg-white text-public-ink transition-colors hover:border-public-ink"
                        :aria-label="`Keranjang belanja${cartCount > 0 ? ' berisi '+cartCount+' item' : ''}`"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3.5 5.5h3.2l2.5 14.2a2.4 2.4 0 0 0 2.4 2h11.8a2.4 2.4 0 0 0 2.3-1.8L28 10H8.1"/>
                            <circle cx="12.5" cy="26.2" r="1.7"/>
                            <circle cx="23.5" cy="26.2" r="1.7"/>
                        </svg>
                        <span
                            x-cloak
                            x-show="cartCount > 0"
                            x-text="cartCount"
                            class="absolute -right-1.5 -top-1.5 grid h-5 min-w-5 place-items-center rounded-full bg-public-ink px-1 font-sans text-[10px] font-extrabold text-white ring-2 ring-white"
                        ></span>
                    </a>
                @endauth

                <button
                    type="button"
                    class="grid h-11 w-11 place-items-center border border-public-line bg-white text-public-ink transition-colors duration-200 hover:border-public-ink"
                    :class="mobileMenuOpen ? 'border-public-ink bg-public-ink text-white' : ''"
                    @click="mobileMenuOpen = ! mobileMenuOpen"
                    aria-controls="mobile-navigation"
                    :aria-expanded="mobileMenuOpen.toString()"
                    :aria-label="mobileMenuOpen ? 'Tutup navigasi' : 'Buka navigasi'"
                >
                    <svg x-show="! mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h12" stroke-linecap="round"></path>
                    </svg>
                    <svg x-cloak x-show="mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Navigation Drawer --}}
        <div
            id="mobile-navigation"
            x-cloak
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="max-h-[calc(100dvh-4rem)] overflow-y-auto border-t border-public-line bg-white pb-6 pt-2 md:hidden"
        >
            <div class="grid text-sm font-extrabold uppercase">
                @foreach($navigationLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        @click="mobileMenuOpen = false"
                        class="group flex min-h-13 items-center justify-between border-b border-public-line py-3.5 transition-colors {{ $link['active'] ? 'text-public-ink' : 'text-public-muted hover:text-public-ink' }}"
                        @if($link['active']) aria-current="page" @endif
                    >
                        <span class="text-base tracking-tight">{{ $link['label'] }}</span>
                        @if($link['active'])
                            <span class="inline-flex items-center gap-2 font-sans text-[10px] font-bold tracking-widest text-public-ink">
                                <span>ACTIVE</span>
                                <span class="h-1.5 w-1.5 bg-public-ink" aria-hidden="true"></span>
                            </span>
                        @else
                            <span class="font-sans text-xs text-public-muted transition-transform duration-200 group-hover:translate-x-1 group-hover:text-public-ink" aria-hidden="true">&rarr;</span>
                        @endif
                    </a>
                @endforeach

                @auth
                    <div class="mt-4 flex items-center justify-between gap-3 border border-public-line bg-public-bone/60 p-3.5 normal-case">
                        <div class="flex min-w-0 items-center gap-3">
                            <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="" width="38" height="38" class="h-9 w-9 shrink-0 rounded-full border border-public-line object-cover">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-extrabold text-public-ink">{{ Auth::user()->name }}</p>
                                <p class="truncate text-xs font-medium text-public-muted">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <span class="shrink-0 border border-public-line bg-white px-2 py-0.5 font-sans text-[10px] font-extrabold uppercase tracking-wider text-public-ink">
                            {{ ucfirst($userRole) }}
                        </span>
                    </div>

                    <a href="{{ route('cart.index') }}" class="flex min-h-12 items-center justify-between border-b border-public-line text-xs tracking-wider">
                        <span>Keranjang <span x-cloak x-show="cartCount > 0">(<span x-text="cartCount"></span>)</span></span>
                        <span class="font-sans text-public-muted" aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('purchases.index') }}" class="flex min-h-12 items-center justify-between border-b border-public-line text-xs tracking-wider">
                        <span>Pembelian Saya</span>
                        <span class="font-sans text-public-muted" aria-hidden="true">&rarr;</span>
                    </a>
                    @if($userRole === 'pembeli')
                        <a href="{{ route('register') }}" class="flex min-h-12 items-center justify-between border-b border-public-line text-xs tracking-wider">
                            <span>Daftar Photographer</span>
                            <span class="font-sans text-public-muted" aria-hidden="true">&rarr;</span>
                        </a>
                    @endif
                    @if(in_array($userRole, ['fotografer', 'superadmin'], true))
                        <a href="{{ route('dashboard') }}" class="flex min-h-12 items-center justify-between border-b border-public-line text-xs tracking-wider {{ request()->routeIs('fotografer.*') || request()->routeIs('superadmin.*') ? 'text-public-ink' : '' }}">
                            <span>Halaman Saya</span>
                            <span class="font-sans text-public-muted" aria-hidden="true">&rarr;</span>
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex min-h-12 w-full items-center justify-between text-left text-xs font-extrabold uppercase tracking-wider text-red-700">
                            <span>Log out</span>
                            <span class="font-sans" aria-hidden="true">&times;</span>
                        </button>
                    </form>
                @else
                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <a href="{{ route('login') }}" class="public-button public-button-secondary">Masuk</a>
                        <a href="{{ route('register') }}" class="public-button">Daftar</a>
                    </div>
                @endauth

                <div class="mt-5 flex items-center justify-between border-t border-public-line pt-4 font-sans text-[10px] font-bold uppercase tracking-widest text-public-muted">
                    <span>Jepret Marketplace</span>
                    <span>ID &middot; {{ date('Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</nav>
