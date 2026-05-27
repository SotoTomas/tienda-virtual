<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Enums\CouponType;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        // 20% de descuento, sin mínimo, sin vencimiento
        Coupon::create([
            'code'       => 'BIENVENIDO20',
            'type'       => CouponType::Percentage,
            'value'      => 20,
            'is_active'  => true,
        ]);

        // $5000 fijos, mínimo $20.000
        Coupon::create([
            'code'           => 'DESCUENTO5K',
            'type'           => CouponType::Fixed,
            'value'          => 5000,
            'minimum_amount' => 20000,
            'is_active'      => true,
        ]);

        // 10% con límite de 100 usos y vencimiento
        Coupon::create([
            'code'        => 'PROMO10',
            'type'        => CouponType::Percentage,
            'value'       => 10,
            'usage_limit' => 100,
            'is_active'   => true,
            'expires_at'  => now()->addMonths(3),
        ]);
    }
}