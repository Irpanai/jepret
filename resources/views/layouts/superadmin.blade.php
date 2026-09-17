<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/jepret.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JEPRET') }} - Superadmin Center</title>

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
                        <span class="text-white font-black text-sm">S</span>
                    </div>
                    <span x-show="sidebarOpen" class="font-black text-sm tracking-tighter text-black uppercase whitespace-nowrap">JEPRET ADMIN</span>
                </a>
            </div>

            <!-- Navigation Menu -->
            <div class="flex-1 overflow-y-auto py-5 px-3 overflow-x-hidden">
                <div x-show="sidebarOpen" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">OPS CENTER</div>
                
                <nav class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('superadmin.dashboard') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Command Center' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('superadmin.dashboard') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Command Center</span>
                    </a>
                    
                    <!-- Verifikasi & Kepatuhan -->
                    <a href="{{ route('superadmin.compliance') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('superadmin.compliance') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Antrean Kepatuhan' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('superadmin.compliance') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap flex-1">Antrean Kepatuhan</span>
                        <span x-show="sidebarOpen" class="bg-gray-200 text-black text-[10px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('superadmin.compliance') ? 'bg-white text-black' : '' }}">3</span>
                    </a>

                    <!-- Ledger Transaksi -->
                    <a href="{{ route('superadmin.orders') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('superadmin.orders') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Ledger Transaksi' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('superadmin.orders') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Ledger Transaksi</span>
                    </a>
                    
                    <!-- Pencairan Dana -->
                    <a href="{{ route('superadmin.earnings') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('superadmin.earnings') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Pencairan Kreator' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('superadmin.earnings') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Pencairan Kreator</span>
                    </a>
                    
                    <!-- Storage & Infrastruktur -->
                    <a href="{{ route('superadmin.storage') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('superadmin.storage') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}" :title="!sidebarOpen ? 'Infrastruktur Cloud' : ''">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('superadmin.storage') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Infrastruktur Cloud</span>
                    </a>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div x-show="sidebarOpen" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">SYSTEM</div>
                        <a href="{{ route('superadmin.settings') }}" :class="sidebarOpen ? 'px-3 justify-start' : 'justify-center'" class="flex items-center gap-3 py-2.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('superadmin.settings') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}" :title="!sidebarOpen ? 'Pengaturan Platform' : ''">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('superadmin.settings') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Pengaturan Platform</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- User Profile Bottom -->
            <div class="p-4 border-t border-gray-100 shrink-0 bg-gray-50/50 overflow-hidden">
                <div class="flex items-center" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center shrink-0 text-white font-bold text-xs uppercase" :title="!sidebarOpen ? 'Superadmin' : ''">
                            SA
                        </div>
                        <div x-show="sidebarOpen">
                            <p class="text-sm font-bold text-gray-900 truncate max-w-[120px]">{{ Auth::user()->name ?? 'Superadmin' }}</p>
                            <p class="text-[10px] font-medium text-red-500 flex items-center gap-1">
                                <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                System Root
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
                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]"></div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest hidden sm:inline">GLOBAL CLUSTER OVERVIEW</span>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-[#F9FAFB] w-full p-4 sm:px-6 lg:px-10 lg:py-8">
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
