# Installation and Deployment Guide

## Quick Start

### 1. Review Implementation Files
The following new files have been created:

**Middleware:**
- `app/Http/Middleware/CheckAuth.php` - Authentication middleware
- `app/Http/Middleware/CheckRole.php` - Role-based authorization middleware

**Controllers:**
- `app/Http/Controllers/AdminDashboardController.php` - Admin dashboard controller
- `app/Http/Controllers/DoctorDashboardController.php` - Doctor dashboard controller
- `app/Http/Controllers/SecretaryDashboardController.php` - Secretary dashboard controller

**Views:**
- Admin: `resources/views/admin/dashboard.blade.php`, `resources/views/admin/settings.blade.php`
- Doctor: `resources/views/doctor/dashboard.blade.php`, `resources/views/doctor/patients.blade.php`, `resources/views/doctor/patient-profile.blade.php`, `resources/views/doctor/appointments.blade.php`, `resources/views/doctor/analytics.blade.php`
- Secretary: `resources/views/secretary/dashboard.blade.php`, `resources/views/secretary/patients.blade.php`, `resources/views/secretary/patient-profile.blade.php`, `resources/views/secretary/appointments.blade.php`

**Configuration:**
- Updated: `bootstrap/app.php` - Added middleware aliases
- Updated: `routes/web.php` - Added role-based routes
- Updated: `app/Http/Controllers/WebAuthController.php` - Added role-based redirects

### 2. Deployment Steps

1. **Clear Laravel Caches:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Verify Database**
   - Ensure your `users` table has the `role` column with enum values
   - Current roles supported: `admin`, `doctor`, `secretary`, `staff`

3. **Test User Accounts**
   Create test accounts with different roles:
   ```sql
   -- Admin account
   INSERT INTO users (username, email, password_hash, full_name, role, is_active, created_at, updated_at)
   VALUES ('admin', 'admin@clinic.local', '$2y$10$...', 'Administrator', 'admin', true, NOW(), NOW());
   
   -- Doctor account
   INSERT INTO users (username, email, password_hash, full_name, role, is_active, created_at, updated_at)
   VALUES ('doctor1', 'doctor@clinic.local', '$2y$10$...', 'Dr. John Smith', 'doctor', true, NOW(), NOW());
   
   -- Secretary account
   INSERT INTO users (username, email, password_hash, full_name, role, is_active, created_at, updated_at)
   VALUES ('secretary1', 'secretary@clinic.local', '$2y$10$...', 'Jane Secretary', 'secretary', true, NOW(), NOW());
   ```

4. **Start the Application**
   ```bash
   php artisan serve
   ```

5. **Verify Access**
   - Visit `http://localhost:8000/login`
   - Log in with each test account
   - Verify redirect to correct dashboard
   - Check each role can only access its designated routes

### 3. Role-Based Workflows

#### Admin Workflow
1. Login as admin → Admin Dashboard
2. Navigate to Settings for system configuration
3. Access User Management for creating/managing accounts
4. Monitor system activity and logs

#### Doctor Workflow
1. Login as doctor → Doctor Dashboard
2. View personal statistics (total appointments, pending)
3. Navigate to "My Patients" to see assigned patients
4. Click on patient to view full profile and appointment history
5. Access Analytics for performance metrics
6. Manage appointments schedule

#### Secretary Workflow
1. Login as secretary → Secretary Dashboard
2. View overall statistics (total patients, appointments)
3. Navigate to "Patient List" to search and view patients
4. Click on patient to view limited profile information
5. Manage appointments filtering by status
6. Schedule and organize appointments (future functionality)

### 4. Key Features Implemented

✅ **Admin Role:**
- Dedicated admin dashboard
- System settings configuration interface
- User and system management capabilities

✅ **Doctor Role:**
- Personal dashboard with appointment statistics
- Full patient list with search functionality
- Complete patient profiles with full access to records
- Appointment management
- Clinical analytics dashboard

✅ **Secretary Role:**
- Personal dashboard with statistics
- Complete patient list with search (view-only)
- Limited patient profile viewing (no edit access)
- Appointment management and filtering
- Supporting staff functions

✅ **Security Features:**
- Middleware-based authentication checking
- Role-based access control on all protected routes
- Session-based authentication
- Automatic redirect on unauthorized access
- Doctor-patient relationship verification
- Separate doctor and secretary access levels

### 5. Troubleshooting

#### Problem: "Unauthorized - Insufficient permissions"
- Solution: Check user role in database
- Verify middleware is enabled in bootstrap/app.php
- Clear route cache: `php artisan route:cache`

#### Problem: Users not redirected to correct dashboard after login
- Solution: Verify route names in routes/web.php match controller redirects
- Check session data is being stored correctly
- Restart Laravel development server

#### Problem: Patient profile not accessible
- Solution: For doctors, verify appointment exists with patient
- Check patient profile route parameters are correct
- Verify database relationships (appointments, patients)

### 6. Testing Checklist

- [ ] Admin can access `/admin/dashboard`
- [ ] Admin can access `/admin/settings`
- [ ] Admin cannot access doctor routes (403)
- [ ] Doctor can access `/doctor/dashboard`
- [ ] Doctor can access patient list
- [ ] Doctor can access their patients' full profiles
- [ ] Doctor cannot access secretary routes (403)
- [ ] Secretary can access `/secretary/dashboard`
- [ ] Secretary can view all patients (read-only)
- [ ] Secretary cannot access doctor routes (403)
- [ ] Unauthenticated users redirect to login
- [ ] Logout clears all session data
- [ ] Login redirects to correct role dashboard

### 7. Performance Considerations

- Session-based auth is lightweight
- No complex permission calculations
- Direct role matching for access control
- Database queries optimized with relationships
- Views include lazy-loaded relationships where needed

### 8. Future Enhancements

- Implement permissions beyond roles
- Add API route protection with roles
- Create admin panel for role management
- Add user activity logging
- Implement two-factor authentication
- Add department-based access control

### 9. Documentation

See `ROLE_BASED_ACCESS_IMPLEMENTATION.md` for comprehensive documentation including:
- Complete API reference
- Access control matrix
- Configuration details
- Security considerations
- Troubleshooting guide

### 10. Support

For issues or questions:
1. Refer to `ROLE_BASED_ACCESS_IMPLEMENTATION.md`
2. Check error logs in `storage/logs/`
3. Verify middleware configuration in `bootstrap/app.php`
4. Review route definitions in `routes/web.php`
