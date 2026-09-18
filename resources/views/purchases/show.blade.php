<x-marketplace-layout title="Detail Pembelian | JepretCFD" description="Detail order JepretCFD." :noindex="true">
    <section class="bg-white py-10 sm:py-14">
        <div class="public-container max-w-5xl">
            <a href="{{ route('purchases.index') }}" class="text-xs font-extrabold uppercase text-public-muted hover:text-public-ink">← Pembelian Saya</a>
            <header class="mt-6 grid gap-6 border border-public-line bg-public-bone p-6 sm:p-8 md:grid-cols-[1fr_auto] md:items-end">
                <div><p class="public-kicker">Order detail</p><h1 class="mt-3 break-all text-3xl font-extrabold text-public-ink sm:text-4xl">{{ $order['order_number'] }}</h1><p class="mt-3 text-sm font-semibold text-public-muted">{{ optional($order['created_at'])->format('d M Y, H:i') }} · {{ $order['count'] }} foto</p></div>
                <div class="md:text-right"><span class="inline-flex border border-public-line bg-white px-3 py-2 text-xs font-extrabold uppercase text-public-muted">{{ $order['status'] }}</span><p class="mt-3 text-2xl font-extrabold text-public-ink">Rp{{ number_format($order['total'], 0, ',', '.') }}</p></div>
            </header>
            <div class="border-x border-b border-public-line p-5 sm:p-8"><div class="divide-y divide-public-line">
                @foreach($transactions as $transaction)
                    <article class="grid gap-4 py-5 first:pt-0 last:pb-0 sm:grid-cols-[112px_minmax(0,1fr)_auto] sm:items-center">
                        <div class="aspect-[4/3] overflow-hidden bg-gray-100">@if($transaction->photo)<img src="{{ $order['status'] === 'paid' ? route('purchases.preview', ['order' => $order['order_number'], 'transaction' => $transaction]) : route('media.preview', $transaction->photo) }}" alt="Preview {{ $transaction->photo->title ?: 'foto' }}" class="h-full w-full object-cover">@endif</div>
                        <div><h2 class="text-sm font-extrabold text-public-ink">{{ $transaction->photo?->title ?: ($transaction->photo?->event?->nama_event ?? 'Foto JepretCFD') }}</h2><p class="mt-1 text-xs font-bold text-public-muted">{{ $transaction->photo?->fotografer?->name ?? 'Photographer' }}</p><p class="mt-2 text-sm font-extrabold text-public-ink">Rp{{ number_format($transaction->total_bayar, 0, ',', '.') }}</p></div>
                        @if($order['status'] === 'paid')<a href="{{ route('purchases.download', ['order' => $order['order_number'], 'transaction' => $transaction]) }}" class="public-button">Download File Pembelian</a>@else<span class="border border-public-line bg-public-bone px-4 py-3 text-center text-xs font-extrabold uppercase text-public-muted">Belum tersedia</span>@endif
                    </article>
                @endforeach
            </div></div>
        </div>
    </section>
</x-marketplace-layout>
