<x-superadmin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1>Package Catalog</h1>
                <p class="helper mt-2">Harga dan entitlement yang dipakai Pricing serta checkout baru.</p>
            </div>
            <a href="{{ route('superadmin.packages.create') }}" class="rounded-lg bg-black px-5 py-3 text-sm font-semibold text-white">Tambah paket</a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
        @endif

        <div class="overflow-hidden rounded-2xl border bg-white">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(['Urutan', 'Paket', 'Harga', 'Durasi', 'Storage', 'Status', 'Versi', ''] as $head)
                                <th class="p-4 text-left">{{ $head }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($packages as $package)
                            <tr>
                                <td class="p-4">{{ $package->sort_order }}</td>
                                <td class="p-4">
                                    <strong>{{ $package->display_name ?? $package->nama_paket }}</strong>
                                    <small class="block text-gray-500">{{ $package->code }}</small>
                                </td>
                                <td class="p-4">{{ $package->is_custom ? 'Custom' : 'Rp'.number_format($package->harga, 0, ',', '.') }}</td>
                                <td class="p-4">{{ $package->duration_days ? $package->duration_days.' hari' : 'Custom' }}</td>
                                <td class="p-4">
                                    @if($package->storageQuotaMb() === null)
                                        Custom
                                    @elseif($package->storageQuotaMb() >= 1024)
                                        {{ number_format($package->storageQuotaMb() / 1024, 0) }} GB
                                    @else
                                        {{ number_format($package->storageQuotaMb(), 0) }} MB
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $package->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $package->is_legacy ? 'Legacy nonaktif' : ($package->is_active ? 'Aktif' : 'Nonaktif') }}
                                    </span>
                                </td>
                                <td class="p-4">v{{ $package->revision }}</td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('superadmin.packages.edit', $package) }}" class="rounded-lg border px-3 py-2 text-sm font-semibold">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="p-8 text-center text-gray-500">Belum ada paket.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-superadmin-layout>
