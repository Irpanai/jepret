@props(['partnerLogos' => collect()])

@php
    $hasLogos = $partnerLogos && $partnerLogos->isNotEmpty();
    if ($hasLogos) {
        $displayLogos = collect();
        $targetCount = max(6, $partnerLogos->count() * 2);
        while ($displayLogos->count() < $targetCount) {
            $displayLogos = $displayLogos->concat($partnerLogos);
        }
        $displayLogos = $displayLogos->take(12);
    } else {
        $displayLogos = collect();
    }
@endphp

<div class="bg-white text-public-ink">
    <section class="hero bg-white" id="jelajahi">
        <div class="hero-top">
            <div class="hero-heading">
                <p class="eyebrow">Marketplace fotografi &middot; Indonesia</p>
                <h1>
                    <span class="line">Find Your Photo</span>
                    <span class="line"><span class="outline">&amp;</span> Photographer<span aria-hidden="true">.</span></span>
                </h1>
            </div>
            <div class="hero-copy">
                <p>Marketplace platform foto yang memudahkan pembeli menemukan fotonya dan membantu fotografer mengelola serta menjual karyanya.</p>
                <div class="hero-ctas">
                    <a class="button-primary" href="{{ route('galeri') }}">
                        <span>Jelajahi Foto</span>
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 8h11M9 4l4 4-4 4"/></svg>
                    </a>
                    <a class="text-link" href="#fotografer">Untuk Fotografer</a>
                </div>
            </div>
        </div>

        {{-- Floating Search Bar melayang di atas foto hero --}}
        <div class="relative z-20 max-w-3xl mx-auto mt-10 sm:mt-14 -mb-6 sm:-mb-7 px-4">
            <form method="GET" action="{{ route('galeri') }}" role="search" class="flex flex-col sm:flex-row items-stretch sm:items-center border-2 border-public-ink bg-white p-1.5 sm:p-2 shadow-[0_12px_32px_rgba(16,17,20,0.12)] transition-all duration-200 focus-within:shadow-[4px_4px_0px_0px_#101114]">
                <div class="flex flex-1 items-center gap-3 px-3 py-2 sm:py-0">
                    <svg class="h-5 w-5 shrink-0 text-public-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-4-4"/>
                    </svg>
                    <label for="hero-search-input" class="sr-only">Cari foto galeri</label>
                    <input
                        id="hero-search-input"
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari event, fotografer, lokasi, atau momen..."
                        autocomplete="off"
                        enterkeyhint="search"
                        class="min-w-0 flex-1 border-0 bg-transparent p-0 text-xs sm:text-sm font-semibold text-public-ink placeholder:text-public-muted/60 focus:ring-0 focus-visible:outline-none"
                    >
                </div>
                <button
                    type="submit"
                    class="button-primary shrink-0 min-h-11 sm:min-h-12 px-6 text-xs font-extrabold uppercase tracking-wider"
                >
                    <span>Cari Foto</span>
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 8h11M9 4l4 4-4 4"/></svg>
                </button>
            </form>
        </div>

        <div class="hero-visual">
            <div class="image-frame">
                <img
                    class="hero-photo"
                    src="{{ asset('images/herosection.png') }}"
                    alt="Jepret Marketplace Fotografi"
                    fetchpriority="high"
                />
            </div>
        </div>
    </section>

    @if($hasLogos)
        <section class="marquee-section bg-white" aria-label="Official Partners & Media">
            <p class="marquee-heading">Official Partners &amp; Media</p>
            <div class="marquee">
                <div class="marquee-track">
                    @foreach($displayLogos as $logo)
                        <div class="flex shrink-0 items-center gap-3">
                            @if($logo->url)
                                <a href="{{ $logo->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5">
                            @else
                                <div class="flex items-center gap-2.5">
                            @endif
                                @if($logo->logo_path)
                                    <img src="{{ $logo->logoUrl() }}" alt="{{ $logo->name }}" class="h-9 sm:h-11 w-auto max-w-[150px] object-contain grayscale opacity-80 hover:opacity-100 transition-opacity">
                                @else
                                    <span class="font-mono text-sm font-bold uppercase tracking-wider text-public-ink/80">{{ $logo->name }}</span>
                                @endif
                            @if($logo->url)
                                </a>
                            @else
                                </div>
                            @endif
                        </div>
                    @endforeach
                    {{-- Duplicate for seamless infinite marquee loop --}}
                    @foreach($displayLogos as $logo)
                        <div class="flex shrink-0 items-center gap-3" aria-hidden="true">
                            @if($logo->url)
                                <a href="{{ $logo->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5" tabindex="-1">
                            @else
                                <div class="flex items-center gap-2.5">
                            @endif
                                @if($logo->logo_path)
                                    <img src="{{ $logo->logoUrl() }}" alt="{{ $logo->name }}" class="h-9 sm:h-11 w-auto max-w-[150px] object-contain grayscale opacity-80 hover:opacity-100 transition-opacity">
                                @else
                                    <span class="font-mono text-sm font-bold uppercase tracking-wider text-public-ink/80">{{ $logo->name }}</span>
                                @endif
                            @if($logo->url)
                                </a>
                            @else
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
