<section class="bg-public-bone py-16 sm:py-24">
    <div class="public-container" data-reveal>
        <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-end">
            <div>
                <p class="public-kicker">Cara kerja</p>
                <h2 class="public-heading mt-4">Dari lintasan ke arsip pribadi.</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                @foreach([
                    ['01', 'Cari', 'Temukan foto berdasarkan event, lokasi, tanggal, photographer, atau tag visual.'],
                    ['02', 'Beli', 'Masukkan foto ke cart, lanjut checkout, lalu selesaikan pembayaran.'],
                    ['03', 'Download', 'File original tanpa watermark terbuka untuk pembelian yang sudah paid.'],
                ] as [$number, $title, $copy])
                    <div class="border border-public-line bg-white p-5">
                        <p class="public-kicker">{{ $number }}</p>
                        <h3 class="mt-8 text-2xl font-extrabold">{{ $title }}</h3>
                        <p class="mt-3 text-sm font-semibold leading-6 text-public-muted">{{ $copy }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
