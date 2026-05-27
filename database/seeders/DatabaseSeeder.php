<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,   // primero usuarios
            CategorySeeder::class,    // luego categorías
            ProductSeeder::class,     // productos dependen de categorías
            CouponSeeder::class,      // cupones son independientes
        ]);
    }
}