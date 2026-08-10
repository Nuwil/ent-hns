<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        User::updateOrCreate(
            ['username' => 'doctor one'],
            [
                'full_name' => 'Doctor One',
                'email' => 'doctor1@entclinic.com',
                'password_hash' => Hash::make('password'),
                'role' => 'doctor',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'doctor two'],
            [
                'full_name' => 'Doctor Two',
                'email' => 'doctor2@entclinic.com',
                'password_hash' => Hash::make('password'),
                'role' => 'doctor',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'secretary one'],
            [
                'full_name' => 'Secretary One',
                'email' => 'secretary1@entclinic.com',
                'password_hash' => Hash::make('password'),
                'role' => 'secretary',
                'is_active' => true,
            ]
        );
    }
}