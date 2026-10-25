<?php

use App\Http\Middleware\EnsureActiveSubscription;
use App\Http\Middleware\EnsureApprovedPhotographer;
use App\Http\Middleware\EnsurePhotographerSubscriptionStarted;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'subscription.active' => EnsureActiveSubscription::class,
            'subscription.started' => EnsurePhotographerSubscriptionStarted::class,
            'photographer.approved' => EnsureApprovedPhotographer::class,
        ]);
        $middleware->validateCsrfTokens(except: ['payments/doku/notification']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            if (! $request->is('fotografer/photos')) {
                return null;
            }

            $message = 'Ukuran upload melebihi batas server. Unggah ulang melalui antrean per file.';

            return $request->expectsJson()
                ? response()->json(['message' => $message, 'errors' => ['photos' => [$message]]], 413)
                : back()->withErrors(['photos' => $message]);
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
