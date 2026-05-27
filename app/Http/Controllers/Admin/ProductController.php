<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with('category')
            ->withCount('variants')
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        return Inertia::render('Admin/Products/Index', [
            'products' => $query->paginate(20)->through(fn ($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'slug'           => $p->slug,
                'sku'            => $p->sku,
                'price'          => $p->price,
                'stock'          => $p->stock,
                'is_active'      => $p->is_active,
                'is_featured'    => $p->is_featured,
                'main_image'     => $p->main_image,
                'category'       => $p->category?->name,
                'variants_count' => $p->variants_count,
            ]),
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::active()
                ->get(['id', 'name', 'parent_id'])
                ->map(fn ($c) => [
                    'id'        => $c->id,
                    'name'      => $c->name,
                    'parent_id' => $c->parent_id,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'slug'              => 'required|string|unique:products,slug',
            'category_id'       => 'required|exists:categories,id',
            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|unique:products,sku',
            'stock'             => 'required|integer|min:0',
            'manage_stock'      => 'boolean',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'weight'            => 'nullable|numeric|min:0',
        ]);

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                'id'                => $product->id,
                'name'              => $product->name,
                'slug'              => $product->slug,
                'category_id'       => $product->category_id,
                'short_description' => $product->short_description,
                'description'       => $product->description,
                'price'             => $product->price,
                'compare_price'     => $product->compare_price,
                'sku'               => $product->sku,
                'stock'             => $product->stock,
                'manage_stock'      => $product->manage_stock,
                'is_active'         => $product->is_active,
                'is_featured'       => $product->is_featured,
                'weight'            => $product->weight,
                'main_image'        => $product->main_image,
                'variants'          => $product->variants->map(fn ($v) => [
                    'id'        => $v->id,
                    'name'      => $v->name,
                    'sku'       => $v->sku,
                    'price'     => $v->price,
                    'stock'     => $v->stock,
                    'options'   => $v->options,
                    'is_active' => $v->is_active,
                ]),
            ],
            'categories' => Category::active()->get(['id', 'name', 'parent_id']),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'sometimes|string|max:255',
            'slug'              => 'sometimes|string|unique:products,slug,' . $product->id,
            'category_id'       => 'sometimes|exists:categories,id',
            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',
            'price'             => 'sometimes|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|unique:products,sku,' . $product->id,
            'stock'             => 'sometimes|integer|min:0',
            'manage_stock'      => 'boolean',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'weight'            => 'nullable|numeric|min:0',
        ]);

        $product->update($validated);

        return back()->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto eliminado.');
    }
}