<x-fg-layout>
    @if(! $user->subscription?->isActive())
        <div class="mb-6 border-l-4 border-amber-500 bg-white p-5 text-gray-900">
            <p class="font-extrabold">{{ $user->subscription?->trial_used_at ? 'Masa trial kamu telah berakhir, silakan melakukan pembelian paket yang tersedia.' : 'Subscription Photographer tidak aktif.' }}</p>
            <p class="mt-1 text-sm text-gray-600">Dashboard tersedia dalam mode read-only.</p>
            <a href="{{ route('pricing') }}" class="mt-4 inline-flex min-h-11 items-center border border-gray-900 px-4 py-2 text-sm font-extrabold uppercase">Upgrade</a>
        </div>
    @elseif(! $user->is_verified)
        <div class="mb-6 border border-blue-300 bg-blue-50 p-5 text-blue-900">
            <p class="font-extrabold">Profil sedang menunggu persetujuan Super Admin.</p>
            <p class="mt-1 text-sm">Upload dan penjualan akan tersedia setelah profil disetujui.</p>
        </div>
    @endif

    <div class="space-y-7" x-data="{ latest: {{ $sales->first()?->id ?? 0 }}, newSale: false }" x-init="setInterval(async () => { if (document.hidden) return; try { const r = await fetch('{{ route('fotografer.dashboard.data') }}', { headers: { Accept: 'application/json' } }); if (r.ok) { const d = await r.json(); if (d.latest_id > latest) { latest = d.latest_id; newSale = true; } } } catch (_) {} }, 15000)">
        <div x-cloak x-show="newSale" class="rounded-lg border border-gray-900 bg-gray-900 p-4 text-sm font-semibold text-white">
            Penjualan baru diterima. <button type="button" class="ml-2 underline" @click="window.location.reload()">Perbarui data</button>
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="helper uppercase font-bold tracking-widest">Ringkasan</p>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Selamat datang, {{ $user->name }}</h1>
                <p class="helper mt-2 text-sm text-gray-500">Data penjualan, saldo, dan aktivitas karya Anda.</p>
            </div>
            <a class="button rounded-lg bg-black px-5 py-3 text-sm font-bold text-white transition hover:bg-neutral-800" href="{{ route('fotografer.photos.index') }}">Upload foto</a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm font-semibold text-green-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['PENDAPATAN BERSIH', 'Rp' . number_format($stats['earnings'], 0, ',', '.'), 'Akumulasi transaksi paid (90%)'],
                ['FOTO TERJUAL', number_format($stats['sold']), 'Pembelian paid'],
                ['TOTAL KUNJUNGAN', number_format($stats['visits']), 'Tampilan detail foto'],
                ['RASIO KONVERSI BELI', $stats['conversion'] . '%', 'Pembelian dibagi kunjungan']
            ] as $card)
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold tracking-wider text-gray-500">{{ $card[0] }}</p>
                    <p class="metric mt-4 text-2xl font-black text-gray-900">{{ $card[1] }}</p>
                    <p class="helper mt-2 text-xs text-gray-500">{{ $card[2] }}</p>
                </section>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Grafik Transaksi & Penjualan Realtime Fotografer --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 xl:col-span-2 shadow-sm"
                x-data="{
                    period: 'tanggal',
                    allData: @js($chartData),
                    loading: false,
                    lastUpdated: '{{ $chartData['tanggal']['updated_at'] ?? now()->format('H:i:s') . ' WITA' }}',
                    hoveredIndex: null,
                    get activeData() {
                        return this.allData[this.period] || { points: [], total_count: 0, total_earnings: 0, total_earnings_formatted: 'Rp0', title: '' };
                    },
                    get points() {
                        return this.activeData.points || [];
                    },
                    get maxVal() {
                        if (!this.points.length) return 5;
                        const vals = this.points.map(p => p.count);
                        const m = Math.max(...vals);
                        return m > 0 ? m : 5;
                    },
                    get pointsWithCoords() {
                        const pts = this.points;
                        if (!pts.length) return [];
                        const max = this.maxVal;
                        const w = 620;
                        const h = 140;
                        const padX = 25;
                        const padY = 15;
                        const usableW = w - (padX * 2);
                        const usableH = h - (padY * 2);
                        const step = pts.length > 1 ? usableW / (pts.length - 1) : 0;

                        return pts.map((p, i) => {
                            const x = padX + (i * step);
                            const y = padY + (usableH - ((p.count / max) * usableH));
                            return {
                                ...p,
                                x: Math.round(x * 10) / 10,
                                y: Math.round(y * 10) / 10,
                            };
                        });
                    },
                    get linePath() {
                        const coords = this.pointsWithCoords;
                        if (!coords.length) return '';
                        return coords.map((p, i) => (i === 0 ? `M ${p.x} ${p.y}` : `L ${p.x} ${p.y}`)).join(' ');
                    },
                    get areaPath() {
                        const coords = this.pointsWithCoords;
                        if (!coords.length) return '';
                        const line = this.linePath;
                        const first = coords[0];
                        const last = coords[coords.length - 1];
                        const bottomY = 140 - 15;
                        return `${line} L ${last.x} ${bottomY} L ${first.x} ${bottomY} Z`;
                    },
                    async setPeriod(p) {
                        this.period = p;
                        this.hoveredIndex = null;
                        await this.fetchRealtime(false);
                    },
                    async fetchRealtime(showSpinner = false) {
                        if (showSpinner) this.loading = true;
                        try {
                            const res = await fetch(`{{ route('fotografer.dashboard.chart-data') }}?period=${this.period}`);
                            if (res.ok) {
                                const json = await res.json();
                                this.allData[this.period] = json;
                                this.lastUpdated = json.updated_at;
                            }
                        } catch (e) {
                            console.error('Failed to update fotografer chart:', e);
                        } finally {
                            this.loading = false;
                        }
                    },
                    init() {
                        // Polling otomatis setiap 15 detik untuk update realtime
                        setInterval(() => {
                            this.fetchRealtime(false);
                        }, 15000);
                    }
                }"
            >
                {{-- Card Header --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-5">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-lg font-bold text-gray-900">Grafik Penjualan</h2>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                </span>
                                <span>Realtime</span>
                            </span>
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                            <span>Periode: <strong class="text-gray-900" x-text="activeData.title"></strong></span>
                            <span>&middot;</span>
                            <span>Terjual: <strong class="text-gray-900" x-text="activeData.total_count + ' foto'"></strong></span>
                            <span>&middot;</span>
                            <span>Pendapatan: <strong class="text-gray-900 font-semibold" x-text="activeData.total_earnings_formatted"></strong></span>
                        </div>
                    </div>

                    {{-- Period Selector & Refresh Button --}}
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <div class="inline-flex rounded-lg bg-gray-100 p-1 text-xs font-semibold">
                            <button
                                type="button"
                                @click="setPeriod('hari')"
                                :class="period === 'hari' ? 'bg-black text-white shadow-sm' : 'text-gray-600 hover:text-black'"
                                class="rounded-md px-3 py-1.5 transition-all duration-150"
                            >
                                Hari
                            </button>
                            <button
                                type="button"
                                @click="setPeriod('tanggal')"
                                :class="period === 'tanggal' ? 'bg-black text-white shadow-sm' : 'text-gray-600 hover:text-black'"
                                class="rounded-md px-3 py-1.5 transition-all duration-150"
                            >
                                Tanggal
                            </button>
                            <button
                                type="button"
                                @click="setPeriod('bulan')"
                                :class="period === 'bulan' ? 'bg-black text-white shadow-sm' : 'text-gray-600 hover:text-black'"
                                class="rounded-md px-3 py-1.5 transition-all duration-150"
                            >
                                Bulan
                            </button>
                        </div>

                        <button
                            type="button"
                            @click="fetchRealtime(true)"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-black transition"
                            title="Perbarui Data Sekarang"
                        >
                            <svg class="h-3.5 w-3.5" :class="loading ? 'animate-spin text-black' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Chart Canvas --}}
                <div class="relative mt-5">
                    {{-- Tooltip Hover Box --}}
                    <div
                        x-show="hoveredIndex !== null && pointsWithCoords[hoveredIndex]"
                        x-cloak
                        class="pointer-events-none absolute -top-2 z-20 -translate-x-1/2 -translate-y-full rounded-lg bg-gray-900 px-3 py-1.5 text-xs text-white shadow-xl transition-all duration-75"
                        :style="hoveredIndex !== null && pointsWithCoords[hoveredIndex] ? `left: ${(pointsWithCoords[hoveredIndex].x / 620) * 100}%;` : ''"
                    >
                        <template x-if="hoveredIndex !== null && pointsWithCoords[hoveredIndex]">
                            <div class="text-center font-sans">
                                <p class="font-bold text-gray-200" x-text="pointsWithCoords[hoveredIndex].full_label"></p>
                                <p class="mt-0.5 text-[11px] font-semibold text-emerald-400">
                                    <span x-text="pointsWithCoords[hoveredIndex].count + ' Foto Terjual'"></span>
                                    <span class="text-gray-400">&middot;</span>
                                    <span class="text-white" x-text="'Bagian Anda: ' + pointsWithCoords[hoveredIndex].earnings_formatted"></span>
                                </p>
                            </div>
                        </template>
                        <div class="absolute -bottom-1 left-1/2 h-2 w-2 -translate-x-1/2 rotate-45 bg-gray-900"></div>
                    </div>

                    {{-- SVG Rendering --}}
                    <div class="relative h-44 w-full">
                        <svg viewBox="0 0 620 140" class="h-full w-full overflow-visible" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="fotograferChartGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#101114" stop-opacity="0.18" />
                                    <stop offset="100%" stop-color="#101114" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>

                            {{-- Horizontal Grid Lines --}}
                            <line x1="25" y1="15" x2="595" y2="15" stroke="#F1F3F5" stroke-dasharray="3 3" stroke-width="1" />
                            <line x1="25" y1="70" x2="595" y2="70" stroke="#F1F3F5" stroke-dasharray="3 3" stroke-width="1" />
                            <line x1="25" y1="125" x2="595" y2="125" stroke="#E5E7EB" stroke-width="1" />

                            {{-- Area Fill --}}
                            <path :d="areaPath" fill="url(#fotograferChartGradient)" />

                            {{-- Main Curve Line --}}
                            <path :d="linePath" fill="none" stroke="#101114" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                            {{-- Vertical Hover Guide Line --}}
                            <template x-if="hoveredIndex !== null && pointsWithCoords[hoveredIndex]">
                                <line
                                    :x1="pointsWithCoords[hoveredIndex].x"
                                    y1="15"
                                    :x2="pointsWithCoords[hoveredIndex].x"
                                    y2="125"
                                    stroke="#101114"
                                    stroke-width="1.5"
                                    stroke-dasharray="2 2"
                                />
                            </template>

                            {{-- Points & Targets --}}
                            <template x-for="(pt, idx) in pointsWithCoords" :key="idx">
                                <g>
                                    <circle
                                        :cx="pt.x"
                                        :cy="pt.y"
                                        :r="hoveredIndex === idx ? 5 : (pt.count > 0 ? 3.5 : 2)"
                                        :fill="hoveredIndex === idx ? '#101114' : (pt.count > 0 ? '#101114' : '#9CA3AF')"
                                        :stroke="hoveredIndex === idx ? '#ffffff' : 'none'"
                                        :stroke-width="hoveredIndex === idx ? 2 : 0"
                                        class="transition-all duration-150"
                                    />
                                    <rect
                                        :x="pt.x - 12"
                                        y="0"
                                        width="24"
                                        height="140"
                                        fill="transparent"
                                        class="cursor-pointer"
                                        @mouseenter="hoveredIndex = idx"
                                        @mouseleave="hoveredIndex = null"
                                    />
                                </g>
                            </template>
                        </svg>
                    </div>

                    {{-- X-Axis Labels --}}
                    <div class="mt-2 flex items-center justify-between px-2 text-[10px] font-bold text-gray-400">
                        <template x-if="period === 'hari'">
                            <div class="flex w-full justify-between">
                                <span>00:00</span>
                                <span>04:00</span>
                                <span>08:00</span>
                                <span>12:00</span>
                                <span>16:00</span>
                                <span>20:00</span>
                                <span>23:00</span>
                            </div>
                        </template>
                        <template x-if="period === 'tanggal'">
                            <div class="flex w-full justify-between">
                                <span x-text="points.length ? points[0].label : ''"></span>
                                <span x-text="points.length >= 8 ? points[7].label : ''"></span>
                                <span x-text="points.length >= 15 ? points[14].label : ''"></span>
                                <span x-text="points.length >= 22 ? points[21].label : ''"></span>
                                <span x-text="points.length ? points[points.length - 1].label : ''"></span>
                            </div>
                        </template>
                        <template x-if="period === 'bulan'">
                            <div class="flex w-full justify-between">
                                <template x-for="(pt, idx) in points" :key="idx">
                                    <span x-text="pt.label" :class="idx % 2 === 0 ? 'inline' : 'hidden sm:inline'"></span>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Footer Note --}}
                    <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 text-[11px] text-gray-500">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-block h-2 w-2 rounded-full bg-black"></span>
                            <span>Volume Penjualan Terbayar (90% Bagian Fotografer)</span>
                        </div>
                        <div>
                            <span>Update: <strong class="font-sans text-gray-700" x-text="lastUpdated"></strong></span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Saldo Siap Tarik & Pengajuan Penarikan --}}
            @php($minimumWithdrawal = \App\Models\PlatformSetting::minimumWithdrawal())
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-gray-500">SALDO SIAP TARIK</p>
                <p class="metric mt-3 text-2xl font-black text-gray-900">Rp{{ number_format($stats['balance'], 0, ',', '.') }}</p>
                <p class="helper mt-2 text-xs text-gray-500">Minimum penarikan Rp{{ number_format($minimumWithdrawal, 0, ',', '.') }}.</p>
                @if($stats['balance'] < $minimumWithdrawal)
                    <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800">Saldo belum mencukupi minimum penarikan.</p>
                @endif
                <form class="mt-5 space-y-3" method="POST" action="{{ route('fotografer.withdrawals.store') }}">
                    @csrf
                    <input type="hidden" name="idempotency_key" value="{{ (string) Str::uuid() }}">
                    <input class="w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" type="number" name="jumlah_tarik" min="{{ $minimumWithdrawal }}" max="{{ $stats['balance'] }}" placeholder="Minimal Rp{{ number_format($minimumWithdrawal, 0, ',', '.') }}" required @disabled($stats['balance'] < $minimumWithdrawal)>
                    <select class="w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" name="destination_type" x-data @change="$refs.method.placeholder=$event.target.value==='bank'?'Nama bank':'Provider e-wallet'" @disabled($stats['balance'] < $minimumWithdrawal)>
                        <option value="bank">Bank</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                    <input x-ref="method" class="w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" name="metode_pembayaran" placeholder="Nama bank" required @disabled($stats['balance'] < $minimumWithdrawal)>
                    <input class="w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" name="nomor_tujuan" placeholder="Nomor rekening / telepon" required @disabled($stats['balance'] < $minimumWithdrawal)>
                    <input class="w-full rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black" name="account_holder" placeholder="Nama pemilik" @disabled($stats['balance'] < $minimumWithdrawal)>
                    <button class="w-full rounded-lg bg-black px-4 py-3 text-sm font-bold text-white transition hover:bg-neutral-800 disabled:cursor-not-allowed disabled:bg-gray-300" @disabled($stats['balance'] < $minimumWithdrawal)>
                        Ajukan penarikan
                    </button>
                </form>
            </section>
        </div>

        {{-- Penjualan Terkini --}}
        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Penjualan terkini</h2>
                <a class="text-sm font-bold underline hover:text-gray-700" href="{{ route('fotografer.orders') }}">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs font-bold uppercase text-gray-500">
                        <tr>
                            <th class="p-4 text-left">Order</th>
                            <th class="p-4 text-left">Foto / Event</th>
                            <th class="p-4 text-left">Pembeli</th>
                            <th class="p-4 text-right">Harga</th>
                            <th class="p-4 text-right">Bagian Anda</th>
                            <th class="p-4 text-right">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="p-4 font-bold text-gray-900">{{ $sale->order_number ?? '#'.$sale->id }}</td>
                                <td class="p-4">
                                    <span class="font-medium text-gray-900">{{ $sale->photo?->title }}</span>
                                    <span class="block text-xs text-gray-500">{{ $sale->photo?->event?->nama_event }}</span>
                                </td>
                                <td class="p-4 text-gray-700">{{ $sale->pembeli?->name ?? $sale->pembeli?->email }}</td>
                                <td class="p-4 text-right font-sans text-gray-600">Rp{{ number_format($sale->harga_foto) }}</td>
                                <td class="p-4 text-right font-sans font-bold text-gray-900">Rp{{ number_format($sale->jumlah_fotografer) }}</td>
                                <td class="p-4 text-right text-xs text-gray-500">{{ ($sale->paid_at ?? $sale->created_at)->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="p-8 text-center text-gray-500" colspan="6">Belum ada penjualan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $sales->links() }}
            </div>
        </section>

        {{-- Penarikan Terbaru --}}
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900">Penarikan terbaru</h2>
            <div class="mt-4 grid gap-3">
                @forelse($withdrawals as $withdrawal)
                    <div class="flex flex-wrap items-center justify-between rounded-lg bg-gray-50 p-4">
                        <div>
                            <strong class="font-sans text-base text-gray-900">Rp{{ number_format($withdrawal->jumlah_tarik) }}</strong>
                            <p class="helper text-xs text-gray-500">{{ $withdrawal->metode_pembayaran }} · {{ $withdrawal->created_at->timezone(config('app.display_timezone'))->format('d M Y') }}</p>
                            @if($withdrawal->status === 'rejected')
                                <p class="mt-1 text-sm text-red-700">{{ $withdrawal->admin_notes }}</p>
                            @endif
                        </div>
                        <span class="rounded-full border px-3 py-1 text-xs font-bold uppercase {{ $withdrawal->status === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : ($withdrawal->status === 'pending' ? 'border-amber-200 bg-amber-50 text-amber-800' : 'border-red-200 bg-red-50 text-red-800') }}">
                            {{ $withdrawal->status }}
                        </span>
                    </div>
                @empty
                    <p class="helper text-sm text-gray-500">Belum ada penarikan.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-fg-layout>
