<x-superadmin-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div><a href="{{ route('superadmin.photographers.index') }}" class="text-sm font-bold text-gray-500 hover:text-black">&larr; Photographers</a><h1 class="mt-3">Tambah Photographer</h1><p class="helper mt-2">Buat akun photographer dan lengkapi data profil awal.</p></div>
        @if($errors->any())<div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('superadmin.photographers.store') }}" class="rounded-2xl border bg-white p-5 sm:p-7">@csrf @include('superadmin.photographers._form')<div class="mt-7 flex justify-end"><button class="rounded-lg bg-black px-6 py-3 text-white">Buat Photographer</button></div></form>
    </div>
</x-superadmin-layout>

