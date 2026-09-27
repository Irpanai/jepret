<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->loadMissing('subscription')->hasActiveSubscription()) {
            abort(403, 'Subscription aktif diperlukan untuk melakukan operasi ini.');
        }

        return $next($request);
    }
}
