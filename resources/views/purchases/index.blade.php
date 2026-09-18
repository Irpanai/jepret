<x-marketplace-layout title="Pembelian Saya | JepretCFD" description="Riwayat pembelian foto JepretCFD." :noindex="true">
    <section class="bg-white py-10 sm:py-14">
        <div class="public-container">
            <div class="grid gap-6 border-b border-public-line pb-8 lg:grid-cols-[1fr_auto] lg:items-end">
                <div><p class="public-kicker">Library</p><h1 class="public-heading mt-3">Pembelian Saya</h1><p class="mt-3 max-w-2xl text-sm font-semibold leading-6 text-public-muted">Riwayat order dan akses unduh ulang untuk foto yang sudah dibayar.</p></div>
                @php($filters = ['' => 'Semua', 'paid' => 'Paid', 'pending' => 'Pending', 'failed' => 'Failed', 'expired' => 'Expired'])
                <nav class="flex flex-wrap gap-2" aria-label="Filter status pembelian">
                    @foreach($filters as $value => $label)
                        <a href="{{ route('purchases.index', $value === '' ? [] : ['status' => $value]) }}" class="border px-3 py-2 text-xs font-extrabold uppercase {{ $status === $value || ($status === '' && $value === '') ? 'border-public-ink bg-public-ink text-white' : 'border-public-line bg-white text-public-muted hover:border-public-ink hover:text-public-ink' }}">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>
            <div class="mt-8 border border-public-line">
                @forelse($orders as $order)
                    @php($firstItem = $order['items']->first())
                    <article class="grid gap-5 border-b border-public-line p-5 last:border-b-0 md:grid-cols-[96px_minmax(0,1fr)_auto] md:items-center">
                        <div class="aspect-square overflow-hidden bg-gray-100">
                            @if($firstItem?->photo)
                                <img src="{{ $order['status'] === 'paid' ? route('purchases.preview', ['order' => $order['order_number'], 'transaction' => $firstItem]) : route('media.preview', $firstItem->photo) }}" alt="Preview {{ $firstItem->photo->title ?: 'foto' }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3"><h2 class="break-all font-mono text-sm font-extrabold text-public-ink">{{ $order['order_number'] }}</h2><span class="border border-public-line bg-public-bone px-2 py-1 text-[10px] font-extrabold uppercase text-public-muted">{{ $order['status'] }}</span></div>
                            <p class="mt-2 text-sm font-extrabold text-public-ink">{{ $firstItem?->photo?->title ?: ($firstItem?->photo?->event?->nama_event ?? 'Foto JepretCFD') }}</p>
                            <p class="mt-1 text-xs font-bold text-public-muted">{{ $order['count'] }} foto · {{ optional($order['created_at'])->format('d M Y, H:i') }} · Rp{{ number_format($order['total'], 0, ',', '.') }}</p>
                        </div>
                        <a href="{{ route('purchases.show', ['order' => $order['order_number']]) }}" class="public-button public-button-secondary">Lihat Order</a>
                    </article>
                @empty
                    <div class="p-10 text-center"><h2 class="text-xl font-extrabold text-public-ink">Belum ada pembelian.</h2><p class="mt-2 text-sm font-semibold text-public-muted">Foto yang dibeli akan tersimpan di sini.</p><a href="{{ route('galeri') }}" class="public-button mt-6">Buka Galeri</a></div>
                @endforelse
            </div>
        </div>
    </section>
</x-marketplace-layout>
