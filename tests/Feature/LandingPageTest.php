<?php

declare(strict_types=1);

namespace Tests\Feature;

use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_returns_successful_response(): void
    {
        $this->seed(PlanSeeder::class);

        $this->get('/')->assertOk();
    }
}
