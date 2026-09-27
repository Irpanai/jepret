<x-marketplace-layout title="Pembayaran Subscription | Jepret" description="Selesaikan pembayaran subscription Photographer." :noindex="true">
    <section class="bg-white py-12">
        <div class="public-container max-w-2xl">
            <div class="border border-public-line p-6 sm:p-8">
                <p class="public-kicker">Subscription Photographer</p>
                <h1 class="mt-3 text-3xl font-extrabold">{{ $order->package_snapshot['name'] }}</h1>
                <p class="mt-3 text-2xl font-extrabold">Rp{{ number_format($order->gross_amount, 0, ',', '.') }}</p>
                <p class="mt-2 text-sm text-public-muted">Order {{ $order->order_id }}</p>
                <button id="pay-subscription" class="public-button mt-8 w-full" type="button">Pilih Metode Pembayaran</button>
                <p id="payment-state" class="mt-4 text-center text-sm font-semibold text-public-muted">Status: {{ $order->status }}</p>
            </div>
        </div>
    </section>

    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        const state = document.getElementById('payment-state');
        document.getElementById('pay-subscription').addEventListener('click', () => window.snap.pay(@json($order->snap_token)));
        const poll = window.setInterval(async () => {
            const response = await fetch(@json(route('subscriptions.status', $order)), { headers: { Accept: 'application/json' } });
            const data = await response.json();
            state.textContent = 'Status: ' + data.status;
            if (data.redirect) {
                window.clearInterval(poll);
                window.location.assign(data.redirect);
            }
        }, 3000);
    </script>
</x-marketplace-layout>
