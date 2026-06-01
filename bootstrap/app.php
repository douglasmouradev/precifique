<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::get('/health', \App\Http\Controllers\HealthController::class)
                ->name('health.detailed');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'plan' => \App\Http\Middleware\PlanMiddleware::class,
            'lgpd' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'admin.2fa' => \App\Http\Middleware\EnsureAdminTwoFactor::class,
            'auth.tenant.api' => \App\Http\Middleware\AuthenticateTenantApi::class,
        ]);
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);

        $proxies = env('TRUSTED_PROXIES');
        if ($proxies !== null && $proxies !== '') {
            $middleware->trustProxies(at: $proxies === '*' ? '*' : explode(',', $proxies));
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if (config('precifique.monitoring.sentry_dsn') && class_exists(\Sentry\Laravel\Integration::class)) {
            \Sentry\Laravel\Integration::handles($exceptions);
        }
    })->create();
