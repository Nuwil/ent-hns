<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;

class AdminDashboardController extends Controller
{
    /**
     * Get authenticated user with fallback to session data
     */
    protected function getAuthUser()
    {
        $user = User::find(session('user_id'));
        
        // If user lookup fails but we have session data, create a fallback
        if (!$user && session('user_id')) {
            $user = (object)[
                'id' => session('user_id'),
                'full_name' => session('user_name', 'User'),
                'username' => session('user_name', 'User'),
                'role' => session('user_role', 'admin'),
            ];
        }
        
        return $user;
    }

    public function dashboard(Request $request)
    {
        $user = $this->getAuthUser();
        
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
        $user = $this->getAuthUser();
        
        // Provide the full list of users for the admin settings view
        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.settings', ['user' => $user, 'users' => $users]);
    }

    /**
     * Delete a user from the system
     */
    public function destroyUser($id)
    {
        // Prevent deleting self
        if ($id == session('user_id')) {
            return redirect()->back()->with('error', 'You cannot delete yourself');
        }

        try {
            $user = User::findOrFail($id);
            $userName = $user->username;
            $user->delete();
            
            return redirect()->route('admin.settings')->with('success', "User '$userName' deleted successfully");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Display edit user form - returns JSON for AJAX
     */
    public function editUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Return JSON if this is an AJAX request
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'User not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store a new user in the database
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'full_name' => 'required|string',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,doctor,secretary',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['password_hash'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
            unset($validated['password']);
            $validated['is_protected'] = 0;

            User::create($validated);
            return redirect()->route('admin.settings')->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Update a user's information
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . $id,
            'full_name' => 'required|string',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,doctor,secretary',
            'is_active' => 'boolean',
        ]);

        try {
            $user->update($validated);
            return redirect()->route('admin.settings')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active/inactive status
     */
    public function toggleUser($id)
    {
        // Prevent deactivating self
        if ($id == session('user_id')) {
            return redirect()->back()->with('error', 'You cannot deactivate yourself');
        }

        try {
            $user = User::findOrFail($id);
            $newStatus = !$user->is_active;
            $user->is_active = $newStatus;
            $user->save();
            
            $statusText = $newStatus ? 'activated' : 'deactivated';
            return redirect()->route('admin.settings')->with('success', "User '{$user->username}' $statusText successfully");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error toggling user status: ' . $e->getMessage());
        }
    }
}
