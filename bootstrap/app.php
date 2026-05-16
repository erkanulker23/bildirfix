<?php

use App\Http\Middleware\EnsurePhoneVerified;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\UseIncomingRequestUrl;
use App\Http\Middleware\VerifyTurnstile;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('web', [
            UseIncomingRequestUrl::class,
        ]);

        $middleware->alias([
            'role' => EnsureRole::class,
            'verified.phone' => EnsurePhoneVerified::class,
            'turnstile' => VerifyTurnstile::class,
        ]);

        /** Cloudflare / TLS sonlandırma: doğru müşteri IP ve şema için (HTTP_CF_* güvenilir) */
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
