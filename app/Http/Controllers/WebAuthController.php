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
        
        // Don't regenerate session - it might be breaking persistence
        // Just set the user data
        $request->session()->put([
            'user_id' => $user->id,
            'user_name' => $user->full_name ?? $user->username,
            'user_role' => strtolower($user->role),
        ]);
        
        \Log::info('Session data before save', ['session_all' => $request->session()->all()]);
        
        // Save session to database immediately
        $request->session()->save();
        
        \Log::info('Session saved', ['session_id' => $request->session()->getId()]);
        
        // Get the session ID
        $sessionId = $request->session()->getId();
        
        // Verify it was saved to database
        $dbCheck = \DB::table('sessions')->where('id', $sessionId)->first();
        \Log::info('Checking database after save', [
            'session_id' => $sessionId,
            'found_in_db' => $dbCheck ? 'YES' : 'NO',
            'db_user_id' => $dbCheck ? $dbCheck->user_id : 'N/A',
        ]);
        
        // Populate the user_id column in the sessions table
        $updated = \DB::table('sessions')
            ->where('id', $sessionId)
            ->update(['user_id' => $user->id]);
        
        \Log::info('User logged in', [
            'user_id' => $user->id,
            'username' => $user->username,
            'session_id' => $sessionId,
            'session_rows_updated' => $updated,
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
