<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function index(Request $request): Response
    {
        $query = $request->get('q', '');

        $products = collect();

        if (strlen($query) >= 2) {
            $products = Product::active()
                ->inStock()
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('short_description', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%");
                })
                ->with('category')
                ->paginate(12)
                ->withQueryString()
                ->through(fn ($p) => [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'slug'        => $p->slug,
                    'price'       => $p->price,
                    'has_discount'=> $p->has_discount,
                    'main_image'  => $p->main_image,
                    'category'    => $p->category?->name,
                ]);
        }

        return Inertia::render('Store/Search', [
            'query'    => $query,
            'products' => $products,
        ]);
    }
}