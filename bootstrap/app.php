<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => \App\Http\Middleware\ResolveTenant::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'license' => \App\Http\Middleware\CheckLicense::class,
            'system_admin' => \App\Http\Middleware\SystemAdmin::class,
        ]);

        // Apply tenant detection to all web routes
        $middleware->web(append: [
            \App\Http\Middleware\ResolveTenant::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // Override default authenticated redirect (Laravel defaults to 'dashboard' route)
        \Illuminate\Auth\Middleware\RedirectIfAuthenticated::redirectUsing(function () {
            return route('app-gateway', [], false);
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
