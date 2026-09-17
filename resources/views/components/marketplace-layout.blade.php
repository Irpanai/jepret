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

        <link rel="icon" type="image/png" href="{{ asset('images/jepret.png') }}">
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
                        <a href="{{ route('landing') }}" class="inline-flex items-center gap-4" aria-label="JepretCFD home">
                            <img src="{{ asset('images/jepret.png') }}" alt="JepretCFD" class="h-20 w-20 bg-white object-contain">
                            <span class="text-2xl font-extrabold uppercase">JepretCFD</span>
                        </a>
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
                        <div class="mt-4 flex items-center gap-3">
                            <a href="https://wa.me/6285156767900" target="_blank" rel="noopener noreferrer" class="grid h-11 w-11 place-items-center border border-white/25 hover:border-white" aria-label="WhatsApp JepretCFD" title="WhatsApp">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" aria-hidden="true"><path fill="#25D366" d="M12 2a9.75 9.75 0 0 0-8.42 14.65L2.25 21.5l4.96-1.3A9.75 9.75 0 1 0 12 2Z"/><path fill="#fff" d="M16.97 14.35c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-1.61-.8-2.67-1.44-3.74-3.27-.28-.49.28-.45.8-1.5.09-.18.05-.34-.02-.48-.07-.13-.61-1.47-.84-2.02-.22-.53-.45-.46-.61-.47h-.52c-.18 0-.48.07-.73.34-.25.27-.95.93-.95 2.27s.98 2.63 1.11 2.81c.14.18 1.92 2.93 4.65 4.11 1.73.75 2.41.81 3.28.68 1.04-.16 1.6-.66 1.83-1.29.23-.64.23-1.18.16-1.29-.07-.12-.25-.18-.52-.32Z"/></svg>
                            </a>
                            <a href="https://instagram.com/jepret.cfd" target="_blank" rel="noopener noreferrer" class="grid h-11 w-11 place-items-center border border-white/25 hover:border-white" aria-label="Instagram JepretCFD" title="Instagram">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" aria-hidden="true"><defs><radialGradient id="instagram-gradient" cx="30%" cy="107%" r="130%"><stop offset="0" stop-color="#FFD600"/><stop offset=".3" stop-color="#FF7A00"/><stop offset=".55" stop-color="#FF0169"/><stop offset=".82" stop-color="#D300C5"/><stop offset="1" stop-color="#7638FA"/></radialGradient></defs><rect width="24" height="24" rx="6" fill="url(#instagram-gradient)"/><rect x="5" y="5" width="14" height="14" rx="4" fill="none" stroke="#fff" stroke-width="1.8"/><circle cx="12" cy="12" r="3.25" fill="none" stroke="#fff" stroke-width="1.8"/><circle cx="17.25" cy="6.75" r="1" fill="#fff"/></svg>
                            </a>
                            <a href="mailto:jepretccfdd@gmail.com" class="grid h-11 w-11 place-items-center border border-white/25 hover:border-white" aria-label="Email JepretCFD" title="Email">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M3.5 19.5h3.2V9.8L2.1 6.35v11.7c0 .8.63 1.45 1.4 1.45Z"/><path fill="#34A853" d="M17.3 19.5h3.2c.77 0 1.4-.65 1.4-1.45V6.35L17.3 9.8v9.7Z"/><path fill="#EA4335" d="M17.3 9.8 12 13.85 6.7 9.8 5.6 5.3 12 10.2l6.4-4.9-1.1 4.5Z"/><path fill="#FBBC04" d="M2.1 6.35 6.7 9.8V4.4L3.5 4.35c-.8 0-1.4.9-1.4 2Z"/><path fill="#C5221F" d="M21.9 6.35 17.3 9.8V4.4l3.2-.05c.8 0 1.4.9 1.4 2Z"/></svg>
                            </a>
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
