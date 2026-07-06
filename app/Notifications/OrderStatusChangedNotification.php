<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $statusLabels = [
            'confirmed'  => 'confirmado',
            'processing' => 'en preparación',
            'shipped'    => 'enviado',
            'delivered'  => 'entregado',
            'cancelled'  => 'cancelado',
        ];

        $statusValue = $this->order->status instanceof \BackedEnum
            ? $this->order->status->value
            : $this->order->status;

        $label = $statusLabels[$statusValue] ?? $statusValue;

        return (new MailMessage)
            ->subject("Tu pedido #{$this->order->order_number} está {$label}")
            ->greeting("¡Hola {$this->order->user?->name}!")
            ->line("Tu pedido **#{$this->order->order_number}** está {$label}.")
            ->when($statusValue === 'shipped', fn ($mail) =>
                $mail->line('Pronto recibirás el seguimiento del envío.')
            )
            ->action('Ver mi pedido', route('orders.show', $this->order->id))
            ->line('Gracias por comprar con nosotros.');
    }
}