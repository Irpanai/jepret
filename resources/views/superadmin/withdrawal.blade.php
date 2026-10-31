<x-superadmin-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Creator Withdrawals</h1>
            <p class="helper mt-2 text-sm text-gray-500">Satu alur penarikan untuk fotografer dan Super Admin.</p>
        </div>

        {{-- Metric Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['Pending', $metrics['pending_count']],
                ['Nominal pending', 'Rp' . number_format($metrics['pending_amount'])],
                ['Held', $metrics['held']],
                ['Berhasil', 'Rp' . number_format($metrics['success_amount'])]
            ] as $card)
                <div class="rounded-xl border bg-white p-5 shadow-sm">
                    <p class="text-xs uppercase text-gray-500">{{ $card[0] }}</p>
                    <p class="metric mt-2 text-xl font-bold">{{ $card[1] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Filter & Search --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-2">
                @foreach(['all', 'pending', 'held', 'success', 'rejected'] as $item)
                    <a class="button rounded-full px-4 py-2 text-sm {{ $status === $item ? 'bg-black text-white' : 'border bg-white text-gray-700 hover:bg-gray-50' }}"
                        href="{{ route('superadmin.earnings', array_filter(['status' => $item, 'q' => request('q')])) }}">
                        {{ ucfirst($item) }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('superadmin.earnings') }}" class="flex gap-2">
                @if(request('status') && request('status') !== 'all')
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari ID (#WD-20), nama kreator..."
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm w-64 focus:border-black focus:ring-black">
                <button type="submit" class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white hover:bg-neutral-800">
                    Cari
                </button>
                @if(request('q'))
                    <a href="{{ route('superadmin.earnings', request('status') ? ['status' => request('status')] : []) }}"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 flex items-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 border border-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Form Batch Approval --}}
        <form id="batch-withdrawal-form" method="POST" action="{{ route('superadmin.withdrawals.batch') }}" onsubmit="return confirm('Setujui semua penarikan terpilih?')">
            @csrf
        </form>

        <div class="overflow-hidden rounded-2xl border bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600">
                        <tr>
                            <th class="p-4 text-center w-12"></th>
                            <th class="p-4 text-left">Kreator</th>
                            <th class="p-4 text-left">Tujuan</th>
                            <th class="p-4 text-right">Nominal</th>
                            <th class="p-4 text-left">Status</th>
                            <th class="p-4 text-left">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        @forelse($withdrawals as $wd)
                            <tr class="{{ request('q') == $wd->id ? 'bg-amber-50/50' : 'hover:bg-gray-50/50' }}">
                                <td class="p-4 text-center">
                                    @if(in_array($wd->status, ['pending', 'held']))
                                        <input type="checkbox" name="withdrawal_ids[]" value="{{ $wd->id }}" form="batch-withdrawal-form" class="rounded border-gray-300 text-black focus:ring-black">
                                    @endif
                                </td>
                                <td class="p-4">
                                    <strong class="text-gray-900">{{ $wd->fotografer?->name ?? 'User #' . $wd->fotografer_id }}</strong>
                                    <small class="block text-gray-500 font-mono">#WD-{{ $wd->id }}</small>
                                </td>
                                <td class="p-4">
                                    <span class="font-medium text-gray-800">{{ $wd->metode_pembayaran }}</span>
                                    <small class="block text-gray-500">{{ $wd->nomor_tujuan }}</small>
                                </td>
                                <td class="p-4 text-right font-bold text-gray-900">
                                    Rp{{ number_format($wd->jumlah_tarik) }}
                                </td>
                                <td class="p-4 uppercase font-semibold text-xs">
                                    <span class="inline-block rounded-full px-2.5 py-1 {{
                                        $wd->status === 'success' ? 'bg-green-100 text-green-800' :
                                        ($wd->status === 'held' ? 'bg-amber-100 text-amber-800' :
                                        ($wd->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))
                                    }}">
                                        {{ $wd->status }}
                                    </span>
                                    @if($wd->admin_notes)
                                        <small class="block normal-case text-red-700 mt-1">{{ $wd->admin_notes }}</small>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if(in_array($wd->status, ['pending', 'held']))
                                        <form method="POST" action="{{ route('superadmin.withdrawals.review', $wd) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <button type="submit" class="rounded border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold hover:bg-gray-50" name="status" value="held">Hold</button>
                                            <button type="submit" class="rounded bg-black px-3 py-1.5 text-xs font-semibold text-white hover:bg-neutral-800" name="status" value="success" onclick="return confirm('Approve penarikan ini?')">Approve</button>
                                            <input class="w-32 rounded border-gray-300 text-xs py-1.5 px-2" name="reason" placeholder="Alasan reject">
                                            <button type="submit" class="rounded border border-red-300 bg-white px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50" name="status" value="rejected" onclick="return confirm('Reject dan kembalikan saldo?')">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-500">{{ $wd->processed_at?->timezone(config('app.display_timezone'))->format('d M Y H:i') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="p-10 text-center text-gray-500" colspan="6">Tidak ada penarikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between p-4 border-t border-gray-100">
                <button type="submit" form="batch-withdrawal-form" class="rounded bg-black px-4 py-2 text-sm font-semibold text-white hover:bg-neutral-800">
                    Approve selected
                </button>
                <div>
                    {{ $withdrawals->links() }}
                </div>
            </div>
        </div>
    </div>
</x-superadmin-layout>
