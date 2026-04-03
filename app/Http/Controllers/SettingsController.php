<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('full_name')->get();
        return view('settings.index', compact('users'));
    }

    // ── My Account update ─────────────────────────────────────────

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password'  => ['nullable', 'string', 'confirmed', 'regex:/(?=(?:.*[A-Z]){3})(?=(?:.*[0-9]){3})(?=(?:.*[^A-Za-z0-9]){3}).{8,}/'],
        ], [
            'password.regex' => 'Password must be at least 8 characters with 3 uppercase letters, 3 numbers, and 3 symbols.',
        ]);

        $user->full_name = $data['full_name'];
        $user->email     = $data['email'];

        if (!empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
        }

        $user->save();

        ActivityLog::log('user.updated', "Updated own account settings", 'info');

        return back()->with('toast_success', 'Account settings saved.');
    }

    // ── Create new user ───────────────────────────────────────────

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:150',
            'username'  => 'required|string|max:100|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'role'      => 'required|in:doctor,secretary',
            'password'  => ['required', 'string', 'confirmed', 'regex:/(?=(?:.*[A-Z]){3})(?=(?:.*[0-9]){3})(?=(?:.*[^A-Za-z0-9]){3}).{8,}/'],
        ], [
            'password.regex' => 'Password must be at least 8 characters with 3 uppercase letters, 3 numbers, and 3 symbols.',
        ]);

        $user = User::create([
            'full_name'     => $data['full_name'],
            'username'      => $data['username'],
            'email'         => $data['email'],
            'role'          => $data['role'],
            'password_hash' => Hash::make($data['password']),
            'is_active'     => true,
            'is_protected'  => false,
        ]);

        ActivityLog::log(
            action:      'user.created',
            description: "Created new {$user->role} account: {$user->full_name}",
            severity:    'info',
            subject:     $user,
        );

        return back()->with('toast_success', "Account for {$user->full_name} created successfully.");
    }

    // ── Update existing user ──────────────────────────────────────

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:150',
            'username'  => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user->id)],
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password'  => ['nullable', 'string', 'confirmed', 'regex:/(?=(?:.*[A-Z]){3})(?=(?:.*[0-9]){3})(?=(?:.*[^A-Za-z0-9]){3}).{8,}/'],
        ], [
            'password.regex' => 'Password must be at least 8 characters with 3 uppercase letters, 3 numbers, and 3 symbols.',
        ]);

        $user->full_name = $data['full_name'];
        $user->username  = $data['username'];
        $user->email     = $data['email'];
        // Role is intentionally NOT updatable after account creation

        if (!empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
        }

        $user->save();

        ActivityLog::log(
            action:      'user.updated',
            description: "Updated account: {$user->full_name} ({$user->role})",
            severity:    'info',
            subject:     $user,
        );

        return back()->with('toast_success', "{$user->full_name}'s account updated.");
    }

    // ── Delete user ───────────────────────────────────────────────

    public function destroyUser(User $user)
    {
        if ($user->is_protected) {
            return back()->with('toast_error', 'The main admin account cannot be deleted.');
        }

        if ($user->id === Auth::id()) {
            return back()->with('toast_error', 'You cannot delete your own account.');
        }

        $name = $user->full_name;
        $user->delete();

        ActivityLog::log(
            action:      'user.deleted',
            description: "Deleted account: {$name}",
            severity:    'warning',
        );

        return back()->with('toast_success', "{$name}'s account has been deleted.");
    }

    // ── Toggle active/inactive ────────────────────────────────────

    public function toggleUser(User $user)
    {
        if ($user->is_protected) {
            return back()->with('toast_error', 'Cannot deactivate the main admin account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';

        ActivityLog::log(
            action:      'user.updated',
            description: "Account {$status}: {$user->full_name}",
            severity:    $user->is_active ? 'info' : 'warning',
            subject:     $user,
        );

        return back()->with('toast_success', "{$user->full_name}'s account has been {$status}.");
    }
}