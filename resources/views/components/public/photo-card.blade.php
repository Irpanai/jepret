@props(['photo'])

<article {{ $attributes->merge(['class' => 'group min-w-0']) }}>
    <a href="{{ route('marketplace.show', $photo) }}" class="protected-photo relative block aspect-[4/5] overflow-hidden border border-public-line bg-public-mist">
        <img
            src="{{ $photo->file_watermark ? Storage::url($photo->file_watermark) : 'https://placehold.co/700x875/f7f5f1/050505?text=JEPRET' }}"
            alt="{{ $photo->title ?: ($photo->event?->nama_event ? 'Foto '.$photo->event->nama_event : 'Foto JepretCFD') }}"
            loading="lazy"
            class="h-full w-full object-cover blur-[1.5px]"
        >
        <span class="absolute left-2 top-2 bg-white px-2 py-1 text-[10px] font-extrabold uppercase text-public-ink">Protected</span>
    </a>

    <div class="grid min-w-0 gap-2 border-x border-b border-public-line bg-white p-3">
        <div class="flex min-w-0 items-center justify-between gap-3 text-[10px] font-extrabold uppercase text-public-muted">
            <span class="truncate">{{ $photo->event?->lokasi ?? 'Lokasi event' }}</span>
            <span class="shrink-0">{{ optional($photo->taken_at ?? $photo->published_at ?? $photo->created_at)->format('d M Y') }}</span>
        </div>

        <a href="{{ route('marketplace.show', $photo) }}" class="line-clamp-2 min-h-[2.5rem] text-sm font-extrabold leading-5 text-public-ink hover:underline">
            {{ $photo->title ?: ($photo->event?->nama_event ?? 'Foto JepretCFD') }}
        </a>

        <div class="flex min-w-0 items-center gap-2 text-xs font-bold text-public-muted">
            <img
                src="{{ $photo->fotografer?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($photo->fotografer?->name ?? 'Photographer').'&background=050505&color=fff' }}"
                alt=""
                loading="lazy"
                class="h-6 w-6 object-cover"
            >
            <span class="truncate">{{ $photo->fotografer?->studio_name ?: ($photo->fotografer?->name ?? 'Photographer') }}</span>
        </div>

        <div class="mt-1 flex items-center justify-between gap-3 border-t border-public-line pt-3">
            <p class="shrink-0 text-base font-extrabold text-public-ink">Rp{{ number_format($photo->harga, 0, ',', '.') }}</p>

            @auth
                @if(auth()->user()->role === 'pembeli')
                    <form method="POST" action="{{ route('cart.store') }}">
                        @csrf
                        <input type="hidden" name="photo_id" value="{{ $photo->id }}">
                        <button class="border border-public-ink bg-public-ink px-3 py-2 text-[10px] font-extrabold uppercase text-white hover:bg-neutral-800">
                            Cart
                        </button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="border border-public-line px-3 py-2 text-[10px] font-extrabold uppercase text-public-ink hover:border-public-ink">
                    Login
                </a>
            @endauth
        </div>
    </div>
</article>
