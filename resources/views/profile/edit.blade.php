@php
    $layout = match (Auth::user()->role) {
        'fotografer' => 'fg-layout',
        'superadmin' => 'superadmin-layout',
        default => 'marketplace-layout',
    };
@endphp

<x-dynamic-component :component="$layout">
    <div class="space-y-8 w-full">
        
        <!-- Header Section -->
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="text-[10px] font-bold tracking-widest text-gray-400 uppercase">Pengaturan Akun</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-2 tracking-tight">
                Profil Saya
            </h1>
            <p class="text-gray-500 font-medium text-sm">
                Kelola informasi profil, alamat email, dan pengaturan keamanan akun Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Form -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Password Form -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <!-- Delete Form -->
                <div class="bg-red-50/50 rounded-2xl border border-red-100 p-6 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-red-500"></div>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
