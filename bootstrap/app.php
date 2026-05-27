<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withRouting(
    web: __DIR__.'/../routes/web.php',
    then: function () {
        Route::middleware('web')
            ->group(base_path('routes/admin.php'));

        // Sin CSRF para webhooks externos
        Route::middleware('api')
            ->group(base_path('routes/webhooks.php'));
    }
)
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
    ]);
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
        // Alias para usar 'admin' en las rutas
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureIsAdmin::class,
    ]);

        //
    })
    
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
