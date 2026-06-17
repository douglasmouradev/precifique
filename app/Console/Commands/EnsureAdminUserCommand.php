<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class EnsureAdminUserCommand extends Command
{
    protected $signature = 'precifique:ensure-admin
                            {--email= : E-mail do superadmin (padrão: ADMIN_EMAIL ou admin@precifique.com.br)}
                            {--password= : Senha (em produção use ADMIN_PASSWORD no .env)}';

    protected $description = 'Cria ou redefine o usuário superadmin do painel /login';

    private const DEFAULT_DEV_PASSWORD = 'Precifique@2026';

    public function handle(): int
    {
        $email = (string) ($this->option('email') ?: env('ADMIN_EMAIL', 'admin@precifique.com.br'));
        $password = (string) ($this->option('password') ?: env('ADMIN_PASSWORD', ''));

        if ($password === '') {
            if (app()->environment('production')) {
                $this->error('Em produção, defina ADMIN_PASSWORD no .env ou passe --password.');

                return self::FAILURE;
            }

            $password = self::DEFAULT_DEV_PASSWORD;
            $this->warn('Usando senha padrão de desenvolvimento. Não use em produção.');
        }

        if (app()->environment('production') && $password === self::DEFAULT_DEV_PASSWORD) {
            $this->error('Senha padrão de desenvolvimento não é permitida em produção.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => $password,
                'email_verified_at' => now(),
            ]
        )->forceFill([
            'is_superadmin' => true,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->info("Superadmin pronto: {$email}");
        $this->line('Acesse: '.url('/login'));

        if (! app()->environment('production')) {
            $this->comment('Tenant demo: php artisan precifique:ensure-demo');
        }

        return self::SUCCESS;
    }
}
