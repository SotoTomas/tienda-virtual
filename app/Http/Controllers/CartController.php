<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;


class CartController extends Controller
{
    public function index(): Response
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product', 'items.variant');

        $couponDiscount = 0;
        if ($cart->coupon_code) {
            $coupon = Coupon::valid()->where('code', $cart->coupon_code)->first();
            if ($coupon) {
                $couponDiscount = $coupon->calculateDiscount($cart->total);
            }
        }

        return Inertia::render('Cart/Index', [
            'cart' => [
                'items' => $cart->items->map(fn ($item) => [
                    'id'          => $item->id,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                    'subtotal'    => $item->subtotal,
                    'product' => [
                        'id'         => $item->product->id,
                        'name'       => $item->product->name,
                        'slug'       => $item->product->slug,
                        'main_image' => $item->product->main_image,
                        'stock'      => $item->product->stock,
                    ],
                    'variant' => $item->variant ? [
                        'id'   => $item->variant->id,
                        'name' => $item->variant->name,
                    ] : null,
                ]),
                'subtotal'        => $cart->total,
                'coupon_code'     => $cart->coupon_code,
                'coupon_discount' => $couponDiscount,
                'total'           => max(0, $cart->total - $couponDiscount),
                'item_count'      => $cart->item_count,
            ],
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id'         => 'required|exists:products,id',
            'quantity'           => 'required|integer|min:1|max:10',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (! $product->is_in_stock) {
            return back()->with('error', 'El producto no tiene stock disponible.');
        }

        $cart = $this->getOrCreateCart();

        // Si ya existe el item, suma la cantidad
        $existingItem = $cart->items()
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $request->quantity);
        } else {
            $cart->items()->create([
                'product_id'         => $request->product_id,
                'product_variant_id' => $request->product_variant_id,
                'quantity'           => $request->quantity,
            ]);
        }

        return back()->with('success', '¡Producto agregado al carrito!');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = $this->getOrCreateCart();

        $item = $cart->items()->findOrFail($id);
        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Carrito actualizado.');
    }

    public function remove(int $id): RedirectResponse
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->findOrFail($id)->delete();

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::valid()
            ->where('code', strtoupper($request->code))
            ->first();

        if (! $coupon) {
            return back()->with('error', 'El cupón no es válido o está vencido.');
        }

        $cart = $this->getOrCreateCart();
        $cart->update(['coupon_code' => $coupon->code]);

        return back()->with('success', "Cupón {$coupon->code} aplicado.");
    }

    public function removeCoupon(): RedirectResponse
    {
        $cart = $this->getOrCreateCart();
        $cart->update(['coupon_code' => null]);

        return back()->with('success', 'Cupón eliminado.');
    }

    // ── Helper ────────────────────────────────────────────────
    private function getOrCreateCart(): Cart
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }

        return Cart::firstOrCreate(['session_id' => session()->getId()]);
    }
}