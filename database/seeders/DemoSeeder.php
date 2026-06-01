<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FixedCost;
use App\Models\LgpdConsent;
use App\Models\MonthlyGoal;
use App\Models\Product;
use App\Models\Sale;
use App\Models\TechnicalSheet;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::updateOrCreate(
            ['email' => 'demo@precifique.com.br'],
            [
                'name' => 'Doceria da Ana (Demo)',
                'password' => 'demo1234',
                'niche' => 'alimentos',
                'plan' => 'premium',
                'interface_mode' => 'alimentos',
                'usage_mode' => 'avancado',
                'onboarding_completed' => true,
                'profile_setup_completed' => true,
                'is_active' => true,
            ]
        );

        foreach (['terms', 'privacy'] as $type) {
            LgpdConsent::firstOrCreate(
                ['tenant_id' => $tenant->id, 'consent_type' => $type],
                [
                    'consented_at' => now(),
                    'ip_address' => '127.0.0.1',
                    'version' => '1.0',
                ]
            );
        }

        FixedCost::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Aluguel'],
            ['amount' => 1200, 'is_active' => true]
        );
        FixedCost::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Energia'],
            ['amount' => 350, 'is_active' => true]
        );

        MonthlyGoal::updateOrCreate(
            ['tenant_id' => $tenant->id, 'year' => now()->year, 'month' => now()->month],
            ['goal_amount' => 8000]
        );

        $product = Product::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Bolo de Pote Chocolate'],
            [
                'description' => 'Bolo no pote 250ml',
                'niche_type' => 'alimentos',
                'stock_quantity' => 20,
                'min_stock_alert' => 5,
                'profit_margin_percent' => 50,
                'selling_price' => 18.50,
                'is_active' => true,
                'niche_fields' => ['portion_yield' => '8 potes', 'shelf_life' => '3 dias'],
            ]
        );

        TechnicalSheet::where('product_id', $product->id)->delete();
        TechnicalSheet::create(['product_id' => $product->id, 'material_name' => 'Chocolate', 'quantity' => 200, 'unit' => 'g', 'unit_cost' => 0.04]);
        TechnicalSheet::create(['product_id' => $product->id, 'material_name' => 'Pote 250ml', 'quantity' => 1, 'unit' => 'un', 'unit_cost' => 1.20]);

        Sale::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 18.50,
            'payment_method' => 'pix',
            'sold_at' => now()->subDays(2),
        ]);
        Sale::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 18.50,
            'payment_method' => 'credito',
            'sold_at' => now()->subDays(5),
        ]);
    }
}
