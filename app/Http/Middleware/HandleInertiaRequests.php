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
            'cart' => fn () => [
                'count' => $this->getCartCount($request),
            ],

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