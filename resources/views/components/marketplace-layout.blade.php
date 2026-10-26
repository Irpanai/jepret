@props([
    'title' => 'Jepret - Temukan Foto & Photographer',
    'description' => 'Jepret adalah marketplace fotografi event untuk menemukan, membeli, dan mengunduh foto original dari photographer lokal.',
    'canonical' => null,
    'noindex' => false,
    'ogImage' => null,
    'type' => 'website',
    'structuredData' => null,
])

@php
    $canonicalUrl = $canonical ?: url()->current();
    $shareImage = $ogImage ?: asset('images/login-bg.jpg');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $description }}">
        <meta name="robots" content="{{ $noindex ? 'noindex, nofollow' : 'index, follow' }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">

        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:image" content="{{ $shareImage }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:type" content="{{ $type }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $title }}">
        <meta name="twitter:description" content="{{ $description }}">
        <meta name="twitter:image" content="{{ $shareImage }}">

        <title>{{ $title }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/jepret.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@600;700;800;900&family=Manrope:wght@400;500;600;700;800&family=Newsreader:ital,opsz,wght@0,6..72,400..700;1,6..72,400..700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if($structuredData)
            <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif
    </head>
    <body class="public-shell font-sans min-h-screen overflow-x-hidden antialiased">
        <a href="#main-content" class="skip-link">Lewati ke konten utama</a>
        <x-navbar />

        <main id="main-content" class="min-w-0 overflow-x-hidden" tabindex="-1">
            {{ $slot }}
        </main>

        <footer
            class="bwf font-sans"
            data-bwf-footer
            data-in="false"
            style="
                --bwf-bg: #101114;
                --bwf-bg-rgb: 16, 17, 20;
                --bwf-ink: #ffffff;
                --bwf-ink-rgb: 255, 255, 255;
                --bwf-muted: #8f96a3;
                --bwf-acc: 255, 255, 255;
                --bwf-lit: 255, 255, 255;
                --bwf-wt: #4a4e58;
                --bwf-wf: #15171c;
                --bwf-ww: 900;
            "
        >
            <style>
                .bwf {
                    --bwf-b: 58;
                    --bwf-px: 50%;
                    --bwf-py: 50%;
                    --bwf-g: 0;
                    --bwf-w: 1440px;
                    --bwf-h: 640px;
                    position: relative;
                    overflow: hidden;
                    background: var(--bwf-bg);
                    color: var(--bwf-ink);
                    isolation: isolate;
                }
                .bwf *, .bwf *::before, .bwf *::after {
                    box-sizing: border-box;
                }
                .bwf-rule {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 1px;
                    z-index: 3;
                    background: linear-gradient(90deg,
                        transparent 0%,
                        rgba(var(--bwf-acc), 0.14) 18%,
                        rgba(var(--bwf-lit), 0.45) calc(var(--bwf-b) * 1%),
                        rgba(var(--bwf-acc), 0.14) 82%,
                        transparent 100%);
                }
                .bwf-sky {
                    position: absolute;
                    inset: 0;
                    pointer-events: none;
                    z-index: 0;
                    overflow: hidden;
                }
                .bwf-wash {
                    position: absolute;
                    inset: 0;
                    background:
                        radial-gradient(62% 70% at 88% 12%, rgba(var(--bwf-acc), 0.10) 0%, rgba(var(--bwf-acc), 0.03) 42%, transparent 74%),
                        radial-gradient(50% 60% at 22% 85%, rgba(var(--bwf-acc), 0.06) 0%, transparent 70%);
                }
                .bwf-bands {
                    position: absolute;
                    inset: -25%;
                    background: repeating-linear-gradient(118deg,
                        rgba(var(--bwf-acc), 0.0) 0px,
                        rgba(var(--bwf-acc), 0.0) 56px,
                        rgba(var(--bwf-acc), 0.025) 86px,
                        rgba(var(--bwf-bg-rgb), 0.35) 118px,
                        rgba(var(--bwf-acc), 0.0) 150px);
                    mask-image: radial-gradient(85% 75% at 62% 48%, #000 20%, transparent 82%);
                    -webkit-mask-image: radial-gradient(85% 75% at 62% 48%, #000 20%, transparent 82%);
                }
                .bwf-beam {
                    position: absolute;
                    inset: 0;
                    background:
                        linear-gradient(118deg,
                            transparent calc(var(--bwf-b) * 1% - 26%),
                            rgba(var(--bwf-acc), 0.03) calc(var(--bwf-b) * 1% - 14%),
                            rgba(var(--bwf-acc), 0.11) calc(var(--bwf-b) * 1% - 5%),
                            rgba(var(--bwf-lit), 0.22) calc(var(--bwf-b) * 1%),
                            rgba(var(--bwf-acc), 0.09) calc(var(--bwf-b) * 1% + 6%),
                            transparent calc(var(--bwf-b) * 1% + 20%)),
                        radial-gradient(38% 64% at calc(var(--bwf-b) * 1% + 10%) 16%, rgba(var(--bwf-lit), 0.16) 0%, transparent 72%);
                    mix-blend-mode: screen;
                }
                .bwf-glow {
                    position: absolute;
                    inset: 0;
                    background: radial-gradient(520px circle at var(--bwf-px) var(--bwf-py),
                        rgba(var(--bwf-lit), calc(0.16 * var(--bwf-g))) 0%,
                        rgba(var(--bwf-acc), calc(0.07 * var(--bwf-g))) 36%,
                        transparent 70%);
                    mix-blend-mode: screen;
                }
                .bwf-grain {
                    position: absolute;
                    inset: 0;
                    opacity: 0.038;
                    mix-blend-mode: overlay;
                    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
                }
                .bwf-inner {
                    position: relative;
                    z-index: 2;
                    max-width: 1360px;
                    margin: 0 auto;
                    padding: clamp(36px, 4.5vw, 60px) clamp(24px, 5vw, 72px) clamp(6px, 1.2vw, 14px);
                    display: grid;
                    grid-template-columns: minmax(250px, 1.35fr) repeat(3, minmax(150px, 1fr));
                    gap: clamp(24px, 3.5vw, 48px);
                    align-items: start;
                }
                .bwf-col {
                    min-width: 0;
                    opacity: 0;
                    transform: translateY(18px);
                    transition:
                        opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1) calc(var(--i, 0) * 90ms),
                        transform 0.7s cubic-bezier(0.22, 1, 0.36, 1) calc(var(--i, 0) * 90ms);
                }
                .bwf[data-in="true"] .bwf-col {
                    opacity: 1;
                    transform: none;
                }
                .bwf-label {
                    position: relative;
                    font-size: 12px;
                    font-weight: 700;
                    letter-spacing: 0.08em;
                    text-transform: uppercase;
                    line-height: 1.4;
                    color: rgba(var(--bwf-lit), 0.72);
                    padding-bottom: 11px;
                    margin: 0 0 16px 0;
                }
                .bwf-label::before,
                .bwf-label::after {
                    content: "";
                    position: absolute;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    height: 1px;
                }
                .bwf-label::before {
                    background: linear-gradient(90deg, rgba(var(--bwf-acc), 0.36) 0%, rgba(var(--bwf-ink-rgb), 0.09) 100%);
                    transform: scaleX(0);
                    transform-origin: left;
                    transition: transform 0.85s cubic-bezier(0.22, 1, 0.36, 1) calc(160ms + var(--i, 0) * 90ms);
                }
                .bwf[data-in="true"] .bwf-label::before {
                    transform: scaleX(1);
                }
                .bwf-label::after {
                    background: linear-gradient(90deg, rgba(var(--bwf-lit), 0.85) 0%, rgba(var(--bwf-acc), 0.4) 60%, transparent 100%);
                    transform: scaleX(0);
                    transform-origin: left;
                    transition: transform 0.45s cubic-bezier(0.22, 1, 0.36, 1);
                }
                .bwf-col:hover .bwf-label::after,
                .bwf-col:focus-within .bwf-label::after {
                    transform: scaleX(1);
                }
                .bwf-brand {
                    display: inline-flex;
                    align-items: center;
                    gap: 12px;
                    text-decoration: none;
                    color: var(--bwf-ink);
                    margin-bottom: 14px;
                }
                .bwf-brand-mark {
                    display: grid;
                    place-items: center;
                    width: 44px;
                    height: 44px;
                    background: var(--bwf-ink);
                    transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
                }
                .bwf-brand:hover .bwf-brand-mark {
                    transform: rotate(-3deg) scale(1.03);
                }
                .bwf-brand-mark img {
                    width: 34px;
                    height: 34px;
                    object-fit: contain;
                }
                .bwf-brand-name {
                    font-size: 18px;
                    font-weight: 800;
                    letter-spacing: 0.04em;
                    text-transform: uppercase;
                }
                .bwf-desc {
                    margin: 0 0 14px 0;
                    max-width: 340px;
                    font-size: 13.5px;
                    line-height: 1.65;
                    color: var(--bwf-muted);
                }
                .bwf-socials {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-bottom: 14px;
                }
                .bwf-soc {
                    position: relative;
                    width: 42px;
                    height: 42px;
                    border-radius: 9px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    color: rgba(var(--bwf-ink-rgb), 0.82);
                    text-decoration: none;
                    background: linear-gradient(180deg, rgba(var(--bwf-ink-rgb), 0.065) 0%, rgba(var(--bwf-ink-rgb), 0.02) 100%);
                    border: 1px solid rgba(var(--bwf-ink-rgb), 0.09);
                    box-shadow: inset 0 1px 0 rgba(var(--bwf-ink-rgb), 0.08), 0 4px 14px -6px rgba(0, 0, 0, 0.6);
                    transition:
                        transform 0.28s cubic-bezier(0.22, 1, 0.36, 1),
                        border-color 0.28s ease,
                        color 0.28s ease,
                        box-shadow 0.28s ease,
                        background 0.28s ease;
                }
                .bwf-soc:hover,
                .bwf-soc:focus-visible {
                    color: var(--bwf-ink);
                    transform: translateY(-3px);
                    border-color: rgba(var(--bwf-lit), 0.45);
                    background: linear-gradient(180deg, rgba(var(--bwf-acc), 0.16) 0%, rgba(var(--bwf-acc), 0.04) 100%);
                    box-shadow:
                        inset 0 1px 0 rgba(var(--bwf-lit), 0.28),
                        0 10px 24px -8px rgba(var(--bwf-acc), 0.35);
                    outline: none;
                }
                .bwf-soc:active {
                    transform: translateY(-1px) scale(0.97);
                }
                .bwf-soc svg {
                    width: 17px;
                    height: 17px;
                    display: block;
                }
                .bwf-tip {
                    position: absolute;
                    left: 50%;
                    bottom: calc(100% + 8px);
                    transform: translate(-50%, 4px);
                    padding: 4px 8px;
                    border-radius: 6px;
                    font-size: 11px;
                    line-height: 1;
                    white-space: nowrap;
                    color: var(--bwf-ink);
                    background: rgba(var(--bwf-bg-rgb), 0.92);
                    border: 1px solid rgba(var(--bwf-lit), 0.25);
                    box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.7);
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity 0.2s ease, transform 0.2s cubic-bezier(0.22, 1, 0.36, 1);
                }
                .bwf-soc:hover .bwf-tip,
                .bwf-soc:focus-visible .bwf-tip {
                    opacity: 1;
                    transform: translate(-50%, 0);
                }
                .bwf-credits {
                    font-size: 12.5px;
                    line-height: 1.65;
                    color: var(--bwf-muted);
                }
                .bwf-credits p {
                    margin: 0;
                }
                .bwf-links {
                    list-style: none;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    flex-direction: column;
                    gap: 6px;
                }
                .bwf-link {
                    position: relative;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 4px 0;
                    font-size: 15px;
                    font-weight: 500;
                    line-height: 1.4;
                    color: rgba(var(--bwf-ink-rgb), 0.86);
                    text-decoration: none;
                    width: fit-content;
                    transition: color 0.22s ease, transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
                }
                .bwf-link-t {
                    position: relative;
                }
                .bwf-link-t::after {
                    content: "";
                    position: absolute;
                    left: 0;
                    right: 0;
                    bottom: -2px;
                    height: 1px;
                    background: linear-gradient(90deg, rgba(var(--bwf-lit), 0.9), rgba(var(--bwf-acc), 0.3));
                    transform: scaleX(0);
                    transform-origin: right;
                    transition: transform 0.32s cubic-bezier(0.22, 1, 0.36, 1);
                }
                .bwf-link svg {
                    width: 11px;
                    height: 11px;
                    opacity: 0;
                    transform: translate(-4px, 4px);
                    color: rgb(var(--bwf-lit));
                    transition: opacity 0.24s ease, transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
                }
                .bwf-link:hover,
                .bwf-link:focus-visible {
                    color: #fff;
                    transform: translateX(4px);
                    outline: none;
                }
                .bwf-link:hover .bwf-link-t::after,
                .bwf-link:focus-visible .bwf-link-t::after {
                    transform: scaleX(1);
                    transform-origin: left;
                }
                .bwf-link:hover svg,
                .bwf-link:focus-visible svg {
                    opacity: 0.9;
                    transform: translate(0, 0);
                }
                .bwf-contact-title {
                    margin: 0 0 6px 0;
                    font-size: 15px;
                    font-weight: 700;
                    color: var(--bwf-ink);
                }
                .bwf-contact-note {
                    margin: 14px 0 0 0;
                    font-size: 12.5px;
                    line-height: 1.6;
                    color: var(--bwf-muted);
                }
                .bwf-word {
                    position: relative;
                    z-index: 1;
                    width: 100%;
                    margin-top: 0;
                    overflow: hidden;
                    user-select: none;
                    -webkit-user-select: none;
                }
                .bwf-word-box {
                    position: relative;
                    width: 100%;
                    display: flex;
                    justify-content: center;
                    align-items: flex-start;
                    padding: 0 clamp(12px, 2vw, 28px);
                }
                .bwf-word-in {
                    display: inline-flex;
                    width: max-content;
                    justify-content: center;
                    align-items: baseline;
                    white-space: nowrap;
                    font-weight: var(--bwf-ww);
                    letter-spacing: -0.04em;
                    line-height: 0.86;
                }
                .bwf-lw {
                    display: inline-block;
                    opacity: 0;
                    transform: translateY(38%);
                    transition:
                        opacity 0.75s cubic-bezier(0.22, 1, 0.36, 1) calc(180ms + var(--li, 0) * 55ms),
                        transform 0.75s cubic-bezier(0.22, 1, 0.36, 1) calc(180ms + var(--li, 0) * 55ms);
                }
                .bwf[data-in="true"] .bwf-lw {
                    opacity: 1;
                    transform: translateY(0);
                }
                .bwf-l {
                    --bwf-x: 0px;
                    --bwf-y: 0px;
                    display: inline-block;
                    cursor: pointer;
                    padding: 0.04em 0.008em;
                    margin: -0.04em -0.008em;
                    background-image:
                        radial-gradient(420px circle at var(--bwf-px) var(--bwf-py),
                            rgba(var(--bwf-ink-rgb), calc(0.95 * var(--bwf-g))) 0%,
                            rgba(var(--bwf-lit), calc(0.60 * var(--bwf-g))) 28%,
                            rgba(var(--bwf-acc), 0) 66%),
                        linear-gradient(118deg,
                            transparent calc(var(--bwf-b) * 1% - 20%),
                            rgba(var(--bwf-acc), 0.30) calc(var(--bwf-b) * 1% - 7%),
                            rgba(var(--bwf-lit), 0.72) calc(var(--bwf-b) * 1%),
                            rgba(var(--bwf-acc), 0.26) calc(var(--bwf-b) * 1% + 7%),
                            transparent calc(var(--bwf-b) * 1% + 18%)),
                        linear-gradient(180deg, var(--bwf-wt) 0%, var(--bwf-wf) 92%);
                    background-size: var(--bwf-w) var(--bwf-h), var(--bwf-w) var(--bwf-h), 100% 100%;
                    background-position: var(--bwf-x) var(--bwf-y), var(--bwf-x) var(--bwf-y), 0 0;
                    background-repeat: no-repeat;
                    -webkit-background-clip: text;
                    background-clip: text;
                    color: transparent;
                    -webkit-text-stroke: 1px rgba(var(--bwf-ink-rgb), 0.06);
                    filter: drop-shadow(0 -1px 0 rgba(var(--bwf-ink-rgb), 0.05));
                    transition:
                        transform 0.38s cubic-bezier(0.22, 1, 0.36, 1),
                        filter 0.38s ease,
                        -webkit-text-stroke-color 0.38s ease;
                }
                .bwf-l:hover {
                    will-change: transform;
                    transform: translateY(-0.045em);
                    -webkit-text-stroke-color: rgba(var(--bwf-lit), 0.32);
                    filter:
                        drop-shadow(0 -1px 0 rgba(var(--bwf-lit), 0.28))
                        drop-shadow(0 12px 26px rgba(var(--bwf-acc), 0.25));
                }
                .bwf-l.is-hop {
                    will-change: transform;
                    animation: bwf-hop 0.56s cubic-bezier(0.22, 1, 0.36, 1);
                }
                @keyframes bwf-hop {
                    0% { transform: translateY(-0.045em) scale(1); }
                    32% { transform: translateY(-0.14em) scale(1.035); }
                    68% { transform: translateY(0.015em) scale(0.99); }
                    100% { transform: translateY(0) scale(1); }
                }
                .bwf-foot {
                    position: absolute;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    height: 32%;
                    pointer-events: none;
                    background: linear-gradient(180deg, transparent 0%, rgba(var(--bwf-bg-rgb), 0.55) 65%, var(--bwf-bg) 100%);
                }
                @media (max-width: 960px) {
                    .bwf-inner {
                        grid-template-columns: 1fr 1fr;
                    }
                    .bwf-brand-col {
                        grid-column: 1 / -1;
                    }
                }
                @media (max-width: 560px) {
                    .bwf-inner {
                        grid-template-columns: 1fr;
                        gap: 32px;
                    }
                }
                @media (hover: none) and (pointer: coarse) {
                    .bwf-col {
                        transform: translateY(10px);
                        transition-duration: 0.5s;
                    }
                    .bwf-lw {
                        transform: translateY(22%);
                        transition-duration: 0.55s;
                    }
                    .bwf-l {
                        filter: none;
                    }
                    .bwf-l:hover {
                        transform: none;
                        filter: none;
                    }
                }
                @media (prefers-reduced-motion: reduce) {
                    .bwf-col,
                    .bwf-lw,
                    .bwf-label::before {
                        opacity: 1 !important;
                        transform: none !important;
                        transition: none !important;
                    }
                }
            </style>

            <div class="bwf-rule" aria-hidden="true"></div>

            <div class="bwf-sky" aria-hidden="true">
                <div class="bwf-wash"></div>
                <div class="bwf-bands"></div>
                <div class="bwf-beam"></div>
                <div class="bwf-glow"></div>
                <div class="bwf-grain"></div>
            </div>

            <div class="bwf-inner">
                <div class="bwf-col bwf-brand-col" style="--i: 0">
                    <p class="bwf-label">&copy; {{ date('Y') }} Jepret. All rights reserved.</p>

                    <a href="{{ route('landing') }}" class="bwf-brand" aria-label="Jepret home">
                        <span class="bwf-brand-mark">
                            <img src="{{ asset('images/jepret.png') }}" alt="">
                        </span>
                        <span class="bwf-brand-name font-heading">Jepret</span>
                    </a>

                    <p class="bwf-desc">
                        Jepret adalah platform digital yang menghubungkan fotografer dengan pembeli dalam satu platfrom fotografi
                    </p>

                    <div class="bwf-socials">
                        <a href="https://wa.me/6285156767900" target="_blank" rel="noopener noreferrer" class="bwf-soc" aria-label="WhatsApp Jepret">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            </svg>
                            <span class="bwf-tip" role="tooltip">WhatsApp</span>
                        </a>
                        <a href="https://instagram.com/jepret.web" target="_blank" rel="noopener noreferrer" class="bwf-soc" aria-label="Instagram Jepret">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2.5" y="2.5" width="19" height="19" rx="5"/>
                                <circle cx="12" cy="12" r="4.2"/>
                                <circle cx="17.6" cy="6.4" r="1.1" fill="currentColor" stroke="none"/>
                            </svg>
                            <span class="bwf-tip" role="tooltip">Instagram</span>
                        </a>
                        <a href="mailto:jepretccfdd@gmail.com" class="bwf-soc" aria-label="Email Jepret">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/>
                                <path d="m3.5 6.5 8.5 6 8.5-6"/>
                            </svg>
                            <span class="bwf-tip" role="tooltip">Email</span>
                        </a>
                    </div>

                    <div class="bwf-credits">
                        <p>Protected previews &middot; Private originals &middot; Secure downloads</p>
                    </div>
                </div>

                <nav class="bwf-col" aria-label="Jelajahi" style="--i: 1">
                    <p class="bwf-label">Jelajahi</p>
                    <ul class="bwf-links">
                        <li>
                            <a href="{{ route('about') }}" class="bwf-link">
                                <span class="bwf-link-t">About</span>
                                <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.5 9.5 9.5 2.5M4 2.5h5.5V8"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('galeri') }}" class="bwf-link">
                                <span class="bwf-link-t">Galeri</span>
                                <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.5 9.5 9.5 2.5M4 2.5h5.5V8"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('photographers.index') }}" class="bwf-link">
                                <span class="bwf-link-t">Photographers</span>
                                <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.5 9.5 9.5 2.5M4 2.5h5.5V8"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pricing') }}" class="bwf-link">
                                <span class="bwf-link-t">Pricing</span>
                                <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.5 9.5 9.5 2.5M4 2.5h5.5V8"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </nav>

                <nav class="bwf-col" aria-label="Legal" style="--i: 2">
                    <p class="bwf-label">Legal</p>
                    <ul class="bwf-links">
                        <li>
                            <a href="{{ route('terms') }}" class="bwf-link">
                                <span class="bwf-link-t">Terms of Service</span>
                                <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.5 9.5 9.5 2.5M4 2.5h5.5V8"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="bwf-link">
                                <span class="bwf-link-t">Privacy Policy</span>
                                <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.5 9.5 9.5 2.5M4 2.5h5.5V8"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="bwf-col" style="--i: 3">
                    <p class="bwf-label">Contact</p>
                    <p class="bwf-contact-title">Punya pertanyaan?</p>
                    <ul class="bwf-links">
                        <li>
                            <a href="mailto:jepretccfdd@gmail.com" class="bwf-link">
                                <span class="bwf-link-t">jepretccfdd@gmail.com</span>
                                <svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.5 9.5 9.5 2.5M4 2.5h5.5V8"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                    <p class="bwf-contact-note">
                        Layanan bantuan aktif setiap hari kerja 09.00 - 18.00 WIB
                    </p>
                </div>
            </div>

            <div class="bwf-word">
                <p class="sr-only">JEPRET</p>
                <div class="bwf-word-box" data-bwf-box aria-hidden="true">
                    <div class="bwf-word-in font-heading" data-bwf-word>
                        @foreach (['J', 'E', 'P', 'R', 'E', 'T'] as $li => $ch)
                            <span class="bwf-lw" style="--li: {{ $li }}">
                                <span class="bwf-l" data-bwf-letter>{{ $ch }}</span>
                            </span>
                        @endforeach
                    </div>
                    <div class="bwf-foot"></div>
                </div>
            </div>

            <script>
                (function () {
                    const root = document.querySelector('[data-bwf-footer]');
                    if (!root) return;

                    const box = root.querySelector('[data-bwf-box]');
                    const word = root.querySelector('[data-bwf-word]');
                    const letters = Array.from(root.querySelectorAll('[data-bwf-letter]'));
                    const cropRatio = 0.12;

                    const clamp = (v, lo, hi) => (v < lo ? lo : v > hi ? hi : v);
                    const fitSize = (measuredAt100, target, cap) => {
                        if (!(measuredAt100 > 0) || !(target > 0)) return 0;
                        const size = (100 * target) / measuredAt100;
                        return cap > 0 ? Math.min(size, cap) : size;
                    };
                    const beamTarget = (u, lo = 30, hi = 82) =>
                        lo + (hi - lo) * clamp(Number.isFinite(u) ? u : 0.5, 0, 1);
                    const drift = (t, centre = 58, amp = 9) =>
                        centre + amp * (0.7 * Math.sin(t * 0.21) + 0.3 * Math.sin(t * 0.077 + 1.3));
                    const approach = (from, to, dt, k = 0.06) => {
                        const a = 1 - Math.pow(1 - clamp(k, 0, 1), clamp(dt, 0, 100) / 16.6667);
                        return from + (to - from) * a;
                    };
                    const baselineAt = (size) => Math.round(size * 0.84);
                    const wordHeight = (size, crop) =>
                        Math.max(0, Math.round(size * 0.84 * (1 - clamp(crop, 0, 0.6))));

                    let ptr = { x: 0, y: 0, u: 0.58, inside: false };
                    let cur = { x: 0, y: 0, b: 58, g: 0 };

                    const align = () => {
                        const sr = root.getBoundingClientRect();
                        root.style.setProperty('--bwf-w', sr.width.toFixed(1) + 'px');
                        root.style.setProperty('--bwf-h', sr.height.toFixed(1) + 'px');
                        for (const el of letters) {
                            const r = el.getBoundingClientRect();
                            el.style.setProperty('--bwf-x', (sr.left - r.left).toFixed(1) + 'px');
                            el.style.setProperty('--bwf-y', (sr.top - r.top).toFixed(1) + 'px');
                        }
                    };

                    const fit = () => {
                        if (!box || !word) return;
                        const cs = getComputedStyle(box);
                        const pad = (parseFloat(cs.paddingLeft) || 0) + (parseFloat(cs.paddingRight) || 0);
                        const target = box.clientWidth - pad;
                        if (target <= 0) return;
                        word.style.fontSize = '100px';
                        const measured = word.getBoundingClientRect().width || word.scrollWidth;
                        const size = fitSize(measured, target, root.clientWidth * 0.42);
                        if (size <= 0) return;
                        word.style.fontSize = size.toFixed(2) + 'px';
                        word.style.height = baselineAt(size) + 'px';
                        box.style.height = wordHeight(size, cropRatio) + 'px';
                        align();
                    };

                    fit();
                    const t1 = setTimeout(fit, 950);
                    let ro = null;
                    if (typeof ResizeObserver !== 'undefined' && box) {
                        ro = new ResizeObserver(fit);
                        ro.observe(box);
                        ro.observe(root);
                    } else {
                        window.addEventListener('resize', fit);
                    }
                    if (document.fonts && document.fonts.ready) {
                        document.fonts.ready.then(fit).catch(() => {});
                    }

                    const reduce =
                        typeof window.matchMedia === 'function' &&
                        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    const isCoarsePointer =
                        typeof window.matchMedia === 'function' &&
                        window.matchMedia('(hover: none) and (pointer: coarse)').matches;

                    let inView = false;
                    let hasRevealed = false;
                    let rafId = null;
                    let last = performance.now();

                    const tick = (now) => {
                        if (!inView || document.hidden) {
                            rafId = null;
                            return;
                        }
                        const dt = Math.min(64, now - last);
                        last = now;
                        const t = now * 0.001;
                        const bGoal = ptr.inside ? beamTarget(ptr.u) : drift(t);
                        const gGoal = ptr.inside ? 1 : 0;

                        cur.b = approach(cur.b, bGoal, dt, ptr.inside ? 0.07 : 0.025);
                        cur.g = approach(cur.g, gGoal, dt, 0.08);
                        cur.x = approach(cur.x, ptr.x, dt, 0.14);
                        cur.y = approach(cur.y, ptr.y, dt, 0.14);

                        root.style.setProperty('--bwf-b', cur.b.toFixed(2));
                        root.style.setProperty('--bwf-g', cur.g.toFixed(3));
                        root.style.setProperty('--bwf-px', cur.x.toFixed(1) + 'px');
                        root.style.setProperty('--bwf-py', cur.y.toFixed(1) + 'px');

                        rafId = requestAnimationFrame(tick);
                    };

                    const startLoop = () => {
                        if (reduce || isCoarsePointer || rafId !== null || !inView || document.hidden) {
                            return;
                        }
                        last = performance.now();
                        rafId = requestAnimationFrame(tick);
                    };

                    const stopLoop = () => {
                        if (rafId !== null) {
                            cancelAnimationFrame(rafId);
                            rafId = null;
                        }
                    };

                    if (typeof IntersectionObserver === 'undefined') {
                        root.setAttribute('data-in', 'true');
                        inView = true;
                        startLoop();
                    } else {
                        const io = new IntersectionObserver(
                            (entries) => {
                                entries.forEach((entry) => {
                                    inView = entry.isIntersecting;
                                    if (inView) {
                                        if (!hasRevealed) {
                                            hasRevealed = true;
                                            root.setAttribute('data-in', 'true');
                                            setTimeout(align, 950);
                                        }
                                        startLoop();
                                    } else {
                                        stopLoop();
                                    }
                                });
                            },
                            { threshold: 0.05 }
                        );
                        io.observe(root);
                    }

                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            stopLoop();
                        } else {
                            startLoop();
                        }
                    });

                    if (!isCoarsePointer) {
                        root.addEventListener('pointermove', (e) => {
                            const r = root.getBoundingClientRect();
                            const x = e.clientX - r.left;
                            const y = e.clientY - r.top;
                            if (!ptr.inside) {
                                cur.x = x;
                                cur.y = y;
                            }
                            ptr.x = x;
                            ptr.y = y;
                            ptr.u = clamp(x / Math.max(1, r.width), 0, 1);
                            ptr.inside = true;
                        }, { passive: true });

                        root.addEventListener('pointerleave', () => {
                            ptr.inside = false;
                        }, { passive: true });
                    }

                    letters.forEach((el) => {
                        el.addEventListener('click', () => {
                            el.classList.remove('is-hop');
                            void el.offsetWidth;
                            el.classList.add('is-hop');
                        });
                        el.addEventListener('animationend', () => {
                            el.classList.remove('is-hop');
                        });
                    });
                })();
            </script>
        </footer>
    </body>
</html>
