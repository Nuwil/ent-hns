<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientsController;
use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\VisitsController;
use App\Http\Controllers\Api\MedicinesController;
use App\Http\Controllers\Api\AnalyticsController;

// Authentication routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/me', [AuthController::class, 'me']);

// Protected API routes that require session authentication
Route::middleware('auth.session')->group(function () {
    // Patients routes
    Route::apiResource('patients', PatientsController::class);

    // Appointments routes
    Route::get('/appointments/doctors', [AppointmentsController::class, 'doctors']);
    Route::apiResource('appointments', AppointmentsController::class);

    // Visits routes
    Route::apiResource('visits', VisitsController::class);

    // Medicines routes
    Route::apiResource('medicines', MedicinesController::class);

    // Analytics routes
    Route::get('/analytics/dashboard', [AnalyticsController::class, 'getDashboard']);
    Route::get('/analytics/metrics', [AnalyticsController::class, 'getMetrics']);
});

// Debug appointments endpoint with full logging
Route::middleware(['debug.session', \Illuminate\Session\Middleware\StartSession::class])->get('/appointments-debug', function (\Illuminate\Http\Request $request) {
    $userId = session('user_id');
    return response()->json([
        'user_id' => $userId,
        'session_id' => session()->getId(),
        'has_user_id' => session()->has('user_id'),
        'session_data' => session()->all(),
    ]);
});

// Locations (public endpoint)
Route::get('/locations', [App\Http\Controllers\Api\LocationsController::class, 'index']);

// Debug endpoints
Route::middleware([\Illuminate\Session\Middleware\StartSession::class, 'debug.session'])->get('/debug/session', function (\Illuminate\Http\Request $request) {
    $cookieName = config('session.cookie');
    $cookies = $request->cookies->all();
    $cookieHeader = $request->header('Cookie');
    
    return response()->json([
        'debug' => 'Session diagnostic endpoint',
        'session_id' => session()->getId(),
        'user_id' => session('user_id'),
        'session_driver' => config('session.driver'),
        'cookie_name' => $cookieName,
        'has_user_id' => session()->has('user_id'),
        'cookie_in_request' => $request->hasCookie($cookieName),
        'all_cookies' => array_keys($cookies),
        'cookie_header_present' => !empty($cookieHeader),
        'cookie_header_value' => $cookieHeader ? substr($cookieHeader, 0, 100) . '...' : 'NO COOKIE HEADER',
        'session_data' => session()->all(),
        'session_data_count' => count(session()->all()),
    ]);
});

Route::get('/debug/database-sessions', function () {
    $sessions = DB::table('sessions')->latest('last_activity')->limit(10)->get();
    
    $decoded = $sessions->map(function($s) {
        $payload = unserialize(base64_decode($s->payload));
        return [
            'id' => substr($s->id, 0, 20) . '...',
            'user_id_column' => $s->user_id,
            'payload_data' => is_array($payload) ? $payload : 'COULD NOT DECODE',
            'last_activity' => \Carbon\Carbon::createFromTimestamp($s->last_activity)->diffForHumans(),
        ];
    })->toArray();
    
    return response()->json([
        'total_sessions' => DB::table('sessions')->count(),
        'recent_sessions' => $decoded,
    ]);
});

// Test endpoint that goes through auth.session middleware
Route::middleware('auth.session')->get('/debug/auth-test', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'debug' => 'auth.session middleware test - if you see this, you are authenticated',
        'user_id' => session('user_id'),
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
    ]);
});

// Compare cookie vs database session
Route::middleware([\Illuminate\Session\Middleware\StartSession::class])->get('/debug/session-comparison', function (\Illuminate\Http\Request $request) {
    $cookieName = config('session.cookie');
    $cookieValue = $request->cookie($cookieName);
    
    $dbSession = null;
    $decodedPayload = null;
    if ($cookieValue) {
        $dbSession = DB::table('sessions')
            ->where('id', $cookieValue)
            ->first();
        
        if ($dbSession) {
            $decodedPayload = unserialize(base64_decode($dbSession->payload));
        }
    }
    
    return response()->json([
        'cookie_name' => $cookieName,
        'cookie_value_from_request' => $cookieValue ?: 'NO COOKIE',
        'session_id_from_laravel' => session()->getId(),
        'do_cookie_and_session_match' => $cookieValue === session()->getId(),
        'database_session_found' => $dbSession ? true : false,
        'database_session_user_id_column' => $dbSession ? $dbSession->user_id : 'N/A',
        'database_payload_decoded' => $decodedPayload ?: 'Could not decode',
        'laravel_session_user_id_value' => session('user_id'),
        'laravel_session_all_data' => session()->all(),
    ]);
});

Route::get('/debug/users', function () {
    $users = DB::table('users')->select('id', 'username', 'role', 'is_active')->limit(5)->get();
    return response()->json([
        'total_users' => DB::table('users')->count(),
        'users' => $users->map(fn($u) => (array) $u)->toArray(),
    ]);
});

// Test manual login
Route::post('/debug/test-manual-login', function (\Illuminate\Http\Request $request) {
    $username = $request->input('username');
    $password = $request->input('password');
    
    \Log::info('Manual login test called', [
        'username' => $username,
    ]);
    
    $user = \App\Models\User::where('username', $username)->first();
    
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    if (!\Illuminate\Support\Facades\Hash::check($password, $user->password_hash)) {
        return response()->json(['error' => 'Password incorrect'], 401);
    }
    
    // Manually set session data
    $request->session()->regenerate();
    $request->session()->put([
        'user_id' => $user->id,
        'user_name' => $user->full_name ?? $user->username,
        'user_role' => strtolower($user->role),
    ]);
    $request->session()->save();
    
    \Log::info('Manual login test - session set', [
        'user_id' => $request->session()->get('user_id'),
        'session_id' => $request->session()->getId(),
    ]);
    
    return response()->json([
        'success' => true,
        'user_id' => $request->session()->get('user_id'),
        'session_id' => $request->session()->getId(),
        'session_data' => $request->session()->all(),
    ]);
});

Route::post('/debug/test-login', function (\Illuminate\Http\Request $request) {
    $username = $request->input('username', 'admin');
    $password = $request->input('password', 'admin123');
    
    $user = DB::table('users')->where('username', $username)->first();
    
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    $passwordMatch = \Illuminate\Support\Facades\Hash::check($password, $user->password_hash);
    
    return response()->json([
        'user_found' => true,
        'username' => $user->username,
        'password_hash' => substr($user->password_hash, 0, 20) . '...',
        'password_matches' => $passwordMatch,
        'role' => $user->role,
    ]);
});

Route::middleware([\Illuminate\Session\Middleware\StartSession::class])->post('/debug/manual-login', function (\Illuminate\Http\Request $request) {
    // Get the username parameter
    $username = $request->input('username', 'admin');
    
    // Find the user
    $user = \App\Models\User::where('username', $username)->first();
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    // Manually set session data (simulating what WebAuthController does)
    session([
        'user_id' => $user->id,
        'user_name' => $user->full_name ?? $user->username,
        'user_role' => strtolower($user->role),
    ]);
    
    $sessionId = session()->getId();
    $userId = session('user_id');
    
    // Return what we set - client should use this session ID in future requests
    return response()->json([
        'message' => 'Session set - use the session ID in Cookie header for next request',
        'session_id' => $sessionId,
        'user_id_set' => $userId,
        'username' => $user->username,
        'role' => $user->role,
        'cookie_to_send' => "ent-clinic-online-session={$sessionId}",
    ]);
});
