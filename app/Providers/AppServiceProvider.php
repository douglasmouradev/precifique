<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\FixedCost;
use App\Models\MonthlyGoal;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleExportRequest;
use App\Models\TenantVariableCost;
use App\Policies\FixedCostPolicy;
use App\Policies\MonthlyGoalPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SalePolicy;
use App\Policies\TenantVariableCostPolicy;
use App\Services\TenantSetupProgressService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(FixedCost::class, FixedCostPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(TenantVariableCost::class, TenantVariableCostPolicy::class);
        Gate::policy(MonthlyGoal::class, MonthlyGoalPolicy::class);

        $this->registerTenantRouteBindings();
        $this->registerRateLimiters();
        $this->registerViewComposers();
    }

    private function registerViewComposers(): void
    {
        View::composer('layouts.tenant', function ($view): void {
            $tenant = auth('tenant')->user();
            if (! $tenant) {
                return;
            }

            $progress = app(TenantSetupProgressService::class)->for($tenant);
            if (($progress['percent'] ?? 100) < 100) {
                $view->with('setupProgress', $progress);
            }
        });
    }

    private function registerTenantRouteBindings(): void
    {
        $bind = function (string $modelClass): \Closure {
            return function (string $value) use ($modelClass) {
                $tenantId = auth('tenant')->id();
                abort_unless($tenantId, 404);

                return $modelClass::query()
                    ->where('tenant_id', $tenantId)
                    ->findOrFail($value);
            };
        };

        Route::bind('product', $bind(Product::class));
        Route::bind('fixed_cost', $bind(FixedCost::class));
        Route::bind('fixedCost', $bind(FixedCost::class));
        Route::bind('sale', $bind(Sale::class));
        Route::bind('tenant_variable_cost', $bind(TenantVariableCost::class));
        Route::bind('tenantVariableCost', $bind(TenantVariableCost::class));
        Route::bind('saleExportRequest', $bind(SaleExportRequest::class));
    }

    private function registerRateLimiters(): void
    {
        RateLimiter::for('tenant-login', function (Request $request) {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('tenant-register', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));

        RateLimiter::for('tenant-password', function (Request $request) {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('webhooks', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));

        RateLimiter::for('admin-login', function (Request $request) {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by($email.$request->ip());
        });
    }
}
