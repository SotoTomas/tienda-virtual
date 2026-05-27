<?php

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'minimum_amount',
        'usage_limit', 'used_count', 'is_active', 'expires_at',
    ];

    protected $casts = [
        'value'          => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'is_active'      => 'boolean',
        'expires_at'     => 'datetime',
        'type'           => CouponType::class,
    ];

    // ─── Scopes ───────────────────────────────────────────

    public function scopeValid($query)
    {
        return $query
            ->where('is_active', true)
            ->where(fn($q) => $q
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now())
            )
            ->where(fn($q) => $q
                ->whereNull('usage_limit')
                ->orWhereColumn('used_count', '<', 'usage_limit')
            );
    }

    // ─── Métodos de negocio ───────────────────────────────

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->minimum_amount && $subtotal < $this->minimum_amount) {
            return 0;
        }

        return match($this->type) {
            CouponType::Percentage => round($subtotal * ($this->value / 100), 2),
            CouponType::Fixed      => min($this->value, $subtotal),
        };
    }
}