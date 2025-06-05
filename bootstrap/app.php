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
    ->withMiddleware(function (Middleware $middleware) {
        // Aquí es donde registras los alias de middleware
        $middleware->alias([
            // Los alias que Laravel Breeze podría haber añadido o los que vienen por defecto
            // (Revisa si Breeze añadió 'auth' y 'verified' aquí, si no, añádelos si los necesitas globalmente o en grupos)
            // Si 'auth' y 'verified' ya son manejados por Breeze de otra forma en L12, no necesitas duplicarlos aquí
            // a menos que quieras usarlos con estos nombres explícitos en tus grupos de rutas.
            // Por ahora, nos enfocaremos en añadir el nuestro:
            'admin' => \App\Http\Middleware\CheckAdminRole::class,
        ]);

        // Ejemplo de cómo Breeze podría añadir middleware globalmente (esto es solo un ejemplo, no lo añadas si no es necesario):
        // $middleware->web(append: [
        //     \App\Http\Middleware\HandleInertiaRequests::class, // Si usaras Inertia
        //     \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class, // Si usaras preloading
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
