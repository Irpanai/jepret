<x-fg-layout>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
        <div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Metadata Kamera</span>
            <h1 class="text-3xl font-black text-black tracking-tight mt-2">Kamera & Lensa</h1>
            <p class="text-sm text-gray-500 font-medium mt-2 max-w-2xl">Simpan body dan lensa yang biasa dipakai agar metadata foto marketplace konsisten.</p>
        </div>
        <a href="{{ route('fotografer.cameras.create') }}" class="bg-black text-white text-xs font-bold px-5 py-3 rounded-lg hover:bg-gray-800 transition text-center">Tambah Kamera</a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm font-bold text-green-700">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm font-bold text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($cameras as $camera)
            <article class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="aspect-video bg-gray-100 overflow-hidden">
                    @if($camera->photo_path)
                        <img src="{{ Storage::url($camera->photo_path) }}" alt="{{ $camera->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.5 4h-5L7 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2h-3l-2.5-3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16a3 3 0 100-6 3 3 0 000 6z"></path></svg>
                        </div>
                    @endif
                </div>
                <div class="p-5">
                    <h2 class="text-lg font-black text-black">{{ $camera->name }}</h2>
                    <p class="text-sm font-bold text-gray-600 mt-1">{{ $camera->brand_model }}</p>
                    <p class="text-xs font-medium text-gray-500 mt-2">{{ $camera->lens ?: 'Lensa belum diisi' }}</p>
                    @if($camera->notes)
                        <p class="text-xs text-gray-500 mt-4 line-clamp-2">{{ $camera->notes }}</p>
                    @endif
                    <div class="flex items-center justify-between gap-3 mt-5 pt-4 border-t border-gray-100">
                        <a href="{{ route('fotografer.cameras.edit', $camera) }}" class="text-xs font-bold text-gray-700 hover:text-black">Edit</a>
                        <form method="POST" action="{{ route('fotografer.cameras.destroy', $camera) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs font-bold text-red-600 hover:text-red-700">Hapus</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-white border border-dashed border-gray-200 rounded-2xl p-12 text-center">
                <h2 class="text-lg font-black text-black">Belum ada kamera</h2>
                <p class="text-sm font-medium text-gray-500 mt-1 mb-5">Tambahkan kamera pertama untuk dipakai saat upload foto.</p>
                <a href="{{ route('fotografer.cameras.create') }}" class="inline-flex bg-black text-white text-xs font-bold px-5 py-3 rounded-lg hover:bg-gray-800 transition">Tambah Kamera</a>
            </div>
        @endforelse
    </div>
</x-fg-layout>
