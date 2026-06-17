<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\TenantManagementController;
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant\BillingController;
use App\Http\Middleware\RestrictPublicDocs;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/.well-known/security.txt', function () {
    $contact = (string) config('security.security_contact');
    $body = "Contact: mailto:{$contact}\nPreferred-Languages: pt-BR, en\nCanonical: ".url('/.well-known/security.txt')."\n";

    return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('security.txt');

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');
Route::get('/docs/api', ApiDocsController::class)->middleware(RestrictPublicDocs::class)->name('docs.api');
Route::get('/openapi.yaml', fn () => response()->file(public_path('openapi.yaml'), ['Content-Type' => 'application/yaml']))
    ->middleware(RestrictPublicDocs::class)
    ->name('openapi');
Route::get('/precificacao-alimentos', [LandingController::class, 'nicheFood'])->name('landing.niche.food');
Route::get('/precificacao-servicos', [LandingController::class, 'nicheService'])->name('landing.niche.service');
Route::get('/precificacao-artesanato', [LandingController::class, 'nicheCraft'])->name('landing.niche.craft');
Route::get('/privacidade', [LandingController::class, 'privacy'])->name('privacy');
Route::get('/termos', [LandingController::class, 'terms'])->name('terms');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'priority' => '1.0'],
        ['loc' => route('landing.niche.food'), 'priority' => '0.8'],
        ['loc' => route('landing.niche.service'), 'priority' => '0.8'],
        ['loc' => route('landing.niche.craft'), 'priority' => '0.8'],
        ['loc' => route('docs.api'), 'priority' => '0.6'],
        ['loc' => route('privacy'), 'priority' => '0.5'],
        ['loc' => route('terms'), 'priority' => '0.5'],
        ['loc' => route('tenant.register'), 'priority' => '0.8'],
    ];
    $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $u) {
        $xml .= '<url><loc>'.e($u['loc']).'</loc><priority>'.$u['priority'].'</priority></url>';
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Allow: /',
        'Disallow: /app/',
        'Disallow: /admin/',
        'Disallow: /onboarding/',
        'Disallow: /lgpd/',
        'Sitemap: '.route('sitemap'),
    ];

    return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
})->name('robots');

Route::post('/webhooks/stripe', [BillingController::class, 'stripeWebhook'])
    ->middleware('throttle:webhooks')
    ->name('webhooks.stripe')
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::post('/webhooks/mercadopago', [BillingController::class, 'mercadopagoWebhook'])
    ->middleware('throttle:webhooks')
    ->name('webhooks.mercadopago')
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::middleware(['auth', 'superadmin', 'admin.2fa.enrolled', 'admin.2fa'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tenants', [AdminDashboardController::class, 'tenants'])->name('tenants.index');
    Route::get('/tenants/create', [TenantManagementController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantManagementController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenant}', [TenantManagementController::class, 'show'])->name('tenants.show');
    Route::patch('/tenants/{tenant}/toggle', [TenantManagementController::class, 'toggle'])->name('tenants.toggle');
    Route::post('/tenants/{tenant}/resend-welcome', [TenantManagementController::class, 'resendWelcome'])->name('tenants.resend-welcome');
    Route::patch('/tenants/{tenant}/extend-trial', [TenantManagementController::class, 'extendTrial'])->name('tenants.extend-trial');
    Route::post('/tenants/{tenant}/impersonate', [TenantManagementController::class, 'impersonate'])->name('tenants.impersonate');
    Route::get('/lgpd', [AdminDashboardController::class, 'lgpd'])->name('lgpd');
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::patch('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::get('/logs', [AuditLogController::class, 'index'])->name('logs.index');
});

Route::middleware('auth')->get('/dashboard', function () {
    if (auth()->user()?->is_superadmin) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/tenant.php';
