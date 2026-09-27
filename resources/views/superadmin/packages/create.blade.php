<x-superadmin-layout>
    <form class="space-y-6" method="POST" action="{{ route('superadmin.packages.store') }}">
        @csrf
        <div><h1>Tambah Package</h1><p class="helper mt-2">Paket aktif akan muncul pada Pricing tanpa deployment.</p></div>
        @include('superadmin.packages._form')
    </form>
</x-superadmin-layout>
