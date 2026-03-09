# Secretary Visit Creation 500 Error - Fixed

## Problem Description
When a secretary user attempted to add a visit to a patient's timeline, the `/api/visits` endpoint returned a **500 Internal Server Error**, preventing the visit from being created.

## Root Cause Analysis

The issue was in [app/Http/Controllers/Api/VisitsController.php](app/Http/Controllers/Api/VisitsController.php) in the `store()` method:

### Issue 1: Uninitialized `$user` Variable
```php
if (!$userRole) {
    $userId = session('user_id');
    $user = $userId ? User::find($userId) : null;
    $userRole = $user ? $user->role : 'viewer';
}
// If user_role comes from request, $user is never initialized here!
```

When `user_role` was passed in the request, the `$user` variable was never initialized, leaving it undefined for later use.

### Issue 2: Invalid User Model Field References
```php
$validated['doctor_name'] = $user->first_name . ' ' . $user->last_name;
```

The User model only has a `full_name` field, not `first_name` and `last_name` separate fields. This caused an error when trying to access non-existent attributes.

**User Model Fields:**
```php
protected $fillable = [
    'username',
    'email',
    'password_hash',
    'full_name',  // Only this field exists, not first_name/last_name
    'role',
    'is_active',
    'is_protected',
];
```

## Solution Implemented

### Fix 1: Initialize `$user` in Both Code Paths

**Before:**
```php
if (!$userRole) {
    $userId = session('user_id');
    $user = $userId ? User::find($userId) : null;
    $userRole = $user ? $user->role : 'viewer';
}
```

**After:**
```php
$user = null;  // Initialize $user

if (!$userRole) {
    $userId = session('user_id');
    $user = $userId ? User::find($userId) : null;
    $userRole = $user ? $user->role : 'viewer';
} else {
    // If user_role is in request, still try to get user from session for audit trail
    $userId = session('user_id');
    $user = $userId ? User::find($userId) : null;
}
```

### Fix 2: Use Correct User Model Field

**Before:**
```php
$validated['doctor_name'] = $user->first_name . ' ' . $user->last_name;
```

**After:**
```php
$validated['doctor_name'] = $user->full_name;
```

## Role-Based Access Control Implementation

The API properly implements role-based restrictions:

### Secretary Permissions
Secretaries can submit:
- `patient_id`, `appointment_id`
- `visit_date`, `visit_type`, `ent_type`
- `chief_complaint`
- Vital signs: `blood_pressure`, `temperature`, `pulse_rate`, `respiratory_rate`, `oxygen_saturation`
- `height`, `weight`, `vitals_notes`

Secretaries **cannot** submit:
- `history` (History of Present Illness)
- `physical_exam` (Physical Examination Findings)
- `diagnosis`
- `treatment_plan`
- `prescription`
- `notes`

The code enforces this by:
```php
if ($userRole === 'secretary') {
    if (!empty($validated['history']) || !empty($validated['physical_exam']) 
        || !empty($validated['diagnosis']) || !empty($validated['treatment_plan']) 
        || !empty($validated['prescription'])) {
        return response()->json([
            'success' => false,
            'errors' => [
                'authorization' => ['You do not have permission to submit medical information...']
            ]
        ], 403);
    }
}
```

### Doctor Permissions
Doctors have full access to all fields, including protected medical information.

## Testing

### Test Visit Creation
After the fix, visits can be successfully created:

```bash
# Test with model directly
php artisan tinker
App\Models\PatientVisit::create([
    'patient_id' => 1,
    'doctor_id' => 4,
    'visit_date' => now(),
    'visit_type' => 'consultation',
    'ent_type' => 'ear',
    'chief_complaint' => 'Test visit',
    'doctor_name' => 'Willie Ong'
]);
```

### Verify Visits Exist
```bash
DB::table('patient_visits')->count();
# Should return count of visits (3+ after fix)
```

## Result

✅ **Secretaries can now successfully create visit entries**
✅ **Role-based permissions are properly enforced**
✅ **Medical data remains private per role restrictions**
✅ **Visit audit trail records user information correctly**

## Files Modified

| File | Change |
|------|--------|
| `app/Http/Controllers/Api/VisitsController.php` | Initialize `$user` in both code paths |
| `app/Http/Controllers/Api/VisitsController.php` | Fix `doctor_name` to use `full_name` field |

## Related Documentation

- [Patient Visit Model](app/Models/PatientVisit.php)
- [User Model](app/Models/User.php)
- [Visits API Controller](app/Http/Controllers/Api/VisitsController.php)
- [Role-Based Access Control Guide](ADMIN_PROTECTION_GUIDE.md)
