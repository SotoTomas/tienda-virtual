<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $query->paginate(20)->through(fn ($o) => [
                'id'             => $o->id,
                'order_number'   => $o->order_number,
                'customer'       => $o->user?->name ?? 'Guest',
                'customer_email' => $o->user?->email ?? '—',
                'total'          => $o->total,
                'status'         => $o->status,
                'payment_status' => $o->payment_status,
                'created_at'     => $o->created_at->format('d/m/Y H:i'),
            ]),
            'filters' => $request->only('status', 'search'),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load('items', 'user');

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id'               => $order->id,
                'order_number'     => $order->order_number,
                'customer'         => $order->user?->name ?? 'Guest',
                'customer_email'   => $order->user?->email ?? '—',
                'status'           => $order->status,
                'payment_status'   => $order->payment_status,
                'payment_method'   => $order->payment_method,
                'subtotal'         => $order->subtotal,
                'discount'         => $order->discount,
                'shipping_cost'    => $order->shipping_cost,
                'total'            => $order->total,
                'notes'            => $order->notes,
                'billing_address'  => $order->billing_address,
                'shipping_address' => $order->shipping_address,
                'paid_at'          => $order->paid_at?->format('d/m/Y H:i'),
                'created_at'       => $order->created_at->format('d/m/Y H:i'),
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

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
        ]);

        $order->update(['status' => $request->status]);

        // Timestamps automáticos según estado
        match($request->status) {
            'shipped'   => $order->update(['shipped_at'   => now()]),
            'delivered' => $order->update(['delivered_at' => now()]),
            default     => null,
        };

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}