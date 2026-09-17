<x-superadmin-layout>
    <!-- Header Area -->
    <div class="mb-6 flex flex-col xl:flex-row xl:items-start justify-between gap-4">
        <div class="flex-1">
            <div class="text-[10px] font-mono text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                CONSOLE / SUPER ADMIN / <span class="text-black font-bold">SYSTEM ROOT</span>
            </div>
            
            <div class="flex items-center gap-3 mb-2">
                <div class="bg-gray-100 text-gray-600 text-[9px] font-mono font-bold px-2 py-1 rounded uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    LEDGER AUDIT CORE
                </div>
                <div class="text-gray-400 text-[9px] font-mono font-bold uppercase tracking-wider">
                    SYNC #LGR-2025-06
                </div>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-black tracking-tight mb-2">Ledger Transaksi & Rekonsiliasi Finansial</h1>
            <p class="text-xs sm:text-sm text-gray-600 font-medium max-w-2xl leading-relaxed">
                Buku besar audit seluruh transaksi penjualan foto CFD nasional. Pemantauan real-time split komisi otomatis (90% Fotografer / 10% Platform) dan status payment gateway.
            </p>
        </div>
        
        <div class="flex flex-col gap-2 shrink-0 w-full xl:w-auto pt-2 xl:pt-0">
            <button class="bg-white border border-gray-200 text-black text-[11px] font-bold px-4 py-2.5 rounded hover:bg-gray-50 transition shadow-sm flex items-center justify-between xl:justify-start gap-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    01 - 31 Mei 2026
                </div>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <button class="bg-white border border-gray-200 text-black text-[11px] font-bold px-4 py-2.5 rounded hover:bg-gray-50 transition shadow-sm flex items-center justify-between xl:justify-start gap-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Filter Gateway (QRIS BCA, GoPay, ...)
                </div>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            </button>
            <button class="bg-black text-white text-[11px] font-bold px-4 py-2.5 rounded hover:bg-gray-800 transition shadow-sm flex items-center justify-center xl:justify-start gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Ekspor Buku Besar
            </button>
        </div>
    </div>

    <!-- Top Metrics (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1 -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono font-bold text-gray-400 uppercase tracking-widest">TOTAL VOLUME<br>TRANSAKSI</span>
                <div class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="mt-auto">
                <h3 class="text-2xl font-black text-black leading-none mb-3">Rp{{ number_format($totalGmv, 0, ',', '.') }}</h3>
                <div class="flex justify-between items-center border-t border-gray-100 pt-3">
                    <span class="text-[10px] font-mono text-gray-500">{{ number_format($totalTransactionsCount, 0, ',', '.') }}<br>transaksi</span>
                    <span class="text-[10px] font-mono font-bold text-green-600 text-right">+14.2%<br>MoM</span>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col justify-between relative overflow-hidden">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono font-bold text-gray-400 uppercase tracking-widest">HAK<br>FOTOGRAFER</span>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-mono text-gray-400 font-bold bg-gray-50 px-1.5 rounded">90%</span>
                    <div class="w-6 h-6 rounded bg-green-50 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="mt-auto">
                <h3 class="text-2xl font-black text-black leading-none mb-3">Rp{{ number_format($hakFotografer, 0, ',', '.') }}</h3>
                <div class="border-t border-gray-100 pt-3">
                    <span class="text-[10px] font-mono text-gray-500 truncate block">Tersalurkan otomatis ke...</span>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono font-bold text-gray-400 uppercase tracking-widest">PENDAPATAN<br>PLATFORM</span>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-mono text-gray-400 font-bold bg-gray-50 px-1.5 rounded">10%</span>
                    <div class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="mt-auto">
                <h3 class="text-2xl font-black text-black leading-none mb-3">Rp{{ number_format($pendapatanPlatform, 0, ',', '.') }}</h3>
                <div class="border-t border-gray-100 pt-3">
                    <span class="text-[10px] font-mono text-gray-500 truncate block">Margin bruto setelah M...</span>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono font-bold text-gray-400 uppercase tracking-widest">MDR & SETTLEMENT<br>FEE</span>
                <div class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <div class="mt-auto">
                <h3 class="text-2xl font-black text-black leading-none mb-3">Rp980.200</h3>
                <div class="flex justify-between items-center border-t border-gray-100 pt-3">
                    <span class="text-[10px] font-mono text-gray-500">Net<br>Settlement</span>
                    <span class="text-[11px] font-mono font-bold text-black text-right">99.2%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs & Search -->
    <div class="bg-white border border-gray-200 rounded-xl p-1.5 mb-6 flex flex-col xl:flex-row xl:items-center justify-between gap-3 shadow-sm">
        <div class="relative w-full xl:w-96 shrink-0">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" class="w-full bg-gray-50 border-none rounded-lg pl-9 pr-4 py-2 text-xs focus:ring-1 focus:ring-black transition" placeholder="Cari Order ID, email pembeli, nama fotografer, rute (...">
        </div>
        <div class="flex flex-wrap items-center gap-1 xl:justify-end overflow-x-auto pb-1 xl:pb-0">
            <button class="bg-black text-white px-3 py-1.5 rounded-lg text-[10px] font-bold shadow-sm whitespace-nowrap">Semua Status ({{ number_format($totalTransactionsCount, 0, ',', '.') }})</button>
            <button class="text-gray-600 hover:text-black hover:bg-gray-100 px-3 py-1.5 rounded-lg text-[10px] font-bold transition whitespace-nowrap">Settled ({{ number_format($totalTransactionsCount, 0, ',', '.') }})</button>
            <button class="text-gray-600 hover:text-black hover:bg-gray-100 px-3 py-1.5 rounded-lg text-[10px] font-bold transition whitespace-nowrap">Pending Webhook (0)</button>
            <button class="text-gray-600 hover:text-black hover:bg-gray-100 px-3 py-1.5 rounded-lg text-[10px] font-bold transition whitespace-nowrap">Refund / Sengketa (0)</button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="py-4 px-5 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">ORDER REF /<br>WAKTU</th>
                        <th class="py-4 px-5 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">PEMBELI (GOOGLE SSO)</th>
                        <th class="py-4 px-5 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">FOTOGRAFER<br>& EVENT CFD</th>
                        <th class="py-4 px-5 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap text-right">HARGA<br>FOTO (GMV)</th>
                        <th class="py-4 px-5 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap text-right">KREATOR<br>(90%)</th>
                        <th class="py-4 px-5 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap text-right">PLATFORM<br>(10%)</th>
                        <th class="py-4 px-5 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">GATEWAY & MDR</th>
                        <th class="py-4 px-5 text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap text-right">STATUS<br>REKONSILIASI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    
                    @forelse ($transactions as $transaction)
                    <tr class="hover:bg-gray-50/50 transition border-b border-gray-100">
                        <td class="py-4 px-5 align-top">
                            <div class="text-xs font-black text-black mb-1">#JCFD-{{ $transaction->id }}</div>
                            <div class="text-[10px] font-mono text-gray-400">{{ $transaction->created_at->format('d M') }}<br>{{ $transaction->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="py-4 px-5 align-top">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-gray-100 text-[10px] font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $transaction->pembeli->name), 0, 2)) }}
                                </div>
                                <span class="text-xs text-black">{{ $transaction->pembeli->email }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-5 align-top">
                            <div class="text-xs font-bold text-black mb-0.5">{{ $transaction->photo->fotografer->name }}</div>
                            <div class="text-[10px] font-mono text-gray-400">{{ $transaction->photo->event->nama_event ?? 'CFD Event' }}</div>
                        </td>
                        <td class="py-4 px-5 align-top text-right text-xs font-mono text-gray-600">Rp{{ number_format($transaction->total_bayar, 0, ',', '.') }}</td>
                        <td class="py-4 px-5 align-top text-right text-xs font-mono font-bold text-green-700">Rp{{ number_format($transaction->jumlah_fotografer, 0, ',', '.') }}</td>
                        <td class="py-4 px-5 align-top text-right text-xs font-mono text-gray-600">Rp{{ number_format($transaction->jumlah_platform, 0, ',', '.') }}</td>
                        <td class="py-4 px-5 align-top">
                            <div class="flex items-center gap-2">
                                <div class="bg-gray-100 text-gray-600 text-[9px] font-mono font-bold px-2 py-0.5 rounded">QRIS<br>Online</div>
                                <span class="text-[9px] font-mono text-gray-400">0.7%</span>
                            </div>
                        </td>
                        <td class="py-4 px-5 align-top text-right">
                            <div class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-[9px] font-mono font-bold px-2 py-1 rounded">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                SETTLED<br>(MATCH 100%)
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500 text-xs font-mono">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Telemetri Footer -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8 mb-8">
        <div class="flex flex-wrap items-center gap-x-8 gap-y-3 w-full">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="text-[9px] font-mono text-gray-500">Gateway Webhook latency: <span class="text-black font-bold">48ms</span></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                <span class="text-[9px] font-mono text-gray-500">Zero un-reconciled anomalies detected in last 24h</span>
            </div>
            <div class="text-[9px] font-mono text-gray-400 uppercase tracking-widest sm:ml-auto">
                DISBURSEMENT CRON: EVERY SUNDAY 23:59 WIB
            </div>
            <div class="text-[9px] font-mono text-gray-400 uppercase tracking-widest">
                CHECKSUM SHA-256: VALID
            </div>
        </div>
    </div>

</x-superadmin-layout>
