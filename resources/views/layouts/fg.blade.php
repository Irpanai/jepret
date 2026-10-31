<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/jepret.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JEPRET') }} - Creator Center</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@600;700;800;900&family=Manrope:wght@400;500;600;700;800&family=Newsreader:ital,opsz,wght@0,6..72,400..700;1,6..72,400..700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-slate-50"
    x-data="{ sidebarOpen: window.innerWidth >= 1024, wasDesktop: window.innerWidth >= 1024 }"
    @resize.window.debounce.150ms="const isDesktop = window.innerWidth >= 1024; if (isDesktop !== wasDesktop) { wasDesktop = isDesktop; sidebarOpen = isDesktop; }">
    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>

    <div class="flex h-dvh overflow-hidden bg-slate-50">
        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0 w-[260px]' : '-translate-x-full w-[260px] lg:translate-x-0 lg:w-[80px]'"
            class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 flex flex-col transition-transform lg:transition-[width,transform] duration-300 ease-in-out lg:static shadow-sm shrink-0">

            <!-- Logo -->
            <div class="h-16 flex items-center border-b border-gray-200 shrink-0 overflow-hidden"
                :class="sidebarOpen ? 'px-6 justify-start' : 'px-0 justify-center'">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-black rounded-full flex items-center justify-center shrink-0">
                        <span class="text-white font-black text-sm">J</span>
                    </div>
                    <span x-show="sidebarOpen"
                        class="font-black text-sm tracking-tighter text-black uppercase whitespace-nowrap">JEPRET</span>
                </a>
            </div>

            <!-- Navigation Menu -->
            <div class="flex-1 overflow-y-auto py-5 px-3 overflow-x-hidden">
                <div x-show="sidebarOpen"
                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">RUANG KERJA</div>

                <nav class="space-y-1">
                    @php
                        $rolePrefix = Auth::user()->role === 'superadmin' ? 'superadmin.' : 'fotografer.';
                        $isDashboard = request()->routeIs($rolePrefix . 'dashboard');
                        $isPhotos = request()->routeIs($rolePrefix . 'photos.*') || request()->routeIs($rolePrefix . 'photos');
                        $isCameras = request()->routeIs('fotografer.cameras.*');
                    @endphp

                    <!-- Overview -->
                    <a href="{{ route($rolePrefix . 'dashboard') ?? '#' }}"
                        :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'"
                        class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ $isDashboard ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}"
                        :title="!sidebarOpen ? 'Ringkasan' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isDashboard ? 'text-white' : 'text-gray-400' }}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Ringkasan</span>
                    </a>

                    @if(Auth::user()->role === 'fotografer')
                        <a href="{{ route('fotografer.cameras.index') }}"
                            :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'"
                            class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ $isCameras ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}"
                            :title="!sidebarOpen ? 'Kamera' : ''">
                            <svg class="w-5 h-5 shrink-0 {{ $isCameras ? 'text-white' : 'text-gray-400' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.5 4h-5L7 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2h-3l-2.5-3z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 16a3 3 0 100-6 3 3 0 000 6z"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Kamera</span>
                        </a>
                    @endif

                    <!-- Photos -->
                    <a href="{{ Auth::user()->role === 'superadmin' ? route('superadmin.photos') : route('fotografer.photos.index') }}"
                        :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'"
                        class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ $isPhotos ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}"
                        :title="!sidebarOpen ? 'Foto' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isPhotos ? 'text-white' : 'text-gray-400' }}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Foto</span>
                    </a>


                    <!-- Orders & Transactions -->
                    <a href="{{ route($rolePrefix . 'orders') }}"
                        :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'"
                        class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs($rolePrefix . 'orders') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}"
                        :title="!sidebarOpen ? 'Pesanan & Transaksi' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs($rolePrefix . 'orders') ? 'text-white' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Pesanan & Transaksi</span>
                    </a>


                    <!-- Storage Usage -->
                    <a href="{{ route($rolePrefix . 'storage') }}"
                        :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'"
                        class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs($rolePrefix . 'storage') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}"
                        :title="!sidebarOpen ? 'Penggunaan Storage' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs($rolePrefix . 'storage') ? 'text-white' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z">
                            </path>
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Penggunaan Storage</span>
                    </a>

                    <!-- Profile & Portfolio -->
                    <a href="{{ route($rolePrefix . 'portfolio') }}"
                        :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'"
                        class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs($rolePrefix . 'portfolio') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}"
                        :title="!sidebarOpen ? 'Profil & Portofolio' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs($rolePrefix . 'portfolio') ? 'text-white' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Profil & Portofolio</span>
                    </a>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div x-show="sidebarOpen"
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">PENGATURAN
                        </div>
                        <a href="{{ route('profile.edit') }}"
                            :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'"
                            class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('profile.edit') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="!sidebarOpen ? 'Pengaturan Akun' : ''">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-gray-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Pengaturan Akun</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- User Profile Bottom -->
            <div class="p-4 border-t border-gray-100 shrink-0 bg-gray-50/50 overflow-hidden">
                <div class="flex items-center" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-black flex items-center justify-center shrink-0 text-white font-bold text-xs uppercase"
                            :title="!sidebarOpen ? '{{ Auth::user()->name ?? 'Dwi Visual' }}' : ''">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div x-show="sidebarOpen">
                            <p class="text-sm font-bold text-gray-900 truncate max-w-[120px]">
                                {{ Auth::user()->name ?? 'Dwi Visual' }}
                            </p>
                            <p class="text-[10px] font-medium text-gray-500 flex items-center gap-1">
                                <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ Auth::user()->verificationState() === 'approved' ? 'Terverifikasi' : (Auth::user()->verificationState() === 'rejected' ? 'Ditolak' : 'Menunggu verifikasi') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2" x-show="sidebarOpen">
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="p-1.5 hover:text-black hover:bg-gray-200 text-gray-400 rounded-lg transition-colors"
                                title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>


        <!-- Main Workspace -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-white w-full">

            <!-- Topbar -->
            <header
                class="bg-white border-b border-gray-200 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 h-16 shrink-0">
                <div class="flex items-center gap-4 flex-1">
                    <!-- Menu toggle button -->
                    <button type="button" @click="sidebarOpen = !sidebarOpen"
                        :aria-expanded="sidebarOpen.toString()"
                        aria-label="Toggle navigasi sidebar"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-md p-2.5 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 focus:outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                        <span class="text-xs font-semibold text-gray-500 hidden sm:inline">Creator Center</span>
                    </div>

                </div>

                <!-- Right actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('landing') }}"
                        class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors"
                        title="View public site">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span class="hidden sm:inline">View Site</span>
                    </a>
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{
                        open: false,
                        notifications: @js($notifications ?? []),
                        readIds: JSON.parse(localStorage.getItem('jepret_read_notifs') || '[]'),
                        isRead(id) {
                            return this.readIds.includes(id);
                        },
                        get unreadCount() {
                            return this.notifications.filter(n => !this.isRead(n.id)).length;
                        },
                        markAsRead(id) {
                            if (!this.readIds.includes(id)) {
                                this.readIds.push(id);
                                localStorage.setItem('jepret_read_notifs', JSON.stringify(this.readIds));
                            }
                        },
                        markAllRead() {
                            this.readIds = this.notifications.map(n => n.id);
                            localStorage.setItem('jepret_read_notifs', JSON.stringify(this.readIds));
                        }
                    }" @click.away="open = false">
                        <button type="button" @click="open = !open"
                            :aria-expanded="open.toString()"
                            class="flex items-center gap-2 p-2 text-gray-600 hover:text-black hover:bg-gray-100 rounded-lg transition-colors relative"
                            title="Notifikasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <template x-if="unreadCount > 0">
                                <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-black text-white ring-2 ring-white"
                                    x-text="unreadCount > 9 ? '9+' : unreadCount">
                                </span>
                            </template>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 top-full mt-2 w-80 sm:w-96 rounded-2xl border border-gray-200 bg-white shadow-2xl z-50 overflow-hidden">
                            
                            {{-- Header --}}
                            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 bg-gray-50/70">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-extrabold text-gray-900">Notifikasi</h3>
                                    <template x-if="unreadCount > 0">
                                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700"
                                            x-text="unreadCount + ' baru'">
                                        </span>
                                    </template>
                                </div>
                                <button type="button" @click="markAllRead()"
                                    x-show="unreadCount > 0"
                                    class="text-xs font-semibold text-gray-500 hover:text-black transition">
                                    Tandai dibaca
                                </button>
                            </div>

                            {{-- Notification Items List --}}
                            <div class="max-h-80 sm:max-h-96 overflow-y-auto divide-y divide-gray-100">
                                <template x-for="item in notifications" :key="item.id">
                                    <a :href="item.url" @click="markAsRead(item.id)"
                                        class="flex items-start gap-3 p-4 transition text-left block"
                                        :class="isRead(item.id) ? 'bg-white hover:bg-gray-50' : 'bg-slate-50/80 hover:bg-slate-100/80'">
                                        
                                        {{-- Icon by Type --}}
                                        <div class="shrink-0 mt-0.5">
                                            <template x-if="item.icon === 'sale'">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </span>
                                            </template>
                                            <template x-if="item.icon === 'withdrawal_success'">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </span>
                                            </template>
                                            <template x-if="item.icon === 'withdrawal_rejected'">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </span>
                                            </template>
                                            <template x-if="item.icon === 'withdrawal_held'">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </span>
                                            </template>
                                            <template x-if="item.icon === 'withdrawal_pending'">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </span>
                                            </template>
                                            <template x-if="item.icon === 'verification'">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                    </svg>
                                                </span>
                                            </template>
                                            <template x-if="item.icon === 'verification_rejected'">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                </span>
                                            </template>
                                            <template x-if="item.icon === 'subscription'">
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </span>
                                            </template>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1.5 mb-1">
                                                <p class="text-xs font-bold text-gray-900 truncate" x-text="item.title"></p>
                                                <span class="rounded-full border px-1.5 py-0.5 text-[10px] font-bold"
                                                    :class="item.badge_class" x-text="item.badge_label">
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed" x-text="item.message"></p>
                                            <div class="mt-1 flex items-center justify-between text-[10px] text-gray-400">
                                                <span x-text="item.time"></span>
                                                <template x-if="!isRead(item.id)">
                                                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </a>
                                </template>

                                {{-- Empty state --}}
                                <template x-if="notifications.length === 0">
                                    <div class="p-8 text-center text-gray-400">
                                        <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        <p class="mt-2 text-xs font-medium">Belum ada notifikasi baru.</p>
                                    </div>
                                </template>
                            </div>

                            {{-- Footer --}}
                            <div class="border-t border-gray-100 bg-gray-50/50 p-2.5 text-center">
                                <a href="{{ route('fotografer.orders') }}" class="text-xs font-bold text-gray-700 hover:text-black">
                                    Lihat Semua Pesanan &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main id="main-content"
                class="workspace flex-1 overflow-y-auto bg-slate-50 w-full p-4 sm:px-6 lg:px-10 lg:py-8" tabindex="-1">
                <div class="w-full mx-auto">
                    {{ $slot }}
                </div>
            </main>

        </div>

        <!-- Sidebar Mobile Backdrop -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40 lg:hidden"
            @click="sidebarOpen = false" style="display: none;"></div>

    </div>
</body>

</html>