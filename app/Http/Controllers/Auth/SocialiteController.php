<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect to Google OAuth provider.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $role = $request->query('role', 'pembeli');
        if (! in_array($role, ['pembeli', 'fotografer'], true)) {
            $role = 'pembeli';
        }

        session(['google_register_role' => $role]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google OAuth provider.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal melakukan otentikasi dengan Google. Silakan coba lagi.']);
        }

        $role = session()->pull('google_register_role', 'pembeli');
        if (! in_array($role, ['pembeli', 'fotografer'], true)) {
            $role = 'pembeli';
        }

        // Find existing user by google_id or email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $user->google_id ?? $googleUser->getId(),
                'avatar' => $googleUser->getAvatar() ?? $user->avatar,
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Pengguna Google',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => $role,
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);

        if ($user->role === 'pembeli') {
            return redirect()->intended(route('galeri', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
