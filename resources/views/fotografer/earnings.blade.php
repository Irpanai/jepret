<x-fg-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">PENDAPATAN & PENCAIRAN</span>
                </div>
                <h1 class="text-3xl font-black text-black tracking-tight mb-2">Keuangan</h1>
                <p class="text-sm text-gray-500 font-medium">Kelola saldo dan riwayat penarikan dana Anda.</p>
            </div>
            <div class="flex gap-2">
                <button class="bg-black text-white text-xs font-bold px-6 py-3.5 rounded-lg shadow-sm hover:bg-gray-800 flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Tarik Saldo
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Saldo Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <span class="text-xs font-bold text-black">Saldo Aktif</span>
                        </div>
                        <span class="bg-green-50 text-green-600 text-[9px] font-bold px-2 py-1 rounded">Tersedia</span>
                    </div>
                    <h3 class="text-4xl font-black text-black mb-2">Rp{{ number_format($user->saldo, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-500 font-medium leading-relaxed">
                        Pendapatan bersih (90%) dari penjualan foto.
                    </p>
                </div>
            </div>

            <!-- Total Pendapatan Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-center">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">TOTAL PENDAPATAN KESELURUHAN</span>
                <h3 class="text-3xl font-black text-black">Rp{{ number_format($totalEarnings, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-green-500 font-bold mt-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    Statistik penjualan Anda
                </p>
            </div>

            <!-- Total Penarikan Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-center">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">TOTAL PENARIKAN BERHASIL</span>
                <h3 class="text-3xl font-black text-black">Rp{{ number_format($withdrawals->where('status', 'success')->sum('jumlah_tarik'), 0, ',', '.') }}</h3>
                <p class="text-[10px] text-gray-400 font-medium mt-2">
                    Riwayat penarikan berhasil Anda.
                </p>
            </div>
        </div>

        <!-- Withdrawal History Table -->
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-black">Riwayat Penarikan Dana</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-white border-b border-gray-100 text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                        <tr>
                            <th class="px-6 py-4">ID PENARIKAN</th>
                            <th class="px-6 py-4">JUMLAH</th>
                            <th class="px-6 py-4">METODE</th>
                            <th class="px-6 py-4">STATUS</th>
                            <th class="px-6 py-4 text-right">TANGGAL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-xs font-medium">
                        @forelse($withdrawals as $wd)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-black">#WD-{{ str_pad($wd->id, 3, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-black">
                                Rp{{ number_format($wd->jumlah_tarik, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $wd->metode_pembayaran }} - {{ $wd->nomor_tujuan }}
                            </td>
                            <td class="px-6 py-4">
                                @if($wd->status === 'success')
                                    <span class="bg-green-50 text-green-700 text-[9px] font-bold px-2 py-1 rounded border border-green-200">BERHASIL</span>
                                @elseif($wd->status === 'pending')
                                    <span class="bg-yellow-50 text-yellow-700 text-[9px] font-bold px-2 py-1 rounded border border-yellow-200">MENUNGGU</span>
                                @else
                                    <span class="bg-red-50 text-red-700 text-[9px] font-bold px-2 py-1 rounded border border-red-200">DITOLAK</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-[10px] text-gray-400">{{ $wd->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-xs font-medium">Belum ada riwayat penarikan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-fg-layout>
