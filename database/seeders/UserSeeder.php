<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Porty',
            'email' => 'admin@porty.it',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '+39 333 0000001',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Marco Rossi',
            'email' => 'owner@porty.it',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'phone' => '+39 333 0000002',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Giulia Bianchi',
            'email' => 'owner2@porty.it',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'phone' => '+39 333 0000003',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Luca Verdi',
            'email' => 'guest@porty.it',
            'password' => bcrypt('password'),
            'role' => 'guest',
            'phone' => '+39 333 0000004',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Anna Marino',
            'email' => 'guest2@porty.it',
            'password' => bcrypt('password'),
            'role' => 'guest',
            'phone' => '+39 333 0000005',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
