<x-fg-layout>
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <p class="text-xs font-extrabold uppercase text-gray-500">Onboarding Photographer</p>
            <h1 class="mt-2">Selamat datang, {{ $user->name }}.</h1>
            <p class="helper mt-2">Profil Photographer Anda sudah aktif. Periksa data awal berikut sebelum masuk ke dashboard.</p>
        </div>

        <section class="rounded-2xl border bg-white p-6">
            <dl class="grid gap-5 sm:grid-cols-2">
                <div><dt class="text-xs font-bold uppercase text-gray-500">Nama</dt><dd class="mt-1 font-bold">{{ $user->name }}</dd></div>
                <div><dt class="text-xs font-bold uppercase text-gray-500">Studio</dt><dd class="mt-1 font-bold">{{ $user->studio_name }}</dd></div>
                <div><dt class="text-xs font-bold uppercase text-gray-500">WhatsApp</dt><dd class="mt-1 font-bold">{{ $user->whatsapp }}</dd></div>
                <div><dt class="text-xs font-bold uppercase text-gray-500">Email</dt><dd class="mt-1 font-bold">{{ $user->email }}</dd></div>
            </dl>

            <p class="helper mt-6">Setelah data dikonfirmasi, pilih Trial atau paket berbayar untuk membuka Creator Center.</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('fotografer.onboarding.store') }}">
                    @csrf
                    <button class="rounded-lg bg-black px-5 py-3 font-bold text-white">Lanjut pilih paket</button>
                </form>
            </div>
        </section>
    </div>
</x-fg-layout>
