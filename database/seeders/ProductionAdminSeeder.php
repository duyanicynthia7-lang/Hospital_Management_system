<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('SEED_ADMIN_EMAIL');
        $password = (string) env('SEED_ADMIN_PASSWORD');

        if ($email === '' || $password === '') {
            $this->command?->warn('SEED_ADMIN_EMAIL or SEED_ADMIN_PASSWORD not set. Skipping.');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Administrator',
                'role' => User::ROLE_SUPER_ADMIN,
                'password' => Hash::make($password),
            ]
        );

        $this->command?->info('Super admin account synced from environment.');
    }
}