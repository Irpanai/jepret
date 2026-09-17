<x-guest-layout>
    <div class="mx-auto grid w-full max-w-5xl border border-public-line bg-white lg:grid-cols-[1fr_480px]">
        <section class="hidden bg-public-ink p-10 text-white lg:flex lg:flex-col lg:justify-between"><div><p class="text-xs font-extrabold uppercase text-white/55">Bergabung dengan JepretCFD</p><h1 class="mt-4 text-5xl font-extrabold leading-[0.95]">Satu akun untuk membeli atau berkarya.</h1><p class="mt-5 max-w-md text-sm font-semibold leading-6 text-white/65">Buyer dapat menyimpan pembelian. Photographer mendapat ruang untuk mengelola karya dan penjualan dengan pembagian 90/10.</p></div><a href="{{ route('photographers.index') }}" class="text-xs font-extrabold uppercase text-white">Lihat photographers →</a></section>
        <section class="p-6 sm:p-10">
            <p class="public-kicker">Buat akun</p><h2 class="mt-3 text-3xl font-extrabold text-public-ink">Mulai dari sini.</h2>
            <div class="mt-7 grid grid-cols-2 border border-public-line p-1 text-center text-xs font-extrabold uppercase"><a href="{{ route('login') }}" class="px-3 py-3 text-public-muted">Masuk</a><a href="{{ route('register') }}" class="bg-public-ink px-3 py-3 text-white">Daftar</a></div>
            <fieldset class="mt-6"><legend class="text-xs font-extrabold uppercase text-public-muted">Daftar sebagai</legend><div class="mt-2 grid grid-cols-2 gap-2"><label class="flex cursor-pointer items-center gap-2 border border-public-line px-3 py-3 text-sm font-bold"><input type="radio" name="account_role" value="pembeli" class="text-public-ink focus:ring-public-ink" @checked(old('role', 'pembeli') === 'pembeli')> Buyer</label><label class="flex cursor-pointer items-center gap-2 border border-public-line px-3 py-3 text-sm font-bold"><input type="radio" name="account_role" value="fotografer" class="text-public-ink focus:ring-public-ink" @checked(old('role') === 'fotografer')> Photographer</label></div></fieldset>
            <a id="google-register" href="{{ route('auth.google.redirect', ['role' => old('role', 'pembeli')]) }}" class="public-button public-button-secondary mt-4 w-full">Lanjutkan dengan Google</a>
            <div class="my-6 flex items-center gap-3"><span class="h-px flex-1 bg-public-line"></span><span class="text-[10px] font-extrabold uppercase text-public-muted">atau email</span><span class="h-px flex-1 bg-public-line"></span></div>
            <form method="POST" action="{{ route('register') }}" class="grid gap-4">@csrf<input id="role-input" type="hidden" name="role" value="{{ old('role', 'pembeli') }}">
                <div><label for="name" class="text-xs font-extrabold uppercase text-public-muted">Nama</label><input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('name')" class="mt-2" /></div>
                <div><label for="email" class="text-xs font-extrabold uppercase text-public-muted">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
                <div class="grid gap-4 sm:grid-cols-2"><div><label for="password" class="text-xs font-extrabold uppercase text-public-muted">Password</label><input id="password" type="password" name="password" required autocomplete="new-password" class="public-input mt-2 min-h-12 px-3"><x-input-error :messages="$errors->get('password')" class="mt-2" /></div><div><label for="password_confirmation" class="text-xs font-extrabold uppercase text-public-muted">Ulangi password</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="public-input mt-2 min-h-12 px-3"></div></div>
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
