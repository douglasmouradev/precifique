<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/auth/token', [AuthController::class, 'token'])
        ->middleware('throttle:tenant-login')
        ->name('auth.token');

    Route::middleware(['auth.tenant.api', 'throttle:60,1'])->group(function () {
        Route::get('/dashboard/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
    });
});
