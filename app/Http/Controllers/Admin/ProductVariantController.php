<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'sku'       => 'nullable|string|unique:product_variants,sku',
            'price'     => 'nullable|numeric|min:0',
            'stock'     => 'required|integer|min:0',
            'options'   => 'required|array',
            'is_active' => 'boolean',
        ]);

        $product->variants()->create($request->only(
            'name', 'sku', 'price', 'stock', 'options', 'is_active'
        ));

        return back()->with('success', 'Variante agregada.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'sku'       => 'nullable|string|unique:product_variants,sku,' . $variant->id,
            'price'     => 'nullable|numeric|min:0',
            'stock'     => 'required|integer|min:0',
            'options'   => 'required|array',
            'is_active' => 'boolean',
        ]);

        $variant->update($request->only(
            'name', 'sku', 'price', 'stock', 'options', 'is_active'
        ));

        return back()->with('success', 'Variante actualizada.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant->delete();

        return back()->with('success', 'Variante eliminada.');
    }
}