<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment - JEPRET CFD</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #f9fafb; color: #111827; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased">

    <x-navbar />

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="bg-black text-white p-6 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Menunggu Pembayaran</p>
                <h1 class="text-3xl font-black mb-1">Rp 35.000</h1>
                <p class="text-sm text-gray-300 font-medium">Order {{ $order_id ?? '#JEPRET-XXXX' }}</p>
            </div>
            
            <!-- Body -->
            <div class="p-8 text-center border-b border-gray-100">
                <h3 class="text-lg font-bold text-black mb-6">Scan QRIS untuk membayar</h3>
                
                <div class="inline-block p-4 bg-white border-2 border-gray-200 rounded-2xl mb-6 shadow-sm">
                    <!-- Placeholder QR -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $order_id ?? 'JEPRET' }}" alt="QRIS" class="w-48 h-48">
                </div>
                
                <p class="text-sm text-gray-500 font-medium mb-4">Gunakan aplikasi e-wallet atau mobile banking Anda (Gopay, OVO, Dana, BCA, dll).</p>
                
                <div class="bg-blue-50 text-blue-800 text-xs font-bold py-2 px-4 rounded-lg inline-flex items-center gap-2 border border-blue-100">
                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Menunggu konfirmasi pembayaran...
                </div>
            </div>

            <!-- Footer / Simulation Action -->
            <div class="p-6 bg-gray-50 flex flex-col gap-3">
                <p class="text-xs text-center text-gray-400 mb-2">Tombol di bawah ini hanya untuk keperluan demo simulasi.</p>
                <form action="{{ route('payment.simulate', ['order_id' => $order_id ?? 'TEST']) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm py-3 rounded-xl transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simulasikan Status: PAID
                    </button>
                </form>
                <a href="{{ route('galeri') }}" class="w-full bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-bold text-sm py-3 rounded-xl transition text-center">
                    Batalkan & Kembali ke Galeri
                </a>
            </div>
        </div>
    </div>

</body>
</html>
