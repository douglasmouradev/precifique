<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Services\AiUsageLimiter;
use Database\Seeders\PlanSeeder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class AiUsageLimiterTest extends TestCase
{
    use RefreshDatabase;

    public function test_premium_tenant_hits_daily_limit(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::factory()->create(['plan' => 'premium']);
        config(['precifique.ai.premium_daily_limit' => 1]);

        app(AiUsageLimiter::class)->assertCanUse($tenant);

        $this->expectException(HttpResponseException::class);

        app(AiUsageLimiter::class)->assertCanUse($tenant);
    }

    public function test_basic_tenant_cannot_use_ai(): void
    {
        $tenant = Tenant::factory()->create(['plan' => 'basic', 'trial_ends_at' => null]);

        $this->expectException(HttpResponseException::class);

        app(AiUsageLimiter::class)->assertCanUse($tenant);
    }
}
