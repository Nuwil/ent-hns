<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminAccountSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            'username' => 'admin',
            'email' => 'admin@entclinic.com',
            'password_hash' => Hash::make('admin123'), // password: admin123
            'full_name' => 'Administrator',
            'role' => 'admin',
            'is_active' => true,
            'is_protected' => 1,
        ]);
    }
}