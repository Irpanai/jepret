<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang - JEPRET CFD</title>
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
        <div class="flex items-start justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-black text-black">Keranjang</h1>
                <p class="text-sm font-medium text-gray-500 mt-1">Foto di sini berasal dari galeri marketplace dan siap diproses ke checkout.</p>
            </div>
            <a href="{{ route('galeri') }}" class="shrink-0 bg-white border border-gray-200 text-gray-700 text-xs font-bold px-4 py-2.5 rounded-lg hover:bg-gray-50 transition">Tambah Foto</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <section class="md:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                @forelse($cart as $item)
                    <div class="p-5 flex gap-4 border-b border-gray-100 last:border-b-0">
                        <div class="w-20 h-24 bg-gray-100 rounded-xl overflow-hidden shrink-0">
                            <img src="{{ $item['preview'] ? Storage::url($item['preview']) : 'https://placehold.co/240x300/f3f4f6/111827?text=JEPRET' }}" alt="" class="w-full h-full object-cover blur-[1px]">
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-sm font-black text-gray-900 truncate">{{ $item['event'] }}</h2>
                            <p class="text-xs font-medium text-gray-500 mt-1">{{ $item['fotografer'] }}</p>
                            <p class="text-lg font-black text-black mt-3">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <form method="POST" action="{{ route('cart.destroy') }}" class="shrink-0">
                            @csrf
                            <input type="hidden" name="photo_id" value="{{ $item['id'] }}">
                            <button class="text-xs font-bold text-red-600 hover:text-red-700">Hapus</button>
                        </form>
                    </div>
                @empty
                    <div class="p-10 text-center">
                        <h2 class="text-lg font-black text-gray-900">Keranjang masih kosong</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1 mb-5">Pilih foto dari galeri sebelum lanjut checkout.</p>
                        <a href="{{ route('galeri') }}" class="inline-flex bg-black text-white text-sm font-bold px-5 py-3 rounded-xl hover:bg-gray-800 transition">Buka Galeri</a>
                    </div>
                @endforelse
            </section>

            <aside class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 h-max sticky top-24">
                <h2 class="text-lg font-black text-black mb-4">Ringkasan</h2>
                <div class="flex justify-between text-sm font-bold text-gray-600 mb-2">
                    <span>{{ count($cart) }} foto</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base font-black text-black pt-4 border-t border-gray-100">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('checkout.page') }}" class="mt-6 block w-full text-center bg-black text-white text-sm font-bold py-3 rounded-xl hover:bg-gray-800 transition {{ count($cart) === 0 ? 'pointer-events-none opacity-50' : '' }}">
                    Lanjut Checkout
                </a>
            </aside>
        </div>
    </main>
</body>
</html>
