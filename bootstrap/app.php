<?php
// ============================================================
// FOR LARAVEL 10 — app/Http/Kernel.php
// Add the following to $middlewareAliases (or $routeMiddleware):
// ============================================================
//
//   protected $middlewareAliases = [
//       // ... existing entries ...
//       'role' => \App\Http\Middleware\RoleMiddleware::class,
//       'session.valid' => \App\Http\Middleware\EnsureSessionIsValid::class,
//   ];
//
//   Also add to $middlewareGroups['web']:
//       \App\Http\Middleware\EnsureSessionIsValid::class,
//
// ============================================================
// FOR LARAVEL 11 — bootstrap/app.php
// ============================================================
//
// use Illuminate\Foundation\Application;
// use Illuminate\Foundation\Configuration\Exceptions;
// use Illuminate\Foundation\Configuration\Middleware;
//
// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware) {
//         $middleware->web(append: [
//             \App\Http\Middleware\EnsureSessionIsValid::class,
//         ]);
//         $middleware->alias([
//             'role' => \App\Http\Middleware\RoleMiddleware::class,
//         ]);
//     })
//     ->withExceptions(function (Exceptions $exceptions) {
//         //
//     })->create();
//
// ============================================================
// COMPLETE bootstrap/app.php for Laravel 11 (copy this file):
// ============================================================

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
        $middleware->web(append: [
            \App\Http\Middleware\EnsureSessionIsValid::class,
            \App\Http\Middleware\LogActivityMiddleware::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            $status = $e->getStatusCode();
            if (view()->exists("errors.$status")) {
                return response()->view("errors.$status", ['exception' => $e], $status);
            }
        });
    })->create();