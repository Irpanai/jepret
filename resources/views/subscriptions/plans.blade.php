<x-marketplace-layout title="Pilih Subscription Photographer | Jepret" description="Pilih paket subscription untuk membuka Creator Center Jepret." :noindex="true">
    <section class="border-b border-public-line bg-white py-12 sm:py-16">
        <div class="public-container">
            <div class="grid gap-8 lg:grid-cols-[1fr_0.55fr] lg:items-end">
                <div>
                    <p class="public-kicker">Subscription Photographer</p>
                    <h1 class="mt-4 max-w-4xl text-4xl font-extrabold leading-none text-public-ink sm:text-6xl">Pilih ruang untuk mulai berkarya.</h1>
                </div>
                <p class="max-w-xl text-sm font-semibold leading-6 text-public-muted lg:justify-self-end">
                    Aktifkan Trial satu kali atau pilih paket berbayar. Creator Center terbuka setelah Trial aktif atau pembayaran dikonfirmasi DOKU.
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

    <section class="bg-public-bone py-12 sm:py-16 lg:py-20" x-data="subscriptionPlansPage({
        hasActiveSubscription: @js((bool) $user->subscription?->isActive()),
        currentPackageName: @js($user->subscription?->packageDisplayName($user->package) ?? ''),
        currentEndsAt: @js($user->subscription?->formattedEndsAtWita() ?? ''),
        currentRemainingDays: @js(intdiv($user->subscription?->remainingSeconds() ?? 0, 86400)),
        currentStorage: @js($user->formattedQuota())
    })">
        <div class="public-container">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($pricingPlans as $plan)
                    @php
                        $isCurrent = $user->subscription?->package_id === $plan->id && $user->subscription->isActive();
                        $isPlanChange = $user->subscription?->isActive() && ! $isCurrent && ! $plan->is_trial && ! $plan->is_custom;
                        $trialUsed = $plan->is_trial && $user->subscription?->trial_used_at !== null;
                        $isTrialClaimWhileActive = $plan->is_trial && $user->subscription?->isActive() && ! $trialUsed;
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
                                <form method="POST" action="{{ route('subscriptions.checkout', $plan) }}" x-data="subscriptionVoucher({{ $plan->id }})" @submit="requestConfirmation($event, {
                                    id: {{ $plan->id }},
                                    name: @js($plan->display_name),
                                    duration: {{ (int) $plan->duration_days }},
                                    storage: @js($plan->formattedStorageQuota()),
                                    price: @js($plan->harga === 0 ? 'Gratis' : 'Rp' . number_format($plan->harga, 0, ',', '.')),
                                    isTrial: @js((bool) $plan->is_trial),
                                    isCurrent: @js((bool) $isCurrent)
                                })">
                                    @csrf
                                    @unless($plan->is_trial)
                                        <label class="mb-2 block text-xs font-extrabold uppercase text-public-muted">Kode voucher</label>
                                        <div class="mb-2 flex gap-2"><input x-model="code" class="public-input min-h-11 min-w-0 flex-1 px-3 uppercase" maxlength="50"><button type="button" @click="apply" :disabled="loading" class="min-h-11 border border-public-ink px-3 text-xs font-extrabold uppercase disabled:opacity-50">Pakai</button></div>
                                        <input type="hidden" name="voucher_code" :value="appliedCode">
                                        <p x-show="message" x-text="message" :class="discount ? 'text-emerald-700' : 'text-red-700'" class="mb-3 text-xs font-semibold" role="status"></p>
                                    @endunless
                                    @if($isPlanChange)
                                        <p class="mb-3 border-l-2 border-public-ink pl-3 text-xs font-semibold leading-5 text-public-muted">
                                            Paket baru aktif segera setelah pembayaran dan masa aktifnya ditambahkan dari tanggal berakhir saat ini.
                                        </p>
                                    @elseif($isTrialClaimWhileActive)
                                        <p class="mb-3 border-l-2 border-emerald-600 bg-emerald-50 p-2 text-xs font-semibold leading-5 text-emerald-900">
                                            Bonus Trial: Menambah 7 hari masa aktif dan 500 MB storage ke paket aktif Anda.
                                        </p>
                                    @endif
                                    <button class="inline-flex min-h-12 w-full items-center justify-center border border-public-ink bg-public-ink px-4 text-center text-xs font-extrabold uppercase text-white hover:bg-neutral-800">
                                        {{ $plan->is_trial ? ($isTrialClaimWhileActive ? 'Klaim Bonus Trial' : 'Aktifkan Trial') : ($isCurrent ? 'Perpanjang Paket' : 'Pilih Paket') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- MODAL KONFIRMASI 2-TAHAP UNTUK PERPANJANG / PEMBELIAN SAAT SUBSCRIPTION AKTIF -->
            <div
                x-cloak
                x-show="modalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-sm"
                @keydown.escape.window="closeModal()"
            >
                <div
                    class="relative w-full max-w-lg border-2 border-public-ink bg-white p-6 shadow-2xl sm:p-8"
                    @click.away="closeModal()"
                >
                    <!-- STEP 1: Rincian Akumulasi -->
                    <template x-if="modalStep === 1">
                        <div>
                            <div class="flex items-center justify-between border-b border-public-line pb-3">
                                <span class="text-[0.68rem] font-black uppercase tracking-wider text-public-muted">Konfirmasi Perpanjangan · Langkah 1 dari 2</span>
                                <button type="button" @click="closeModal()" class="text-sm font-bold text-gray-400 hover:text-public-ink">✕</button>
                            </div>

                            <h3 class="mt-4 text-2xl font-black text-public-ink" x-text="targetPlan.isTrial ? 'Klaim Bonus Paket Trial' : (targetPlan.isCurrent ? 'Perpanjang Paket Subscription' : 'Pilih Paket Subscription Baru')"></h3>

                            <p class="mt-2 text-sm text-public-muted">
                                Subscription fotografer Anda saat ini masih aktif. Pembelian paket ini akan <strong>menambahkan masa aktif dan kapasitas penyimpanan secara akumulatif</strong>.
                            </p>

                            <div class="mt-5 space-y-3">
                                <div class="border border-public-line bg-public-bone p-4">
                                    <p class="text-xs font-black uppercase tracking-wider text-public-muted">Kondisi Paket Anda Saat Ini</p>
                                    <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                        <div><span class="text-xs text-gray-500">Paket:</span> <p class="font-bold text-public-ink" x-text="currentPackageName"></p></div>
                                        <div><span class="text-xs text-gray-500">Kapasitas Storage:</span> <p class="font-bold text-public-ink" x-text="currentStorage"></p></div>
                                        <div class="col-span-2"><span class="text-xs text-gray-500">Berakhir pada:</span> <p class="font-bold text-public-ink" x-text="currentEndsAt + ' (' + currentRemainingDays + ' hari lagi)'"></p></div>
                                    </div>
                                </div>

                                <div class="border-2 border-public-ink bg-white p-4">
                                    <p class="text-xs font-black uppercase tracking-wider text-public-ink">Yang Akan Diperoleh dari <span x-text="targetPlan.name"></span></p>
                                    <div class="mt-2 space-y-1.5 text-sm">
                                        <p class="flex items-center justify-between">
                                            <span class="text-gray-600">Tambahan Masa Aktif:</span>
                                            <span class="font-black text-emerald-700" x-text="'+' + targetPlan.duration + ' Hari (Diakumulasikan)'"></span>
                                        </p>
                                        <p class="flex items-center justify-between">
                                            <span class="text-gray-600">Tambahan Kapasitas:</span>
                                            <span class="font-black text-emerald-700" x-text="'+' + targetPlan.storage + ' (Diakumulasikan)'"></span>
                                        </p>
                                        <p class="flex items-center justify-between border-t border-gray-100 pt-1.5">
                                            <span class="text-gray-600">Biaya / Harga:</span>
                                            <span class="font-black text-public-ink" x-text="targetPlan.price"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                                <button type="button" @click="closeModal()" class="inline-flex min-h-11 items-center justify-center border border-gray-300 px-5 text-xs font-extrabold uppercase text-gray-700 hover:bg-gray-50">
                                    Batal
                                </button>
                                <button type="button" @click="goToStep2()" class="inline-flex min-h-11 items-center justify-center border border-public-ink bg-public-ink px-5 text-xs font-extrabold uppercase text-white hover:bg-neutral-800">
                                    Lanjut ke Konfirmasi Akhir →
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- STEP 2: Kepastian Final -->
                    <template x-if="modalStep === 2">
                        <div>
                            <div class="flex items-center justify-between border-b border-public-line pb-3">
                                <span class="text-[0.68rem] font-black uppercase tracking-wider text-red-600">Kepastian Final · Langkah 2 dari 2</span>
                                <button type="button" @click="closeModal()" class="text-sm font-bold text-gray-400 hover:text-public-ink">✕</button>
                            </div>

                            <h3 class="mt-4 text-2xl font-black text-public-ink">Apakah Anda Benar-benar Yakin?</h3>

                            <p class="mt-2 text-sm text-gray-600">
                                Harap pastikan kembali pilihan Anda sebelum melanjutkan. Setelah konfirmasi, sistem akan langsung mengarahkan Anda ke pembayaran QRIS DOKU (atau aktivasi instan untuk paket Trial).
                            </p>

                            <div class="mt-4 border-l-4 border-emerald-600 bg-emerald-50 p-4 text-sm text-emerald-950">
                                <p class="font-extrabold">Jaminan Akumulasi:</p>
                                <p class="mt-1 text-xs leading-5">
                                    Masa aktif paket Anda yang tersisa <strong>tidak akan hangus</strong>, dan kapasitas penyimpanan yang telah Anda miliki <strong>tidak akan terhapus atau ditimpa</strong>.
                                </p>
                            </div>

                            <label class="mt-5 flex cursor-pointer items-start gap-3 border border-public-line bg-public-bone p-4">
                                <input type="checkbox" x-model="confirmedAgreement" class="mt-0.5 h-4 w-4 rounded border-gray-400 text-public-ink focus:ring-public-ink">
                                <span class="text-xs font-bold leading-5 text-public-ink">
                                    Saya telah memeriksa dan menyetujui bahwa masa aktif (+<span x-text="targetPlan.duration"></span> hari) dan storage (+<span x-text="targetPlan.storage"></span>) akan diakumulasikan ke akun saya.
                                </span>
                            </label>

                            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                                <button type="button" @click="backToStep1()" class="inline-flex min-h-11 items-center justify-center border border-gray-300 px-5 text-xs font-extrabold uppercase text-gray-700 hover:bg-gray-50">
                                    ← Kembali
                                </button>
                                <button
                                    type="button"
                                    @click="submitCheckout()"
                                    :disabled="!confirmedAgreement"
                                    class="inline-flex min-h-11 items-center justify-center border border-public-ink bg-public-ink px-5 text-xs font-extrabold uppercase text-white hover:bg-neutral-800 disabled:cursor-not-allowed disabled:opacity-40"
                                >
                                    Ya, Saya Yakin & Lanjutkan
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <p class="mt-8 max-w-3xl text-sm font-semibold leading-6 text-public-muted">
                Paket berbayar aktif hanya setelah pembayaran diverifikasi oleh server Jepret. Menutup atau menyelesaikan halaman pembayaran tidak otomatis mengaktifkan subscription.
            </p>
        </div>
    </section>
</x-marketplace-layout>

<script>
    function subscriptionPlansPage(initialContext) {
        return {
            hasActiveSubscription: initialContext.hasActiveSubscription,
            currentPackageName: initialContext.currentPackageName,
            currentEndsAt: initialContext.currentEndsAt,
            currentRemainingDays: initialContext.currentRemainingDays,
            currentStorage: initialContext.currentStorage,
            modalOpen: false,
            modalStep: 1,
            targetForm: null,
            confirmedAgreement: false,
            targetPlan: {
                id: 0,
                name: '',
                duration: 0,
                storage: '',
                price: '',
                isTrial: false,
                isCurrent: false
            },
            requestConfirmation(event, planData) {
                if (!this.hasActiveSubscription) {
                    return true;
                }

                event.preventDefault();
                this.targetForm = event.target;
                this.targetPlan = planData;
                this.modalStep = 1;
                this.confirmedAgreement = false;
                this.modalOpen = true;
            },
            goToStep2() {
                this.modalStep = 2;
            },
            backToStep1() {
                this.modalStep = 1;
            },
            closeModal() {
                this.modalOpen = false;
                this.targetForm = null;
            },
            submitCheckout() {
                if (!this.confirmedAgreement || !this.targetForm) {
                    return;
                }
                const formToSubmit = this.targetForm;
                this.modalOpen = false;
                this.targetForm = null;
                formToSubmit.submit();
            }
        };
    }

    function subscriptionVoucher(packageId) {
        return {
            code: '',
            appliedCode: '',
            discount: 0,
            loading: false,
            message: '',
            async apply() {
                this.loading = true;
                this.message = '';
                try {
                    const response = await fetch(@json(route('vouchers.preview', absolute: false)), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            voucher_code: this.code,
                            scope: 'subscription',
                            package_id: packageId
                        })
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.errors?.voucher_code?.[0] ?? 'Voucher tidak dapat digunakan.');
                    }
                    this.appliedCode = data.code;
                    this.discount = data.discount;
                    this.message = `${data.code}: hemat Rp${new Intl.NumberFormat('id-ID').format(data.discount)}, bayar Rp${new Intl.NumberFormat('id-ID').format(data.final)}`;
                } catch (error) {
                    this.appliedCode = '';
                    this.discount = 0;
                    this.message = error.message;
                } finally {
                    this.loading = false;
                }
            }
        };
    }
</script>
