<x-guest-layout>
    <div class="mx-auto grid w-full max-w-5xl border border-public-line bg-white lg:grid-cols-[1fr_440px]">
        <section class="hidden bg-public-ink p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div><p class="text-xs font-extrabold uppercase text-white/55">JepretCFD account</p><h1 class="mt-4 text-5xl font-extrabold leading-[0.95]">Temukan momenmu. Simpan originalnya.</h1><p class="mt-5 max-w-md text-sm font-semibold leading-6 text-white/65">Masuk untuk melanjutkan checkout, melihat pembelian, dan mengunduh ulang foto yang sudah lunas.</p></div>
            <a href="{{ route('galeri') }}" class="text-xs font-extrabold uppercase text-white">Lihat galeri →</a>
        </section>
        <section class="p-6 sm:p-10" x-data="{ showPassword: false }">
            <p class="public-kicker">Selamat datang kembali</p><h2 class="mt-3 text-3xl font-extrabold text-public-ink">Masuk ke akun.</h2>
            <div class="mt-7 grid grid-cols-2 border border-public-line p-1 text-center text-xs font-extrabold uppercase"><a href="{{ route('login') }}" class="bg-public-ink px-3 py-3 text-white">Masuk</a><a href="{{ route('register') }}" class="px-3 py-3 text-public-muted">Daftar</a></div>
            <x-auth-session-status class="mt-5 text-sm font-semibold text-green-700" :status="session('status')" />
            <a href="{{ route('auth.google.redirect') }}" class="public-button public-button-secondary mt-6 w-full gap-3">
                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M21.6 12.23c0-.71-.06-1.4-.18-2.07H12v3.92h5.38a4.6 4.6 0 0 1-2 3.02v2.54h3.24c1.9-1.75 2.98-4.32 2.98-7.41Z"/>
                    <path fill="#34A853" d="M12 22c2.7 0 4.97-.9 6.62-2.36l-3.24-2.54c-.9.6-2.05.96-3.38.96-2.61 0-4.82-1.76-5.61-4.13H3.04v2.62A10 10 0 0 0 12 22Z"/>
                    <path fill="#FBBC05" d="M6.39 13.93A6.01 6.01 0 0 1 6.07 12c0-.67.11-1.32.32-1.93V7.45H3.04A10 10 0 0 0 2 12c0 1.61.38 3.14 1.04 4.55l3.35-2.62Z"/>
                    <path fill="#EA4335" d="M12 5.94c1.47 0 2.8.51 3.84 1.5l2.85-2.86A9.57 9.57 0 0 0 12 2a10 10 0 0 0-8.96 5.45l3.35 2.62C7.18 7.7 9.39 5.94 12 5.94Z"/>
                </svg>
                Lanjutkan dengan Google
            </a>
            <div class="my-6 flex items-center gap-3"><span class="h-px flex-1 bg-public-line"></span><span class="text-[10px] font-extrabold uppercase text-public-muted">atau email</span><span class="h-px flex-1 bg-public-line"></span></div>
            <form method="POST" action="{{ route('login') }}" class="grid gap-5">@csrf
                <div><label for="email" class="text-xs font-extrabold uppercase text-public-muted">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
                <div>
                    <div class="flex justify-between gap-4"><label for="password" class="text-xs font-extrabold uppercase text-public-muted">Password</label><a href="{{ route('password.request') }}" class="text-xs font-bold text-public-muted hover:text-public-ink">Lupa password?</a></div>
                    <div class="relative mt-2">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" class="public-input min-h-12 px-3 pr-12">
                        <button type="button" @click="showPassword = ! showPassword" class="absolute inset-y-0 right-0 grid w-12 place-items-center text-public-muted hover:text-public-ink" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'" :title="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                            <svg x-show="! showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-cloak x-show="showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 5.1A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-2.1 3M6.6 6.6C3.6 8.4 2 12 2 12s3.5 7 10 7a10 10 0 0 0 4.1-.9"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <label class="flex items-center gap-3 text-sm font-semibold text-public-muted"><input type="checkbox" name="remember" class="h-4 w-4 border-public-line text-public-ink focus:ring-public-ink"> Ingat saya</label>
                <button type="submit" class="public-button w-full">Masuk</button>
            </form>
        </section>
    </div>
</x-guest-layout>
