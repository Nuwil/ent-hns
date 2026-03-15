<?php
/**
 * Debug script to test authentication and session issues
 * Run from CLI: php debug-auth-session.php
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Test 1: Check database sessions table
echo "=== TEST 1: Database Sessions Table ===\n";
try {
    $sessions = \DB::table('sessions')->limit(5)->get(['id', 'user_id', 'last_activity']);
    echo "Total sessions in DB: " . \DB::table('sessions')->count() . "\n";
    echo "Sample sessions:\n";
    foreach ($sessions as $session) {
        echo "  - ID: " . substr($session->id, 0, 20) . "... User: " . ($session->user_id ?? 'NULL') . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Test 2: Check users
echo "\n=== TEST 2: Users in Database ===\n";
try {
    $users = \App\Models\User::select('id', 'username', 'email', 'role', 'is_active')->limit(5)->get();
    echo "Total users: " . \App\Models\User::count() . "\n";
    foreach ($users as $user) {
        echo "  - ID: {$user->id} | Username: {$user->username} | Role: {$user->role} | Active: {$user->is_active}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Test 3: Simulate API request with session middleware
echo "\n=== TEST 3: Simulating API Request Flow ===\n";
try {
    // Get a valid session from database
    $validSession = \DB::table('sessions')->where('user_id', '!=', null)->first(['id', 'user_id', 'payload']);
    
    if (!$validSession) {
        echo "No valid sessions found in database!\n";
    } else {
        echo "Testing with valid session:\n";
        echo "  - Session ID: " . substr($validSession->id, 0, 20) . "...\n";
        echo "  - User ID in DB: {$validSession->user_id}\n";
        
        // Try to deserialize payload
        if ($validSession->payload) {
            try {
                $payload = unserialize($validSession->payload);
                echo "  - Payload keys: " . implode(', ', array_keys($payload)) . "\n";
                echo "  - Has user_id in payload: " . (isset($payload['user_id']) ? 'YES' : 'NO') . "\n";
            } catch (\Exception $e) {
                echo "  - ERROR deserializing payload: " . $e->getMessage() . "\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Test 4: Check session configuration
echo "\n=== TEST 4: Session Configuration ===\n";
echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Table: " . config('session.table') . "\n";
echo "Session Lifetime: " . config('session.lifetime') . " minutes\n";
echo "Session Cookie Name: " . config('session.cookie') . "\n";

// Test 5: Check middleware configuration
echo "\n=== TEST 5: Middleware Configuration ===\n";
echo "Middleware aliases configured:\n";
$aliases = config('middleware.aliases') ?? [];
foreach ($aliases as $alias => $class) {
    if (strpos($alias, 'session') !== false || strpos($alias, 'auth') !== false) {
        echo "  - $alias => " . (class_exists($class) ? '✓ EXISTS' : '✗ MISSING') . "\n";
    }
}

// Test 6: Check if routes are properly configured
echo "\n=== TEST 6: API Routes ===\n";
$routes = \Route::getRoutes();
$apiRoutes = [];
foreach ($routes as $route) {
    if (strpos($route->uri(), 'api/') === 0) {
        $apiRoutes[] = $route->uri();
    }
}
echo "API routes found: " . count($apiRoutes) . "\n";
echo "Sample routes:\n";
foreach (array_slice($apiRoutes, 0, 5) as $route) {
    echo "  - $route\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
