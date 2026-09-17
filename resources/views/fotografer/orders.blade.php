<x-fg-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">TRANSAKSI & ORDER</span>
                </div>
                <h1 class="text-3xl font-black text-black tracking-tight mb-2">Pesanan & Transaksi</h1>
                <p class="text-sm text-gray-500 font-medium">Pantau setiap pembelian karya Anda secara real-time.</p>
            </div>
            <div class="flex gap-2">
                <button class="bg-black text-white text-xs font-bold px-6 py-3.5 rounded-lg shadow-sm hover:bg-gray-800 flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export CSV
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="relative max-w-sm w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" class="block w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-black focus:border-black" placeholder="Cari ID Pesanan atau Email Pembeli...">
                </div>
                <div class="flex gap-2">
                    <select class="border border-gray-200 text-gray-600 text-xs font-bold rounded-lg px-3 py-2 focus:ring-0">
                        <option>Semua Status</option>
                        <option>Berhasil (Lunas)</option>
                        <option>Menunggu Pembayaran</option>
                    </select>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-white border-b border-gray-100 text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                        <tr>
                            <th class="px-6 py-4">ID PESANAN</th>
                            <th class="px-6 py-4">FOTO & EVENT</th>
                            <th class="px-6 py-4">PEMBELI</th>
                            <th class="px-6 py-4">HARGA (100%)</th>
                            <th class="px-6 py-4">PENDAPATAN (90%)</th>
                            <th class="px-6 py-4">STATUS</th>
                            <th class="px-6 py-4 text-right">WAKTU</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-xs font-medium">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-black">#ORD-{{ str_pad($trx->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded overflow-hidden">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($trx->photo->file_watermark) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-bold text-black truncate max-w-[150px]">{{ basename($trx->photo->file_asli) }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $trx->photo->event->nama_event ?? 'Unknown Event' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $trx->pembeli->email ?? 'Guest' }}</td>
                            <td class="px-6 py-4">Rp{{ number_format($trx->harga_foto, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 font-bold text-black">Rp{{ number_format($trx->jumlah_fotografer, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($trx->status === 'paid')
                                    <span class="bg-green-50 text-green-700 text-[9px] font-bold px-2 py-1 rounded border border-green-200">LUNAS</span>
                                @else
                                    <span class="bg-yellow-50 text-yellow-700 text-[9px] font-bold px-2 py-1 rounded border border-yellow-200">PENDING</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-[10px] text-gray-400">{{ $trx->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 text-xs font-medium">Belum ada transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 flex justify-between items-center text-[10px] text-gray-500 font-medium">
                <span>Menampilkan 5 dari 184 transaksi</span>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 border border-gray-200 rounded text-gray-400 cursor-not-allowed">Previous</button>
                    <button class="px-3 py-1.5 border border-gray-200 rounded hover:bg-gray-50 text-black">Next</button>
                </div>
            </div>
        </div>
    </div>
</x-fg-layout>
