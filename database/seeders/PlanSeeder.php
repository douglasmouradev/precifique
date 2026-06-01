<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['slug' => 'basic'],
            [
                'name' => 'Basic',
                'price_monthly' => 0,
                'max_products' => 5,
                'has_ai' => false,
                'features' => [
                    'Até 5 produtos',
                    'Relatório básico',
                    '3 margens de lucro',
                ],
            ]
        );

        Plan::updateOrCreate(
            ['slug' => 'premium'],
            [
                'name' => 'Premium',
                'price_monthly' => 29.90,
                'max_products' => null,
                'has_ai' => true,
                'features' => [
                    'Produtos ilimitados',
                    'Relatório Excel completo',
                    '5 margens (inclui 150%)',
                    'IA integrada',
                    'Chatbot de precificação',
                ],
            ]
        );
    }
}
