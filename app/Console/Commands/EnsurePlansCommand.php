<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\PlanSeeder;
use Illuminate\Console\Command;

class EnsurePlansCommand extends Command
{
    protected $signature = 'precifique:ensure-plans';

    protected $description = 'Cria ou atualiza os planos Basic e Premium (landing e painel admin)';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => PlanSeeder::class, '--force' => true]);

        $this->info('Planos Basic e Premium prontos.');

        return self::SUCCESS;
    }
}
