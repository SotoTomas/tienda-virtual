<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'name', 'sku', 'price', 'stock', 'options', 'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
        'options'   => 'array',   // { "color": "Rojo", "talle": "XL" }
    ];

    // ─── Relaciones ───────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Accessors ────────────────────────────────────────

    // Precio efectivo: usa el del variante si existe, sino el del producto base
    public function getEffectivePriceAttribute(): string
    {
        return $this->price ?? $this->product->price;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }
}