<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Store/Home', [
            'featuredProducts' => Product::active()
                ->featured()
                ->inStock()
                ->with('category')
                ->take(8)
                ->get()
                ->map(fn ($p) => $this->formatProduct($p)),

            'newArrivals' => Product::active()
                ->inStock()
                ->with('category')
                ->latest()
                ->take(8)
                ->get()
                ->map(fn ($p) => $this->formatProduct($p)),

            'topCategories' => Category::active()
                ->roots()
                ->ordered()
                ->withCount(['products' => fn ($q) => $q->active()])
                ->take(6)
                ->get()
                ->map(fn ($c) => [
                    'id'            => $c->id,
                    'name'          => $c->name,
                    'slug'          => $c->slug,
                    'image'         => $c->image,
                    'products_count'=> $c->products_count,
                ]),
        ]);
    }

    private function formatProduct(Product $product): array
    {
        return [
            'id'                  => $product->id,
            'name'                => $product->name,
            'slug'                => $product->slug,
            'short_description'   => $product->short_description,
            'price'               => $product->price,
            'compare_price'       => $product->compare_price,
            'has_discount'        => $product->has_discount,
            'discount_percentage' => $product->discount_percentage,
            'main_image'          => $product->main_image,
            'is_in_stock'         => $product->is_in_stock,
            'category'            => $product->category?->name,
        ];
    }
}