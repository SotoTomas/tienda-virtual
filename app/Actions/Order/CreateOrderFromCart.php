<?php

namespace App\Actions\Order;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Support\Str;

class CreateOrderFromCart
{
    public function handle(Cart $cart, array $data): Order
    {
        $cart->load('items.product', 'items.variant');

        // Calcular totales
        $subtotal = $cart->total;
        $discount = 0;

        if ($cart->coupon_code) {
            $coupon = Coupon::valid()->where('code', $cart->coupon_code)->first();
            if ($coupon) {
                $discount = $coupon->calculateDiscount($subtotal);
                $coupon->increment('used_count');
            }
        }

        $total = max(0, $subtotal - $discount);

        // Snapshot de dirección
        $address = [
            'name'        => $data['name'],
            'phone'       => $data['phone'] ?? null,
            'street'      => $data['street'],
            'number'      => $data['number'],
            'floor'       => $data['floor'] ?? null,
            'apartment'   => $data['apartment'] ?? null,
            'city'        => $data['city'],
            'state'       => $data['state'],
            'postal_code' => $data['postal_code'],
            'country'     => $data['country'] ?? 'AR',
        ];

        // Crear orden
        $order = Order::create([
            'order_number'     => $this->generateOrderNumber(),
            'user_id'          => auth()->id(),
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
            'payment_method'   => $data['payment_method'],
            'subtotal'         => $subtotal,
            'discount'         => $discount,
            'shipping_cost'    => 0,
            'tax'              => 0,
            'total'            => $total,
            'billing_address'  => $address,
            'shipping_address' => $address,
            'notes'            => $data['notes'] ?? null,
            'coupon_code'      => $cart->coupon_code,
        ]);

        // Crear items y descontar stock
        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id'         => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name'       => $item->product->name,
                'variant_name'       => $item->variant?->name,
                'sku'                => $item->variant?->sku ?? $item->product->sku,
                'quantity'           => $item->quantity,
                'unit_price'         => $item->unit_price,
                'subtotal'           => $item->subtotal,
                'options'            => $item->variant?->options,
            ]);

            // Descontar stock
            if ($item->variant) {
                $item->variant->decrement('stock', $item->quantity);
            } elseif ($item->product->manage_stock) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        // Vaciar carrito
        $cart->items()->delete();
        $cart->update(['coupon_code' => null]);

        // Guardar dirección si el usuario lo pidió
        if (! empty($data['save_address']) && auth()->check()) {
            auth()->user()->addresses()->create([
                ...$address,
                'is_default' => ! auth()->user()->addresses()->exists(),
            ]);
        }

        return $order;
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . strtoupper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}