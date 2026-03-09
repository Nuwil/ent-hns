<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__."/../routes/web.php",
        api: __DIR__."/../routes/api.php",
        commands: __DIR__."/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Must encrypt/decrypt cookies for both web and API routes
        $middleware->encryptCookies(except: [
            // Add any cookie names that should NOT be encrypted here
        ]);
        
        // Configure web middleware
        $middleware->web(prepend: [
            \Illuminate\Session\Middleware\StartSession::class,
        ], append: [
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\CheckSessionTimeout::class,
        ]);
        
        // Configure API middleware - MUST include session for authenticated API calls
        $middleware->api(prepend: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ], append: [
            \App\Http\Middleware\HandleCors::class,
        ]);
        
        // Add middleware aliases
        $middleware->alias([
            "auth.session" => \App\Http\Middleware\CheckAuth::class,
            "role" => \App\Http\Middleware\CheckRole::class,
            "debug.session" => \App\Http\Middleware\DebugSessionMiddleware::class,
        ]);
        
        // Enable CSRF validation for all routes except API and logout
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'logout',  // TODO: Fix CSRF issue on logout and re-enable
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
