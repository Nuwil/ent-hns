<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        \Log::info('LOGIN METHOD CALLED', ['at' => now()]);
        
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
            \Log::warning('Login failed - invalid credentials', ['username' => $data['username']]);
            return back()->withInput()->with('error', 'Invalid credentials');   
        }

        \Log::info('User found and password correct', ['user_id' => $user->id, 'username' => $user->username]);
        
        // Regenerate session ID for security, but keep the session data
        $request->session()->regenerate();
        
        // Store user data in session
        $request->session()->put([
            'user_id' => $user->id,
            'user_name' => $user->full_name ?? $user->username,
            'user_role' => strtolower($user->role),
            'last_activity_timestamp' => time(),  // CRITICAL: Set for CheckSessionTimeout middleware
        ]);
        
        $sessionId = $request->session()->getId();
        
        \Log::info('Session data before save', [
            'session_id' => $sessionId,
            'session_all' => $request->session()->all(),
        ]);
        
        // Save session to database immediately and flush handlers
        $request->session()->save();
        
        \Log::info('Session saved', [
            'session_id' => $sessionId,
            'session_keys' => array_keys($request->session()->all()),
        ]);

        // CRITICAL: Manually update user_id in database sessions table
        // Laravel doesn't automatically set this column
        \DB::table('sessions')->where('id', $sessionId)->update([
            'user_id' => $user->id,
            'last_activity' => time(),
        ]);
        
        // Verify it was saved to database
        $dbSession = \DB::table('sessions')->where('id', $sessionId)->first();
        
        \Log::info('Verified session in database', [
            'session_id' => $sessionId,
            'found_in_db' => $dbSession ? 'YES' : 'NO',
            'db_user_id' => $dbSession?->user_id,
            'payload_length' => strlen($dbSession?->payload ?? ''),
        ]);
        
        \Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'username' => $user->username,
            'session_id' => $sessionId,
            'role' => $user->role,
        ]);
        
        // Redirect to role-specific dashboard
        return $this->redirectToDashboard($user->role);
    }

    private function redirectToDashboard($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'doctor':
                return redirect()->route('doctor.dashboard');
            case 'secretary':
                return redirect()->route('secretary.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }

    public function dashboard(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        return view('dashboard', ['user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['user_id', 'user_name', 'user_role']);
        return redirect()->route('login');
    }
}
