<x-marketplace-layout title="Siapkan Checkout | JepretCFD" description="Tambahkan foto ke keranjang sebelum checkout." :noindex="true">
    <section class="bg-white py-10 sm:py-16">
        <div class="public-container max-w-4xl">
            <div class="grid border border-public-line md:grid-cols-[minmax(0,1fr)_360px]">
                <div class="protected-photo aspect-[4/3] overflow-hidden bg-public-bone md:aspect-auto">
                    @if($photo->file_watermark)
                        <img src="{{ Storage::url($photo->file_watermark) }}" alt="Preview {{ $photo->title ?: 'foto JepretCFD' }}" class="h-full w-full object-cover blur-[1.5px]">
                    @endif
                </div>
                <div class="p-6 sm:p-8">
                    <p class="public-kicker">Siapkan checkout</p>
                    <h1 class="mt-3 text-3xl font-extrabold text-public-ink">{{ $photo->title ?: ($photo->event?->nama_event ?? 'Foto JepretCFD') }}</h1>
                    <p class="mt-3 text-sm font-semibold text-public-muted">{{ $photo->fotografer?->name ?? 'Photographer' }}</p>
                    <p class="mt-8 text-3xl font-extrabold text-public-ink">Rp{{ number_format($photo->harga, 0, ',', '.') }}</p>
                    <form action="{{ route('cart.store') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="photo_id" value="{{ $photo->id }}">
                        <button class="public-button w-full">Tambah ke Keranjang</button>
                    </form>
                    <a href="{{ route('marketplace.show', $photo) }}" class="public-button public-button-secondary mt-3 w-full">Kembali ke Detail</a>
                    <p class="mt-5 text-xs font-semibold leading-5 text-public-muted">Preview tetap ber-watermark. File original hanya tersedia setelah pembayaran terkonfirmasi.</p>
                </div>
            </div>
        </div>
    </section>
</x-marketplace-layout>
