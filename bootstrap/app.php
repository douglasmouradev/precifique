<?php

use App\Http\Controllers\HealthController;
use App\Http\Middleware\AuthenticateTenantApi;
use App\Http\Middleware\AuthenticateTenantOrMember;
use App\Http\Middleware\CheckTenantApiAbility;
use App\Http\Middleware\EnsureTenantApiReady;
use App\Http\Middleware\EnsureTenantEmailVerified;
use App\Http\Middleware\EnsureTenantTwoFactor;
use App\Http\Middleware\GenerateCspNonce;
use App\Http\Middleware\PlanMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\TenantMiddleware;
use App\Http\Middleware\VerifyHealthCheckToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::get('/health', HealthController::class)
                ->middleware([
                    VerifyHealthCheckToken::class,
                    'throttle:health',
                ])
                ->name('health.detailed');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'tenant' => TenantMiddleware::class,
            'plan' => PlanMiddleware::class,
            'superadmin' => SuperAdminMiddleware::class,
            'auth.tenant.api' => AuthenticateTenantApi::class,
            'tenant.api.ready' => EnsureTenantApiReady::class,
            'tenant.api.ability' => CheckTenantApiAbility::class,
            'verified.tenant' => EnsureTenantEmailVerified::class,
            'tenant.2fa' => EnsureTenantTwoFactor::class,
            'auth.tenant_or_member' => AuthenticateTenantOrMember::class,
        ]);
        $middleware->web(prepend: [
            GenerateCspNonce::class,
        ]);
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->api(append: [
            SetLocale::class,
        ]);
        $middleware->append(SecurityHeadersMiddleware::class);

        $proxies = env('TRUSTED_PROXIES');
        if ($proxies !== null && $proxies !== '') {
            $middleware->trustProxies(at: $proxies === '*' ? '*' : explode(',', $proxies));
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if (config('precifique.monitoring.sentry_dsn') && class_exists(Integration::class)) {
            Integration::handles($exceptions);
        }
    })->create();
