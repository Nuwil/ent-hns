<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            \Log::info('API LOGIN METHOD CALLED', ['at' => now()]);
            
            $username = trim($request->input('username') ?? $request->input('email'));
            $password = $request->input('password');

            if (!$username || !$password) {
                return response()->json(['error' => 'username/email and password required'], 400);
            }

            // Find user by username or email
            $user = User::where('username', $username)
                ->orWhere('email', $username)
                ->first();

            if (!$user) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            if (!$user->is_active) {
                return response()->json(['error' => 'Account is disabled'], 403);
            }

            if (!Hash::check($password, $user->password_hash)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            \Log::info('API User found and password correct', ['user_id' => $user->id, 'username' => $user->username]);

            // Regenerate session ID for security
            $request->session()->regenerate();

            // Store user data in session - MUST match web auth controller format
            $request->session()->put([
                'user_id' => $user->id,
                'user_name' => $user->full_name ?? $user->username,
                'user_role' => strtolower($user->role),
            ]);
            
            $sessionId = $request->session()->getId();

            \Log::info('API Session data before save', ['session_all' => $request->session()->all()]);

            // Save session to database immediately
            $request->session()->save();

            \Log::info('API Session saved', ['session_id' => $sessionId]);

            // CRITICAL: Manually update user_id in database sessions table
            // Laravel doesn't automatically set this column
            \DB::table(config('session.table'))->where('id', $sessionId)->update([
                'user_id' => $user->id,
                'last_activity' => time(),
            ]);

            // Verify it was saved to database
            $dbSession = \DB::table(config('session.table'))->where('id', $sessionId)->first();

            \Log::info('Checking database after save', [
                'session_id' => $sessionId,
                'found_in_db' => $dbSession ? 'YES' : 'NO',
                'db_user_id' => $dbSession?->user_id,
            ]);

            \Log::info('User logged in', [
                'user_id' => $user->id,
                'username' => $user->username,
                'session_id' => $sessionId,
                'session_rows_updated' => $dbSession ? 1 : 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'full_name' => $user->full_name,
                        'role' => $user->role,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('API Login failed: ' . $e->getMessage());
            return response()->json(['error' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        \Log::info('API Logout called');
        
        $sessionId = $request->session()->getId();
        
        // Forget user session data
        $request->session()->forget(['user_id', 'user_name', 'user_role']);
        
        // Save the cleared session
        $request->session()->save();
        
        \Log::info('API User logged out', ['session_id' => $sessionId]);

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'full_name' => $user->full_name,
                'role' => $user->role,
            ]
        ]);
    }
}
