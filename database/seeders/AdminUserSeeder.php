<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@tienda.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin'          => true,  
        ]);
        // Usuario de prueba normal
        User::create([
            'name'              => 'Cliente Test',
            'email'             => 'cliente@tienda.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}