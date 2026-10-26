<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/jepret.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Jepret') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@600;700;800;900&family=Manrope:wght@400;500;600;700;800&family=Newsreader:ital,opsz,wght@0,6..72,400..700;1,6..72,400..700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-900 text-gray-100 selection:bg-accent selection:text-white"
    x-data="{ sidebarOpen: false }">
    <div class="flex h-dvh overflow-hidden bg-gray-900">
        <!-- Sidebar Navigation -->
        @include('layouts.navigation')

        <!-- Content Wrapper -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            <!-- Mobile Top Bar -->
            <div
                class="lg:hidden flex items-center justify-between bg-gray-800/90 backdrop-blur-md px-4 py-3 border-b border-gray-700/50 sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button type="button" @click="sidebarOpen = true"
                        aria-label="Buka menu navigasi"
                        :aria-expanded="sidebarOpen.toString()"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center text-gray-400 hover:text-white focus:outline-none transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-8 w-auto fill-current text-accent" />
                        <span class="font-extrabold text-lg tracking-tight text-white">Jepret</span>
                    </a>
                </div>
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gray-800/50 backdrop-blur-sm border-b border-gray-700/50">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="text-xl font-semibold text-gray-100 leading-tight">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="w-full grow">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>