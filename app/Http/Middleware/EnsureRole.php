<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $allowed = collect($roles)
            ->flatMap(fn (string $r) => array_map(trim(...), explode(',', $r)))
            ->filter()
            ->values();

        if ($allowed->isEmpty() || ! $allowed->contains($user->role->value)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
