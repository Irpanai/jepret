<x-guest-layout>
    <div class="mx-auto grid w-full max-w-5xl border border-public-line bg-white lg:grid-cols-[1fr_440px]">
        <section class="hidden bg-public-ink p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div><p class="text-xs font-extrabold uppercase text-white/55">JepretCFD account</p><h1 class="mt-4 text-5xl font-extrabold leading-[0.95]">Temukan momenmu. Simpan originalnya.</h1><p class="mt-5 max-w-md text-sm font-semibold leading-6 text-white/65">Masuk untuk melanjutkan checkout, melihat pembelian, dan mengunduh ulang foto yang sudah lunas.</p></div>
            <a href="{{ route('galeri') }}" class="text-xs font-extrabold uppercase text-white">Lihat galeri →</a>
        </section>
        <section class="p-6 sm:p-10">
            <p class="public-kicker">Selamat datang kembali</p><h2 class="mt-3 text-3xl font-extrabold text-public-ink">Masuk ke akun.</h2>
            <div class="mt-7 grid grid-cols-2 border border-public-line p-1 text-center text-xs font-extrabold uppercase"><a href="{{ route('login') }}" class="bg-public-ink px-3 py-3 text-white">Masuk</a><a href="{{ route('register') }}" class="px-3 py-3 text-public-muted">Daftar</a></div>
            <x-auth-session-status class="mt-5 text-sm font-semibold text-green-700" :status="session('status')" />
            <a href="{{ route('auth.google.redirect') }}" class="public-button public-button-secondary mt-6 w-full">Lanjutkan dengan Google</a>
            <div class="my-6 flex items-center gap-3"><span class="h-px flex-1 bg-public-line"></span><span class="text-[10px] font-extrabold uppercase text-public-muted">atau email</span><span class="h-px flex-1 bg-public-line"></span></div>
            <form method="POST" action="{{ route('login') }}" class="grid gap-5">@csrf
                <div><label for="email" class="text-xs font-extrabold uppercase text-public-muted">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
                <div><div class="flex justify-between gap-4"><label for="password" class="text-xs font-extrabold uppercase text-public-muted">Password</label><a href="{{ route('password.request') }}" class="text-xs font-bold text-public-muted hover:text-public-ink">Lupa password?</a></div><input id="password" type="password" name="password" required autocomplete="current-password" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('password')" class="mt-2" /></div>
                <label class="flex items-center gap-3 text-sm font-semibold text-public-muted"><input type="checkbox" name="remember" class="h-4 w-4 border-public-line text-public-ink focus:ring-public-ink"> Ingat saya</label>
                <button type="submit" class="public-button w-full">Masuk</button>
            </form>
        </section>
    </div>
</x-guest-layout>
