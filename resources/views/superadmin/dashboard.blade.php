<x-superadmin-layout>
    <div class="space-y-7">
        {{-- Header & Export PDF --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Command Center</h1>
                <p class="helper mt-2 text-sm text-gray-500">Ringkasan operasional dari transaksi dan akun nyata.</p>
            </div>
            <a class="button rounded-lg bg-black px-5 py-3 text-sm font-bold text-white transition hover:bg-neutral-800" href="{{ route('superadmin.report', request()->query()) }}">
                Download PDF
            </a>
        </div>

        {{-- 5 Operational Metrics Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach([
                ['GMV', 'Rp' . number_format($metrics['gmv'])],
                ['Platform 10%', 'Rp' . number_format($metrics['platform'])],
                ['Fotografer 90%', 'Rp' . number_format($metrics['photographer'])],
                ['Transaksi paid', number_format($metrics['paid_count'])],
                ['Storage', number_format($storage['used_mb'] / 1024, 2) . ' GB']
            ] as $card)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $card[0] }}</p>
                    <p class="metric mt-3 text-xl font-black text-gray-900">{{ $card[1] }}</p>
                </section>
            @endforeach
        </div>

        {{-- Grafik Transaksi Realtime & Distribusi Lokasi --}}
        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Realtime Transaction Chart Component --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 xl:col-span-2 shadow-sm"
                x-data="{
                    period: 'tanggal',
                    allData: @js($chartData),
                    loading: false,
                    lastUpdated: '{{ $chartData['tanggal']['updated_at'] ?? now()->format('H:i:s') . ' WITA' }}',
                    hoveredIndex: null,
                    get activeData() {
                        return this.allData[this.period] || { points: [], total_count: 0, total_gmv: 0, total_gmv_formatted: 'Rp0', title: '' };
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
                            const res = await fetch(`{{ route('superadmin.chart-data') }}?period=${this.period}`);
                            if (res.ok) {
                                const json = await res.json();
                                this.allData[this.period] = json;
                                this.lastUpdated = json.updated_at;
                            }
                        } catch (e) {
                            console.error('Failed to update realtime chart:', e);
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
                            <h2 class="text-lg font-bold text-gray-900">Grafik Transaksi</h2>
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
                            <span>Total: <strong class="text-gray-900" x-text="activeData.total_count + ' transaksi'"></strong></span>
                            <span>&middot;</span>
                            <span class="font-semibold text-gray-800" x-text="activeData.total_gmv_formatted"></span>
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
                                    <span x-text="pointsWithCoords[hoveredIndex].count + ' Transaksi'"></span>
                                    <span class="text-gray-400">&middot;</span>
                                    <span class="text-white" x-text="pointsWithCoords[hoveredIndex].gmv_formatted"></span>
                                </p>
                            </div>
                        </template>
                        <div class="absolute -bottom-1 left-1/2 h-2 w-2 -translate-x-1/2 rotate-45 bg-gray-900"></div>
                    </div>

                    {{-- SVG Rendering --}}
                    <div class="relative h-44 w-full">
                        <svg viewBox="0 0 620 140" class="h-full w-full overflow-visible" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#101114" stop-opacity="0.18" />
                                    <stop offset="100%" stop-color="#101114" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>

                            {{-- Horizontal Grid Lines --}}
                            <line x1="25" y1="15" x2="595" y2="15" stroke="#F1F3F5" stroke-dasharray="3 3" stroke-width="1" />
                            <line x1="25" y1="70" x2="595" y2="70" stroke="#F1F3F5" stroke-dasharray="3 3" stroke-width="1" />
                            <line x1="25" y1="125" x2="595" y2="125" stroke="#E5E7EB" stroke-width="1" />

                            {{-- Area Fill --}}
                            <path :d="areaPath" fill="url(#chartGradient)" />

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
                            <span>Volume Transaksi Terbayar</span>
                        </div>
                        <div>
                            <span>Update: <strong class="font-sans text-gray-700" x-text="lastUpdated"></strong></span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Distribusi Lokasi --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Distribusi lokasi</h2>
                <div class="mt-5 space-y-3">
                    @forelse($locations as $location)
                        <div class="flex justify-between gap-4 text-sm">
                            <span class="text-gray-700">{{ $location->lokasi }}</span>
                            <strong class="font-bold text-gray-900">{{ $location->sales }} penjualan</strong>
                        </div>
                    @empty
                        <p class="helper text-sm text-gray-500">Tidak ada data.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Antrean Verifikasi & Penarikan --}}
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Antrean verifikasi</h2>
                <div class="mt-4 space-y-3">
                    @forelse($pendingUsers as $user)
                        <a class="flex justify-between items-center rounded-lg bg-gray-50 p-4 text-sm transition hover:bg-gray-100" href="{{ route('superadmin.compliance') }}">
                            <span>
                                <span class="font-bold text-gray-900">{{ $user->name }}</span>
                                <small class="block text-gray-500">{{ $user->email }}</small>
                            </span>
                            <strong class="text-xs font-bold uppercase tracking-wider text-black">Tinjau &rarr;</strong>
                        </a>
                    @empty
                        <p class="helper text-sm text-gray-500">Antrean kosong.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Antrean penarikan</h2>
                <div class="mt-4 space-y-3">
                    @forelse($pendingWithdrawals as $wd)
                        <a class="flex justify-between items-center rounded-lg bg-gray-50 p-4 text-sm transition hover:bg-gray-100" href="{{ route('superadmin.earnings') }}">
                            <span class="font-bold text-gray-900">{{ $wd->fotografer?->name }}</span>
                            <strong class="font-sans text-sm font-bold text-gray-900">Rp{{ number_format($wd->jumlah_tarik) }}</strong>
                        </a>
                    @empty
                        <p class="helper text-sm text-gray-500">Antrean kosong.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Status Integrasi --}}
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900">Status integrasi</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach($integrations as $name => $state)
                    <div class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs font-bold uppercase text-gray-500">{{ $name }}</p>
                        <p class="mt-1 text-sm font-bold text-gray-900">{{ $state ?: 'Not configured' }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-superadmin-layout>
