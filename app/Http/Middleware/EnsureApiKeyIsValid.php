<?php

namespace App\Http\Middleware;

use App\Support\ApiErrorResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKeyIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = (string) config('api.write_key');

        if ($configuredKey === '') {
            return $next($request);
        }

        $providedKey = (string) $request->header('X-API-Key');

        if (! hash_equals($configuredKey, $providedKey)) {
            return ApiErrorResponse::make(
                'The provided API key is invalid.',
                401,
                code: 'unauthorized',
            );
        }

        return $next($request);
    }
}
