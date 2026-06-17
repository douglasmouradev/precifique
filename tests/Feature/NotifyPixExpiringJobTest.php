<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\NotifyPixExpiringJob;
use App\Mail\PixExpiringMail;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\TenantNotificationPreferences;
use App\Services\TenantNotificationService;
use Database\Seeders\PlanSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class NotifyPixExpiringJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_email_for_pix_subscription_expiring_soon(): void
    {
        Mail::fake();
        $this->seed(PlanSeeder::class);
        config(['precifique.pix.notify_days_before' => 3]);

        $tenant = Tenant::factory()->create([
            'plan' => 'premium',
            'is_active' => true,
        ]);

        $plan = Plan::query()->where('slug', 'premium')->firstOrFail();
        $endsAt = now()->addDays(3)->startOfDay();

        Subscription::query()->create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'mercadopago_payment_id' => 'mp-test-123',
            'starts_at' => now()->subMonth(),
            'ends_at' => $endsAt,
        ]);

        (new NotifyPixExpiringJob)->handle(app(TenantNotificationService::class), app(TenantNotificationPreferences::class));

        Mail::assertQueued(PixExpiringMail::class, function (PixExpiringMail $mail) use ($tenant) {
            return $mail->tenant->is($tenant);
        });
    }

    public function test_job_does_not_send_duplicate_notifications(): void
    {
        Mail::fake();
        $this->seed(PlanSeeder::class);
        config(['precifique.pix.notify_days_before' => 3]);

        $tenant = Tenant::factory()->create([
            'plan' => 'premium',
            'is_active' => true,
        ]);

        $plan = Plan::query()->where('slug', 'premium')->firstOrFail();
        $endsAt = now()->addDays(3)->startOfDay();

        Subscription::query()->create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'mercadopago_payment_id' => 'mp-test-123',
            'starts_at' => now()->subMonth(),
            'ends_at' => $endsAt,
        ]);

        Cache::put("pix_expiring_notified_{$tenant->id}_{$endsAt->toDateString()}", true, now()->addDays(7));

        (new NotifyPixExpiringJob)->handle(app(TenantNotificationService::class), app(TenantNotificationPreferences::class));

        Mail::assertNothingSent();
    }
}
