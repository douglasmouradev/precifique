<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Product;
use App\Services\AIAssistantService;
use Database\Seeders\PlanSeeder;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class AIControllerTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_premium_tenant_can_chat_with_ai(): void
    {
        $this->seed(PlanSeeder::class);
        $tenant = $this->readyTenant(['plan' => 'premium', 'email' => 'ai@precifique.com.br', 'password' => 'demo1234']);

        $this->mock(AIAssistantService::class, function ($mock): void {
            $mock->shouldReceive('helpChat')->once()->andReturn('Use margem de 50% para seu nicho.');
        });

        $this->post('/entrar', ['email' => 'ai@precifique.com.br', 'password' => 'demo1234']);

        $this->postJson(route('tenant.ai.chat'), [
            'question' => 'Qual margem usar?',
        ])->assertOk()->assertJson(['answer' => 'Use margem de 50% para seu nicho.']);
    }
}
