@props(['photos'])

@php
    $categories = $photos->map(function ($p) {
        return $p->event?->kategori ?: ($p->fotografer?->category ?: null);
    })->filter()->unique()->values();
@endphp

<section class="border-b border-public-line bg-public-paper py-16 sm:py-24" aria-labelledby="featured-gallery-title"
    x-data="{ activeCategory: 'all' }">
    <div class="public-container">
        {{-- Section Header --}}
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between" data-reveal>
            <div>
                <p class="public-kicker">Katalog Foto</p>
                <h2 id="featured-gallery-title" class="landing-heading mt-3 text-public-ink">
                    Momen pilihan dari <span class="font-display italic">berbagai event.</span>
                </h2>
                <p class="mt-2 text-sm font-semibold text-public-muted max-w-xl">
                    Arsip visual asimetris dari dokumentasi olahraga, panggung musik, dan perayaan. Setiap frame
                    dilindungi preview aman dan siap diunduh dalam resolusi penuh.
                </p>
            </div>
            <a href="{{ route('galeri') }}"
                class="landing-link inline-flex items-center gap-2 self-start text-xs font-extrabold uppercase tracking-wider text-public-ink sm:self-end">
                <span>Lihat Semua Foto</span>
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        {{-- Category Filters (Rendered only when categories exist) --}}
        @if($photos->isNotEmpty() && $categories->isNotEmpty())
            <div class="mt-8 flex flex-wrap items-center gap-2 border-b border-public-line pb-4" data-reveal>
                <span class="mr-2 text-[10px] font-extrabold uppercase tracking-wider text-public-muted">Filter:</span>
                <button type="button" @click="activeCategory = 'all'"
                    :aria-pressed="activeCategory === 'all' ? 'true' : 'false'"
                    class="border px-3 py-1 text-xs font-extrabold uppercase tracking-wider transition-colors duration-150"
                    :class="activeCategory === 'all' ? 'border-public-ink bg-public-ink text-white' : 'border-public-line bg-white text-public-ink hover:border-public-ink'">
                    Semua ({{ $photos->count() }})
                </button>
                @foreach($categories as $category)
                    <button type="button" @click="activeCategory = @js($category)"
                        :aria-pressed="activeCategory === @js($category) ? 'true' : 'false'"
                        class="border px-3 py-1 text-xs font-extrabold uppercase tracking-wider transition-colors duration-150"
                        :class="activeCategory === @js($category) ? 'border-public-ink bg-public-ink text-white' : 'border-public-line bg-white text-public-ink hover:border-public-ink'">
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Gallery Grid / Contact Sheet --}}
        @if($photos->isNotEmpty())
            @php
                $totalPhotos = $photos->count();
                $aspects = [
                    0 => ['aspect' => $totalPhotos >= 4 ? 'aspect-[3/4] sm:aspect-[4/5] lg:aspect-[3/4]' : 'aspect-[4/3] sm:aspect-[16/10]', 'span' => $totalPhotos >= 4 ? 'sm:col-span-2 lg:col-span-2 lg:row-span-2' : 'col-span-1'],
                    1 => ['aspect' => 'aspect-[4/5]', 'span' => 'col-span-1'],
                    2 => ['aspect' => 'aspect-square', 'span' => 'col-span-1'],
                    3 => ['aspect' => 'aspect-[16/9] sm:aspect-[16/10]', 'span' => $totalPhotos >= 5 ? 'sm:col-span-2 lg:col-span-2' : 'col-span-1'],
                    4 => ['aspect' => 'aspect-square', 'span' => 'col-span-1'],
                    5 => ['aspect' => 'aspect-[4/5]', 'span' => 'col-span-1'],
                    6 => ['aspect' => 'aspect-[16/9]', 'span' => 'sm:col-span-2 lg:col-span-2'],
                    7 => ['aspect' => 'aspect-square', 'span' => 'col-span-1'],
                ];
            @endphp

            <div class="mt-8 grid grid-cols-1 gap-5 {{ $totalPhotos === 1 ? 'max-w-xl' : ($totalPhotos === 2 ? 'sm:grid-cols-2 max-w-4xl' : 'sm:grid-cols-2 lg:grid-cols-4') }}"
                data-stagger style="--stagger-step: 60ms">
                @foreach($photos as $index => $photo)
                    @php
                        $config = $aspects[$index] ?? ['aspect' => 'aspect-[4/5]', 'span' => 'col-span-1'];
                        $categoryTag = $photo->event?->kategori ?: ($photo->fotografer?->category ?: '');
                    @endphp
                    <x-home.gallery-frame :photo="$photo" :aspect="$config['aspect']" :span="$config['span']"
                        :category-tag="$categoryTag" />
                @endforeach
            </div>
        @else
            <div class="mt-10 border border-public-line bg-public-bone p-12 text-center" data-reveal>
                <p class="font-sans text-xs uppercase tracking-widest text-public-muted">EMPTY ARCHIVE STATE</p>
                <p class="mt-3 text-xl font-extrabold text-public-ink">Belum ada foto aktif yang dipublikasikan.</p>
                <p class="mt-2 text-sm font-semibold text-public-muted">Foto dari photographer terverifikasi akan otomatis
                    muncul di sini setelah diunggah dan disetujui.</p>
                <a href="{{ route('galeri') }}"
                    class="landing-cta mt-6 inline-flex min-h-11 items-center border border-public-ink bg-public-ink px-6 text-xs font-extrabold uppercase tracking-wider text-white hover:bg-white hover:text-public-ink">
                    Jelajahi Arsip Galeri
                </a>
            </div>
        @endif
    </div>
</section>