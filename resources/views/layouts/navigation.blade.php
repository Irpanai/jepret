<!-- Sidebar Backdrop for Mobile -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100" 
     x-transition:leave="transition-opacity ease-linear duration-300" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0" 
     class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-40 lg:hidden" 
     @click="sidebarOpen = false"
     style="display: none;"></div>

<!-- Sidebar Navigation -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 border-r border-gray-700/50 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">
    
    <!-- Sidebar Header (Logo) -->
    <div class="flex items-center justify-center h-16 border-b border-gray-700/50 px-6 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 w-full">
            <x-application-logo class="block h-8 w-auto fill-current text-accent" />
            <span class="font-extrabold text-xl tracking-tight text-white">Jepret</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors w-full {{ request()->routeIs('dashboard') ? 'bg-accent/10 text-accent' : 'text-gray-300 hover:text-white hover:bg-gray-700/50' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            {{ __('Dashboard') }}
        </a>

        @if(auth()->user()->role === 'pembeli')
        <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors w-full {{ request()->routeIs('purchases.*') ? 'bg-accent/10 text-accent' : 'text-gray-300 hover:text-white hover:bg-gray-700/50' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            {{ __('My Library') }}
        </a>
        @endif

        @if(auth()->user()->role === 'fotografer')
        <a href="{{ route('fotografer.events.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors w-full {{ request()->routeIs('fotografer.events.*') ? 'bg-accent/10 text-accent' : 'text-gray-300 hover:text-white hover:bg-gray-700/50' }}">
            <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            {{ __('Events') }}
        </a>
        @endif
    </nav>

    <!-- Sidebar Footer (Profile & Logout) -->
    <div class="border-t border-gray-700/50 p-4 shrink-0">
        <x-dropdown align="top" width="56">
            <x-slot name="trigger">
                <button class="flex items-center w-full gap-3 px-3 py-2 text-sm font-medium text-gray-300 rounded-lg hover:text-white hover:bg-gray-700/50 transition-colors focus:outline-none">
                    <div class="flex-1 text-left truncate">
                        <div class="text-white">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</div>
                    </div>
                    <svg class="w-4 h-4 opacity-50 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')" class="hover:bg-gray-100 dark:hover:bg-gray-800">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</aside>
