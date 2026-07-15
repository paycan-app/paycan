<?php

use App\Http\Middleware\CheckInstallation;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Route::middleware('web')->group(base_path('routes/portal.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            CheckInstallation::class,
            //    SetLocale::class,
            //    HandleAppearance::class,
            //    HandleInertiaRequests::class,
            //    AddLinkHeadersForPreloadedAssets::class,
        ]);

        // API rate limiting (no session-based auth - public API)
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->throttleApi('60,1');

        // Custom middleware aliases
        $middleware->alias([
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'api.key' => \App\Http\Middleware\ValidateApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle ModelNotFoundException (route model binding failures)
        $exceptions->render(function (Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Resource not found',
                    'message' => 'The requested resource was not found.',
                ], 404);
            }
        });

        // Handle NotFoundHttpException (which wraps ModelNotFoundException)
        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Resource not found',
                    'message' => 'The requested resource was not found.',
                ], 404);
            }
        });

        // Handle other HTTP exceptions
        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $statusCode = $e->getStatusCode();
                $message = $e->getMessage() ?: match ($statusCode) {
                    404 => 'The requested resource was not found.',
                    403 => 'Access denied.',
                    401 => 'Unauthenticated.',
                    422 => 'The given data was invalid.',
                    500 => 'Internal server error.',
                    default => 'An error occurred.',
                };

                return response()->json([
                    'error' => $message,
                    'message' => $message,
                ], $statusCode);
            }
        });
    })->create();
