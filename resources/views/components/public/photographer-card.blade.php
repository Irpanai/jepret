@props(['photographer', 'featured' => false])

@php
    $profileUrl = route('photographers.show', $photographer->slug ?: $photographer->id);
    $photo = $photographer->featuredPhoto;
@endphp

<article {{ $attributes->merge(['class' => 'grid h-full border border-public-line bg-white']) }}>
    <a href="{{ $profileUrl }}" class="relative block aspect-[5/4] overflow-hidden bg-public-mist">
        <img
            src="{{ $photo?->file_watermark ? Storage::url($photo->file_watermark) : 'https://placehold.co/800x640/f7f5f1/050505?text=JEPRET' }}"
            alt="{{ $photo?->title ?: 'Karya dari '.($photographer->studio_name ?: $photographer->name) }}"
            loading="lazy"
            class="h-full w-full object-cover {{ $photo ? '' : 'opacity-70' }}"
        >
        <span class="absolute left-3 top-3 bg-white px-2 py-1 text-[10px] font-extrabold uppercase text-public-ink">
            {{ $photographer->category ?: 'Photographer' }}
        </span>
    </a>

    <div class="grid gap-5 p-4 sm:p-5">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="public-kicker">{{ $photographer->location ?: 'Indonesia' }}</p>
                <a href="{{ $profileUrl }}" class="mt-2 block text-xl font-extrabold leading-6 text-public-ink hover:underline">
                    {{ $photographer->studio_name ?: $photographer->name }}
                </a>
                @if($photographer->studio_name)
                    <p class="mt-1 text-sm font-semibold text-public-muted">{{ $photographer->name }}</p>
                @endif
            </div>
            <img
                src="{{ $photographer->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($photographer->name).'&background=050505&color=fff' }}"
                alt=""
                loading="lazy"
                class="h-11 w-11 shrink-0 object-cover"
            >
        </div>

        <div class="grid grid-cols-2 gap-3 border-y border-public-line py-3 text-xs">
            <div>
                <p class="font-extrabold text-public-ink">{{ number_format($photographer->active_photos_count ?? 0) }}</p>
                <p class="font-bold uppercase text-public-muted">Foto Aktif</p>
            </div>
            <div>
                <p class="font-extrabold text-public-ink">{{ number_format($photographer->paid_sales_count ?? 0) }}</p>
                <p class="font-bold uppercase text-public-muted">Terjual</p>
            </div>
        </div>

        <a href="{{ $profileUrl }}" class="public-button public-button-secondary w-full">View Profile</a>
    </div>
</article>
