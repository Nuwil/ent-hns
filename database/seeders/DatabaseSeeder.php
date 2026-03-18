<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin Account ─────────────────────────────────────────
        User::create([
            'username'      => 'admin',
            'full_name'     => 'Admin User',
            'email'         => 'admin@entclinic.com',
            'password_hash' => Hash::make('password'),
            'role'          => 'admin',
            'is_active'     => true,
        ]);

        $this->command->info('✅ Seeder complete. Login credentials:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@entclinic.com', 'password'],
            ]
        );
    }
}