<x-superadmin-layout>
    <!-- Header Area -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <div class="text-[10px] font-mono text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                    CONSOLE / SUPER ADMIN / <span class="text-black font-bold">COMMAND CENTER</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-black tracking-tight mb-2">Command Center — Ringkasan Eksekutif</h1>
                <div class="text-[10px] font-mono text-gray-400 mb-3 uppercase tracking-widest">
                    CLUSTER JKT-01 • ROOT #001
                </div>
                <p class="text-xs sm:text-sm text-gray-500 font-medium max-w-2xl">
                    Ringkasan metrik makro transaksi, pertumbuhan ekosistem, serta sinyal tindakan cepat JEPRETCFD.
                </p>
            </div>
            <div class="flex flex-col items-end gap-3 shrink-0">
                <div class="bg-gray-50 border border-gray-200 text-black text-[10px] font-mono font-bold px-3 py-1.5 rounded flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    Mei 2026 • Real-time Sync
                </div>
                <button class="bg-black text-white text-xs font-bold px-5 py-2.5 rounded hover:bg-gray-800 transition flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Unduh Laporan Eksekutif (PDF)
                </button>
            </div>
        </div>
    </div>

    <!-- 5 Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Card 1: GMV -->
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col h-full">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono font-bold text-gray-500 uppercase tracking-widest leading-tight">01 / GMV<br>NASIONAL</span>
                <span class="bg-green-50 text-green-700 text-[9px] font-mono font-bold px-1.5 py-0.5 rounded text-right">
                    +22.4%<br>MoM
                </span>
            </div>
            <div class="mt-auto">
                <div class="text-sm font-bold text-black mb-0.5">Rp</div>
                <h3 class="text-2xl font-black text-black leading-none">{{ number_format($totalGmv, 0, ',', '.') }}</h3>
                <p class="text-[9px] text-gray-500 font-mono mt-2">{{ number_format($totalTransactions, 0, ',', '.') }} transaksi<br>foto terverifikasi</p>
            </div>
            <div class="flex justify-between items-end mt-4 pt-3 border-t border-gray-100">
                <span class="text-[9px] font-mono text-gray-400">vs Apr: Rp<br>104.910.000</span>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
            </div>
        </div>

        <!-- Card 2: Platform Share -->
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col h-full">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono font-bold text-gray-500 uppercase tracking-widest leading-tight">02 /<br>PLATFORM<br>SHARE (10%)</span>
                <span class="text-[9px] font-mono font-bold text-gray-400 text-right">
                    Nett<br>Fee
                </span>
            </div>
            <div class="mt-auto">
                <div class="text-sm font-bold text-black mb-0.5">Rp</div>
                <h3 class="text-2xl font-black text-black leading-none">{{ number_format($platformShare, 0, ',', '.') }}</h3>
                <p class="text-[9px] text-gray-500 font-mono mt-2">Margin bersih<br>terverifikasi</p>
            </div>
            <div class="flex justify-between items-end mt-4 pt-3 border-t border-gray-100">
                <span class="text-[9px] font-mono text-gray-400">BCA/GoPay MDR<br>ditanggung platform</span>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>

        <!-- Card 3: Payout Kreator -->
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col h-full">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono font-bold text-gray-500 uppercase tracking-widest leading-tight">03 /<br>PAYOUT<br>KREATOR<br>(90%)</span>
                <span class="text-[9px] font-mono font-bold text-gray-400 text-right">
                    Disbursed
                </span>
            </div>
            <div class="mt-auto">
                <div class="text-sm font-bold text-black mb-0.5">Rp</div>
                <h3 class="text-2xl font-black text-black leading-none">{{ number_format($totalWithdrawal, 0, ',', '.') }}</h3>
                <p class="text-[9px] text-gray-500 font-mono mt-2">Hak fotografer<br>terdistribusi</p>
            </div>
            <div class="flex justify-between items-end mt-4 pt-3 border-t border-gray-100">
                <span class="text-[9px] font-mono text-gray-400">{{ $totalFotografer }} fotografer aktif<br>CFD</span>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

        <!-- Card 4: Antrean Pencairan -->
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col h-full">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono font-bold text-gray-500 uppercase tracking-widest leading-tight">04 / ANTREAN<br>PENCAIRAN</span>
                <span class="bg-black text-white text-[10px] font-mono font-bold px-2 py-1 rounded text-center leading-none">
                    {{ $pendingWithdrawalsCount }}<br>Req
                </span>
            </div>
            <div class="mt-auto">
                <div class="text-sm font-bold text-black mb-0.5">Rp</div>
                <h3 class="text-2xl font-black text-black leading-none">{{ number_format($pendingWithdrawalsAmount, 0, ',', '.') }}</h3>
                <p class="text-[9px] text-gray-500 font-mono mt-2">Menunggu otorisasi<br>admin</p>
            </div>
            <div class="flex justify-between items-end mt-4 pt-3 border-t border-gray-100">
                <span class="text-[9px] font-mono text-gray-400">SLA Rata-rata: < 2<br>Jam</span>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Card 5: Cloud Storage -->
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col h-full">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono font-bold text-gray-500 uppercase tracking-widest leading-tight">05 / CLOUD<br>STORAGE</span>
                <span class="text-[9px] font-mono font-bold text-gray-400 text-right">
                    18.4%<br>Quota
                </span>
            </div>
            <div class="mt-auto mb-2">
                <div class="flex items-baseline gap-1">
                    <h3 class="text-2xl font-black text-black leading-none">1.84 TB</h3>
                    <span class="text-[10px] font-mono text-gray-500">/ 10 TB</span>
                </div>
                <div class="w-full h-1 bg-gray-100 rounded-full mt-3 overflow-hidden">
                    <div class="h-full bg-black rounded-full" style="width: 18.4%"></div>
                </div>
            </div>
            <div class="flex justify-between items-end mt-4 pt-3 border-t border-gray-100">
                <span class="text-[9px] font-mono text-gray-400">AWS Jakarta +<br>Cloudflare CDN</span>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Middle Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Tren Transaksi (2/3) -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm p-6 flex flex-col">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-1.5 h-1.5 rounded-full bg-black"></div>
                <h3 class="text-xs font-bold text-black uppercase tracking-widest">TREN VOLUME TRANSAKSI & GMV (MEI 2026)</h3>
            </div>
            <p class="text-[11px] text-gray-500 mb-6">Pola lonjakan berkala mingguan setiap Minggu pagi jam 06:00 - 11:00 WIB</p>
            
            <div class="bg-gray-50 border border-gray-100 rounded-lg px-4 py-2 mb-8 inline-block self-start">
                <span class="text-[10px] font-mono text-gray-500">Total: 4 Gelombang CFD</span>
            </div>

            <!-- Mock Chart -->
            <div class="flex-1 relative min-h-[160px] flex items-end justify-between px-4 pb-4">
                <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 400 100">
                    <path d="M 0 95 
                             C 20 95, 30 20, 40 10 
                             C 50 20, 60 95, 80 95
                             C 100 95, 110 20, 120 10
                             C 130 20, 140 95, 160 95
                             C 180 95, 190 20, 200 10
                             C 210 20, 220 95, 240 95
                             C 260 95, 270 20, 280 10
                             C 290 20, 300 95, 320 95
                             L 400 95" 
                          fill="none" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
                          
                    <!-- Points -->
                    <circle cx="40" cy="10" r="2.5" fill="black"/>
                    <circle cx="120" cy="10" r="2.5" fill="black"/>
                    <circle cx="200" cy="10" r="2.5" fill="black"/>
                    <circle cx="280" cy="10" r="2.5" fill="black"/>
                </svg>
            </div>

            <!-- X Axis Labels -->
            <div class="flex justify-between border-t border-gray-100 pt-4 px-4">
                <div class="text-center">
                    <div class="text-[9px] font-mono text-gray-400 mb-1">04 Mei (Sun)</div>
                    <div class="text-[10px] font-mono font-bold text-black">Rp 26.2jt</div>
                </div>
                <div class="text-center">
                    <div class="text-[9px] font-mono text-gray-400 mb-1">11 Mei (Sun)</div>
                    <div class="text-[10px] font-mono font-bold text-black">Rp 28.5jt</div>
                </div>
                <div class="text-center">
                    <div class="text-[9px] font-mono text-gray-400 mb-1">18 Mei (Sun)</div>
                    <div class="text-[10px] font-mono font-bold text-black">Rp 34.1jt</div>
                </div>
                <div class="text-center">
                    <div class="text-[9px] font-mono text-gray-400 mb-1">25 Mei (Sun)</div>
                    <div class="text-[10px] font-mono font-bold text-black">Rp 39.6jt</div>
                </div>
            </div>

            <!-- Footer Stats -->
            <div class="mt-6 border-t border-gray-100 pt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <p class="text-[10px] font-mono text-gray-500 max-w-xs">82.6% transaksi terjadi dalam kurun 6 jam setelah fotografer publish album.</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-mono text-gray-400">Avg Checkout: Rp</p>
                    <p class="text-xs font-mono font-bold text-black">25.687 / order</p>
                </div>
            </div>
        </div>

        <!-- Distribusi Wilayah (1/3) -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 flex flex-col">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-xs font-bold text-black uppercase tracking-widest">DISTRIBUSI WILAYAH CFD AKTIF</h3>
                <span class="text-[9px] font-mono text-gray-400">Mei 2026</span>
            </div>
            <p class="text-[10px] text-gray-500 mb-6">Kontribusi volume order menurut klaster rute CFD</p>

            <div class="space-y-5 flex-1">
                <!-- Jakarta -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-[11px] font-bold text-black">Jakarta (Sudirman - Thamrin)</span>
                        <span class="text-[10px] font-mono text-gray-500">48.6% <span class="text-gray-400">(2.487 foto)</span></span>
                    </div>
                    <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-black rounded-full" style="width: 48.6%"></div>
                    </div>
                </div>

                <!-- Banjarbaru -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-[11px] font-bold text-black">Banjarbaru (Lapangan Murjani)</span>
                        <span class="text-[10px] font-mono text-gray-500">22.6% <span class="text-gray-400">(1.126 foto)</span></span>
                    </div>
                    <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gray-600 rounded-full" style="width: 22.6%"></div>
                    </div>
                </div>

                <!-- Bandung -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-[11px] font-bold text-black">Bandung (Dago - Gasibu)</span>
                        <span class="text-[10px] font-mono text-gray-500">18.0% <span class="text-gray-400">(921 foto)</span></span>
                    </div>
                    <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gray-400 rounded-full" style="width: 18.0%"></div>
                    </div>
                </div>

                <!-- Solo -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-[11px] font-bold text-black">Solo (Jl. Slamet Riyadi)</span>
                        <span class="text-[10px] font-mono text-gray-500">12.0% <span class="text-gray-400">(618 foto)</span></span>
                    </div>
                    <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gray-300 rounded-full" style="width: 12.0%"></div>
                    </div>
                </div>
            </div>

            <!-- Conversion Box -->
            <div class="mt-6 bg-gray-50 border border-gray-100 rounded-lg p-4 flex items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded bg-white border border-gray-200 flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-[11px] font-bold text-black">Bib & Face Search Conversion</h4>
                        <p class="text-[9px] font-mono text-gray-500 mt-0.5">Pencarian terasosiasi langsung ke checkout</p>
                    </div>
                </div>
                <span class="text-lg font-black text-black">34.2%</span>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Antrean Kepatuhan -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <div class="flex justify-between items-start">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-black mt-1.5"></div>
                        <div>
                            <h3 class="text-sm font-bold text-black mb-1">Antrean Kepatuhan Butuh Verifikasi</h3>
                            <p class="text-[10px] font-mono text-gray-500">3 Permohonan Fotografer Baru Menunggu Tinjauan</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded px-2 py-1 text-[9px] font-mono text-gray-500 text-center">
                        SLA: 24<br>Jam
                    </div>
                </div>
            </div>
            <div class="p-4 flex-1 space-y-2">
                @forelse ($pendingUsers as $user)
                <!-- Item -->
                <div class="bg-white border border-gray-100 rounded-lg p-3 flex justify-between items-center gap-3 hover:border-gray-300 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-gray-100 text-black text-[10px] font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $user->name), 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <h4 class="text-xs font-bold text-black">{{ $user->name }}</h4>
                                <span class="bg-gray-100 text-gray-500 text-[8px] font-mono px-1 py-0.5 rounded">PENDING</span>
                            </div>
                            <p class="text-[9px] font-mono text-gray-400">{{ $user->email }} • Diajukan {{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <a href="{{ route('superadmin.compliance') }}" class="border border-gray-200 text-black text-[10px] font-bold px-3 py-1.5 rounded hover:bg-gray-50 transition shrink-0">Tinjau</a>
                </div>
                @empty
                <div class="text-center py-4 text-gray-400 text-xs font-mono">
                    Tidak ada antrean
                </div>
                @endforelse
            </div>
            <a href="{{ route('superadmin.compliance') }}" class="p-4 border-t border-gray-100 flex justify-between items-center hover:bg-gray-50 transition rounded-b-xl group">
                <span class="text-[10px] font-bold text-black">Buka Antrean Kepatuhan Penuh ({{ $pendingUsersCount }} Pending)</span>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-black transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <!-- Pencairan Dana -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <div class="flex justify-between items-start">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 mt-1.5"></div>
                        <div>
                            <h3 class="text-sm font-bold text-black mb-1">Pencairan Dana Prioritas Siap Eksekusi</h3>
                            <p class="text-[10px] font-mono text-gray-500">5 Permohonan • Total Rp 14.800.000</p>
                        </div>
                    </div>
                    <div class="bg-green-50 text-green-700 rounded px-2 py-1 text-[9px] font-mono font-bold">
                        BI-FAST Batch Ready
                    </div>
                </div>
            </div>
            <div class="p-4 flex-1 space-y-2">
                @forelse ($pendingWithdrawals as $withdrawal)
                <!-- Item -->
                <div class="bg-white border border-gray-100 rounded-lg p-3 flex justify-between items-center gap-3 hover:border-gray-300 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-gray-50 border border-gray-100 text-gray-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-black mb-0.5">{{ $withdrawal->fotografer->name }}</h4>
                            <p class="text-[9px] font-mono text-gray-400">{{ $withdrawal->metode_pembayaran }} • {{ substr($withdrawal->nomor_tujuan, 0, 4) }}-xxxx-{{ substr($withdrawal->nomor_tujuan, -2) }} • Saldo: Rp {{ number_format($withdrawal->fotografer->saldo, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-mono font-bold text-black">Rp {{ number_format($withdrawal->jumlah_tarik, 0, ',', '.') }}</span>
                        <a href="{{ route('superadmin.earnings') }}">
                            <svg class="w-4 h-4 text-green-500 hover:text-green-600 transition cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-400 text-xs font-mono">
                    Tidak ada antrean pencairan
                </div>
                @endforelse
            </div>
            <a href="{{ route('superadmin.earnings') }}" class="p-4 border-t border-gray-100 flex justify-between items-center hover:bg-gray-50 transition rounded-b-xl group">
                <span class="text-[10px] font-bold text-black">Buka Menu Pencairan Kreator Penuh ({{ $pendingWithdrawalsCount }} Permohonan)</span>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-black transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>

    <!-- Telemetri Footer -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-8">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
            <span class="text-[10px] font-bold text-black uppercase tracking-widest">TELEMETRI PLATFORM & GATEWAY</span>
        </div>
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 flex-1">
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="text-[9px] font-mono text-gray-500">API BI-FAST: <span class="text-black">99.98% (32ms)</span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="text-[9px] font-mono text-gray-500">QRIS Webhook: <span class="text-black">Connected (0 drop)</span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="text-[9px] font-mono text-gray-500">S3 JKT Node: <span class="text-black">Healthy (1.84 TB)</span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                <span class="text-[9px] font-mono text-gray-500">Security Audit: <span class="text-black">0 Anomaly (24h)</span></span>
            </div>
        </div>
    </div>

</x-superadmin-layout>
