<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\SiteIntegrations;
use Illuminate\Http\Response;

final class IndexNowKeyController extends Controller
{
    public function __invoke(string $key): Response
    {
        $stored = SiteIntegrations::fromPlatform()->indexnowKey;

        if ($stored === null || ! hash_equals($stored, $key)) {
            abort(404);
        }

        return response($stored, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
