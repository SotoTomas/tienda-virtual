<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        $orders = auth()->user()
            ->orders()
            ->withCount('items')
            ->paginate(10);

        return Inertia::render('Account/Orders/Index', [
            'orders' => $orders->through(fn ($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'total'        => $o->total,
                'status'       => $o->status,
                'items_count'  => $o->items_count,
                'created_at'   => $o->created_at->format('d/m/Y'),
            ]),
        ]);
    }

    public function show(Order $order): Response
    {
        // Verificar que la orden pertenece al usuario
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load('items');

        return Inertia::render('Account/Orders/Show', [
            'order' => [
                'id'               => $order->id,
                'order_number'     => $order->order_number,
                'status'           => $order->status,
                'payment_status'   => $order->payment_status,
                'payment_method'   => $order->payment_method,
                'subtotal'         => $order->subtotal,
                'discount'         => $order->discount,
                'shipping_cost'    => $order->shipping_cost,
                'total'            => $order->total,
                'shipping_address' => $order->shipping_address,
                'notes'            => $order->notes,
                'created_at'       => $order->created_at->format('d/m/Y H:i'),
                'paid_at'          => $order->paid_at?->format('d/m/Y H:i'),
                'shipped_at'       => $order->shipped_at?->format('d/m/Y H:i'),
                'delivered_at'     => $order->delivered_at?->format('d/m/Y H:i'),
                'items'            => $order->items->map(fn ($i) => [
                    'id'           => $i->id,
                    'product_name' => $i->product_name,
                    'variant_name' => $i->variant_name,
                    'sku'          => $i->sku,
                    'quantity'     => $i->quantity,
                    'unit_price'   => $i->unit_price,
                    'subtotal'     => $i->subtotal,
                ]),
            ],
        ]);
    }
}