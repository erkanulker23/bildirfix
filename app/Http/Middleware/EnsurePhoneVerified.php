<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneVerified
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Oturum açmanız gerekli.'),
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        if ($user->role === UserRole::Admin || $user->role === UserRole::SuperAdmin) {
            return $next($request);
        }

        if (! $user->hasVerifiedPhone()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Telefon doğrulaması gerekli.'),
                ], 403);
            }

            return redirect()->guest(route('verify.phone.form'));
        }

        return $next($request);
    }
}
