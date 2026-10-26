<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name', 'Jepret') }} · Akun</title>
    <link rel="icon" type="image/png" href="{{ asset('images/jepret.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@600;700;800;900&family=Manrope:wght@400;500;600;700;800&family=Newsreader:ital,opsz,wght@0,6..72,400..700;1,6..72,400..700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="public-shell font-sans min-h-screen bg-public-paper text-public-ink antialiased"><a href="#main-content"
        class="skip-link">Lewati ke konten utama</a><x-navbar />
    <main id="main-content" class="public-container grid min-h-[calc(100dvh-64px)] items-center py-10 sm:py-16"
        tabindex="-1">{{ $slot }}</main>
</body>

</html>