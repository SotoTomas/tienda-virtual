<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            // ── Usuario autenticado ────────────────────────
            'auth' => [
                'user' => $request->user() ? [
                    'id'    => $request->user()->id,
                    'name'  => $request->user()->name,
                    'email' => $request->user()->email,
                ] : null,
            ],

            // ── Carrito ────────────────────────────────────
            // Se evalúa lazy para no ejecutarse si no se necesita
            'cart' => fn () => $this->getCartData($request),

            // ── Flash messages ─────────────────────────────
            'flash' => [
                'success' => session('success'),
                'error'   => session('error'),
                'info'    => session('info'),
            ],

            // ── Categorías para el navbar ──────────────────
            'categories' => fn () => Category::active()
                ->roots()
                ->ordered()
                ->with('children')
                ->get()
                ->map(fn ($cat) => [
                    'id'       => $cat->id,
                    'name'     => $cat->name,
                    'slug'     => $cat->slug,
                    'children' => $cat->children->map(fn ($child) => [
                        'id'   => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                    ]),
                ]),
        ];
    }
    private function getCartData(Request $request): array
{
    $cart = null;

    if ($request->user()) {
        $cart = \App\Models\Cart::where('user_id', $request->user()->id)
            ->with('items.product', 'items.variant')
            ->first();
    } else {
        $cart = \App\Models\Cart::where('session_id', session()->getId())
            ->with('items.product', 'items.variant')
            ->first();
    }

    if (! $cart) {
        return ['count' => 0, 'items' => [], 'total' => 0, 'item_count' => 0];
    }

    return [
        'count'      => $cart->item_count,
        'item_count' => $cart->item_count,
        'total'      => $cart->total,
        'items'      => $cart->items->map(fn ($item) => [
            'id'         => $item->id,
            'quantity'   => $item->quantity,
            'unit_price' => $item->unit_price,
            'subtotal'   => $item->subtotal,
            'product'    => [
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
        ])->toArray(),
    ];
}
    private function getCartCount(Request $request): int
    {
        // Usuario logueado: busca su carrito en BD
        if ($request->user()) {
            return Cart::where('user_id', $request->user()->id)
                ->with('items')
                ->first()
                ?->item_count ?? 0;
        }

        // Guest: busca por session_id
        return Cart::where('session_id', session()->getId())
            ->with('items')
            ->first()
            ?->item_count ?? 0;
    }
}