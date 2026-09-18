<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('fotografer.events.index') }}" class="text-gray-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                {{ $event->nama_event }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-gray-800 shadow-xl sm:rounded-2xl border border-gray-700 p-6">
                <h3 class="text-xl font-bold text-white">Upload ke event ini</h3>
                <p class="mt-2 text-base text-gray-300">Gunakan uploader batch agar watermark pribadi, kuota, dan metadata diproses dengan benar.</p>
                <a href="{{ route('fotografer.photos.create', ['event_id' => $event->id]) }}" class="mt-4 inline-flex rounded-lg bg-white px-5 py-3 text-sm font-bold text-gray-900">Buka uploader batch</a>
            </div>

            <!-- Photos Grid -->
            <div class="bg-gray-800 shadow-xl sm:rounded-2xl border border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-white">Uploaded Photos <span class="text-sm font-normal text-gray-400">({{ $photos->total() }})</span></h3>
                </div>

                @if($photos->isEmpty())
                    <div class="p-12 text-center border-2 border-dashed border-gray-700 rounded-xl">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-gray-400">No photos uploaded to this event yet.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($photos as $photo)
                            <div class="relative group bg-gray-900 rounded-xl overflow-hidden border border-gray-700 aspect-square">
                                <img src="{{ route('media.preview', $photo) }}" alt="Photo" class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                                    <p class="text-white font-bold text-sm">Rp {{ number_format($photo->harga, 0, ',', '.') }}</p>
                                    
                                    <form action="{{ route('fotografer.photos.destroy', $photo) }}" method="POST" class="absolute top-2 right-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500/80 hover:bg-red-600 text-white p-1.5 rounded-lg transition backdrop-blur-sm" onclick="return confirm('Delete this photo?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                                
                                @if($photo->ai_tags)
                                    @php $tags = json_decode($photo->ai_tags); @endphp
                                    <div class="absolute top-2 left-2 flex flex-wrap gap-1 max-w-[80%]">
                                        @foreach(array_slice($tags, 0, 2) as $tag)
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-gray-900/80 backdrop-blur-sm text-accent rounded-full border border-accent/30">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $photos->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
