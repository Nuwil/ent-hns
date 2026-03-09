# Role-Based Access Control Implementation Guide

## Overview
This document describes the role-based access control (RBAC) system implemented for the ENT Clinic Online application. The system segregates user interfaces based on user roles: Admin, Doctor, and Secretary.

## Implemented Components

### 1. Middleware

#### CheckAuth Middleware (`app/Http/Middleware/CheckAuth.php`)
- **Purpose**: Protects routes by verifying user authentication
- **Functionality**: Checks if `user_id` exists in session; redirects to login if not authenticated
- **Usage**: Applied globally to protected routes via middleware alias `auth.session`

#### CheckRole Middleware (`app/Http/Middleware/CheckRole.php`)
- **Purpose**: Implements fine-grained role-based access control
- **Functionality**: Verifies that the authenticated user's role matches the allowed roles
- **Usage**: Applied as `role:admin`, `role:doctor`, `role:secretary` in route groups
- **Parameter**: Accepts multiple roles as variadic parameters

### 2. Controllers

#### AdminDashboardController (`app/Http/Controllers/AdminDashboardController.php`)
**Routes:**
- `GET /admin/dashboard` - View admin dashboard with system overview
- `GET /admin/settings` - Access system settings and configuration

**Access Control:** Admin role only

**Views:**
- Admin Dashboard: Quick access to system overview, user management, activity logs
- Admin Settings: Configure clinic information, session timeout, database maintenance

#### DoctorDashboardController (`app/Http/Controllers/DoctorDashboardController.php`)
**Routes:**
- `GET /doctor/dashboard` - Doctor's main dashboard with statistics
- `GET /doctor/patients` - List of doctor's patients (with search)
- `GET /doctor/patients/{patient_id}/profile` - Detailed patient profile with full access
- `GET /doctor/appointments` - Doctor's appointment schedule
- `GET /doctor/analytics` - Clinical analytics and performance metrics

**Access Control:** Doctor role only, with additional patient access verification

**Key Features:**
- Full access to patient records (can view and edit)
- Appointment management specific to the doctor
- Clinical analytics and performance tracking
- Patient filtering and search capabilities

#### SecretaryDashboardController (`app/Http/Controllers/SecretaryDashboardController.php`)
**Routes:**
- `GET /secretary/dashboard` - Secretary's dashboard with statistics
- `GET /secretary/patients` - Complete patient list (view-only)
- `GET /secretary/patients/{patient_id}/profile` - Partial patient profile (view-only)
- `GET /secretary/appointments` - Appointment management interface

**Access Control:** Secretary role only

**Key Features:**
- View-only access to patient records (cannot edit)
- Appointment filtering and status management
- Limited patient profile information
- Patient search and list management

### 3. Authentication Controller Updates

#### WebAuthController (`app/Http/Controllers/WebAuthController.php`)
**Modifications:**
- Enhanced `login()` method to store user role in session
- Added `redirectToDashboard()` method for role-based redirect after login
- Updated `logout()` to clear all session variables including role

**Session Data Stored:**
- `user_id`: User's database ID
- `user_name`: User's display name
- `user_role`: User's role (admin, doctor, secretary, or staff)

### 4. Routes

#### Web Routes (`routes/web.php`)

**Public Routes:**
```
GET /           → Redirect to /login
GET /login      → Show login form
POST /login     → Process login
```

**Protected Routes (All authenticated users):**
```
POST /logout    → Process logout
GET /dashboard  → Generic dashboard
```

**Admin Routes (Role: admin):**
```
GET /admin/dashboard    → Admin dashboard
GET /admin/settings     → Admin settings
```

**Doctor Routes (Role: doctor):**
```
GET /doctor/dashboard                    → Doctor dashboard
GET /doctor/patients                     → Patient list
GET /doctor/patients/{id}/profile        → Patient profile
GET /doctor/appointments                 → Appointments
GET /doctor/analytics                    → Analytics
```

**Secretary Routes (Role: secretary):**
```
GET /secretary/dashboard                 → Secretary dashboard
GET /secretary/patients                  → Patient list (view-only)
GET /secretary/patients/{id}/profile     → Patient profile (view-only)
GET /secretary/appointments              → Appointments
```

### 5. Views Created

#### Admin Views
- `resources/views/admin/dashboard.blade.php` - Admin dashboard interface
- `resources/views/admin/settings.blade.php` - System settings interface

#### Doctor Views
- `resources/views/doctor/dashboard.blade.php` - Doctor dashboard with statistics
- `resources/views/doctor/patients.blade.php` - Searchable patient list
- `resources/views/doctor/patient-profile.blade.php` - Full patient profile with actions
- `resources/views/doctor/appointments.blade.php` - Appointment management
- `resources/views/doctor/analytics.blade.php` - Clinical analytics dashboard

#### Secretary Views
- `resources/views/secretary/dashboard.blade.php` - Secretary dashboard with statistics
- `resources/views/secretary/patients.blade.php` - Patient list with search
- `resources/views/secretary/patient-profile.blade.php` - Limited patient profile (view-only)
- `resources/views/secretary/appointments.blade.php` - Appointment management

## Access Control Summary

| Feature | Admin | Doctor | Secretary | Staff |
|---------|-------|--------|-----------|-------|
| Dashboard | ✓ | ✓ | ✓ | ✓ |
| Patient List | - | ✓ | ✓ | - |
| Patient Profile (Edit) | - | ✓ | ✗ | - |
| Patient Profile (View) | - | ✓ | ✓ | - |
| Appointments | - | ✓ | ✓ | - |
| Analytics | - | ✓ | - | - |
| Settings | ✓ | - | - | - |
| User Management | ✓ | - | - | - |

## Database User Roles

The system supports the following roles (via `users` table `role` enum):
- `admin` - Full system access
- `doctor` - Clinical access and patient management
- `secretary` - Administrative support and scheduling
- `staff` - General staff (no specific dashboard)

## Configuration

### Middleware Aliases (bootstrap/app.php)
```php
$middleware->alias([
    'auth.session' => \App\Http\Middleware\CheckAuth::class,
    'role' => \App\Http\Middleware\CheckRole::class,
]);
```

### Usage in Routes
```php
Route::middleware(['auth.session', 'role:doctor'])->group(function () {
    // Doctor-only routes
});
```

## Testing Guide

### Test Cases

1. **Unauthenticated Access**
   - Visit `/admin/dashboard` without login → Should redirect to `/login`
   - Visit `/doctor/patients` without login → Should redirect to `/login`
   - Visit `/secretary/appointments` without login → Should redirect to `/login`

2. **Role-Based Access Control**
   - Log in as Admin → Should access `/admin/dashboard` and `/admin/settings`
   - Admin accessing `/doctor/dashboard` → Should get 403 Unauthorized
   - Log in as Doctor → Should access all doctor routes
   - Doctor accessing `/admin/settings` → Should get 403 Unauthorized
   - Log in as Secretary → Should access all secretary routes
   - Secretary accessing `/doctor/analytics` → Should get 403 Unauthorized

3. **Patient Access Control (Doctor)**
   - Doctor can view only their own patients
   - Doctor accessing patient profile they don't have appointments with → Should get 403
   - Doctor can view and edit patient information

4. **Patient Access Control (Secretary)**
   - Secretary can view all patients
   - Secretary cannot edit patient information (view-only)
   - Secretary can view appointment information

5. **Login and Redirect**
   - Admin logs in → Should redirect to `/admin/dashboard`
   - Doctor logs in → Should redirect to `/doctor/dashboard`
   - Secretary logs in → Should redirect to `/secretary/dashboard`

6. **Session Management**
   - Session should contain `user_id`, `user_name`, and `user_role`
   - Session timeout should work as configured
   - Logout should clear all session data

## Future Enhancements

1. **Permission-Based Access**: Implement fine-grained permissions beyond roles
2. **Audit Logging**: Enhanced logging of user actions by role
3. **API Endpoints**: Role-based API access control
4. **Department-Based Access**: Multi-department support with departmental permissions
5. **Custom Role Creation**: Admin interface to create custom roles
6. **Activity Monitoring**: Track and display user activities by role
7. **Two-Factor Authentication**: Enhanced security with 2FA
8. **Session Management UI**: Admin panel to manage active sessions

## Security Considerations

1. **SQL Injection**: All database queries use parameterized statements
2. **CSRF Protection**: CSRF tokens are implemented in forms
3. **Session Security**: Session data is stored server-side
4. **Password Security**: Passwords are hashed using Laravel's Hash facade
5. **Authorization**: All routes verify both authentication and authorization
6. **Redirect Safety**: Safe redirects after login prevent open redirect vulnerabilities

## Troubleshooting

### Users Getting 403 Errors
- Verify the user's role in the database (`users.role` field)
- Check that middleware is properly configured in `bootstrap/app.php`
- Ensure route middleware syntax is correct: `role:rolename`

### Users Not Redirected to Correct Dashboard
- Check `WebAuthController::redirectToDashboard()` method
- Verify route names match the method's switch statement
- Check session data is being stored: `session('user_role')`

### Middleware Not Working
- Restart Laravel application
- Clear route cache: `php artisan route:cache`
- Clear config cache: `php artisan config:cache`

## File Structure

```
app/Http/
├── Controllers/
│   ├── WebAuthController.php
│   ├── AdminDashboardController.php
│   ├── DoctorDashboardController.php
│   └── SecretaryDashboardController.php
└── Middleware/
    ├── CheckAuth.php
    └── CheckRole.php

resources/views/
├── admin/
│   ├── dashboard.blade.php
│   └── settings.blade.php
├── doctor/
│   ├── dashboard.blade.php
│   ├── patients.blade.php
│   ├── patient-profile.blade.php
│   ├── appointments.blade.php
│   └── analytics.blade.php
└── secretary/
    ├── dashboard.blade.php
    ├── patients.blade.php
    ├── patient-profile.blade.php
    └── appointments.blade.php

routes/
└── web.php

bootstrap/
└── app.php
```

## License

This implementation is part of the ENT Clinic Online system. All code follows the project's licensing guidelines.
