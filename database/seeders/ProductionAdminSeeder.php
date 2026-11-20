<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionAdminSeeder extends Seeder
{
    /**
     * Creates one Super Administrator from environment variables.
     * Safe to run on every start: firstOrCreate never overwrites an existing account.
     */
    public function run(): void
    {
        $email = strtolower(trim((string) env('SEED_ADMIN_EMAIL')));
        $password = (string) env('SEED_ADMIN_PASSWORD');

        if ($email === '' || $password === '') {
            $this->command?->warn('SEED_ADMIN_EMAIL or SEED_ADMIN_PASSWORD not set. Skipping.');

            return;
        }

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Administrator',
                'role' => User::ROLE_SUPER_ADMIN,
                'password' => Hash::make($password),
            ]
        );
    }
}
