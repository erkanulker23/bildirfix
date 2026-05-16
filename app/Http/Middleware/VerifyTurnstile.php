<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

final class VerifyTurnstile
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('services.turnstile.secret');

        if ($secret === '') {
            return $next($request);
        }

        $token = (string) $request->input('cf-turnstile-response');

        if ($token === '') {
            throw ValidationException::withMessages([
                'cf-turnstile' => __('Güvenlik doğrulaması bekleniyor.'),
            ]);
        }

        try {
            $response = Http::asForm()->timeout(8)->throw()->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ],
            );
        } catch (RequestException) {
            throw ValidationException::withMessages([
                'cf-turnstile' => __('Doğrulama servisi yanıt vermedi; tekrar deneyin.'),
            ]);
        }

        if (! filter_var($response->json('success'), FILTER_VALIDATE_BOOL)) {
            throw ValidationException::withMessages([
                'cf-turnstile' => __('Robot doğrulaması başarısız.'),
            ]);
        }

        return $next($request);
    }
}
