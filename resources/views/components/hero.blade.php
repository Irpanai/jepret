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
                <p class="eyebrow">Marketplace Photographers &middot; Indonesia</p>
                <h1 class="font-heading">
                    <span class="line">Find Your Photo</span>
                    <span class="line"><span class="outline font-display italic">&amp;</span> Photographer<span aria-hidden="true">.</span></span>
                </h1>
            </div>
            <div class="hero-copy">
                <p>Marketplace platform foto yang memudahkan pembeli menemukan fotonya dan membantu fotografer mengelola serta menjual karyanya.</p>
                <div class="hero-ctas">
                    <a class="button-primary" href="{{ route('galeri') }}">
                        <span>Jelajahi Foto</span>
                        <svg class="h-4 w-4 transition-transform duration-200" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" ari
                        a-hidden="true"><path d="M2 8h11M9 4l4 4-4 4"/></svg>
                    </a>
                    <a class="button-secondary" href="#fotografer">
                        <span>Untuk Fotografer</span>
                        <svg class="h-4 w-4 transition-transform duration-200" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 8h11M9 4l4 4-4 4"/></svg>
                    </a>
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
                    <span>Search</span>
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
        <section class="marquee-section bg-white" data-hero-marquee aria-label="Official Partners & Media">
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
                                    <img src="{{ $logo->logoUrl() }}" alt="{{ $logo->name }}" width="150" height="44" loading="lazy" decoding="async" class="h-9 sm:h-11 w-auto max-w-[150px] object-contain grayscale opacity-80 hover:opacity-100 transition-opacity">
                                @else
                                    <span class="font-sans text-sm font-bold uppercase tracking-wider text-public-ink/80">{{ $logo->name }}</span>
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
                                    <img src="{{ $logo->logoUrl() }}" alt="{{ $logo->name }}" width="150" height="44" loading="lazy" decoding="async" class="h-9 sm:h-11 w-auto max-w-[150px] object-contain grayscale opacity-80 hover:opacity-100 transition-opacity">
                                @else
                                    <span class="font-sans text-sm font-bold uppercase tracking-wider text-public-ink/80">{{ $logo->name }}</span>
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

    <style>
        .hero {
            padding-top: clamp(2.25rem, 4.5vw, 4.5rem);
            padding-bottom: clamp(2.5rem, 5vw, 4.5rem);
            border-bottom: 1px solid var(--public-line, #d7dde4);
        }

        .hero-top {
            width: min(100% - 2rem, 1600px);
            margin-inline: auto;
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 0.7fr);
            gap: clamp(1.75rem, 4.5vw, 4.5rem);
            align-items: end;
        }

        @media (min-width: 640px) {
            .hero-top {
                width: min(100% - 3rem, 1600px);
            }
        }

        @media (max-width: 960px) {
            .hero-top {
                grid-template-columns: 1fr;
                align-items: start;
            }
        }

        .hero .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            margin: 0 0 1.25rem;
            color: var(--public-muted, #5f6875);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.12em !important;
            text-transform: uppercase;
        }

        .hero .eyebrow::before {
            content: "";
            display: inline-block;
            width: 1.5rem;
            height: 1px;
            background: var(--public-ink, #101114);
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(2.75rem, 6.4vw, 6.25rem);
            font-weight: 800;
            line-height: 0.94;
            letter-spacing: -0.045em !important;
            color: var(--public-ink, #101114);
        }

        .hero h1 .line {
            display: block;
        }

        .hero h1 .outline {
            outline: none !important;
            border: 0 !important;
            font-weight: 400;
            color: transparent;
            -webkit-text-stroke: 1.5px var(--public-ink, #101114);
            padding-right: 0.08em;
        }

        .hero-copy {
            max-width: 30rem;
            justify-self: end;
            padding-bottom: 0.35rem;
        }

        @media (max-width: 960px) {
            .hero-copy {
                justify-self: start;
                padding-bottom: 0;
            }
        }

        .hero-copy > p {
            margin: 0;
            font-size: clamp(0.95rem, 1.05vw, 1.05rem);
            line-height: 1.7;
            font-weight: 500;
            color: var(--public-muted, #5f6875);
        }

        .hero-ctas {
            margin-top: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
        }

        .hero .button-primary,
        .hero .button-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            min-height: 2.875rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em !important;
            line-height: 1;
            border: 1.5px solid var(--public-ink, #101114);
            transition:
                background-color 200ms ease,
                color 200ms ease,
                border-color 200ms ease,
                transform 200ms cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 200ms ease;
        }

        .hero .button-primary {
            background: var(--public-ink, #101114);
            color: #ffffff;
        }

        .hero .button-primary:hover {
            background: #2b2b2b;
        }

        .hero .button-secondary {
            background: #ffffff;
            color: var(--public-ink, #101114);
        }

        .hero .button-secondary:hover {
            background: var(--public-bone, #f5f3ef);
        }

        .hero-ctas .button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -10px rgba(16, 17, 20, 0.28);
        }

        .hero-ctas .button-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 4px 4px 0 0 var(--public-ink, #101114);
        }

        .hero .button-primary:hover svg,
        .hero .button-secondary:hover svg {
            transform: translateX(3px);
        }

        @media (max-width: 480px) {
            .hero-ctas {
                width: 100%;
            }
            .hero-ctas .button-primary,
            .hero-ctas .button-secondary {
                flex: 1 1 auto;
            }
        }

        .hero-visual {
            width: min(100% - 2rem, 1600px);
            margin-inline: auto;
        }

        @media (min-width: 640px) {
            .hero-visual {
                width: min(100% - 3rem, 1600px);
            }
        }

        .hero .image-frame {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--public-line, #d7dde4);
            background: var(--public-mist, #e8edf2);
            aspect-ratio: 16 / 8.2;
            min-height: 260px;
        }

        .hero .hero-photo {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .marquee-section {
            border-bottom: 1px solid var(--public-line, #d7dde4);
            padding: 1.75rem 0;
            overflow: hidden;
        }

        .marquee-heading {
            margin: 0 0 1.25rem;
            text-align: center;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.14em !important;
            color: var(--public-muted, #5f6875);
        }

        .marquee {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .marquee-track {
            display: flex;
            align-items: center;
            gap: clamp(2.5rem, 5vw, 4.5rem);
            width: max-content;
            animation: hero-marquee 30s linear infinite;
        }

        .marquee:hover .marquee-track,
        .marquee-section.is-offscreen .marquee-track {
            animation-play-state: paused;
        }

        @keyframes hero-marquee {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-50%);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .marquee-track {
                animation: none;
            }
        }
    </style>

    @if($hasLogos)
        <script>
            (function () {
                const marqueeSection = document.querySelector('[data-hero-marquee]');
                if (!marqueeSection || typeof IntersectionObserver === 'undefined') {
                    return;
                }
                const observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            marqueeSection.classList.toggle('is-offscreen', !entry.isIntersecting);
                        });
                    },
                    { threshold: 0 }
                );
                observer.observe(marqueeSection);
            })();
        </script>
    @endif
</div>
