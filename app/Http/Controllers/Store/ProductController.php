<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::active()->inStock()->with('category');

        // ── Filtros ───────────────────────────────────────
        if ($request->filled('categoria')) {
            $category = Category::where('slug', $request->categoria)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->filled('precio_min')) {
            $query->where('price', '>=', $request->precio_min);
        }

        if ($request->filled('precio_max')) {
            $query->where('price', '<=', $request->precio_max);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->buscar}%")
                  ->orWhere('short_description', 'like', "%{$request->buscar}%");
            });
        }

        // ── Ordenamiento ──────────────────────────────────
        match($request->get('orden', 'recientes')) {
            'precio_asc'  => $query->orderBy('price', 'asc'),
            'precio_desc' => $query->orderBy('price', 'desc'),
            'nombre'      => $query->orderBy('name', 'asc'),
            default       => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return Inertia::render('Store/Products/Index', [
            'products' => $products->through(fn ($p) => [
                'id'                  => $p->id,
                'name'                => $p->name,
                'slug'                => $p->slug,
                'price'               => $p->price,
                'compare_price'       => $p->compare_price,
                'has_discount'        => $p->has_discount,
                'discount_percentage' => $p->discount_percentage,
                'main_image'          => $p->main_image,
                'is_in_stock'         => $p->is_in_stock,
                'category'            => $p->category?->name,
            ]),
            'filters' => $request->only([
                'categoria', 'precio_min', 'precio_max', 'buscar', 'orden'
            ]),
            'categories' => Category::active()->roots()->ordered()->get(['id', 'name', 'slug']),
        ]);
    }

    public function show(string $slug): Response
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['category', 'variants', 'images', 'reviews' => fn ($q) => $q->approved()->with('user')->latest()])
            ->firstOrFail();

        // Productos relacionados de la misma categoría
        $related = Product::active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get()
            ->map(fn ($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'slug'          => $p->slug,
                'price'         => $p->price,
                'compare_price' => $p->compare_price,
                'has_discount'  => $p->has_discount,
                'main_image'    => $p->main_image,
            ]);

        return Inertia::render('Store/Products/Show', [
            'product' => [
                'id'                  => $product->id,
                'name'                => $product->name,
                'slug'                => $product->slug,
                'short_description'   => $product->short_description,
                'description'         => $product->description,
                'price'               => $product->price,
                'compare_price'       => $product->compare_price,
                'has_discount'        => $product->has_discount,
                'discount_percentage' => $product->discount_percentage,
                'main_image'          => $product->main_image,
                'gallery'             => $product->gallery,
                'is_in_stock'         => $product->is_in_stock,
                'stock'               => $product->stock,
                'average_rating'      => $product->average_rating,
                'sku'                 => $product->sku,
                'category'            => [
                    'name' => $product->category?->name,
                    'slug' => $product->category?->slug,
                ],
                'variants' => $product->variants->map(fn ($v) => [
                    'id'       => $v->id,
                    'name'     => $v->name,
                    'options'  => $v->options,
                    'price'    => $v->effective_price,
                    'stock'    => $v->stock,
                    'in_stock' => $v->is_in_stock,
                ]),
                'images' => $product->images->map(fn ($i) => [
                    'path'    => $i->path,
                    'alt'     => $i->alt ?? $product->name,
                    'is_main' => $i->is_main,
                ]),
                'reviews' => $product->reviews->map(fn ($r) => [
                    'id'         => $r->id,
                    'rating'     => $r->rating,
                    'title'      => $r->title,
                    'body'       => $r->body,
                    'user_name'  => $r->user->name,
                    'created_at' => $r->created_at->diffForHumans(),
                ]),
            ],
            'related' => $related,
        ]);
    }
}