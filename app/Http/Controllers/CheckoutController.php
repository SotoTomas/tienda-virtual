<?php

namespace App\Http\Controllers;

use App\Actions\Order\CreateOrderFromCart;
use App\Models\Cart;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function index(): Response|RedirectResponse
    {
        $cart = $this->getCart();

        // Si el carrito está vacío redirigir
        if (! $cart || $cart->items()->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $cart->load('items.product', 'items.variant');

        return Inertia::render('Checkout/Index', [
            'cart' => [
                'items' => $cart->items->map(fn ($item) => [
                    'id'       => $item->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal'   => $item->subtotal,
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
                'coupon_discount' => 0,
                'total'           => $cart->total,
            ],
            'addresses' => auth()->user()
                ?->addresses()
                ->get()
                ->map(fn ($a) => [
                    'id'          => $a->id,
                    'name'        => $a->name,
                    'phone'       => $a->phone,
                    'street'      => $a->street,
                    'number'      => $a->number,
                    'floor'       => $a->floor,
                    'apartment'   => $a->apartment,
                    'city'        => $a->city,
                    'state'       => $a->state,
                    'postal_code' => $a->postal_code,
                ]) ?? [],
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'street'         => 'required|string|max:255',
            'number'         => 'required|string|max:20',
            'floor'          => 'nullable|string|max:10',
            'apartment'      => 'nullable|string|max:10',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'postal_code'    => 'required|string|max:10',
            'payment_method' => 'required|in:mercadopago,transferencia',
            'notes'          => 'nullable|string|max:500',
            'save_address'   => 'boolean',
        ]);

        $cart = $this->getCart();

        if (! $cart || $cart->items()->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        // Crear la orden
        $order = app(CreateOrderFromCart::class)->handle($cart, $request->all());

        // Redirigir según método de pago
        if ($request->payment_method === 'mercadopago') {
            try {
                $mp         = new MercadoPagoService();
                $preference = $mp->createPreference($order);

                // Guardar preference_id en la orden
                $order->update(['payment_id' => $preference['preference_id']]);

                // Redirigir a MercadoPago
                return redirect($preference['init_point']);

            } catch (\Exception $e) {
                return redirect()->route('checkout.success', ['order' => $order->id])
                    ->with('info', 'Orden creada. Completá el pago cuando puedas.');
            }
        }

        // Transferencia bancaria — ir directo al éxito
        return redirect()->route('checkout.success', ['order' => $order->id]);
    }

    public function success(Request $request): Response|RedirectResponse
    {
        $orderId = $request->get('order');

        if (! $orderId) {
            return redirect()->route('store.home');
        }

        $order = auth()->user()
            ?->orders()
            ->with('items')
            ->find($orderId);

        if (! $order) {
            return redirect()->route('store.home');
        }

        // Si viene de MercadoPago con pago aprobado
        if ($request->get('status') === 'approved') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
                'paid_at'        => now(),
            ]);
        }

        return Inertia::render('Checkout/Success', [
            'order' => [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'total'        => $order->total,
                'items'        => $order->items->map(fn ($i) => [
                    'id'           => $i->id,
                    'product_name' => $i->product_name,
                    'variant_name' => $i->variant_name,
                    'quantity'     => $i->quantity,
                    'subtotal'     => $i->subtotal,
                ]),
            ],
        ]);
    }

    // ── Webhook de MercadoPago ────────────────────────────
    public function webhook(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->get('type');

        if ($type !== 'payment') {
            return response()->json(['status' => 'ignored']);
        }

        try {
            $paymentId = $request->get('data')['id'] ?? null;

            if (! $paymentId) {
                return response()->json(['status' => 'no payment id'], 400);
            }

            // Consultar el pago a la API de MP
            $client  = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $client->get($paymentId);

            $order = \App\Models\Order::where('order_number', $payment->external_reference)->first();

            if (! $order) {
                return response()->json(['status' => 'order not found'], 404);
            }

            match($payment->status) {
                'approved' => $order->update([
                    'payment_status' => 'paid',
                    'status'         => 'confirmed',
                    'payment_id'     => $paymentId,
                    'paid_at'        => now(),
                ]),
                'rejected', 'cancelled' => $order->update([
                    'payment_status' => 'unpaid',
                    'status'         => 'cancelled',
                ]),
                default => null,
            };

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    // ── Helper ────────────────────────────────────────────
    private function getCart(): ?Cart
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->with('items')->first();
        }

        return Cart::where('session_id', session()->getId())->with('items')->first();
    }
}