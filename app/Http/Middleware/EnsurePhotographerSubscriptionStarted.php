<?php

namespace App\Http\Middleware;

use App\Models\SubscriptionOrder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhotographerSubscriptionStarted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->loadMissing('subscription')->subscription) {
            return $next($request);
        }

        $pendingOrder = SubscriptionOrder::query()
            ->where('user_id', $user?->id)
            ->where('status', 'pending')
            ->whereNotNull('snap_token')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($pendingOrder) {
            return redirect()->route('subscriptions.payment', $pendingOrder);
        }

        return redirect()->route('subscriptions.plans')
            ->with('status', 'Pilih dan aktifkan paket subscription untuk membuka Creator Center.');

    }
}
