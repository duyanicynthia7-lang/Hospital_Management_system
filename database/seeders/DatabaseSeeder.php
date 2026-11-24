<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Local demo data only. Never run this on the live server.
        if (app()->environment('production')) {
            $this->command?->warn('DatabaseSeeder skipped in production.');
            return;
        }

        $demoUsers = [
            ['name' => 'System Administrator', 'email' => 'admin@hospital.test', 'role' => User::ROLE_ADMIN, 'phone' => '+254700000001'],
            ['name' => 'Dr. James Mwangi', 'email' => 'james@hospital.test', 'role' => User::ROLE_DOCTOR, 'phone' => '+254700000002'],
            ['name' => 'Dr. Amina Otieno', 'email' => 'amina@hospital.test', 'role' => User::ROLE_DOCTOR, 'phone' => '+254700000003'],
            ['name' => 'Dr. Peter Kamau', 'email' => 'peter@hospital.test', 'role' => User::ROLE_DOCTOR, 'phone' => '+254700000004'],
            ['name' => 'Grace Wanjiru', 'email' => 'grace@hospital.test', 'role' => User::ROLE_RECEPTIONIST, 'phone' => '+254700000005'],
        ];

        foreach ($demoUsers as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    ...$user,
                    'password' => Hash::make(Str::random(16)),
                ]
            );
        }
    }
}