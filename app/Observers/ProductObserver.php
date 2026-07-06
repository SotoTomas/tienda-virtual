<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductObserver
{
    public function creating(Product $product): void
    {
        if (empty($product->slug)) {
            $product->slug = $this->uniqueSlug($product->name);
        }
    }

    public function updating(Product $product): void
    {
        if ($product->isDirty('name') && ! $product->isDirty('slug')) {
            $product->slug = $this->uniqueSlug($product->name, $product->id);
        }
    }

    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i    = 1;

        while (
            Product::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}