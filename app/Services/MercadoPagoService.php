<?php

namespace App\Services;

use App\Models\Order;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
        MercadoPagoConfig::setRuntimeEnviroment(
            config('mercadopago.sandbox')
                ? MercadoPagoConfig::LOCAL
                : MercadoPagoConfig::SERVER
        );
    }

    public function createPreference(Order $order): array
    {
        $client = new PreferenceClient();

        $items = $order->items->map(fn ($item) => [
            'id'          => (string) $item->id,
            'title'       => $item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : ''),
            'quantity'    => $item->quantity,
            'unit_price'  => (float) $item->unit_price,
            'currency_id' => 'ARS',
        ])->toArray();

        $preference = $client->create([
            'items'               => $items,
            'external_reference'  => $order->order_number,
            'back_urls'           => [
                'success' => route('checkout.success') . '?order=' . $order->id,
                'failure' => route('checkout.index'),
                'pending' => route('checkout.success') . '?order=' . $order->id,
            ],
            'auto_return'         => 'approved',
            'notification_url'    => route('webhooks.mercadopago'),
            'statement_descriptor'=> config('app.name'),
            'payer'               => [
                'email' => $order->user?->email ?? '',
            ],
        ]);

        return [
            'preference_id' => $preference->id,
            'init_point'    => config('mercadopago.sandbox')
                ? $preference->sandbox_init_point
                : $preference->init_point,
        ];
    }
}