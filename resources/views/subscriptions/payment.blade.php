<x-marketplace-layout title="Pembayaran Subscription | Jepret" description="Selesaikan pembayaran subscription Photographer." :noindex="true">
    <section class="bg-white py-12">
        <div class="public-container max-w-2xl">
            <div class="border border-public-line p-6 sm:p-8">
                <p class="public-kicker">Subscription Photographer</p>
                <h1 class="mt-3 text-3xl font-extrabold">{{ $order->package_snapshot['name'] }}</h1>
                <p class="mt-3 text-2xl font-extrabold">Rp{{ number_format($order->gross_amount, 0, ',', '.') }}</p>
                <p class="mt-2 text-sm text-public-muted">Order {{ $order->order_id }}</p>

                @if($order->status === 'pending' && $order->qr_content)
                    <canvas id="qris-code" class="mx-auto mt-8 max-w-full" aria-label="QRIS untuk order {{ $order->order_id }}"></canvas>

                    <p class="mt-4 text-center text-sm font-semibold text-public-muted">
                        Scan menggunakan mobile banking atau aplikasi pembayaran QRIS.
                    </p>

                    <p id="payment-expiry" class="mt-2 text-center text-sm font-semibold text-public-muted" aria-live="polite"></p>

                    <div class="mt-4 flex items-center justify-center gap-2 text-sm font-semibold text-public-muted">
                        <span id="payment-spinner" class="size-4 animate-spin rounded-full border-2 border-public-line border-t-public-ink motion-reduce:animate-none" aria-hidden="true"></span>
                        <p id="payment-state" aria-live="polite">Menunggu konfirmasi pembayaran...</p>
                    </div>
                @else
                    <p class="mt-8 text-center text-lg font-bold text-public-ink">
                        Pembayaran {{ $order->status }}.
                    </p>

                    <a href="{{ route('subscriptions.plans') }}" class="public-button public-button-secondary mt-6 w-full">
                        Kembali ke Pilihan Paket
                    </a>
                @endif
            </div>
        </div>
    </section>

    @if($order->status === 'pending' && $order->qr_content)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const state = document.getElementById('payment-state');
            const spinner = document.getElementById('payment-spinner');
            const canvas = document.getElementById('qris-code');

            if (typeof window.QRCode !== 'undefined' && canvas) {
                window.QRCode.toCanvas(
                    canvas,
                    @json($order->qr_content),
                    {
                        width: 280,
                        margin: 1
                    },
                    function (error) {
                        if (error) {
                            console.error('Subscription QRIS render error:', error);
                        }
                    }
                );
            } else {
                console.error('QRCode library tidak tersedia.');
            }

            const expiry = new Date(@json($order->expires_at?->toIso8601String()));
            const expiryLabel = document.getElementById('payment-expiry');

            const updateCountdown = () => {
                const seconds = Math.max(
                    0,
                    Math.floor((expiry.getTime() - Date.now()) / 1000)
                );

                expiryLabel.textContent = seconds > 0
                    ? `Berlaku ${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, '0')} lagi`
                    : 'QRIS telah kedaluwarsa.';
            };

            updateCountdown();
            const countdown = window.setInterval(updateCountdown, 1000);
            const terminalStatuses = ['expired', 'failed', 'cancelled'];
            let poll;
            let requestController;
            let stopped = false;
            let retryDelay = 3000;
            let consecutiveFailures = 0;
            const maximumFailures = 10;

            const stopPolling = () => {
                stopped = true;
                window.clearTimeout(poll);
                window.clearInterval(countdown);
                requestController?.abort();
            };

            const pollPayment = async () => {
                if (stopped) {
                    return;
                }

                if (document.hidden) {
                    poll = window.setTimeout(pollPayment, retryDelay);
                    return;
                }

                try {
                    requestController = new AbortController();
                    const response = await fetch(
                        @json(route('subscriptions.status', $order)),
                        {
                            headers: {
                                Accept: 'application/json'
                            },
                            credentials: 'same-origin',
                            cache: 'no-store',
                            signal: requestController.signal
                        }
                    );

                    if (!response.ok) {
                        throw new Error(`Payment status request failed with HTTP ${response.status}`);
                    }

                    if (!response.headers.get('content-type')?.includes('application/json')) {
                        throw new Error('Payment status response was not JSON');
                    }

                    const data = await response.json();
                    const status = String(data.status ?? '').toLowerCase();

                    state.textContent = status === 'pending'
                        ? 'Menunggu konfirmasi pembayaran...'
                        : `Pembayaran ${status}.`;
                    retryDelay = 3000;
                    consecutiveFailures = 0;

                    if (status === 'paid' && data.redirect) {
                        stopPolling();
                        window.location.assign(data.redirect);
                        return;
                    }

                    if (terminalStatuses.includes(status)) {
                        stopPolling();
                        spinner?.classList.add('hidden');
                        return;
                    }
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    console.error('Subscription payment polling error:', error);
                    state.textContent = 'Koneksi terputus, mencoba kembali';
                    consecutiveFailures += 1;

                    if (consecutiveFailures >= maximumFailures) {
                        stopPolling();
                        spinner?.classList.add('hidden');
                        state.textContent = 'Status belum dapat diperiksa. Silakan muat ulang halaman.';
                        return;
                    }

                    retryDelay = Math.min(retryDelay * 2, 30000);
                } finally {
                    requestController = undefined;
                }

                if (!stopped) {
                    poll = window.setTimeout(pollPayment, retryDelay);
                }
            };

            const resumePolling = () => {
                if (!document.hidden && !stopped && !requestController) {
                    window.clearTimeout(poll);
                    poll = window.setTimeout(pollPayment, 0);
                }
            };

            poll = window.setTimeout(pollPayment, 0);
            document.addEventListener('visibilitychange', resumePolling);
            window.addEventListener('pagehide', stopPolling, { once: true });
        });
    </script>
    @endif
</x-marketplace-layout>
