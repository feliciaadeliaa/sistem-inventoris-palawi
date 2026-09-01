<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
      {
        User::create([
            'name' => 'Admin Palawi',
            'email' => 'admin@palawi.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'General Manager Palawi',
            'email' => 'gm@palawi.test',
            'password' => Hash::make('password123'),
            'role' => 'gm',
            'is_active' => true,
            'email_verified_at' => now(),
         ]);

        User::create([
            'name' => 'Senior Analis Palawi',
            'email' => 'senioranalis@palawi.test',
            'password' => Hash::make('password123'),
            'role' => 'senior_analis',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
