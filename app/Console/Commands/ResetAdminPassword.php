<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password {password=admin123}';
    protected $description = 'Reset the admin password';

    public function handle()
    {
        $password = $this->argument('password');
        $hashed = Hash::make($password);
        
        DB::table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@entclinic.com',
            'password_hash' => $hashed,
            'full_name' => 'Administrator',
            'role' => 'admin',
            'is_active' => true,
            'is_protected' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->info('Admin account created successfully!');
        $this->info('Username: admin');
        $this->info('Password: ' . $password);
        $this->info('Hash: ' . $hashed);
    }
}
