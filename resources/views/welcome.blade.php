@php
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Jepret',
        'url' => route('landing'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => route('galeri').'?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
@endphp

<x-marketplace-layout
    title="Jepret — Modern Photography Marketplace"
    description="Temukan foto event, simpan momen terbaik, dan beli file original resolusi penuh dari fotografer terverifikasi dengan preview aman."
    :structured-data="$structuredData"
>
    {{-- A & B: Hero Section matching reference design with full-width visual and marquee --}}
    <x-hero :partner-logos="$partnerLogos" />

    {{-- C: Featured Gallery with 8 photos, varied aspect ratios, protected badge, and real cart --}}
    <x-home.featured-gallery :photos="$galleryPhotos" />

    {{-- D: How It Works (01 Pilih & Keranjang, 02 Checkout, 03 Bayar QRIS, 04 Unduh Original) --}}
    <x-home.how-it-works />

    {{-- D2: Photographer features showcase (sticky scroll desktop, stacked mobile) --}}
    <x-home.photographer-features />

    {{-- E: Featured Photographers from active subscribed photographers in DB --}}
    <x-home.featured-photographers :photographers="$featuredPhotographers" />

    {{-- G: Pricing with existing dynamic packages (Trial, Starter, Creator featured, Studio) --}}
    <x-home.pricing :pricing-plans="$pricingPlans" />

    {{-- H: FAQ Section matching editorial reference design --}}
    <x-faq />
</x-marketplace-layout>
