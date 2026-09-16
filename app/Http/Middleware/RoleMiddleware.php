<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            abort(401, 'Silakan login terlebih dahulu.');
        }
        
        if ($request->user()->role !== $role) {
            $userRole = $request->user()->role;
            $userEmail = $request->user()->email;
            abort(403, "Akses Ditolak (403). Akun Anda ($userEmail) terdaftar sebagai '$userRole', namun halaman ini membutuhkan akses '$role'.");
        }

        return $next($request);
    }
}
