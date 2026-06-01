<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpireSubscriptionsJob implements ShouldQueue
{
    use Queueable;

    public function handle(PaymentService $payments): void
    {
        $payments->expireSubscriptions();
    }
}
