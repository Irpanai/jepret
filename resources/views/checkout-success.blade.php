<x-marketplace-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="text-center border-b border-gray-100 pb-8">
                <div class="w-14 h-14 mx-auto rounded-full bg-green-50 border border-green-200 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-xs font-black text-green-700 uppercase tracking-widest">Pembayaran Berhasil</p>
                <h1 class="text-3xl sm:text-4xl font-black text-black mt-2">Order {{ $order['order_number'] }}</h1>
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-xl mx-auto">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Transaksi</p>
                        <p class="text-sm font-bold text-black mt-1">{{ optional($order['paid_at'] ?? $order['created_at'])->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Pembayaran</p>
                        <p class="text-sm font-bold text-black mt-1">Rp{{ number_format($order['total'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($transactions as $transaction)
                    <div class="py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="w-full sm:w-28 aspect-[4/3] bg-gray-100 rounded-xl overflow-hidden shrink-0">
                            <img src="{{ $transaction->photo?->file_watermark ? Storage::url($transaction->photo->file_watermark) : 'https://placehold.co/320x240/f3f4f6/111827?text=JEPRET' }}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-sm font-black text-gray-900">{{ $transaction->photo?->title ?: ($transaction->photo?->event?->nama_event ?? 'Foto Jepret') }}</h2>
                            <p class="text-xs font-medium text-gray-500 mt-1">Photographer: {{ $transaction->photo?->fotografer?->name ?? 'Photographer' }}</p>
                            <p class="text-sm font-black text-black mt-3">Rp{{ number_format($transaction->total_bayar, 0, ',', '.') }}</p>
                        </div>
                        <a href="{{ route('purchases.download', ['order' => $order['order_number'], 'transaction' => $transaction]) }}" class="shrink-0 inline-flex justify-center bg-black text-white text-xs font-bold px-5 py-3 rounded-xl hover:bg-gray-800 transition">
                            Download Original
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('galeri') }}" class="w-full sm:w-auto text-center bg-white border border-gray-200 text-black text-sm font-bold px-5 py-3 rounded-xl hover:bg-gray-50 transition">Kembali ke Galeri</a>
                <a href="{{ route('purchases.index') }}" class="w-full sm:w-auto text-center bg-black text-white text-sm font-bold px-5 py-3 rounded-xl hover:bg-gray-800 transition">Lihat Pembelian Saya</a>
            </div>
        </div>
    </div>
</x-marketplace-layout>
