{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('landing') }}</loc></url>
    <url><loc>{{ route('galeri') }}</loc></url>
    <url><loc>{{ route('photographers.index') }}</loc></url>
    @foreach($photos as $photo)
        <url><loc>{{ route('marketplace.show', $photo) }}</loc><lastmod>{{ $photo->updated_at->toAtomString() }}</lastmod></url>
    @endforeach
    @foreach($photographers as $photographer)
        <url><loc>{{ route('photographers.show', $photographer->slug ?: $photographer->id) }}</loc><lastmod>{{ $photographer->updated_at->toAtomString() }}</lastmod></url>
    @endforeach
</urlset>
