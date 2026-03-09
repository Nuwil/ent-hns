# Quick Reference Guide - Role-Based Access Control

## 🎯 Quick Start for Developers

### Adding a New Role

1. **Add to Database User Roles:**
   Update `database/migrations/0001_01_01_000000_create_users_table.php`:
   ```php
   $table->enum('role', ['admin', 'doctor', 'staff', 'secretary', 'new_role'])->default('staff');
   ```

2. **Create Controller:**
   Create `app/Http/Controllers/NewRoleDashboardController.php`

3. **Add Routes:**
   Update `routes/web.php`:
   ```php
   Route::middleware(['auth.session', 'role:new_role'])->prefix('new-role')->name('new_role.')->group(function () {
       Route::get('/dashboard', [NewRoleDashboardController::class, 'dashboard'])->name('dashboard');
   });
   ```

4. **Create Views:**
   Create `resources/views/new_role/dashboard.blade.php`

5. **Update Login Redirect:**
   Update `WebAuthController::redirectToDashboard()`:
   ```php
   case 'new_role':
       return redirect()->route('new_role.dashboard');
   ```

### Adding a New Protected Route

```php
// Doctor-only route that's not in the default group
Route::middleware(['auth.session', 'role:doctor'])->get('/prescription-form', function () {
    return view('doctor.prescription');
})->name('prescription.form');

// Multiple roles can access
Route::middleware(['auth.session', 'role:doctor,secretary'])->get('/patient-search', function () {
    return view('patient-search');
})->name('patient.search');
```

## 📖 Common Patterns

### Checking Role in Blade Template
```blade
@if(session('user_role') === 'admin')
    <div>Admin content</div>
@endif
```

### Checking Role in Controller
```php
if (session('user_role') !== 'admin') {
    abort(403, 'Unauthorized');
}
```

### Storing Additional Session Data
```php
session([
    'user_id' => $user->id,
    'user_name' => $user->full_name,
    'user_role' => $user->role,
    'department' => $user->department, // Add custom data
]);
```

### Retrieving Session Data
```php
$userId = session('user_id');
$role = session('user_role');
$user = User::find(session('user_id'));
```

## 🛣️ Route Structure

### Creating Role-Specific Route Groups
```php
Route::middleware(['auth.session', 'role:doctor'])->group(function () {
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/patients', [DoctorDashboardController::class, 'patients'])->name('patients');
        Route::post('/patients/{id}/update', [DoctorDashboardController::class, 'updatePatient'])->name('update.patient');
    });
});
```

### Route Naming Convention
- Route name: `role.action` (e.g., `doctor.dashboard`, `admin.settings`)
- URL prefix: `/role/` (e.g., `/doctor/`, `/admin/`)
- This makes it easy to use in views: `route('doctor.dashboard')`

## 🔐 Security Best Practices

### ✅ DO:
- Always use middleware on protected routes
- Verify user has access to related resources
- Clear sensitive data on logout
- Log significant business actions
- Use CSRF tokens in forms
- Hash passwords with Laravel's Hash facade

### ❌ DON'T:
- Trust user roles from query parameters
- Store sensitive data in session without encrypting
- Skip authorization checks in controllers
- Log sensitive patient information
- Rely solely on client-side role checks
- Use plain-text passwords

## 🧪 Testing Examples

### Test Unauthorized Access
```php
// Test that non-admin cannot access admin route
$response = $this->actingAs($doctorUser)->get('/admin/settings');
$response->assertStatus(403);
```

### Test Authorized Access
```php
// Test that admin can access admin route
$response = $this->actingAs($adminUser)->get('/admin/settings');
$response->assertStatus(200);
```

### Test Login Redirect
```php
// Test that doctor is redirected to correct dashboard
$this->post('/login', ['username' => 'doctor', 'password' => 'password']);
$this->assertRedirected('/doctor/dashboard');
```

## 📊 Middleware Flow

```
Request
  ↓
CheckAuth Middleware [auth.session]
  ├─ Session exists with user_id?
  │  ├─ YES → Continue
  │  └─ NO → Redirect to /login
  ↓
CheckRole Middleware [role:doctor]
  ├─ User role matches required role?
  │  ├─ YES → Continue to Controller
  │  └─ NO → Return 403 Forbidden
  ↓
Route Handler (Controller)
  ├─ Execute business logic
  └─ Return Response
```

## 🔍 Debugging Tips

### Check Current Session
```php
// In controller
dd(session()->all());

// In Tinker
>>> session()->all()
```

### Check Routes
```bash
php artisan route:list | grep doctor
```

### Check Middleware
```bash
php artisan route:list -v
```

### Monitor Session Changes
```php
// Log session changes
\Log::info('Session data:', session()->all());
```

### Test Middleware Directly
```php
// Create a test request
$response = $this->get('/doctor/dashboard');
echo $response->status(); // Should be 200 if authenticated
```

## 🎨 View Patterns

### Display User Information
```blade
<div class="user-info">
    Welcome, {{ session('user_name') }}
    <span class="role-badge">{{ ucfirst(session('user_role')) }}</span>
</div>
```

### Conditional Navigation
```blade
<nav class="sidebar">
    @if(session('user_role') === 'admin')
        <a href="{{ route('admin.settings') }}">Settings</a>
    @elseif(session('user_role') === 'doctor')
        <a href="{{ route('doctor.patients') }}">Patients</a>
        <a href="{{ route('doctor.analytics') }}">Analytics</a>
    @elseif(session('user_role') === 'secretary')
        <a href="{{ route('secretary.patients') }}">Patients</a>
        <a href="{{ route('secretary.appointments') }}">Appointments</a>
    @endif
</nav>
```

### Data-Dependent Access
```blade
@if(Auth::check() && session('user_role') === 'doctor')
    <a href="{{ route('doctor.patient-profile', $patient->id) }}">View Full Profile</a>
@elseif(session('user_role') === 'secretary')
    <p>{{ $patient->first_name }} {{ $patient->last_name }}</p>
@endif
```

## 🚨 Common Errors & Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| 403 Forbidden | Role mismatch | Verify `session('user_role')` matches route role requirement |
| Redirect to Login | No session | Ensure login sets session with `user_id` |
| Route not found | Route not registered | Check `routes/web.php` for route definition |
| Middleware error | Not registered | Check `bootstrap/app.php` for middleware aliases |
| Patient access denied | Doctor-patient relationship missing | Verify appointment exists between doctor and patient |

## 📱 API Integration Notes

Current implementation uses **session-based authentication** for web routes.

For API routes, consider:
1. Using token-based authentication (Sanctum)
2. Adding role-based API middleware
3. Different rate limits per role
4. API-specific response formats

Example API role protection:
```php
Route::middleware(['auth:sanctum', 'role:doctor'])->group(function () {
    Route::get('/api/patients', [Api\PatientsController::class, 'index']);
});
```

## 🗝️ Key Files to Remember

- **Middleware**: `app/Http/Middleware/CheckAuth.php`, `CheckRole.php`
- **Controllers**: `*DashboardController.php` files
- **Routes**: `routes/web.php`
- **Bootstrap**: `bootstrap/app.php` (middleware registration)
- **Views**: `resources/views/{role}/*.blade.php`

## 💾 Database Queries

### Find users by role
```sql
SELECT * FROM users WHERE role = 'doctor';
```

### Count users per role
```sql
SELECT role, COUNT(*) FROM users GROUP BY role;
```

### Update user role
```sql
UPDATE users SET role = 'secretary' WHERE username = 'jane';
```

### Find doctor-patient relationships
```sql
SELECT DISTINCT u.id, u.full_name, p.id, p.first_name
FROM users u
JOIN appointments a ON u.id = a.doctor_id
JOIN patients p ON a.patient_id = p.id
WHERE u.role = 'doctor';
```

---

**Last Updated:** February 22, 2026
**For Questions:** See ROLE_BASED_ACCESS_IMPLEMENTATION.md
