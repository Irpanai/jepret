<x-superadmin-layout>
    <div class="space-y-6">
        <div><h1>Storage Management</h1><p class="helper mt-2">Kuota paket dan override per fotografer dari data aplikasi.</p></div>
        <section class="rounded-xl border bg-white p-5"><p class="text-xs uppercase text-gray-500">Total digunakan</p><p class="metric mt-2">{{ number_format($totalStorage / 1024, 2) }} GB</p></section>
        @if($errors->any())<div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif

        <form id="storage-search" method="GET" action="{{ route('superadmin.storage') }}" class="relative max-w-xl">
            <label for="storage-search-input" class="sr-only">Cari fotografer berdasarkan nama atau email</label>
            <input id="storage-search-input" class="w-full rounded-lg border-gray-300 pr-24 text-sm" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email fotografer" autocomplete="off">
            <span id="storage-search-status" class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-gray-500" aria-live="polite"></span>
        </form>

        <div id="storage-results" aria-live="polite">@include('superadmin.storage-results', ['photographers' => $photographers])</div>
    </div>

    <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('storage-search');
                const input = document.getElementById('storage-search-input');
                const results = document.getElementById('storage-results');
                const status = document.getElementById('storage-search-status');
                let timer;
                let controller;

                const load = async (url) => {
                    controller?.abort();
                    controller = new AbortController();
                    status.textContent = 'Mencari…';
                    results.setAttribute('aria-busy', 'true');

                    try {
                        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: controller.signal });
                        if (!response.ok) throw new Error('Search request failed');
                        results.innerHTML = await response.text();
                        history.replaceState({}, '', url);
                        status.textContent = '';
                    } catch (error) {
                        if (error.name !== 'AbortError') status.textContent = 'Gagal mencari';
                    } finally {
                        results.removeAttribute('aria-busy');
                    }
                };

                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    load(`${form.action}?${new URLSearchParams(new FormData(form))}`);
                });
                input.addEventListener('input', () => {
                    clearTimeout(timer);
                    timer = setTimeout(() => form.requestSubmit(), 300);
                });
                results.addEventListener('click', (event) => {
                    const link = event.target.closest('a[href]');
                    if (link) {
                        event.preventDefault();
                        load(link.href);
                    }
                });
            });
    </script>
</x-superadmin-layout>
