<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\CategoryController;
use App\Http\Controllers\Store\SearchController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;

// ── Tienda pública ─────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('store.home');

// Productos
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/productos/{slug}', [ProductController::class, 'show'])->name('products.show');

// Categorías
Route::get('/categoria/{slug}', [CategoryController::class, 'show'])->name('categories.show');

// Búsqueda
Route::get('/buscar', [SearchController::class, 'index'])->name('search');

// ── Carrito (público — funciona para guests y logueados) ───────
Route::prefix('carrito')->name('cart.')->group(function () {
    Route::get('/',                  [CartController::class, 'index'])->name('index');
    Route::post('/agregar',          [CartController::class, 'add'])->name('add');
    Route::patch('/actualizar/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/eliminar/{id}',  [CartController::class, 'remove'])->name('remove');
    Route::post('/cupon',            [CartController::class, 'applyCoupon'])->name('coupon');
    Route::delete('/cupon',          [CartController::class, 'removeCoupon'])->name('coupon.remove');
});

// ── Rutas autenticadas ─────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/',         [CheckoutController::class, 'index'])->name('index');
        Route::post('/procesar',[CheckoutController::class, 'process'])->name('process');
        Route::get('/exito',    [CheckoutController::class, 'success'])->name('success');
    });

    // Mis órdenes
    Route::prefix('mis-pedidos')->name('orders.')->group(function () {
        Route::get('/',      [OrderController::class, 'index'])->name('index');
        Route::get('/{order}',[OrderController::class, 'show'])->name('show');
    });

    // Wishlist
    Route::post('/wishlist/{product}',   [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist',              [WishlistController::class, 'index'])->name('wishlist.index');

    // Reseñas
    Route::post('/productos/{product}/resena', [ReviewController::class, 'store'])->name('reviews.store');

    // Perfil y direcciones (los genera Breeze, solo agregamos direcciones)
    Route::prefix('mi-cuenta')->name('account.')->group(function () {
        Route::get('/direcciones',          [App\Http\Controllers\AddressController::class, 'index'])->name('addresses.index');
        Route::post('/direcciones',         [App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
        Route::patch('/direcciones/{id}',   [App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/direcciones/{id}',  [App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
    });
});

// ── Auth (generado por Breeze) ─────────────────────────────────
require __DIR__.'/auth.php';

// ── Admin ──────────────────────────────────────────────────────
require __DIR__.'/admin.php';