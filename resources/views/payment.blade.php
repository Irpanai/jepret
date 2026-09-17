<x-marketplace-layout
    title="Payment | JepretCFD"
    description="Halaman pembayaran JepretCFD."
    :noindex="true"
>
    <section class="bg-white py-10 sm:py-16">
        <div class="public-container max-w-3xl">
            <div class="border border-public-line bg-white">
                <div class="border-b border-public-line bg-public-ink p-6 text-white sm:p-8">
                    <p class="text-xs font-extrabold uppercase text-white/55">{{ $paymentStatus === 'paid' ? 'Pembayaran Lunas' : 'Menunggu Pembayaran' }}</p>
                    <h1 class="mt-3 text-5xl font-extrabold leading-none">Rp{{ number_format($total, 0, ',', '.') }}</h1>
                    <p class="mt-3 break-all text-sm font-semibold text-white/65">Order {{ $order_id }}</p>
                </div>

                <div class="p-6 text-center sm:p-8">
                    @if($paymentStatus === 'paid')
                        <h2 class="text-2xl font-extrabold text-public-ink">Foto sudah siap diunduh.</h2>
                        <p class="mt-2 text-sm font-semibold text-public-muted">Original file dapat diakses dari halaman Pembelian Saya.</p>
                        <a href="{{ route('checkout.success', ['order' => $order_id]) }}" class="public-button mt-6">Lihat Hasil Pembelian</a>
                    @else
                        <h2 class="text-2xl font-extrabold text-public-ink">Scan QRIS untuk membayar.</h2>
                        <div class="mx-auto mt-6 inline-block border border-public-line bg-white p-4">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($order_id.'|'.$total) }}" alt="QRIS untuk order {{ $order_id }}" class="h-56 w-56">
                        </div>
                        <p class="mx-auto mt-5 max-w-md text-sm font-semibold leading-6 text-public-muted">
                            Gunakan e-wallet atau mobile banking. Setelah pembayaran dikonfirmasi, file original akan terbuka di riwayat pembelian.
                        </p>
                        <div class="mt-5 inline-flex border border-public-line bg-public-bone px-4 py-3 text-xs font-extrabold uppercase text-public-muted">
                            Status: menunggu konfirmasi pembayaran
                        </div>
                    @endif
                </div>

                <div class="border-t border-public-line bg-public-bone p-5">
                    @if($paymentStatus !== 'paid' && app()->environment('local'))
                        <form action="{{ route('checkout.payment.simulate', ['order' => $order_id]) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="public-button w-full">Tandai Paid (Local)</button>
                        </form>
                    @endif
                    <a href="{{ route('galeri') }}" class="public-button public-button-secondary w-full">Kembali ke Galeri</a>
                </div>
            </div>
        </div>
    </section>
</x-marketplace-layout>
