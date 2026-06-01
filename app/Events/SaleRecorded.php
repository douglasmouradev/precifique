<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Sale;
use App\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleRecorded
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Tenant $tenant,
        public readonly Sale $sale,
    ) {}
}
