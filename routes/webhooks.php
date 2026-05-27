<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;

Route::post('/webhooks/mercadopago', [CheckoutController::class, 'webhook'])
    ->name('webhooks.mercadopago');