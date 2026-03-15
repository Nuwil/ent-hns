<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        $remember = $request->boolean('remember_me');

        if (!Auth::attempt($credentials, $remember)) {
            // Log failed login attempt
            ActivityLog::log(
                action:      'auth.login_failed',
                description: "Failed login attempt for email: {$request->email}",
                severity:    'danger',
            );

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Log successful login
        ActivityLog::log(
            action:      'auth.login',
            description: "Logged in successfully",
            severity:    'info',
            subject:     null,
            actor:       $user,
        );

        return redirect()->route($user->dashboardRoute());
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log logout before session is cleared
        ActivityLog::log(
            action:      'auth.logout',
            description: "Logged out",
            severity:    'info',
            actor:       $user,
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('toast_success', 'You have been logged out successfully.');
    }
}