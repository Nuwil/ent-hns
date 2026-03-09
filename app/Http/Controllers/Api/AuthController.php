<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
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

            // Successful login: create session
            session(['user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'name' => $user->full_name,
                'role' => $user->role,
            ]]);
            session(['last_activity' => time()]);

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
            return response()->json(['error' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        session()->forget('user');
        session()->flush();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = session('user');

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}
