<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiErrorResponse
{
    public static function make(
        string $message,
        int $status,
        ?array $errors = null,
        ?string $code = null,
    ): JsonResponse {
        $payload = [
            'message' => $message,
            'code' => $code ?? self::defaultCode($status),
            'request_id' => request()?->attributes->get('request_id'),
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected static function defaultCode(int $status): string
    {
        return match ($status) {
            404 => 'resource_not_found',
            422 => 'validation_error',
            default => 'api_error',
        };
    }
}
