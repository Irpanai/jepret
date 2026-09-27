<x-marketplace-layout title="Pilih Subscription Photographer | Jepret" description="Pilih paket subscription untuk membuka Creator Center Jepret." :noindex="true">
    <section class="border-b border-public-line bg-white py-12 sm:py-16">
        <div class="public-container">
            <div class="grid gap-8 lg:grid-cols-[1fr_0.55fr] lg:items-end">
                <div>
                    <p class="public-kicker">Subscription Photographer</p>
                    <h1 class="mt-4 max-w-4xl text-4xl font-extrabold leading-none text-public-ink sm:text-6xl">Pilih ruang untuk mulai berkarya.</h1>
                </div>
                <p class="max-w-xl text-sm font-semibold leading-6 text-public-muted lg:justify-self-end">
                    Aktifkan Trial satu kali atau pilih paket berbayar. Creator Center terbuka setelah Trial aktif atau pembayaran dikonfirmasi Midtrans.
                </p>
            </div>

            @if(session('status'))
                <div class="mt-8 border border-public-line bg-public-bone p-4 text-sm font-bold text-public-ink" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-8 border border-public-ink bg-white p-4 text-sm font-bold text-public-ink" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if($pendingOrder)
                <div class="mt-8 flex flex-col gap-4 border border-public-ink bg-public-ink p-5 text-white sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase text-white/55">Pembayaran pending</p>
                        <p class="mt-1 font-bold">{{ $pendingOrder->package_snapshot['name'] }} · Rp{{ number_format($pendingOrder->gross_amount, 0, ',', '.') }}</p>
                    </div>
                    <a href="{{ route('subscriptions.payment', $pendingOrder) }}" class="inline-flex min-h-11 items-center justify-center border border-white bg-white px-5 text-xs font-extrabold uppercase text-public-ink hover:bg-public-bone">Lanjutkan pembayaran</a>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-public-bone py-12 sm:py-16 lg:py-20">
        <div class="public-container">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($pricingPlans as $plan)
                    @php
                        $isCurrent = $user->subscription?->package_id === $plan->id && $user->subscription->isActive();
                        $trialUsed = $plan->is_trial && $user->subscription?->trial_used_at !== null;
                        $quotaMb = $plan->storageQuotaMb();
                        $storage = $plan->is_custom ? 'Sesuai kebutuhan' : ($quotaMb >= 1024 ? number_format($quotaMb / 1024, 0).' GB' : number_format($quotaMb, 0).' MB');
                    @endphp
                    <article class="flex min-w-0 flex-col border border-public-line bg-white p-5 text-public-ink sm:p-6">
                        <div class="flex min-h-7 items-start justify-between gap-3">
                            <h2 class="text-xl font-extrabold">{{ $plan->display_name }}</h2>
                            @if($isCurrent)
                                <span class="border border-public-ink px-2 py-1 text-[0.62rem] font-extrabold uppercase">Aktif</span>
                            @endif
                        </div>

                        <div class="mt-8 flex flex-wrap items-baseline gap-x-1">
                            <p class="text-3xl font-extrabold sm:text-4xl">{{ $plan->is_custom ? 'Custom' : ($plan->harga === 0 ? 'Gratis' : 'Rp'.number_format($plan->harga, 0, ',', '.')) }}</p>
                            @if($plan->duration_days)
                                <span class="text-xs font-bold text-public-muted">/ {{ $plan->duration_days }} hari</span>
                            @endif
                        </div>

                        <p class="mt-3 text-xs font-extrabold uppercase text-public-muted">{{ $storage }} storage</p>
                        <p class="mt-5 min-h-20 text-sm font-semibold leading-6 text-public-muted">{{ $plan->description }}</p>

                        <div class="mt-6 border-t border-public-line pt-5">
                            <p class="text-xs font-extrabold uppercase text-public-muted">Entitlement</p>
                            <ul class="mt-4 grid gap-3">
                                @foreach($plan->features ?? [] as $feature)
                                    <li class="grid grid-cols-[18px_minmax(0,1fr)] gap-2 text-sm font-semibold leading-5">
                                        <span class="grid h-[18px] w-[18px] place-items-center border border-public-ink text-[10px]" aria-hidden="true">+</span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mt-auto pt-8">
                            @if($plan->is_custom)
                                <a href="https://wa.me/6285156767900?text={{ urlencode('Halo Jepret, saya tertarik dengan paket Studio Jepret.') }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-12 w-full items-center justify-center border border-public-ink bg-public-ink px-4 text-center text-xs font-extrabold uppercase text-white">Hubungi Jepret</a>
                            @elseif($trialUsed)
                                <button type="button" disabled class="min-h-12 w-full cursor-not-allowed border border-public-line bg-public-bone px-4 text-xs font-extrabold uppercase text-public-muted">Trial sudah digunakan</button>
                            @else
                                <form method="POST" action="{{ route('subscriptions.checkout', $plan) }}">
                                    @csrf
                                    <button class="inline-flex min-h-12 w-full items-center justify-center border border-public-ink bg-public-ink px-4 text-center text-xs font-extrabold uppercase text-white hover:bg-neutral-800">
                                        {{ $plan->is_trial ? 'Aktifkan Trial' : ($isCurrent ? 'Perpanjang Paket' : 'Pilih Paket') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <p class="mt-8 max-w-3xl text-sm font-semibold leading-6 text-public-muted">
                Paket berbayar aktif hanya setelah pembayaran diverifikasi oleh server Jepret. Menutup atau menyelesaikan halaman pembayaran tidak otomatis mengaktifkan subscription.
            </p>
        </div>
    </section>
</x-marketplace-layout>
