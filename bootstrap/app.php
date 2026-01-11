<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\LogApiRequests;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Configure rate limiting
            RateLimiter::for('api', function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
            });

            RateLimiter::for('login', function (Request $request) {
                return Limit::perMinute(5)->by($request->ip());
            });

            RateLimiter::for('register', function (Request $request) {
                return Limit::perMinute(3)->by($request->ip());
            });

            RateLimiter::for('upload', function (Request $request) {
                return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware for all requests
        $middleware->append(SecurityHeaders::class);

        // API-specific middleware
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
            SanitizeInput::class,
            LogApiRequests::class,
            'throttle:api',
        ]);

        // Middleware aliases
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'throttle.login' => 'throttle:login',
            'throttle.register' => 'throttle:register',
            'throttle.upload' => 'throttle:upload',
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API exception handling - don't expose stack traces
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Method not allowed.',
                ], 405);
            }
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Log the actual error for debugging
                \Illuminate\Support\Facades\Log::channel('security')->error('API Error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'user_id' => $request->user()?->id,
                ]);

                // Return sanitized error in production
                if (app()->environment('production')) {
                    return response()->json([
                        'message' => 'An error occurred. Please try again later.',
                    ], 500);
                }
            }
        });
    })->create();
