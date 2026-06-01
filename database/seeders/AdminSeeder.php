<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@precifique.com.br'],
            [
                'name' => 'Super Admin',
                'password' => 'Precifique@2026',
                'is_superadmin' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
