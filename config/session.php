<?php

/**
 * session.php — Optimized for stability
 *
 * Key settings that prevent unexpected logouts:
 * - driver: 'file' or 'database' (never 'cookie' in production)
 * - lifetime: reasonable session length
 * - expire_on_close: false (keeps session alive)
 * - same_site: 'lax' (allows navigation without clearing session)
 */

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    | Use 'file' for development. Use 'database' or 'redis' in production.
    | NEVER use 'cookie' driver — it causes logout on page navigation.
    */
    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    | 120 minutes = 2 hours. Increase to reduce logout frequency.
    */
    'lifetime' => env('SESSION_LIFETIME', 120),

    /*
    |--------------------------------------------------------------------------
    | Expire On Close
    |--------------------------------------------------------------------------
    | Set to false so session persists when the browser closes and reopens
    | (when remember_me is checked, the remember_token handles this).
    */
    'expire_on_close' => false,

    'encrypt' => false,
    'files'   => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table'      => 'sessions',
    'store'      => env('SESSION_STORE'),
    'lottery'    => [2, 100],
    'cookie'     => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_') . '_session'
    ),
    'path'   => '/',
    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Session Secure Cookie
    |--------------------------------------------------------------------------
    | Set to true in production (HTTPS only).
    */
    'secure' => env('SESSION_SECURE_COOKIE', false),

    'http_only' => true,

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    | 'lax' allows the session cookie to be sent during top-level navigations.
    | This prevents logouts when the user navigates between pages.
    | DO NOT use 'strict' — it causes logout on every navigation from an
    | external link or bookmark.
    */
    'same_site' => 'lax',

    'partitioned' => false,
];
