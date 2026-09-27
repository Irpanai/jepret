<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\SubscriptionOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __invoke(): View
    {
        $pricingPlans = Package::query()->publiclyAvailable()->ordered()->get();

        return view('pricing', compact('pricingPlans'));
    }

    public function subscriptions(Request $request): View
    {
        $pricingPlans = Package::query()->publiclyAvailable()->ordered()->get();
        $user = $request->user()->loadMissing('subscription');
        $pendingOrder = SubscriptionOrder::query()
            ->whereBelongsTo($user)
            ->where('status', 'pending')
            ->whereNotNull('snap_token')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return view('subscriptions.plans', compact('pricingPlans', 'user', 'pendingOrder'));
    }
}
