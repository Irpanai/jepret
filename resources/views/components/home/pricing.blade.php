@props(['pricingPlans'])

<section id="pricing" class="border-y border-public-line bg-public-bone py-16 sm:py-24">
    <div class="public-container" data-reveal>
        <div class="grid gap-8 border-b border-public-line pb-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
            <div>
                <p class="public-kicker">Pricing Photographer</p>
                <h2 class="public-heading mt-4">Pilih ruang untuk setiap karya.</h2>
            </div>
            <p class="max-w-xl text-sm font-semibold leading-6 text-public-muted lg:justify-self-end">
                Simpan, kelola, dan jual foto melalui Jepret. Pilih kapasitas yang sesuai dengan aktivitas fotografimu.
            </p>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($pricingPlans as $plan)
                @php
                    $isFeatured = $plan->code === 'creator';
                    $quotaMb = $plan->storageQuotaMb();
                    $storage = $plan->is_custom ? 'Custom Storage' : ($quotaMb >= 1024 ? number_format($quotaMb / 1024, 0).' GB' : number_format($quotaMb, 0).' MB');
                    $price = $plan->is_custom ? 'Custom' : ($plan->harga === 0 ? 'Gratis' : 'Rp'.number_format($plan->harga, 0, ',', '.'));
                    $cta = $plan->is_custom ? 'Hubungi Kami' : ($plan->is_trial ? 'Mulai Gratis' : 'Pilih '.$plan->display_name);
                    $href = $plan->is_custom
                        ? 'https://wa.me/6285156767900?text='.urlencode('Halo Jepret, saya tertarik dengan paket Studio Jepret.')
                        : route('register', ['role' => 'fotografer', 'package' => $plan->code]);
                @endphp
                <article class="flex min-w-0 flex-col border p-5 sm:p-6 {{ $isFeatured ? 'border-public-ink bg-public-ink text-white' : 'border-public-line bg-white text-public-ink' }}">
                    <div class="flex min-h-7 items-start justify-between gap-3">
                        <h3 class="text-xl font-extrabold">{{ $plan->display_name }}</h3>
                    </div>

                    <div class="mt-8 flex min-h-12 flex-wrap items-baseline gap-x-1">
                        <p class="text-3xl font-extrabold sm:text-4xl">{{ $price }}</p>
                        @if($plan->billing_period)
                            <span class="text-xs font-bold {{ $isFeatured ? 'text-white/55' : 'text-public-muted' }}">/ {{ $plan->billing_period }}</span>
                        @endif
                    </div>

                    <p class="mt-3 text-xs font-extrabold uppercase {{ $isFeatured ? 'text-white/55' : 'text-public-muted' }}">{{ $storage }}</p>
                    <p class="mt-5 min-h-[4.5rem] text-sm font-semibold leading-6 {{ $isFeatured ? 'text-white/68' : 'text-public-muted' }}">{{ $plan->description }}</p>

                    <div class="mt-6 border-t pt-5 {{ $isFeatured ? 'border-white/20' : 'border-public-line' }}">
                        <p class="text-xs font-extrabold uppercase {{ $isFeatured ? 'text-white/55' : 'text-public-muted' }}">Termasuk</p>
                        <ul class="mt-4 grid gap-3">
                            @foreach($plan->features ?? [] as $feature)
                                <li class="grid grid-cols-[18px_minmax(0,1fr)] gap-2 text-sm font-semibold leading-5">
                                    <span class="grid h-[18px] w-[18px] place-items-center border text-[10px] {{ $isFeatured ? 'border-white/35 text-white' : 'border-public-ink text-public-ink' }}" aria-hidden="true">+</span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @auth
                        @if($plan->is_custom)
                            <a href="{{ $href }}" class="mt-8 inline-flex min-h-12 w-full items-center justify-center border px-4 text-center text-xs font-extrabold uppercase {{ $isFeatured ? 'border-white bg-white text-public-ink hover:bg-public-bone' : 'border-public-ink bg-public-ink text-white hover:bg-neutral-800' }}">{{ $cta }}</a>
                        @else
                            <form class="mt-8" method="POST" action="{{ route('subscriptions.checkout', $plan) }}">
                                @csrf
                                <button class="inline-flex min-h-12 w-full items-center justify-center border px-4 text-center text-xs font-extrabold uppercase {{ $isFeatured ? 'border-white bg-white text-public-ink hover:bg-public-bone' : 'border-public-ink bg-public-ink text-white hover:bg-neutral-800' }}">{{ $cta }}</button>
                            </form>
                        @endif
                    @else
                        <a href="{{ $href }}" class="mt-8 inline-flex min-h-12 w-full items-center justify-center border px-4 text-center text-xs font-extrabold uppercase {{ $isFeatured ? 'border-white bg-white text-public-ink hover:bg-public-bone' : 'border-public-ink bg-public-ink text-white hover:bg-neutral-800' }}">{{ $cta }}</a>
                    @endauth
                </article>
            @endforeach
        </div>

        <div class="mt-8 flex flex-col gap-4 border-t border-public-line pt-7 sm:flex-row sm:items-center sm:justify-between">
            <p class="max-w-3xl text-sm font-extrabold leading-6 text-public-ink">Tampilkan karya terbaikmu, jangkau lebih banyak buyer, dan kelola penjualan melalui satu platform.</p>
            <a href="{{ route('register', ['role' => 'fotografer']) }}" class="shrink-0 text-xs font-extrabold uppercase text-public-ink hover:underline">Mulai sebagai Photographer →</a>
        </div>
    </div>
</section>

<section class="bg-white py-16 sm:py-24">
    <div class="public-container grid gap-8 border border-public-line bg-public-bone p-6 sm:p-10 lg:grid-cols-[1fr_auto] lg:items-center" data-reveal>
        <div>
            <p class="public-kicker">Untuk photographer</p>
            <h2 class="mt-4 text-4xl font-extrabold leading-none sm:text-6xl">Upload karya. Jual lebih mudah.</h2>
            <p class="mt-5 max-w-2xl text-sm font-semibold leading-6 text-public-muted">
                Kelola event, unggah foto, lindungi preview dengan watermark, lalu jual file original dengan skema revenue 90% photographer dan 10% platform.
            </p>
        </div>
        <a href="{{ route('register', ['role' => 'fotografer']) }}" class="public-button">Mulai sebagai Photographer</a>
    </div>
</section>
