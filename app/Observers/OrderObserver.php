<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Support\Str;

class OrderObserver
{
    public function creating(Order $order): void
    {
        if (empty($order->order_number)) {
            $order->order_number = $this->generateNumber();
        }
    }

    public function created(Order $order): void
    {
        // Notificar al cliente cuando se crea la orden
        $order->user?->notify(new OrderPlacedNotification($order));
    }

    public function updated(Order $order): void
    {
        // Notificar cuando cambia el estado
        if ($order->isDirty('status')) {
            $notifiableStatuses = ['confirmed', 'shipped', 'delivered', 'cancelled'];
            $statusValue = $order->status instanceof \BackedEnum
                ? $order->status->value
                : $order->status;

            if (in_array($statusValue, $notifiableStatuses)) {
                $order->user?->notify(new OrderStatusChangedNotification($order));
            }
        }
    }

    private function generateNumber(): string
    {
        do {
            $number = 'ORD-' . strtoupper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}