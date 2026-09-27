<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect to Google OAuth provider.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google OAuth provider.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal melakukan otentikasi dengan Google. Silakan coba lagi.']);
        }

        $googleId = (string) $googleUser->getId();
        $email = $googleUser->getEmail();
        $user = User::where('google_id', $googleId)->first();

        if ($user) {
            $user->update([
                'avatar' => $googleUser->getAvatar() ?? $user->avatar,
            ]);
        } else {
            if (! $email || User::where('email', $email)->exists()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Email tersebut sudah digunakan akun lokal. Login dengan password terlebih dahulu; akun tidak ditautkan otomatis demi keamanan.',
                ]);
            }

            $user = DB::transaction(fn (): User => User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Pengguna Google',
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $googleUser->getAvatar(),
                'role' => 'pembeli',
                'email_verified_at' => now(),
            ]));
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if (! $user->hasRole('fotografer')) {
            return redirect()->intended(route('galeri', absolute: false));
        }

        if (! $user->hasCompletedPhotographerOnboarding()) {
            return redirect()->intended(route('fotografer.onboarding', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
