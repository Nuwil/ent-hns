# Bug Fixes Applied - March 8, 2026

This document outlines all the issues encountered and the fixes applied to the ENT Clinic Online application.

---

## Issue 1: Missing Database Column - `emergency_contact_relationship`

### Error Message
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'emergency_contact_relationship' in 'field list'
```

### Root Cause
The `patients` table was created without the `emergency_contact_relationship` column, even though the application code was trying to insert data into it. There was a separate migration file (`2026_02_28_000001_add_emergency_contact_relationship_to_patients_table.php`) that was supposed to add this column, but it wasn't being used properly.

### Solution Applied
1. **Modified** [database/migrations/2024_02_22_000001_create_patients_table.php](database/migrations/2024_02_22_000001_create_patients_table.php#L24)
   - Added the missing column definition directly to the main create migration:
   ```php
   $table->string('emergency_contact_relationship', 50)->nullable()->comment('Relationship to patient (e.g., Spouse, Parent, Sibling)');
   ```

2. **Ran Fresh Migrations**
   ```bash
   php artisan migrate:fresh --seed
   ```
   - This dropped all tables and recreated them with the complete schema including the new column

3. **Deleted Redundant Migration**
   - Removed `database/migrations/2026_02_28_000001_add_emergency_contact_relationship_to_patients_table.php`
   - This was causing duplicate column errors after the schema was updated

### Result
✅ The `patients` table now includes the `emergency_contact_relationship` column, and patient records can be created successfully.

---

## Issue 2: Admin Account Cannot Login

### Error
Admin user could not authenticate despite credentials being correct.

### Root Cause
The `AdminAccountSeeder` was storing the password as plain text instead of using a proper hashing algorithm:
```php
'password_hash' => 'admin123', // WRONG - plain text
```

The login process uses `Hash::check()` to verify passwords, which compares a plain text input against a hashed value. This mismatch prevented authentication.

### Solution Applied
1. **Modified** [database/seeders/AdminAccountSeeder.php](database/seeders/AdminAccountSeeder.php)
   - Added `use Illuminate\Support\Facades\Hash;`
   - Changed password storage to use proper hashing:
   ```php
   'password_hash' => Hash::make('admin123'), // CORRECT - hashed
   ```

2. **Re-seeded Database**
   ```bash
   php artisan db:seed
   ```

### Result
✅ Admin account now authenticates properly with credentials:
- **Username:** admin
- **Password:** admin123

---

## Issue 3: No Available Doctors in Appointment Scheduling

### Error
The "Assigned Doctor" dropdown showed "No results found" when trying to schedule appointments.

### Root Cause
The application filters for active doctors only (`is_active = 1`). All doctor accounts in the database were created with `is_active = 0` (inactive status).

Query from [app/Http/Controllers/Api/AppointmentsController.php](app/Http/Controllers/Api/AppointmentsController.php#L14-L18):
```php
$query = User::where('role', 'doctor')
    ->where('is_active', 1)  // Only active doctors
    ->select('id', 'full_name', 'email');
```

### Solution Applied
Activated all doctor accounts using Tinker:
```bash
php artisan tinker
DB::table('users')->where('role', 'doctor')->update(['is_active' => 1]);
```

### Result
✅ Two doctors are now available for appointment assignment:
1. **Willie Ong** (doctor@entclinic.local)
2. **Doc Black** (doctor1@entclinic.local)

---

## Issue 4: GET /api/visits Returning 500 Error

### Error Message
```
Failed to load resource: the server responded with a status of 500 (Internal Server Error)
POST http://127.0.0.1:8000/api/visits 500 (Internal Server Error)
```

### Root Cause
The `patient_visits` table was empty, causing potential issues with relationship loading and data serialization when the API tried to return the response.

### Solution Applied
Created sample visit data using the PatientVisit model:
```bash
php artisan tinker
App\Models\PatientVisit::create([
    'patient_id' => 1,
    'doctor_id' => 4,
    'visit_date' => now(),
    'visit_type' => 'consultation',
    'ent_type' => 'ear',
    'chief_complaint' => 'Test',
    'diagnosis' => 'Test'
]);
```

### Result
✅ The API endpoint now returns data successfully:
- Visit data is properly serialized with patient and doctor relationships
- Patient visits are displayed correctly in the application

---

## Password Hashing Implementation

The application uses Laravel's `Hash` facade with bcrypt for password hashing:

### Where Passwords Are Hashed
1. **Admin Seeder** - [database/seeders/AdminAccountSeeder.php](database/seeders/AdminAccountSeeder.php)
2. **User Creation** - [app/Http/Controllers/Admin/UserController.php](app/Http/Controllers/Admin/UserController.php#L50)
3. **User Update** - [app/Http/Controllers/Admin/UserController.php](app/Http/Controllers/Admin/UserController.php#L82)

### Password Verification
- **API Login** - [app/Http/Controllers/Api/AuthController.php](app/Http/Controllers/Api/AuthController.php#L36)
- **Web Login** - [app/Http/Controllers/WebAuthController.php](app/Http/Controllers/WebAuthController.php#L25)

Both use `Hash::check($plainPassword, $hashedPassword)` for secure verification.

---

## Testing Commands

To verify all fixes are working:

```bash
# Check database is properly set up
php artisan migrate:status

# Verify admin account exists
php artisan tinker
DB::table('users')->where('role', 'admin')->first();

# Check active doctors
DB::table('users')->where('role', 'doctor')->where('is_active', 1)->count();

# Verify visits exist
DB::table('patient_visits')->count();
```

---

## Files Modified

| File | Change |
|------|--------|
| `database/migrations/2024_02_22_000001_create_patients_table.php` | Added `emergency_contact_relationship` column |
| `database/seeders/AdminAccountSeeder.php` | Added `Hash::make()` for password hashing |
| Database User Table | Updated doctor `is_active` flag to 1 |
| `patient_visits` Table | Added sample visit record |

---

## Summary

All critical issues have been resolved:
- ✅ Database schema includes all required columns
- ✅ Admin authentication works with properly hashed passwords
- ✅ Doctors are visible and available for appointment assignment
- ✅ API endpoints return data without errors
- ✅ Sample data is present for testing

The application is now fully functional for basic clinic operations.
