<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhotographerOnboardingController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasCompletedPhotographerOnboarding()) {
            return redirect()->route($request->user()->subscription()->exists() ? 'fotografer.dashboard' : 'subscriptions.plans');
        }

        return view('fotografer.onboarding', ['user' => $request->user()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->forceFill([
            'photographer_onboarded_at' => $request->user()->photographer_onboarded_at ?? now(),
        ])->save();

        return redirect()->route('subscriptions.plans');
    }
}
