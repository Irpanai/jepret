<x-superadmin-layout>
    <form class="space-y-6" method="POST" action="{{ route('superadmin.packages.update', $package) }}">
        @csrf
        @method('PUT')
        <div><h1>Edit Package</h1><p class="helper mt-2">Perubahan hanya berlaku untuk checkout baru; revision akan bertambah.</p></div>
        @include('superadmin.packages._form')
    </form>
</x-superadmin-layout>
