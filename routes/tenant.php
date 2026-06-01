<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\TenantAuthController;
use App\Http\Controllers\Auth\TenantPasswordResetController;
use App\Http\Controllers\Tenant\AIController;
use App\Http\Controllers\Tenant\BillingController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\FixedCostController;
use App\Http\Controllers\Tenant\LGPDController;
use App\Http\Controllers\Tenant\MonthlyGoalController;
use App\Http\Controllers\Tenant\PricingController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\QuoteController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\SaleController;
use App\Http\Controllers\Tenant\StockController;
use App\Http\Controllers\Tenant\TenantVariableCostController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:tenant')->group(function () {
    Route::get('/entrar', [TenantAuthController::class, 'showLogin'])->name('tenant.login');
    Route::post('/entrar', [TenantAuthController::class, 'login'])->middleware('throttle:tenant-login')->name('tenant.login.store');
    Route::get('/cadastro', [TenantAuthController::class, 'showRegister'])->name('tenant.register');
    Route::post('/cadastro', [TenantAuthController::class, 'register'])->middleware('throttle:tenant-register')->name('tenant.register.store');

    Route::get('/recuperar-senha', [TenantPasswordResetController::class, 'showForgot'])->name('tenant.password.request');
    Route::post('/recuperar-senha', [TenantPasswordResetController::class, 'sendReset'])->middleware('throttle:tenant-password')->name('tenant.password.email');
    Route::get('/redefinir-senha/{token}', [TenantPasswordResetController::class, 'showReset'])->name('tenant.password.reset');
    Route::post('/redefinir-senha', [TenantPasswordResetController::class, 'reset'])->middleware('throttle:tenant-password')->name('tenant.password.store');
});

Route::post('/sair', [TenantAuthController::class, 'logout'])
    ->middleware('auth:tenant')
    ->name('tenant.logout');

Route::middleware('auth:tenant')->group(function () {
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/welcome', [OnboardingController::class, 'welcome'])->name('welcome');
        Route::get('/pular', [OnboardingController::class, 'skip'])->name('skip');
        Route::get('/niche', [OnboardingController::class, 'niche'])->name('niche');
        Route::post('/niche', [OnboardingController::class, 'saveNiche'])->name('niche.store');
        Route::get('/mode', [OnboardingController::class, 'mode'])->name('mode');
        Route::post('/mode', [OnboardingController::class, 'saveMode'])->name('mode.store');
        Route::get('/plan', [OnboardingController::class, 'plan'])->name('plan');
        Route::post('/plan', [OnboardingController::class, 'savePlan'])->name('plan.store');
        Route::get('/setup', [OnboardingController::class, 'setup'])->name('setup');
        Route::post('/setup', [OnboardingController::class, 'complete'])->name('setup.store');
    });

    Route::get('/lgpd/consentimento', [LGPDController::class, 'consentForm'])->name('lgpd.consent');
    Route::post('/lgpd/consentimento', [LGPDController::class, 'storeConsent'])->name('lgpd.consent.store');

    Route::prefix('app')->name('tenant.')->group(function () {
        Route::get('/monte-seu-perfil', [\App\Http\Controllers\Tenant\ProfileSetupController::class, 'show'])->name('profile.setup');
        Route::post('/monte-seu-perfil', [\App\Http\Controllers\Tenant\ProfileSetupController::class, 'store'])->name('profile.setup.store');
    });
});

Route::middleware(['auth:tenant', 'tenant'])->prefix('app')->name('tenant.')->group(function () {
    Route::get('/perfil', [\App\Http\Controllers\Tenant\ProfileSetupController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [\App\Http\Controllers\Tenant\ProfileSetupController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('/products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::get('/products/{product}/pricing', [PricingController::class, 'edit'])->name('pricing.edit');
    Route::put('/products/{product}/pricing', [PricingController::class, 'update'])->name('pricing.update');
    Route::post('/products/{product}/pricing/preview', [PricingController::class, 'preview'])->name('pricing.preview');
    Route::post('/products/{product}/ai', [PricingController::class, 'aiSuggest'])
        ->middleware(['plan:premium', 'throttle:15,1'])
        ->name('pricing.ai');
    Route::get('/products/{product}/orcamento.pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');

    Route::get('/sales/export', [SaleController::class, 'export'])->name('sales.export');
    Route::get('/sales/export/{saleExportRequest}', [SaleController::class, 'downloadExport'])->name('sales.export.download');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('fixed-costs', FixedCostController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('variable-costs', TenantVariableCostController::class)
        ->parameters(['variable-costs' => 'tenantVariableCost'])
        ->only(['index', 'store', 'update', 'destroy']);
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::patch('/stock/{product}', [StockController::class, 'update'])->name('stock.update');

    Route::get('/goals', [MonthlyGoalController::class, 'edit'])->name('goals.edit');
    Route::post('/goals', [MonthlyGoalController::class, 'store'])->name('goals.store');

    Route::get('/reports/monthly', [ReportController::class, 'monthly'])
        ->middleware('plan:premium')
        ->name('reports.monthly');

    Route::post('/ai/chat', [AIController::class, 'chat'])
        ->middleware(['plan:premium', 'throttle:20,1'])
        ->name('ai.chat');

    Route::get('/lgpd/portal', [LGPDController::class, 'portal'])->name('lgpd.portal');
    Route::get('/lgpd/export', [LGPDController::class, 'export'])->name('lgpd.export');
    Route::delete('/lgpd/account', [LGPDController::class, 'destroyAccount'])->name('lgpd.destroy');

    Route::get('/billing/upgrade', [BillingController::class, 'upgrade'])->name('billing.upgrade');
    Route::post('/billing/stripe', [BillingController::class, 'stripeCheckout'])->name('billing.stripe');
    Route::get('/billing/pix', [BillingController::class, 'pix'])->name('billing.pix');
    Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');
    Route::get('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
});
