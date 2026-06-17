<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@precifique.com.br');
        $password = env('ADMIN_PASSWORD');

        if (app()->environment('production')) {
            if ($password === null || $password === '') {
                $this->command?->warn(
                    'AdminSeeder: defina ADMIN_PASSWORD no .env ou use php artisan precifique:ensure-admin'
                );

                return;
            }
        } else {
            $password ??= 'Precifique@2026';
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => $password,
                'is_superadmin' => true,
                'email_verified_at' => now(),
                'two_factor_secret' => null,
                'two_factor_confirmed_at' => null,
            ]
        );
    }
}
