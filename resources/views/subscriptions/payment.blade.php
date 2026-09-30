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
                    <p class="mt-4 text-center text-sm font-semibold text-public-muted">Scan menggunakan mobile banking atau aplikasi pembayaran QRIS.</p>
                    <p id="payment-expiry" class="mt-2 text-center text-sm font-semibold text-public-muted" aria-live="polite"></p>
                    <p id="payment-state" class="mt-4 text-center text-sm font-semibold text-public-muted" aria-live="polite">Status: {{ $order->status }}</p>
                @else
                    <p class="mt-8 text-center text-lg font-bold text-public-ink">Pembayaran {{ $order->status }}.</p>
                    <a href="{{ route('subscriptions.plans') }}" class="public-button public-button-secondary mt-6 w-full">Kembali ke Pilihan Paket</a>
                @endif
            </div>
        </div>
    </section>

    @if($order->status === 'pending' && $order->qr_content)
    <script>
        const state = document.getElementById('payment-state');
        window.QRCode.toCanvas(document.getElementById('qris-code'), @json($order->qr_content), { width: 280, margin: 1 });
        const expiry = new Date(@json($order->expires_at?->toIso8601String()));
        const expiryLabel = document.getElementById('payment-expiry');
        const updateCountdown = () => {
            const seconds = Math.max(0, Math.floor((expiry.getTime() - Date.now()) / 1000));
            expiryLabel.textContent = seconds > 0
                ? `Berlaku ${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, '0')} lagi`
                : 'QRIS telah kedaluwarsa.';
        };
        updateCountdown();
        window.setInterval(updateCountdown, 1000);
        const poll = window.setInterval(async () => {
            if (document.hidden) {
                return;
            }

            try {
                const response = await fetch(@json(route('subscriptions.status', $order)), { headers: { Accept: 'application/json' } });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                state.textContent = 'Status: ' + data.status;
                if (data.redirect) {
                    window.clearInterval(poll);
                    window.location.assign(data.redirect);
                }
            } catch (_) {
                state.textContent = 'Koneksi terputus, mencoba kembali';
            }
        }, 3000);
    </script>
    @endif
</x-marketplace-layout>
