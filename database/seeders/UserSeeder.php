<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['email' => 'admin@porty.it',  'name' => 'Admin Porty',   'role' => 'admin',  'phone' => '+39 333 0000001'],
            ['email' => 'owner@porty.it',  'name' => 'Marco Rossi',   'role' => 'owner',  'phone' => '+39 333 0000002'],
            ['email' => 'owner2@porty.it', 'name' => 'Giulia Bianchi','role' => 'owner',  'phone' => '+39 333 0000003'],
            ['email' => 'guest@porty.it',  'name' => 'Luca Verdi',    'role' => 'guest',  'phone' => '+39 333 0000004'],
            ['email' => 'guest2@porty.it', 'name' => 'Anna Marino',   'role' => 'guest',  'phone' => '+39 333 0000005'],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => bcrypt('password'),
                    'role'              => $data['role'],
                    'phone'             => $data['phone'],
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
