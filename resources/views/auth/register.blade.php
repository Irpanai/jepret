<x-guest-layout>
    <div class="mx-auto grid w-full max-w-5xl border border-public-line bg-white lg:grid-cols-[1fr_480px]">
        <section class="hidden bg-public-ink p-10 text-white lg:flex lg:flex-col lg:justify-between"><div><p class="text-xs font-extrabold uppercase text-white/55">Bergabung dengan Jepret</p><h1 class="mt-4 text-5xl font-extrabold leading-[0.95]">Satu akun untuk membeli atau berkarya.</h1><p class="mt-5 max-w-md text-sm font-semibold leading-6 text-white/65">Buyer dapat menyimpan pembelian. Photographer mendapat ruang untuk mengelola karya dan penjualan dengan pembagian 90/10.</p></div><a href="{{ route('photographers.index') }}" class="text-xs font-extrabold uppercase text-white">Lihat photographers →</a></section>
        <section class="p-6 sm:p-10" x-data="{ showPassword: false, showConfirmation: false }">
            <p class="public-kicker">Buat akun</p><h2 class="mt-3 text-3xl font-extrabold text-public-ink">Mulai dari sini.</h2>
            <div class="mt-7 grid grid-cols-2 border border-public-line p-1 text-center text-xs font-extrabold uppercase"><a href="{{ route('login') }}" class="px-3 py-3 text-public-muted">Masuk</a><a href="{{ route('register') }}" class="bg-public-ink px-3 py-3 text-white">Daftar</a></div>
            <fieldset class="mt-6"><legend class="text-xs font-extrabold uppercase text-public-muted">Daftar sebagai</legend><div class="mt-2 grid grid-cols-2 gap-2"><label class="flex cursor-pointer items-center gap-2 border border-public-line px-3 py-3 text-sm font-bold"><input type="radio" name="account_role" value="pembeli" class="text-public-ink focus:ring-public-ink" @checked(old('role', 'pembeli') === 'pembeli')> Buyer</label><label class="flex cursor-pointer items-center gap-2 border border-public-line px-3 py-3 text-sm font-bold"><input type="radio" name="account_role" value="fotografer" class="text-public-ink focus:ring-public-ink" @checked(old('role') === 'fotografer')> Photographer</label></div></fieldset>
            <a id="google-register" href="{{ route('auth.google.redirect', ['role' => old('role', 'pembeli')]) }}" class="public-button public-button-secondary mt-4 w-full gap-3">
                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M21.6 12.23c0-.71-.06-1.4-.18-2.07H12v3.92h5.38a4.6 4.6 0 0 1-2 3.02v2.54h3.24c1.9-1.75 2.98-4.32 2.98-7.41Z"/><path fill="#34A853" d="M12 22c2.7 0 4.97-.9 6.62-2.36l-3.24-2.54c-.9.6-2.05.96-3.38.96-2.61 0-4.82-1.76-5.61-4.13H3.04v2.62A10 10 0 0 0 12 22Z"/><path fill="#FBBC05" d="M6.39 13.93A6.01 6.01 0 0 1 6.07 12c0-.67.11-1.32.32-1.93V7.45H3.04A10 10 0 0 0 2 12c0 1.61.38 3.14 1.04 4.55l3.35-2.62Z"/><path fill="#EA4335" d="M12 5.94c1.47 0 2.8.51 3.84 1.5l2.85-2.86A9.57 9.57 0 0 0 12 2a10 10 0 0 0-8.96 5.45l3.35 2.62C7.18 7.7 9.39 5.94 12 5.94Z"/></svg>
                Lanjutkan dengan Google
            </a>
            <div class="my-6 flex items-center gap-3"><span class="h-px flex-1 bg-public-line"></span><span class="text-[10px] font-extrabold uppercase text-public-muted">atau email</span><span class="h-px flex-1 bg-public-line"></span></div>
            <form method="POST" action="{{ route('register') }}" class="grid gap-4">@csrf<input id="role-input" type="hidden" name="role" value="{{ old('role', 'pembeli') }}">
                <div><label for="name" class="text-xs font-extrabold uppercase text-public-muted">Nama</label><input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('name')" class="mt-2" /></div>
                <div><label for="email" class="text-xs font-extrabold uppercase text-public-muted">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="text-xs font-extrabold uppercase text-public-muted">Password</label>
                        <div class="relative mt-2"><input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" class="public-input min-h-12 px-3 pr-12"><button type="button" @click="showPassword = ! showPassword" class="absolute inset-y-0 right-0 grid w-12 place-items-center text-public-muted hover:text-public-ink" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'" :title="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"><svg x-show="! showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg><svg x-cloak x-show="showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 5.1A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-2.1 3M6.6 6.6C3.6 8.4 2 12 2 12s3.5 7 10 7a10 10 0 0 0 4.1-.9"/></svg></button></div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="text-xs font-extrabold uppercase text-public-muted">Ulangi password</label>
                        <div class="relative mt-2"><input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" class="public-input min-h-12 px-3 pr-12"><button type="button" @click="showConfirmation = ! showConfirmation" class="absolute inset-y-0 right-0 grid w-12 place-items-center text-public-muted hover:text-public-ink" :aria-label="showConfirmation ? 'Sembunyikan konfirmasi password' : 'Tampilkan konfirmasi password'" :title="showConfirmation ? 'Sembunyikan konfirmasi password' : 'Tampilkan konfirmasi password'"><svg x-show="! showConfirmation" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg><svg x-cloak x-show="showConfirmation" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 5.1A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-2.1 3M6.6 6.6C3.6 8.4 2 12 2 12s3.5 7 10 7a10 10 0 0 0 4.1-.9"/></svg></button></div>
                    </div>
                </div>
                <button type="submit" class="public-button mt-2 w-full">Buat Akun</button>
            </form>
        </section>
    </div>
    <script>
        document.querySelectorAll('input[name="account_role"]').forEach((input) => input.addEventListener('change', (event) => {
            const role = event.target.value;
            document.getElementById('role-input').value = role;
            document.getElementById('google-register').href = @json(route('auth.google.redirect')) + '?role=' + role;
        }));
    </script>
</x-guest-layout>
