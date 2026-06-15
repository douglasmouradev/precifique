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
use App\Models\TenantVariableCost;
use Illuminate\Database\Seeder;

class TestProfilesSeeder extends Seeder
{
    /** Senha padrão de todas as contas tenant de teste */
    public const TENANT_PASSWORD = 'demo1234';

    public function run(): void
    {
        $this->seedPremiumAlimentos();
        $this->seedBasicAlimentos();
        $this->seedPremiumServicos();
        $this->seedPremiumArtesanato();
        $this->seedOnboardingPendente();

        $this->printCredentials();
    }

    private function seedPremiumAlimentos(): void
    {
        $tenant = $this->createTenant([
            'email' => 'demo@precifique.com.br',
            'name' => 'Doceria da Ana (Demo)',
            'niche' => 'alimentos',
            'plan' => 'premium',
            'interface_mode' => 'alimentos',
            'usage_mode' => 'avancado',
        ]);

        $this->seedLgpd($tenant);
        $this->seedFixedCosts($tenant);
        $this->seedVariableCosts($tenant);
        $this->seedGoal($tenant, 8000);

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

        if ($tenant->sales()->count() === 0) {
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

    private function seedBasicAlimentos(): void
    {
        $tenant = $this->createTenant([
            'email' => 'basico@precifique.com.br',
            'name' => 'Padaria do João (Basic)',
            'niche' => 'alimentos',
            'plan' => 'basic',
            'interface_mode' => 'alimentos',
            'usage_mode' => 'iniciante',
        ]);

        $this->seedLgpd($tenant);
        $this->seedFixedCosts($tenant, 800);
        $this->seedVariableCosts($tenant, 120);
        $this->seedGoal($tenant, 3000);

        $produtos = ['Pão Francês', 'Croissant', 'Bolo Simples', 'Torta de Frango'];
        foreach ($produtos as $i => $nome) {
            Product::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $nome],
                [
                    'description' => "Produto demo {$nome}",
                    'niche_type' => 'alimentos',
                    'stock_quantity' => 10 + $i,
                    'min_stock_alert' => 3,
                    'profit_margin_percent' => 30,
                    'selling_price' => 5.00 + ($i * 2),
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedPremiumServicos(): void
    {
        $tenant = $this->createTenant([
            'email' => 'servicos@precifique.com.br',
            'name' => 'Eletricista Silva (Serviços)',
            'niche' => 'servico',
            'plan' => 'premium',
            'interface_mode' => 'servico',
            'usage_mode' => 'avancado',
        ]);

        $this->seedLgpd($tenant);
        $this->seedFixedCosts($tenant, 450);
        $this->seedVariableCosts($tenant, 80);
        $this->seedGoal($tenant, 12000);

        $product = Product::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Instalação de Tomadas'],
            [
                'description' => 'Serviço de instalação residencial',
                'niche_type' => 'servico',
                'stock_quantity' => 0,
                'min_stock_alert' => 0,
                'profit_margin_percent' => 80,
                'selling_price' => 150.00,
                'is_active' => true,
                'production_time_minutes' => 120,
                'niche_fields' => [
                    'minimum_visit_fee' => 80,
                    'travel_cost' => 25,
                    'tools_cost' => 15,
                ],
            ]
        );

        if ($tenant->sales()->count() === 0) {
            Sale::create([
                'tenant_id' => $tenant->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => 150.00,
                'payment_method' => 'pix',
                'sold_at' => now()->subDay(),
            ]);
        }
    }

    private function seedPremiumArtesanato(): void
    {
        $tenant = $this->createTenant([
            'email' => 'artesanato@precifique.com.br',
            'name' => 'Ateliê Cerâmica Lua (Artesanato)',
            'niche' => 'artesanato',
            'plan' => 'premium',
            'interface_mode' => 'artesanato',
            'usage_mode' => 'avancado',
        ]);

        $this->seedLgpd($tenant);
        $this->seedFixedCosts($tenant, 600);
        $this->seedVariableCosts($tenant, 200);
        $this->seedGoal($tenant, 6000);

        $product = Product::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Vaso Terracota 20cm'],
            [
                'description' => 'Peça artesanal única',
                'niche_type' => 'artesanato',
                'stock_quantity' => 8,
                'min_stock_alert' => 2,
                'profit_margin_percent' => 100,
                'selling_price' => 89.90,
                'is_active' => true,
                'is_custom_order' => true,
                'niche_fields' => [
                    'collection' => 'Terra Viva',
                    'production_line' => 'Vasos',
                ],
            ]
        );

        TechnicalSheet::where('product_id', $product->id)->delete();
        TechnicalSheet::create(['product_id' => $product->id, 'material_name' => 'Argila', 'quantity' => 1500, 'unit' => 'g', 'unit_cost' => 0.012]);
        TechnicalSheet::create(['product_id' => $product->id, 'material_name' => 'Esmalte', 'quantity' => 100, 'unit' => 'ml', 'unit_cost' => 0.08]);
    }

    private function seedOnboardingPendente(): void
    {
        $this->createTenant([
            'email' => 'novo@precifique.com.br',
            'name' => 'Conta Nova (Onboarding)',
            'niche' => 'alimentos',
            'plan' => 'basic',
            'interface_mode' => 'alimentos',
            'usage_mode' => 'iniciante',
            'onboarding_completed' => false,
            'profile_setup_completed' => false,
        ]);
    }

    /** @param array<string, mixed> $data */
    private function createTenant(array $data): Tenant
    {
        return Tenant::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => self::TENANT_PASSWORD,
                'niche' => $data['niche'],
                'plan' => $data['plan'],
                'interface_mode' => $data['interface_mode'],
                'usage_mode' => $data['usage_mode'] ?? 'iniciante',
                'onboarding_completed' => $data['onboarding_completed'] ?? true,
                'profile_setup_completed' => $data['profile_setup_completed'] ?? ($data['onboarding_completed'] ?? true),
                'is_active' => $data['is_active'] ?? true,
                'trial_ends_at' => now()->addDays(14),
            ]
        );
    }

    private function seedLgpd(Tenant $tenant): void
    {
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
    }

    private function seedFixedCosts(Tenant $tenant, float $energia = 350): void
    {
        FixedCost::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Aluguel'],
            ['amount' => 1200, 'is_active' => true]
        );
        FixedCost::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Energia'],
            ['amount' => $energia, 'is_active' => true]
        );
    }

    private function seedVariableCosts(Tenant $tenant, float $gas = 180): void
    {
        TenantVariableCost::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Gás de cozinha'],
            ['amount' => $gas, 'is_active' => true, 'description' => 'Botijão / produção mensal']
        );
        TenantVariableCost::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Embalagens'],
            ['amount' => 150, 'is_active' => true, 'description' => 'Potes, sacolas e etiquetas']
        );
    }

    private function seedGoal(Tenant $tenant, float $amount): void
    {
        MonthlyGoal::updateOrCreate(
            ['tenant_id' => $tenant->id, 'year' => now()->year, 'month' => now()->month],
            ['goal_amount' => $amount]
        );
    }

    private function printCredentials(): void
    {
        if (! $this->command) {
            return;
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  PERFIS DE TESTE — Precifique');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->table(
            ['Perfil', 'URL', 'E-mail', 'Senha'],
            [
                ['Admin (super)', '/login', 'admin@precifique.com.br', 'Precifique@2026'],
                ['Tenant Premium — Alimentos', '/entrar', 'demo@precifique.com.br', self::TENANT_PASSWORD],
                ['Tenant Basic — Alimentos (4/5 produtos)', '/entrar', 'basico@precifique.com.br', self::TENANT_PASSWORD],
                ['Tenant Premium — Serviços', '/entrar', 'servicos@precifique.com.br', self::TENANT_PASSWORD],
                ['Tenant Premium — Artesanato', '/entrar', 'artesanato@precifique.com.br', self::TENANT_PASSWORD],
                ['Tenant — Onboarding pendente', '/entrar', 'novo@precifique.com.br', self::TENANT_PASSWORD],
            ]
        );
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
    }
}
