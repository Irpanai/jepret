<x-superadmin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div><h1>Photographers</h1><p class="helper mt-2">Kelola akun, status, profil, kamera, karya, dan data operasional photographer.</p></div>
            <a href="{{ route('superadmin.photographers.create') }}" class="button inline-flex min-h-11 items-center justify-center rounded-lg bg-black px-5 text-white">Tambah Photographer</a>
        </div>

        @if(session('success'))<div class="rounded-lg bg-green-50 p-4 text-sm font-semibold text-green-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="rounded-lg bg-red-50 p-4 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>@endif

        <div class="grid gap-4 sm:grid-cols-3">
            @foreach([['Total',$counts['total']],['Aktif',$counts['active']],['Nonaktif',$counts['inactive']]] as [$label,$value])
                <div class="rounded-xl border bg-white p-5"><p class="text-xs font-bold uppercase text-gray-500">{{ $label }}</p><p class="metric mt-2">{{ number_format($value) }}</p></div>
            @endforeach
        </div>

        <form method="GET" class="grid gap-3 rounded-xl border bg-white p-4 md:grid-cols-[minmax(260px,1fr)_180px_180px_auto]">
            <input name="q" value="{{ request('q') }}" class="rounded-lg border-gray-300" placeholder="Cari nama, email, atau studio">
            <select name="account_status" class="rounded-lg border-gray-300"><option value="">Semua status akun</option><option value="active" @selected(request('account_status') === 'active')>Aktif</option><option value="inactive" @selected(request('account_status') === 'inactive')>Nonaktif</option></select>
            <select name="approval_status" class="rounded-lg border-gray-300"><option value="">Semua approval</option><option value="approved" @selected(request('approval_status') === 'approved')>Approved</option><option value="pending" @selected(request('approval_status') === 'pending')>Pending</option><option value="rejected" @selected(request('approval_status') === 'rejected')>Rejected</option></select>
            <button class="rounded-lg bg-black px-5 py-3 text-white">Filter</button>
        </form>

        <div class="overflow-hidden rounded-2xl border bg-white">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px]">
                    <thead class="bg-gray-50"><tr>@foreach(['Photographer','Status','Approval','Data','Penjualan','Paket','Aksi'] as $head)<th class="p-4 text-left">{{ $head }}</th>@endforeach</tr></thead>
                    <tbody class="divide-y">
                        @forelse($photographers as $photographer)
                            <tr>
                                <td class="p-4"><div class="flex items-center gap-3"><img src="{{ $photographer->profilePhotoUrl() }}" alt="" class="h-11 w-11 rounded-full object-cover"><div><a href="{{ route('superadmin.photographers.show', $photographer) }}" class="font-extrabold hover:underline">{{ $photographer->studio_name ?: $photographer->name }}</a><p class="text-xs text-gray-500">{{ $photographer->email }}</p></div></div></td>
                                <td class="p-4"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $photographer->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">{{ $photographer->is_active ? 'AKTIF' : 'NONAKTIF' }}</span></td>
                                <td class="p-4"><span class="text-xs font-bold uppercase">{{ $photographer->verificationState() }}</span></td>
                                <td class="p-4 text-xs text-gray-600">{{ $photographer->photos_count }} foto<br>{{ $photographer->events_count }} event &middot; {{ $photographer->cameras_count }} kamera</td>
                                <td class="p-4 font-bold">{{ $photographer->paid_sales_count }}</td>
                                <td class="p-4 text-sm">{{ $photographer->package?->display_name ?? $photographer->package?->nama_paket ?? '—' }}</td>
                                <td class="p-4"><div class="flex items-center gap-2"><a href="{{ route('superadmin.photographers.show', $photographer) }}" class="rounded-lg border px-3 py-2 text-xs font-bold">Detail</a><form method="POST" action="{{ route('superadmin.photographers.status', $photographer) }}">@csrf @method('PATCH')<input type="hidden" name="is_active" value="{{ $photographer->is_active ? 0 : 1 }}"><button class="rounded-lg px-3 py-2 text-xs font-bold {{ $photographer->is_active ? 'border border-red-200 text-red-700' : 'bg-black text-white' }}" onclick="return confirm('{{ $photographer->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun ini?')">{{ $photographer->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form></div></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-10 text-center text-gray-500">Tidak ada photographer sesuai filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t p-4">{{ $photographers->links() }}</div>
        </div>
    </div>
</x-superadmin-layout>

