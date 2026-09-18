<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/jepret.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JEPRET') }} - Creator Center</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-[#F9FAFB]" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @resize.window="sidebarOpen = window.innerWidth >= 1024">
    
    <div class="flex h-screen overflow-hidden bg-[#F9FAFB]">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0 w-[260px]' : '-translate-x-full w-[260px] lg:translate-x-0 lg:w-[80px]'" 
               class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 flex flex-col transition-all duration-300 ease-in-out lg:static shadow-sm shrink-0">
            
            <!-- Logo -->
            <div class="h-16 flex items-center border-b border-gray-200 shrink-0 overflow-hidden" :class="sidebarOpen ? 'px-6 justify-start' : 'px-0 justify-center'">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-black rounded-full flex items-center justify-center shrink-0">
                        <span class="text-white font-black text-sm">J</span>
                    </div>
                    <span x-show="sidebarOpen" class="font-black text-sm tracking-tighter text-black uppercase whitespace-nowrap">JEPRET</span>
                </a>
            </div>

            <!-- Navigation Menu -->
            <div class="flex-1 overflow-y-auto py-5 px-3 overflow-x-hidden">
                <div x-show="sidebarOpen" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">RUANG KERJA</div>
                
                <nav class="space-y-1">
                    @php
                        $rolePrefix = Auth::user()->role === 'superadmin' ? 'superadmin.' : 'fotografer.';
                        $isDashboard = request()->routeIs($rolePrefix . 'dashboard');
                        $isPhotos = request()->routeIs($rolePrefix . 'photos.*') || request()->routeIs($rolePrefix . 'photos');
                        $isCameras = request()->routeIs('fotografer.cameras.*');
                    @endphp
                    
                    <!-- Overview -->
                    <a href="{{ route($rolePrefix . 'dashboard') ?? '#' }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ $isDashboard ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Ringkasan' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isDashboard ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Ringkasan</span>
                    </a>

                    @if(Auth::user()->role === 'fotografer')
                    <a href="{{ route('fotografer.cameras.index') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ $isCameras ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Kamera' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isCameras ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.5 4h-5L7 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2h-3l-2.5-3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16a3 3 0 100-6 3 3 0 000 6z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Kamera</span>
                    </a>
                    @endif
                    
                    <!-- Photos -->
                    <a href="{{ Auth::user()->role === 'superadmin' ? route('superadmin.photos') : route('fotografer.photos.index') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ $isPhotos ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Foto' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ $isPhotos ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Foto</span>
                    </a>
                    

                    <!-- Orders & Transactions -->
                    <a href="{{ route($rolePrefix . 'orders') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs($rolePrefix . 'orders') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Pesanan & Transaksi' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs($rolePrefix . 'orders') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Pesanan & Transaksi</span>
                    </a>
                    
                    
                    <!-- Storage Usage -->
                    <a href="{{ route($rolePrefix . 'storage') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs($rolePrefix . 'storage') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Penggunaan Storage' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs($rolePrefix . 'storage') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Penggunaan Storage</span>
                    </a>
                    
                    <!-- Profile & Portfolio -->
                    <a href="{{ route($rolePrefix . 'portfolio') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs($rolePrefix . 'portfolio') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Profil & Portofolio' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs($rolePrefix . 'portfolio') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Profil & Portofolio</span>
                    </a>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div x-show="sidebarOpen" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">PENGATURAN</div>
                        <a href="{{ route('profile.edit') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('profile.edit') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}" :title="!sidebarOpen ? 'Pengaturan Akun' : ''">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Pengaturan Akun</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- User Profile Bottom -->
            <div class="p-4 border-t border-gray-100 shrink-0 bg-gray-50/50 overflow-hidden">
                <div class="flex items-center" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-black flex items-center justify-center shrink-0 text-white font-bold text-xs uppercase" :title="!sidebarOpen ? '{{ Auth::user()->name ?? 'Dwi Visual' }}' : ''">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div x-show="sidebarOpen">
                            <p class="text-sm font-bold text-gray-900 truncate max-w-[120px]">{{ Auth::user()->name ?? 'Dwi Visual' }}</p>
                            <p class="text-[10px] font-medium text-gray-500 flex items-center gap-1">
                                <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ Auth::user()->verificationState() === 'approved' ? 'Terverifikasi' : (Auth::user()->verificationState() === 'rejected' ? 'Ditolak' : 'Menunggu verifikasi') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2" x-show="sidebarOpen">
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="p-1.5 hover:text-black hover:bg-gray-200 text-gray-400 rounded-lg transition-colors" title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>


        <!-- Main Workspace -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-white w-full">
            
            <!-- Topbar -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 h-16 shrink-0">
                <div class="flex items-center gap-4 flex-1">
                    <!-- Menu toggle button -->
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-900 focus:outline-none p-1.5 rounded-md hover:bg-gray-100 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                        <span class="text-xs font-semibold text-gray-500 hidden sm:inline">Creator Center</span>
                    </div>
                    
                    <div class="relative max-w-[320px] lg:max-w-md w-full ml-2 lg:ml-4 hidden md:block">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" class="block w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-black focus:border-black sm:text-sm transition text-gray-900 font-medium" placeholder="Search catalog, tags, orders (⌘K)...">
                    </div>
                </div>

                <!-- Right actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('landing') }}" class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors" title="View public site">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span class="hidden sm:inline">View Site</span>
                    </a>
                    <button class="flex items-center gap-2 p-2 text-gray-600 hover:text-black hover:bg-gray-100 rounded-lg transition-colors relative" title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="workspace flex-1 overflow-y-auto bg-[#F9FAFB] w-full p-4 sm:px-6 lg:px-10 lg:py-8">
                <div class="w-full mx-auto">
                    {{ $slot }}
                </div>
            </main>
            
        </div>
        
        <!-- Sidebar Mobile Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-linear duration-300" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40 lg:hidden" 
             @click="sidebarOpen = false"
             style="display: none;"></div>
             
    </div>
</body>
</html>
