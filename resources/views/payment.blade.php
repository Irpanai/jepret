<x-marketplace-layout
    title="Payment | Jepret"
    description="Halaman pembayaran Jepret."
    :noindex="true"
>
    <section class="bg-white py-10 sm:py-16">
        <div class="public-container max-w-3xl">
            <div class="border border-public-line bg-white">
                <div class="border-b border-public-line bg-public-ink p-6 text-white sm:p-8">
                    <p class="text-xs font-extrabold uppercase text-white/55">
                        {{ $paymentStatus === 'paid' ? 'Pembayaran Lunas' : 'Menunggu Pembayaran' }}
                    </p>

                    <h1 class="mt-3 text-5xl font-extrabold leading-none">
                        Rp{{ number_format($total, 0, ',', '.') }}
                    </h1>

                    <p class="mt-3 break-all text-sm font-semibold text-white/65">
                        Order {{ $order_id }}
                    </p>
                </div>

                <div class="p-6 text-center sm:p-8">
                    @if($paymentStatus === 'paid')

                        <h2 class="text-2xl font-extrabold text-public-ink">
                            Foto sudah siap diunduh.
                        </h2>

                        <p class="mt-2 text-sm font-semibold text-public-muted">
                            Original file dapat diakses dari halaman Pembelian Saya.
                        </p>

                        <a
                            href="{{ route('checkout.success', ['order' => $order_id]) }}"
                            class="public-button mt-6"
                        >
                            Lihat Hasil Pembelian
                        </a>

                    @elseif($paymentStatus === 'pending')

                        <canvas
                            id="qris-code"
                            class="mx-auto max-w-full"
                            aria-label="QRIS untuk order {{ $order_id }}"
                        ></canvas>

                        <p
                            id="qris-error"
                            class="mt-3 hidden text-sm font-semibold text-red-600"
                        >
                            QRIS gagal ditampilkan. Silakan refresh halaman.
                        </p>

                        <h2 class="mt-5 text-2xl font-extrabold text-public-ink">
                            Scan QRIS untuk membayar.
                        </h2>

                        <p class="mx-auto mt-5 max-w-md text-sm font-semibold leading-6 text-public-muted">
                            Gunakan mobile banking atau aplikasi pembayaran yang mendukung QRIS.
                        </p>

                        <p
                            id="payment-expiry"
                            class="mt-3 text-sm font-semibold text-public-muted"
                            aria-live="polite"
                        ></p>

                        <div class="mt-5 inline-flex items-center gap-2 border border-public-line bg-white px-4 py-3 text-xs font-extrabold uppercase text-public-muted">
                            <span class="size-3 animate-spin rounded-full border-2 border-public-line border-t-public-ink motion-reduce:animate-none" aria-hidden="true"></span>
                            <span
                                id="payment-state"
                                aria-live="polite"
                            >
                                Menunggu konfirmasi pembayaran...
                            </span>
                        </div>

                    @else

                        <h2 class="text-2xl font-extrabold text-public-ink">
                            Pembayaran tidak dapat dilanjutkan.
                        </h2>

                        <p class="mx-auto mt-5 max-w-md text-sm font-semibold leading-6 text-public-muted">
                            Status order: {{ $paymentStatus }}.
                            Kembali ke keranjang untuk membuat pembayaran baru.
                        </p>

                    @endif
                </div>

                <div class="border-t border-public-line bg-public-bone p-5">
                    <a
                        href="{{ route('checkout.page') }}"
                        class="public-button public-button-secondary w-full"
                    >
                        Kembali ke Checkout
                    </a>
                </div>
            </div>
        </div>
    </section>

    @if($paymentStatus === 'pending' && $qrContent)

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const paymentState = document.getElementById('payment-state');
                const expiryLabel = document.getElementById('payment-expiry');
                const qrisCanvas = document.getElementById('qris-code');
                const qrisError = document.getElementById('qris-error');

                const qrContent = @json($qrContent);
                const expiresAt = @json($expiresAt);

                /*
                |--------------------------------------------------------------------------
                | Render QRIS
                |--------------------------------------------------------------------------
                */

                if (
                    typeof window.QRCode !== 'undefined' &&
                    qrisCanvas &&
                    qrContent
                ) {
                    window.QRCode.toCanvas(
                        qrisCanvas,
                        qrContent,
                        {
                            width: 280,
                            margin: 2,
                            errorCorrectionLevel: 'M'
                        },
                        function (error) {
                            if (error) {
                                console.error('QRIS render error:', error);

                                if (qrisError) {
                                    qrisError.classList.remove('hidden');
                                }
                            }
                        }
                    );
                } else {
                    console.error(
                        'QRCode library atau QR content tidak tersedia.'
                    );

                    if (qrisError) {
                        qrisError.classList.remove('hidden');
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | QRIS Expiry Countdown
                |--------------------------------------------------------------------------
                */

                const expiry = new Date(expiresAt);

                const updateCountdown = () => {
                    if (!expiryLabel || Number.isNaN(expiry.getTime())) {
                        return;
                    }

                    const seconds = Math.max(
                        0,
                        Math.floor(
                            (expiry.getTime() - Date.now()) / 1000
                        )
                    );

                    if (seconds > 0) {
                        const minutes = Math.floor(seconds / 60);
                        const remainingSeconds = String(
                            seconds % 60
                        ).padStart(2, '0');

                        expiryLabel.textContent =
                            `Berlaku ${minutes}:${remainingSeconds} lagi`;
                    } else {
                        expiryLabel.textContent =
                            'QRIS telah kedaluwarsa.';
                    }
                };

                updateCountdown();

                const countdown = window.setInterval(
                    updateCountdown,
                    1000
                );

                /*
                |--------------------------------------------------------------------------
                | Payment Status Polling
                |--------------------------------------------------------------------------
                */

                const statusUrl = @json(route('checkout.payment.status', ['order' => $order_id]));
                const terminalStatuses = ['expired', 'failed', 'cancelled'];
                let retryDelay = 3000;
                let paymentPoll;
                let requestController;
                let stopped = false;

                const stopPolling = () => {
                    stopped = true;
                    window.clearTimeout(paymentPoll);
                    window.clearInterval(countdown);
                    requestController?.abort();
                };

                const pollPayment = async () => {
                    if (stopped) {
                        return;
                    }

                    if (document.hidden) {
                        paymentPoll = window.setTimeout(pollPayment, retryDelay);
                        return;
                    }

                    try {
                        requestController = new AbortController();
                        const response = await fetch(statusUrl, {
                            headers: { Accept: 'application/json' },
                            credentials: 'same-origin',
                            cache: 'no-store',
                            signal: requestController.signal,
                        });
                        if (!response.ok) {
                            throw new Error(`Payment status request failed with HTTP ${response.status}`);
                        }

                        const data = await response.json();
                        paymentState.textContent = data.status === 'pending'
                            ? 'Menunggu konfirmasi pembayaran...'
                            : `Pembayaran ${data.status}.`;
                        retryDelay = 3000;

                        if (data.status === 'paid' && data.redirect) {
                            stopPolling();
                            window.location.assign(data.redirect);
                            return;
                        }
                        if (terminalStatuses.includes(data.status)) {
                            stopPolling();
                            window.location.reload();
                            return;
                        }
                    } catch (error) {
                        if (error.name === 'AbortError') {
                            return;
                        }

                        console.error('Payment polling error:', error);
                        paymentState.textContent = 'koneksi terputus, mencoba kembali';
                        retryDelay = Math.min(retryDelay * 2, 30000);
                    } finally {
                        requestController = undefined;
                    }

                    if (!stopped) {
                        paymentPoll = window.setTimeout(pollPayment, retryDelay);
                    }
                };

                paymentPoll = window.setTimeout(pollPayment, retryDelay);
                window.addEventListener('pagehide', stopPolling, { once: true });

            });
        </script>

    @endif

</x-marketplace-layout>
