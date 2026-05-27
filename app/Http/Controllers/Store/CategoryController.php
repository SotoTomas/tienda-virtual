<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function show(string $slug): Response
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->with('children')
            ->firstOrFail();

        $products = $category->products()
            ->active()
            ->inStock()
            ->paginate(12)
            ->through(fn ($p) => [
                'id'                  => $p->id,
                'name'                => $p->name,
                'slug'                => $p->slug,
                'price'               => $p->price,
                'compare_price'       => $p->compare_price,
                'has_discount'        => $p->has_discount,
                'discount_percentage' => $p->discount_percentage,
                'main_image'          => $p->main_image,
                'is_in_stock'         => $p->is_in_stock,
            ]);

        return Inertia::render('Store/Category/Show', [
            'category' => [
                'id'          => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'description' => $category->description,
                'image'       => $category->image,
                'children'    => $category->children->map(fn ($c) => [
                    'id'   => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                ]),
            ],
            'products' => $products,
        ]);
    }
}