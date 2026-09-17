<x-marketplace-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl sm:text-4xl font-black text-black tracking-tight">Pembelian Saya</h1>
                <p class="mt-2 text-sm font-medium text-gray-500 max-w-2xl">
                    Lihat riwayat foto yang pernah kamu beli dan unduh kembali file original dari transaksi yang berhasil.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @php
                    $filters = [
                        '' => 'Semua',
                        'paid' => 'Paid',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                    ];
                @endphp

                @foreach($filters as $value => $label)
                    <a href="{{ route('purchases.index', $value === '' ? [] : ['status' => $value]) }}" class="px-3 py-2 rounded-lg text-xs font-bold border transition {{ $status === $value || ($status === '' && $value === '') ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-200 hover:border-black hover:text-black' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="hidden md:block bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100 text-[10px] uppercase tracking-widest text-gray-400 font-black">
                    <tr>
                        <th class="px-5 py-4">Order ID</th>
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">Foto</th>
                        <th class="px-5 py-4">Photographer</th>
                        <th class="px-5 py-4 text-right">Total</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        @php($firstItem = $order['items']->first())
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-5 py-5 align-top">
                                <div class="font-mono text-xs font-black text-black">{{ $order['order_number'] }}</div>
                            </td>
                            <td class="px-5 py-5 align-top text-xs font-medium text-gray-500">
                                {{ optional($order['created_at'])->format('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-5 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                        <img src="{{ $firstItem?->photo?->file_watermark ? Storage::url($firstItem->photo->file_watermark) : 'https://placehold.co/120/f3f4f6/111827?text=J' }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="text-xs font-black text-gray-900">{{ $firstItem?->photo?->title ?: ($firstItem?->photo?->event?->nama_event ?? 'Foto Jepret') }}</div>
                                        <div class="text-[10px] font-medium text-gray-500 mt-1">{{ $order['count'] }} foto</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 align-top text-xs font-medium text-gray-600">
                                {{ $firstItem?->photo?->fotografer?->name ?? 'Photographer' }}
                            </td>
                            <td class="px-5 py-5 align-top text-right text-xs font-black text-black">
                                Rp{{ number_format($order['total'], 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-5 align-top text-center">
                                <span class="inline-flex px-2.5 py-1 rounded text-[10px] font-black uppercase {{ $order['status'] === 'paid' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                    {{ $order['status'] }}
                                </span>
                            </td>
                            <td class="px-5 py-5 align-top text-right">
                                <a href="{{ route('purchases.show', ['order' => $order['order_number']]) }}" class="inline-flex items-center justify-center bg-black text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                                    {{ $order['status'] === 'paid' ? 'Lihat Pembelian' : 'Detail Order' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <h2 class="text-lg font-black text-gray-900">Belum ada pembelian</h2>
                                <p class="text-sm text-gray-500 mt-1 mb-5">Foto yang kamu beli akan muncul di sini.</p>
                                <a href="{{ route('galeri') }}" class="inline-flex bg-black text-white text-sm font-bold px-5 py-3 rounded-xl hover:bg-gray-800 transition">Buka Galeri</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-4">
            @forelse($orders as $order)
                @php($firstItem = $order['items']->first())
                <article class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
                    <div class="flex gap-3">
                        <div class="w-20 h-20 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                            <img src="{{ $firstItem?->photo?->file_watermark ? Storage::url($firstItem->photo->file_watermark) : 'https://placehold.co/160/f3f4f6/111827?text=J' }}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h2 class="text-xs font-mono font-black text-black truncate">{{ $order['order_number'] }}</h2>
                                <span class="shrink-0 px-2 py-1 rounded text-[9px] font-black uppercase {{ $order['status'] === 'paid' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">{{ $order['status'] }}</span>
                            </div>
                            <p class="text-sm font-black text-gray-900 mt-2 line-clamp-1">{{ $firstItem?->photo?->title ?: ($firstItem?->photo?->event?->nama_event ?? 'Foto Jepret') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $order['count'] }} foto • Rp{{ number_format($order['total'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('purchases.show', ['order' => $order['order_number']]) }}" class="mt-4 block w-full text-center bg-black text-white text-xs font-bold py-3 rounded-xl hover:bg-gray-800 transition">
                        {{ $order['status'] === 'paid' ? 'Lihat Pembelian' : 'Detail Order' }}
                    </a>
                </article>
            @empty
                <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center">
                    <h2 class="text-lg font-black text-gray-900">Belum ada pembelian</h2>
                    <p class="text-sm text-gray-500 mt-1 mb-5">Foto yang kamu beli akan muncul di sini.</p>
                    <a href="{{ route('galeri') }}" class="inline-flex bg-black text-white text-sm font-bold px-5 py-3 rounded-xl hover:bg-gray-800 transition">Buka Galeri</a>
                </div>
            @endforelse
        </div>
    </div>
</x-marketplace-layout>
