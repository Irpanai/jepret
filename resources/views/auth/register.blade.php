<x-guest-layout>
    <div class="mx-auto grid w-full max-w-5xl border border-public-line bg-white lg:grid-cols-[1fr_480px]">
        <section class="hidden gap-8 bg-public-ink p-10 text-white lg:flex lg:flex-col">
            <div>
                <p class="text-xs font-extrabold uppercase text-white/55">Photographer Jepret</p>
                <h1 class="mt-4 text-5xl font-extrabold leading-[0.95]">Mulai menjual karya fotografimu.</h1>
            </div>
            <figure class="aspect-[736/981] w-full overflow-hidden border border-white/15">
                <img src="{{ asset('images/herophotographer.jpg') }}" alt="Photographer Jepret sedang mengabadikan sebuah momen" width="736" height="981" decoding="async" fetchpriority="high" class="h-full w-full object-cover grayscale">
            </figure>
            <a href="{{ route('photographers.index') }}" class="text-xs font-extrabold uppercase text-white">Lihat photographers →</a>
        </section>
        <section class="p-6 sm:p-10" x-data="{ showPassword: false, showConfirmation: false }">
            <p class="public-kicker">{{ $user ? 'Aktifkan akses' : 'Daftar sebagai' }}</p>
            <h2 class="mt-3 text-3xl font-extrabold text-public-ink">Photographer.</h2>
            <p class="mt-3 text-sm font-semibold leading-6 text-public-muted">{{ $user ? 'Lengkapi profil Photographer pada akun Buyer Anda.' : 'Isi data profil untuk membuat akun Photographer.' }}</p>
            @guest
                <div class="mt-7 grid grid-cols-2 border border-public-line p-1 text-center text-xs font-extrabold uppercase">
                    <a href="{{ route('login') }}" class="px-3 py-3 text-public-muted">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-public-ink px-3 py-3 text-white">Daftar</a>
                </div>
            @endguest
            <form method="POST" action="{{ route('register') }}" class="mt-7 grid gap-4">
                @csrf
                <div><label for="name" class="text-xs font-extrabold uppercase text-public-muted">Nama lengkap</label><input id="name" type="text" name="name" value="{{ old('name', $user?->name) }}" required autofocus autocomplete="name" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('name')" class="mt-2" /></div>
                <div><label for="studio_name" class="text-xs font-extrabold uppercase text-public-muted">Nama studio</label><input id="studio_name" type="text" name="studio_name" value="{{ old('studio_name', $user?->studio_name) }}" required autocomplete="organization" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('studio_name')" class="mt-2" /></div>
                <div><label for="whatsapp" class="text-xs font-extrabold uppercase text-public-muted">Nomor WhatsApp</label><input id="whatsapp" type="tel" name="whatsapp" value="{{ old('whatsapp', $user?->whatsapp) }}" required autocomplete="tel" placeholder="081234567890" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('whatsapp')" class="mt-2" /></div>
                <div><label for="email" class="text-xs font-extrabold uppercase text-public-muted">Email</label><input id="email" type="email" @auth value="{{ $user->email }}" readonly @else name="email" value="{{ old('email') }}" required @endauth autocomplete="username" class="public-input mt-2 min-h-12 px-3 read-only:bg-gray-100"><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
                @guest
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label for="password" class="text-xs font-extrabold uppercase text-public-muted">Password</label><input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" class="public-input mt-2 min-h-12 px-3"><button type="button" class="mt-2 text-xs font-bold text-public-muted" @click="showPassword = ! showPassword" x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'"></button><x-input-error :messages="$errors->get('password')" class="mt-2" /></div>
                        <div><label for="password_confirmation" class="text-xs font-extrabold uppercase text-public-muted">Konfirmasi password</label><input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" class="public-input mt-2 min-h-12 px-3"><button type="button" class="mt-2 text-xs font-bold text-public-muted" @click="showConfirmation = ! showConfirmation" x-text="showConfirmation ? 'Sembunyikan' : 'Tampilkan'"></button></div>
                    </div>
                @endguest
                <button type="submit" class="public-button mt-2 w-full">{{ $user ? 'Aktifkan Photographer' : 'Buat akun Photographer' }}</button>
            </form>
        </section>
    </div>
</x-guest-layout>
