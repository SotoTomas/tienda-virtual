<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ReviewController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin']) // middleware admin lo creamos abajo
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Productos
        Route::prefix('productos')->name('products.')->group(function () {
            Route::get('/',            [ProductController::class, 'index'])->name('index');
            Route::get('/crear',       [ProductController::class, 'create'])->name('create');
            Route::post('/',           [ProductController::class, 'store'])->name('store');
            Route::get('/{product}',   [ProductController::class, 'edit'])->name('edit');
            Route::patch('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}',[ProductController::class, 'destroy'])->name('destroy');
        });

        // Categorías
        Route::prefix('categorias')->name('categories.')->group(function () {
            Route::get('/',              [CategoryController::class, 'index'])->name('index');
            Route::post('/',             [CategoryController::class, 'store'])->name('store');
            Route::patch('/{category}',  [CategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        // Órdenes
        Route::prefix('pedidos')->name('orders.')->group(function () {
            Route::get('/',             [OrderController::class, 'index'])->name('index');
            Route::get('/{order}',      [OrderController::class, 'show'])->name('show');
            Route::patch('/{order}/estado', [OrderController::class, 'updateStatus'])->name('status');
        });

        // Cupones
        Route::prefix('cupones')->name('coupons.')->group(function () {
            Route::get('/',           [CouponController::class, 'index'])->name('index');
            Route::post('/',          [CouponController::class, 'store'])->name('store');
            Route::patch('/{coupon}', [CouponController::class, 'update'])->name('update');
            Route::delete('/{coupon}',[CouponController::class, 'destroy'])->name('destroy');
        });

        // Reseñas
        Route::prefix('resenas')->name('reviews.')->group(function () {
            Route::get('/',           [ReviewController::class, 'index'])->name('index');
            Route::patch('/{review}', [ReviewController::class, 'approve'])->name('approve');
            Route::delete('/{review}',[ReviewController::class, 'destroy'])->name('destroy');
        });
    });