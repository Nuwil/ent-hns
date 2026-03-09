<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;

class AdminDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        // Provide some simple stats for the dashboard
        $totalPatients = Patient::count();
        $systemUsers = User::count();

        return view('admin.dashboard', [
            'user' => $user,
            'totalPatients' => $totalPatients,
            'systemUsers' => $systemUsers,
        ]);
    }

    public function settings(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        // Provide the full list of users for the admin settings view
        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.settings', ['user' => $user, 'users' => $users]);
    }
}
