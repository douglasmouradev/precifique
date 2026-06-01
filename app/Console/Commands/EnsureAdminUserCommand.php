<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
class EnsureAdminUserCommand extends Command
{
    protected $signature = 'precifique:ensure-admin
                            {--email=admin@precifique.com.br : E-mail do superadmin}
                            {--password=Precifique@2026 : Senha do superadmin}';

    protected $description = 'Cria ou redefine o usuário superadmin do painel /login';

    public function handle(): int
    {
        $email = (string) $this->option('email');
        $password = (string) $this->option('password');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => $password,
                'is_superadmin' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->info("Superadmin pronto: {$email}");
        $this->line('Acesse: '.url('/login'));
        $this->comment('Tenant demo: php artisan precifique:ensure-demo');

        return self::SUCCESS;
    }
}
