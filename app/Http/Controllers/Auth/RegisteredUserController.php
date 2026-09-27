<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterPhotographerRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasRole('fotografer')) {
            return redirect()->route($user->subscription()->exists() ? 'fotografer.dashboard' : 'subscriptions.plans');
        }

        return view('auth.register', ['user' => $user]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterPhotographerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $isNewUser = $request->user() === null;

        $user = DB::transaction(function () use ($data, $request, $isNewUser): User {
            if ($isNewUser) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => 'fotografer',
                ]);
                $user->forceFill([
                    'studio_name' => $data['studio_name'],
                    'whatsapp' => User::normalizeWhatsapp($data['whatsapp']),
                    'email_verified_at' => now(),
                    'is_verified' => true,
                    'verified_at' => now(),
                    'photographer_onboarded_at' => now(),
                ])->save();

                event(new Registered($user));

                return $user;
            }

            $user = User::query()->lockForUpdate()->findOrFail($request->user()->id);

            if (! $user->hasRole('fotografer')) {
                $user->forceFill([
                    'name' => $data['name'],
                    'studio_name' => $data['studio_name'],
                    'whatsapp' => User::normalizeWhatsapp($data['whatsapp']),
                    'role' => 'fotografer',
                    'is_verified' => true,
                    'verified_at' => now(),
                    'verification_rejection_reason' => null,
                    'rejected_at' => null,
                    'photographer_onboarded_at' => now(),
                ])->save();
            }

            return $user;
        });

        if ($isNewUser) {
            Auth::login($user);
        }

        $request->session()->regenerate();

        return redirect()->route($user->subscription()->exists() ? 'fotografer.dashboard' : 'subscriptions.plans');
    }
}
