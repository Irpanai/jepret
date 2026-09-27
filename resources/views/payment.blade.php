<x-marketplace-layout
    title="Payment | Jepret"
    description="Halaman pembayaran Jepret."
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
                    @elseif($paymentStatus === 'pending')
                        <div class="mx-auto size-10 animate-spin rounded-full border-4 border-public-line border-t-public-ink" aria-hidden="true"></div>
                        <h2 class="mt-5 text-2xl font-extrabold text-public-ink">Membuka pembayaran Midtrans.</h2>
                        <p class="mx-auto mt-5 max-w-md text-sm font-semibold leading-6 text-public-muted">
                            Pilih QRIS, transfer bank, e-wallet, atau metode lain melalui jendela pembayaran yang terbuka otomatis.
                        </p>
                        <div class="mt-5 inline-flex border border-public-line bg-public-bone px-4 py-3 text-xs font-extrabold uppercase text-public-muted">
                            Status: <span id="payment-state" class="ml-1">{{ $paymentStatus }}</span>
                        </div>
                    @else
                        <h2 class="text-2xl font-extrabold text-public-ink">Pembayaran tidak dapat dilanjutkan.</h2>
                        <p class="mx-auto mt-5 max-w-md text-sm font-semibold leading-6 text-public-muted">Status order: {{ $paymentStatus }}. Kembali ke keranjang untuk membuat pembayaran baru.</p>
                    @endif
                </div>

                <div class="border-t border-public-line bg-public-bone p-5">
                    <a href="{{ route('checkout.page') }}" class="public-button public-button-secondary w-full">Kembali ke Checkout</a>
                </div>
            </div>
        </div>
    </section>

    @if($paymentStatus === 'pending' && $snapToken)
        <script src="{{ $snapIsProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $snapClientKey }}"></script>
        <script>
            const paymentState = document.getElementById('payment-state');
            const successUrl = @json(route('checkout.success', ['order' => $order_id]));

            window.snap.pay(@json($snapToken), {
                onSuccess: () => paymentState.textContent = 'memverifikasi pembayaran',
                onPending: () => paymentState.textContent = 'menunggu pembayaran',
                onError: () => paymentState.textContent = 'pembayaran gagal',
                onClose: () => paymentState.textContent = 'jendela pembayaran ditutup',
            });

            const paymentPoll = window.setInterval(async () => {
                const response = await fetch(@json(route('checkout.payment.status', ['order' => $order_id])), {
                    headers: { Accept: 'application/json' },
                });
                const data = await response.json();
                paymentState.textContent = data.status;

                if (data.status === 'paid') {
                    window.clearInterval(paymentPoll);
                    window.location.assign(successUrl);
                }
            }, 3000);
        </script>
    @endif
</x-marketplace-layout>
