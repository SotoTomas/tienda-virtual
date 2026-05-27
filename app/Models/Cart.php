<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id', 'coupon_code'];

    // ─── Relaciones ───────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ─── Accessors ────────────────────────────────────────

    public function getTotalAttribute(): float
    {
        return $this->items->sum(fn($item) => $item->subtotal);
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}