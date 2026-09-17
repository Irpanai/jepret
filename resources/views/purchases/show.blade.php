<x-marketplace-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('purchases.index') }}" class="text-xs font-bold text-gray-500 hover:text-black">Kembali ke Pembelian Saya</a>

        <div class="mt-5 bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 border-b border-gray-100 pb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-black tracking-tight">Order {{ $order['order_number'] }}</h1>
                    <p class="text-sm font-medium text-gray-500 mt-2">Detail foto yang dibeli dan status transaksi.</p>
                </div>
                <span class="w-max px-3 py-1.5 rounded-lg text-xs font-black uppercase {{ $order['status'] === 'paid' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                    {{ $order['status'] }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 py-6 border-b border-gray-100">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</p>
                    <p class="text-sm font-bold text-black mt-1">{{ optional($order['created_at'])->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</p>
                    <p class="text-sm font-bold text-black mt-1">Rp{{ number_format($order['total'], 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jumlah Foto</p>
                    <p class="text-sm font-bold text-black mt-1">{{ $order['count'] }} foto</p>
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
                        <div class="shrink-0">
                            @if($order['status'] === 'paid')
                                <a href="{{ route('purchases.download', ['order' => $order['order_number'], 'transaction' => $transaction]) }}" class="inline-flex w-full sm:w-auto justify-center bg-black text-white text-xs font-bold px-5 py-3 rounded-xl hover:bg-gray-800 transition">
                                    Download Original
                                </a>
                            @else
                                <span class="inline-flex w-full sm:w-auto justify-center bg-gray-100 text-gray-500 text-xs font-bold px-5 py-3 rounded-xl">
                                    Belum tersedia
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-marketplace-layout>
