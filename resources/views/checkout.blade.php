<x-marketplace-layout
    title="Checkout | JepretCFD"
    description="Checkout foto JepretCFD."
    :noindex="true"
>
    <section class="bg-white py-10 sm:py-14">
        <div class="public-container max-w-6xl">
            <p class="public-kicker">Checkout</p>
            <h1 class="public-heading mt-3">Konfirmasi pembelian.</h1>

            @if(count($cart) === 0)
                <div class="mt-8 border border-public-line bg-public-bone p-10 text-center">
                    <h2 class="text-2xl font-extrabold text-public-ink">Keranjang masih kosong.</h2>
                    <p class="mt-2 text-sm font-semibold text-public-muted">Pilih foto dari galeri sebelum membuat order pembayaran.</p>
                    <a href="{{ route('galeri') }}" class="public-button mt-6">Buka Galeri</a>
                </div>
            @else
                <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
                    <section class="grid gap-5">
                        <div class="border border-public-line bg-white p-5">
                            <h2 class="text-lg font-extrabold text-public-ink">Buyer identity</h2>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-xs font-extrabold uppercase text-public-muted">Nama</label>
                                    <input type="text" value="{{ Auth::user()->name }}" readonly class="public-input mt-2 min-h-12 px-3 bg-public-bone">
                                </div>
                                <div>
                                    <label class="text-xs font-extrabold uppercase text-public-muted">Email</label>
                                    <input type="email" value="{{ Auth::user()->email }}" readonly class="public-input mt-2 min-h-12 px-3 bg-public-bone">
                                </div>
                            </div>
                        </div>

                        <div class="border border-public-line bg-white p-5">
                            <h2 class="text-lg font-extrabold text-public-ink">Item pesanan</h2>
                            <div class="mt-4 divide-y divide-public-line">
                                @foreach($cart as $item)
                                    <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                                        <div>
                                            <h3 class="text-sm font-extrabold text-public-ink">{{ $item['event'] ?? 'Foto Jepret' }}</h3>
                                            <p class="mt-1 text-xs font-bold text-public-muted">{{ $item['fotografer'] ?? 'Photographer' }}</p>
                                        </div>
                                        <p class="text-sm font-extrabold text-public-ink">Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <aside class="h-max border border-public-line bg-public-bone p-5 lg:sticky lg:top-24">
                        <p class="public-kicker">Order summary</p>
                        <div class="mt-5 grid gap-3 border-y border-public-line py-5 text-sm font-bold">
                            <div class="flex justify-between">
                                <span>Subtotal ({{ count($cart) }} foto)</span>
                                <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Biaya layanan</span>
                                <span>Rp0</span>
                            </div>
                            <div class="flex justify-between pt-3 text-lg font-extrabold">
                                <span>Total</span>
                                <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <form action="{{ route('checkout.process') }}" method="POST" class="mt-5">
                            @csrf
                            <button type="submit" class="public-button w-full">Buat Order Pembayaran</button>
                        </form>
                        <p class="mt-4 text-xs font-semibold leading-5 text-public-muted">Original hanya terbuka setelah pembayaran lunas.</p>
                    </aside>
                </div>
            @endif
        </div>
    </section>
</x-marketplace-layout>
