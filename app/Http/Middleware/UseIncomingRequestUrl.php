<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * APP_URL farklı olsa bile (ör. Valet alanı vs php artisan serve) tarayıcıdaki host ile
 * route()/asset()/url() üretimini hizalar — tıklanabilir linkler yanlış domaine gitmez.
 */
final class UseIncomingRequestUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->runningInConsole()) {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }

        return $next($request);
    }
}
