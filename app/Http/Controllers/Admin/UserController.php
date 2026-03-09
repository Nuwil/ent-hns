<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        // Redirect to settings which lists users in this app
        return redirect()->route('admin.settings');
    }

    public function edit(User $user)
    {
        // Return the user data as JSON for modal editing
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'full_name' => 'nullable|string|max:255',
            'role' => 'required|string|max:50',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = new User();
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->full_name = $data['full_name'] ?? null;
        $user->role = $data['role'];
        $user->is_active = $request->has('is_active') ? true : false;

        // automatically protect the very first administrator account created
        // this covers both the seeder case as well as any manual creation
        // through the UI/API.  We protect based on whether there are currently
        // *no* admins in the database and the incoming role is 'admin'.
        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 0) {
            $user->is_protected = true;
        }

        $user->password_hash = Hash::make($data['password']);

        $user->save();

        return redirect()->route('admin.settings')->with('status', 'User created.');
    }

    public function update(Request $request, User $user)
    {
        // Validate the request
        $data = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'full_name' => 'nullable|string|max:255',
            'role' => 'required|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Update user attributes
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->full_name = $data['full_name'] ?? null;
        
        // Only allow role change if not demoting the last admin
        if ($data['role'] !== $user->role && $user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.settings')->with('error', 'Cannot change role of the only administrator.');
        }
        
        $user->role = $data['role'];

        // Update password if provided
        if (! empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.settings')->with('status', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // never allow deletion of a protected user
        if ($user->is_protected) {
            abort(403, 'Cannot delete a protected user.');
        }

        // also prevent removing the last remaining administrator in the
        // system – this could otherwise happen if an unprotected admin is
        // accidentally removed, which is what happened in the report.
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            abort(403, 'Cannot delete the only administrator account.');
        }

        $user->delete();

        return redirect()->route('admin.settings')->with('status', 'User deleted.');
    }

    public function toggle(User $user)
    {
        if ($user->is_protected) {
            abort(403, 'Cannot modify a protected user.');
        }

        // prevent deactivating the last active admin account
        if (! $user->is_active && $user->role === 'admin' && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            abort(403, 'Cannot deactivate the only active administrator.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return redirect()->route('admin.settings')->with('status', 'User status updated.');
    }
}
