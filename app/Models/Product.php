<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'short_description',
        'description', 'price', 'compare_price', 'sku',
        'stock', 'manage_stock', 'is_active', 'is_featured',
        'main_image', 'gallery', 'attributes', 'weight',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'compare_price'  => 'decimal:2',
        'weight'         => 'decimal:2',
        'manage_stock'   => 'boolean',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
        'gallery'        => 'array',
        'attributes'     => 'array',
    ];

    // ─── Relaciones ───────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ─── Accessors ────────────────────────────────────────

    public function getHasDiscountAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (! $this->has_discount) return 0;
        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function getIsInStockAttribute(): bool
    {
        if (! $this->manage_stock) return true;
        return $this->stock > 0;
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->where('is_approved', true)->avg('rating') ?? 0, 1);
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, string $term)
    {
    return $query->where(function ($q) use ($term) {
        $q->where('name', 'like', "%{$term}%")
          ->orWhere('sku', 'like', "%{$term}%")
          ->orWhere('short_description', 'like', "%{$term}%");
        });
    }
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('manage_stock', false)
              ->orWhere('stock', '>', 0);
        });
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}