<x-marketplace-layout title="Pembayaran Berhasil | JepretCFD" description="Pembayaran JepretCFD berhasil." :noindex="true">
    <section class="bg-white py-10 sm:py-16">
        <div class="public-container max-w-5xl">
            <div class="border border-public-line">
                <header class="grid gap-6 bg-public-ink p-6 text-white sm:p-8 lg:grid-cols-[1fr_auto] lg:items-end">
                    <div>
                        <p class="text-xs font-extrabold uppercase text-white/55">Pembayaran berhasil</p>
                        <h1 class="mt-3 text-4xl font-extrabold leading-none sm:text-5xl">Original siap diunduh.</h1>
                        <p class="mt-4 break-all text-sm font-semibold text-white/65">Order {{ $order['order_number'] }}</p>
                    </div>
                    <div class="border-l border-white/20 pl-5">
                        <p class="text-xs font-extrabold uppercase text-white/55">Total dibayar</p>
                        <p class="mt-2 text-2xl font-extrabold">Rp{{ number_format($order['total'], 0, ',', '.') }}</p>
                    </div>
                </header>
                <div class="divide-y divide-public-line p-5 sm:p-8">
                    @foreach($transactions as $transaction)
                        <article class="grid gap-4 py-5 first:pt-0 last:pb-0 sm:grid-cols-[112px_minmax(0,1fr)_auto] sm:items-center">
                            <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                                @if($transaction->photo?->purchased_path)
                                    <img src="{{ route('purchases.preview', ['order' => $order['order_number'], 'transaction' => $transaction]) }}" alt="Preview {{ $transaction->photo->title ?: 'foto' }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-public-ink">{{ $transaction->photo?->title ?: ($transaction->photo?->event?->nama_event ?? 'Foto JepretCFD') }}</h2>
                                <p class="mt-1 text-xs font-bold text-public-muted">{{ $transaction->photo?->fotografer?->name ?? 'Photographer' }}</p>
                            </div>
                            <a href="{{ route('purchases.download', ['order' => $order['order_number'], 'transaction' => $transaction]) }}" class="public-button">Download File Pembelian</a>
                        </article>
                    @endforeach
                </div>
            </div>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('purchases.index') }}" class="public-button">Pembelian Saya</a>
                <a href="{{ route('galeri') }}" class="public-button public-button-secondary">Kembali ke Galeri</a>
            </div>
        </div>
    </section>
</x-marketplace-layout>
