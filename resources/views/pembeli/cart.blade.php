<x-marketplace-layout
    title="Keranjang | Jepret"
    description="Keranjang foto Jepret."
    :noindex="true"
>
    <section class="bg-white py-10 sm:py-14">
        <div class="public-container">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="public-kicker">Cart</p>
                    <h1 class="public-heading mt-3">Keranjang.</h1>
                </div>
                <a href="{{ route('galeri') }}" class="public-button public-button-secondary">Tambah Foto</a>
            </div>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
                <section class="border border-public-line bg-white">
                    @forelse($cart as $item)
                        <div class="grid gap-4 border-b border-public-line p-4 last:border-b-0 sm:grid-cols-[96px_1fr_auto] sm:items-center">
                            <div class="relative aspect-[4/5] overflow-hidden bg-gray-100 sm:aspect-[4/5]">
                                <img src="{{ route('media.preview', $item['id']) }}" alt="" class="h-full w-full object-cover">
                            </div>
                            <div>
                                <h2 class="text-lg font-extrabold text-public-ink">{{ $item['event'] }}</h2>
                                <p class="mt-1 text-sm font-semibold text-public-muted">{{ $item['fotografer'] }}</p>
                                <p class="mt-4 text-xl font-extrabold text-public-ink">Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <form method="POST" action="{{ route('cart.destroy') }}">
                                @csrf
                                <input type="hidden" name="photo_id" value="{{ $item['id'] }}">
                                <button class="border border-public-line px-4 py-3 text-xs font-extrabold uppercase text-public-muted hover:border-public-ink hover:text-public-ink">Hapus</button>
                            </form>
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <h2 class="text-2xl font-extrabold text-public-ink">Keranjang masih kosong.</h2>
                            <p class="mt-2 text-sm font-semibold text-public-muted">Pilih foto dari galeri sebelum lanjut checkout.</p>
                            <a href="{{ route('galeri') }}" class="public-button mt-6">Buka Galeri</a>
                        </div>
                    @endforelse
                </section>

                <aside class="h-max border border-public-line bg-public-bone p-5 lg:sticky lg:top-24">
                    <p class="public-kicker">Ringkasan order</p>
                    <div class="mt-6 grid gap-3 border-y border-public-line py-5 text-sm font-bold">
                        <div class="flex justify-between">
                            <span>{{ count($cart) }} foto</span>
                            <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-extrabold">
                            <span>Total</span>
                            <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('checkout.page') }}" class="public-button mt-5 w-full {{ count($cart) === 0 ? 'pointer-events-none opacity-50' : '' }}">Lanjut ke Checkout</a>
                </aside>
            </div>
        </div>
    </section>
</x-marketplace-layout>
