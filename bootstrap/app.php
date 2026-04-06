<?php

use App\Support\ApiErrorResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiErrorResponse::make(
                    'The given data was invalid.',
                    422,
                    $exception->errors(),
                );
            }
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiErrorResponse::make(
                    'The requested resource was not found.',
                    404,
                );
            }
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                if ($exception->getPrevious() instanceof ModelNotFoundException) {
                    return ApiErrorResponse::make(
                        'The requested resource was not found.',
                        404,
                    );
                }

                return ApiErrorResponse::make(
                    'The requested endpoint was not found.',
                    404,
                    code: 'route_not_found',
                );
            }
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            if ($request->is('api/*') && ! config('app.debug')) {
                return ApiErrorResponse::make(
                    'An unexpected error occurred.',
                    500,
                    code: 'internal_server_error',
                );
            }
        });
    })->create();
