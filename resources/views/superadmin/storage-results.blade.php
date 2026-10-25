<div class="overflow-hidden rounded-2xl border bg-white">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[960px]">
            <thead class="bg-gray-50"><tr>@foreach(['Fotografer', 'Paket', 'Kuota efektif', 'Digunakan', 'Tersisa', 'Penggunaan', 'Foto', 'Upload terakhir', 'Sumber', 'Override'] as $head)<th class="p-4 text-left">{{ $head }}</th>@endforeach</tr></thead>
            <tbody class="divide-y">
                @forelse($photographers as $user)
                    @php($quota = $user->effectiveQuotaMb())
                    <tr>
                        <td class="p-4"><strong>{{ $user->name }}</strong><small class="block text-gray-500">{{ $user->email }}</small></td>
                        <td class="p-4">{{ $user->package?->display_name ?? $user->package?->nama_paket ?? '—' }}</td>
                        <td class="p-4">{{ number_format($quota / 1024, 2) }} GB</td>
                        <td class="p-4">{{ number_format($user->storage_terpakai_mb / 1024, 2) }} GB</td>
                        <td class="p-4">{{ number_format(max(0, $quota - $user->storage_terpakai_mb) / 1024, 2) }} GB</td>
                        <td class="p-4">{{ $quota ? number_format(min(100, $user->storage_terpakai_mb / $quota * 100), 1) : 0 }}%</td>
                        <td class="p-4">{{ $user->photos_count }}</td>
                        <td class="p-4">{{ $user->photos_max_created_at ? \Carbon\Carbon::parse($user->photos_max_created_at)->timezone(config('app.display_timezone'))->format('d M Y') : '—' }}</td>
                        <td class="p-4">{{ $user->storage_quota_override_mb !== null ? 'Override' : 'Paket' }}</td>
                        <td class="p-4"><form class="flex gap-2" method="POST" action="{{ route('superadmin.storage.update', $user) }}">@csrf @method('PATCH')<input class="w-28 rounded border-gray-300 text-sm" type="number" step="0.01" name="quota_mb" value="{{ $user->storage_quota_override_mb }}" placeholder="MB paket"><button class="rounded border px-3 py-2" onclick="return confirm('Ubah kuota fotografer ini?')">Simpan</button></form></td>
                    </tr>
                @empty
                    <tr><td class="p-8 text-center text-gray-500" colspan="10">Tidak ada fotografer yang cocok. Coba nama atau email lain.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $photographers->links() }}</div>
</div>
