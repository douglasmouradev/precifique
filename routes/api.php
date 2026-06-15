<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\TokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/auth/token', [AuthController::class, 'token'])
        ->middleware('throttle:tenant-login')
        ->name('auth.token');

    Route::middleware(['auth.tenant.api', 'tenant.api.ready', 'throttle:60,1'])->group(function () {
        Route::get('/dashboard/summary', [DashboardController::class, 'summary'])
            ->middleware('tenant.api.ability:dashboard:read')
            ->name('dashboard.summary');

        Route::get('/auth/tokens', [TokenController::class, 'index'])
            ->middleware('tenant.api.ability:tokens:read')
            ->name('auth.tokens.index');

        Route::delete('/auth/tokens/{token}', [TokenController::class, 'destroy'])
            ->middleware('tenant.api.ability:tokens:write')
            ->name('auth.tokens.destroy');
    });
});
