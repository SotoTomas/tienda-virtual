<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function index(): Response
    {
        $items = auth()->user()
            ->wishlist()
            ->with('product')
            ->get()
            ->map(fn ($w) => [
                'id'      => $w->id,
                'product' => [
                    'id'                  => $w->product->id,
                    'name'                => $w->product->name,
                    'slug'                => $w->product->slug,
                    'price'               => $w->product->price,
                    'compare_price'       => $w->product->compare_price,
                    'has_discount'        => $w->product->has_discount,
                    'discount_percentage' => $w->product->discount_percentage,
                    'main_image'          => $w->product->main_image,
                    'is_in_stock'         => $w->product->is_in_stock,
                ],
            ]);

        return Inertia::render('Account/Wishlist', [
            'items' => $items,
        ]);
    }

    public function toggle(Product $product): RedirectResponse
    {
        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Producto eliminado de tu lista de deseos.');
        }

        Wishlist::create([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Producto agregado a tu lista de deseos.');
    }
}