<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendMonthlyReportJob;
use App\Jobs\SendTenantMonthlyReportJob;
use App\Models\Tenant;
use Illuminate\Support\Facades\Bus;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class SendMonthlyReportJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_dispatches_per_premium_tenant(): void
    {
        Bus::fake();

        Tenant::factory()->create([
            'plan' => 'premium',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        (new SendMonthlyReportJob)->handle();

        Bus::assertDispatched(SendTenantMonthlyReportJob::class);
    }
}
