@props(['photo'])

@php
    [$imageWidth, $imageHeight] = array_pad(array_map('intval', preg_split('/[xX×]/', $photo->resolusi ?? '') ?: []), 2, 0);
    $hasImageDimensions = $imageWidth > 0 && $imageHeight > 0;
    $photoTitle = $photo->title ?: ($photo->event?->nama_event ?? 'Foto JepretCFD');
    $photographerName = $photo->fotografer?->studio_name ?: ($photo->fotografer?->name ?? 'Photographer');
@endphp

<article data-gallery-item data-photo-id="{{ $photo->id }}" {{ $attributes->merge(['class' => 'gallery-tile group min-w-0 break-inside-avoid']) }}>
    <a href="{{ route('marketplace.show', $photo) }}" class="gallery-photo relative block overflow-hidden rounded-[10px] bg-public-mist" aria-label="Lihat {{ $photoTitle }}">
        <img
            src="{{ route('media.preview', $photo) }}"
            alt="{{ $photoTitle }}"
            loading="lazy"
            @if($hasImageDimensions)
                width="{{ $imageWidth }}"
                height="{{ $imageHeight }}"
            @endif
            class="block h-auto w-full object-contain"
        >
        <span class="gallery-protected absolute left-2 top-2 inline-flex items-center gap-1.5 rounded bg-black/75 px-2 py-1 text-[9px] font-bold uppercase tracking-[0.08em] text-white">
            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>
            Protected
        </span>
    </a>

    <div class="grid min-w-0 gap-1.5 px-1 pt-2.5">
        <div class="flex min-w-0 items-start justify-between gap-2">
            <a href="{{ route('marketplace.show', $photo) }}" class="line-clamp-2 text-[13px] font-bold leading-[1.25] text-public-ink hover:underline sm:text-sm">
                {{ $photoTitle }}
            </a>
            <p class="public-serif shrink-0 text-sm leading-none text-public-ink sm:text-base">Rp{{ number_format($photo->harga, 0, ',', '.') }}</p>
        </div>
        <div class="flex min-w-0 items-center gap-2 text-[10px] font-medium text-public-muted sm:text-xs">
            <img
                src="{{ $photo->fotografer?->profilePhotoUrl() }}"
                alt=""
                loading="lazy"
                class="h-5 w-5 shrink-0 rounded-full object-cover"
            >
            <span class="truncate">{{ $photographerName }}</span>
        </div>
        <time class="pl-7 text-[9px] font-medium text-public-muted sm:text-[10px]" datetime="{{ optional($photo->taken_at ?? $photo->published_at ?? $photo->created_at)->toDateString() }}">
            {{ optional($photo->taken_at ?? $photo->published_at ?? $photo->created_at)->translatedFormat('d M Y') }}
        </time>
    </div>
</article>
