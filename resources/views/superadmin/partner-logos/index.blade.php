<x-superadmin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-black">Partner & Event Logos</h1>
                <p class="helper mt-1">Kelola logo partner, sponsor, dan komunitas event yang ditampilkan pada running marquee hero landing page.</p>
            </div>
            <a href="{{ route('superadmin.partner-logos.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-black px-5 py-3 text-sm font-semibold text-white shadow hover:bg-neutral-800">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                <span>Tambah Logo</span>
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm font-semibold text-green-800 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="p-4 text-left">Urutan</th>
                            <th class="p-4 text-left">Preview Logo</th>
                            <th class="p-4 text-left">Nama Partner</th>
                            <th class="p-4 text-left">Link Web / Event</th>
                            <th class="p-4 text-left">Status</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logos as $logo)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 font-mono font-bold">{{ $logo->sort_order }}</td>
                                <td class="p-4">
                                    <div class="flex h-12 w-28 items-center justify-center rounded-lg border border-gray-200 bg-white p-2">
                                        <img src="{{ $logo->logoUrl() }}" alt="{{ $logo->name }}" class="max-h-full max-w-full object-contain filter grayscale contrast-125">
                                    </div>
                                </td>
                                <td class="p-4">
                                    <strong class="font-extrabold text-black">{{ $logo->name }}</strong>
                                </td>
                                <td class="p-4">
                                    @if($logo->url)
                                        <a href="{{ $logo->url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-blue-600 hover:underline">
                                            {{ Str::limit($logo->url, 30) }} &nearr;
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $logo->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $logo->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <a href="{{ route('superadmin.partner-logos.edit', $logo) }}" class="inline-flex rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.partner-logos.destroy', $logo) }}" class="inline" onsubmit="return confirm('Hapus logo {{ $logo->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center text-gray-500">
                                    <p class="font-bold">Belum ada logo yang diunggah.</p>
                                    <p class="mt-1 text-xs text-gray-400">Hero section landing page saat ini menampilkan logo placeholder default.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-superadmin-layout>
