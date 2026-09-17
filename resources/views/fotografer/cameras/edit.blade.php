<x-fg-layout>
    <div class="max-w-2xl">
        <div class="mb-8">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Edit Kamera</span>
            <h1 class="text-3xl font-black text-black tracking-tight mt-2">{{ $camera->name }}</h1>
        </div>

        <form method="POST" action="{{ route('fotografer.cameras.update', $camera) }}" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-5">
            @csrf
            @method('PUT')
            @include('fotografer.cameras.form', ['camera' => $camera])

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('fotografer.cameras.index') }}" class="text-xs font-bold text-gray-600 hover:text-black">Batal</a>
                <button class="bg-black text-white text-xs font-bold px-5 py-3 rounded-lg hover:bg-gray-800 transition">Update Kamera</button>
            </div>
        </form>
    </div>
</x-fg-layout>
