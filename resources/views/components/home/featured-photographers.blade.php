@props(['photographers'])

@if($photographers && $photographers->isNotEmpty())
    <section class="border-b border-public-line bg-public-bone py-16 sm:py-24" aria-labelledby="featured-photographers-heading" data-reveal>
        <div class="public-container">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="public-kicker">Kreator Terverifikasi</p>
                    <h2 id="featured-photographers-heading" class="landing-heading mt-2">
                        Fotografer <span class="font-display italic">Pilihan.</span>
                    </h2>
                </div>
                <a href="{{ route('photographers.index') }}" class="landing-link text-xs font-extrabold uppercase tracking-wider text-public-ink">
                    Lihat Semua Photographer &rarr;
                </a>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($photographers as $photographer)
                    <x-public.photographer-card :photographer="$photographer" />
                @endforeach
            </div>
        </div>
    </section>
@endif
