<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject("Pedido #{$this->order->order_number} recibido")
            ->greeting("¡Hola {$this->order->user?->name}!")
            ->line("Recibimos tu pedido **#{$this->order->order_number}**.")
            ->line("**Total:** $" . number_format($this->order->total, 2, ',', '.'))
            ->action('Ver mi pedido', route('orders.show', $this->order->id))
            ->line('Gracias por tu compra.');
    }
}