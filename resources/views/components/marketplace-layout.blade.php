@props([
    'title' => 'JepretCFD - Temukan Foto Event & Photographer',
    'description' => 'JepretCFD adalah marketplace fotografi event untuk menemukan, membeli, dan mengunduh foto original dari photographer lokal.',
    'canonical' => null,
    'noindex' => false,
    'ogImage' => null,
    'type' => 'website',
    'structuredData' => null,
])

@php
    $canonicalUrl = $canonical ?: url()->current();
    $shareImage = $ogImage ?: asset('images/login-bg.jpg');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $description }}">
        <meta name="robots" content="{{ $noindex ? 'noindex, nofollow' : 'index, follow' }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">

        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:image" content="{{ $shareImage }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:type" content="{{ $type }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $title }}">
        <meta name="twitter:description" content="{{ $description }}">
        <meta name="twitter:image" content="{{ $shareImage }}">

        <title>{{ $title }}</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800|instrument-serif:400,400i&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if($structuredData)
            <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif
    </head>
    <body class="public-shell min-h-screen antialiased" x-data="{ mobileMenuOpen: false }">
        <x-navbar />

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-public-line bg-public-ink text-white">
            <div class="public-container py-10 sm:py-14">
                <div class="grid gap-10 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)_minmax(0,1fr)]">
                    <div>
                        <a href="{{ route('landing') }}" class="text-2xl font-extrabold uppercase">JepretCFD</a>
                        <p class="mt-4 max-w-md text-sm leading-6 text-white/60">
                            Marketplace foto event yang menjaga karya photographer tetap terlindungi sampai buyer menyelesaikan pembayaran.
                        </p>
                    </div>

                    <nav aria-label="Footer navigation" class="grid grid-cols-2 gap-3 text-sm font-bold uppercase text-white/70">
                        <a href="{{ url('/#tentang') }}" class="hover:text-white">Tentang</a>
                        <a href="{{ route('galeri') }}" class="hover:text-white">Galeri</a>
                        <a href="{{ route('photographers.index') }}" class="hover:text-white">Photographers</a>
                        <a href="{{ url('/#pricing') }}" class="hover:text-white">Pricing</a>
                    </nav>

                    <div class="text-sm text-white/70">
                        <p class="font-bold uppercase text-white">Contact</p>
                        <div class="mt-3 grid gap-2">
                            <a href="https://wa.me/6285156767900" class="hover:text-white">WhatsApp 085156767900</a>
                            <a href="https://instagram.com/jepret.cfd" class="hover:text-white">Instagram @jepret.cfd</a>
                            <a href="mailto:jepretccfdd@gmail.com" class="hover:text-white">jepretccfdd@gmail.com</a>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 text-xs font-semibold text-white/50 sm:flex-row sm:items-center sm:justify-between">
                    <p>&copy; {{ date('Y') }} JepretCFD. All rights reserved.</p>
                    <p>Protected previews. Private originals. Repeat downloads after purchase.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
