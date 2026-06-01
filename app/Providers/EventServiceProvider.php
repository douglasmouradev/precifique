<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ProductPriced;
use App\Events\SaleRecorded;
use App\Listeners\InvalidateDashboardCache;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<class-string, array<int, class-string>> */
    protected $listen = [
        SaleRecorded::class => [
            InvalidateDashboardCache::class,
        ],
        ProductPriced::class => [
            InvalidateDashboardCache::class,
        ],
    ];
}
