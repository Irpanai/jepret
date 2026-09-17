<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - JEPRET CFD</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #f9fafb; color: #111827; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased">
    <x-navbar />

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-black text-black mb-8">Checkout</h1>

        @if(count($cart) === 0)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-10 text-center">
                <h2 class="text-lg font-black text-gray-900">Keranjang masih kosong</h2>
                <p class="text-sm font-medium text-gray-500 mt-1 mb-5">Pilih foto dari galeri sebelum membuat order pembayaran.</p>
                <a href="{{ route('galeri') }}" class="inline-flex bg-black text-white text-sm font-bold px-5 py-3 rounded-xl hover:bg-gray-800 transition">Buka Galeri</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <section class="md:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                        <h2 class="text-lg font-bold text-black mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Informasi Pembeli
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" value="{{ Auth::user()->name }}" readonly class="w-full bg-gray-50 border border-gray-200 text-gray-500 text-sm rounded-xl px-4 py-3 focus:ring-0 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                                <input type="email" value="{{ Auth::user()->email }}" readonly class="w-full bg-gray-50 border border-gray-200 text-gray-500 text-sm rounded-xl px-4 py-3 focus:ring-0 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                        <h2 class="text-lg font-bold text-black mb-4">Item Pesanan</h2>
                        <div class="space-y-4">
                            @foreach($cart as $item)
                                <div class="flex items-center justify-between gap-4 border-b border-gray-100 pb-4 last:border-b-0 last:pb-0">
                                    <div>
                                        <h3 class="text-sm font-black text-gray-900">{{ $item['event'] ?? 'Foto Jepret' }}</h3>
                                        <p class="text-xs font-medium text-gray-500 mt-1">{{ $item['fotografer'] ?? 'Photographer' }}</p>
                                    </div>
                                    <div class="text-sm font-black text-black">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <aside class="md:col-span-1">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm sticky top-24">
                        <h2 class="text-lg font-bold text-black mb-4">Ringkasan Pesanan</h2>

                        <div class="border-t border-gray-100 pt-4 mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-500 font-medium">Subtotal ({{ count($cart) }} Foto)</span>
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-500 font-medium">Biaya Layanan</span>
                                <span class="text-sm font-bold text-gray-900">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
                                <span class="text-base font-bold text-black">Total Tagihan</span>
                                <span class="text-xl font-black text-black">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <form action="{{ route('checkout.process') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold text-base py-3.5 rounded-xl transition shadow-md flex items-center justify-center gap-2">
                                Buat Order Pembayaran
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </form>

                        <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Original hanya terbuka setelah pembayaran lunas
                        </div>
                    </div>
                </aside>
            </div>
        @endif
    </main>
</body>
</html>
