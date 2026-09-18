<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name', 'Jepret') }} · Akun</title>
    <link rel="icon" type="image/png" href="{{ asset('images/jepret.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net"><link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&family=instrument-serif:400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-shell min-h-screen bg-public-paper text-public-ink antialiased"><x-navbar /><main class="public-container grid min-h-[calc(100vh-64px)] items-center py-10 sm:py-16">{{ $slot }}</main></body>
</html>
