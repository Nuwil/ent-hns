# Deployment Checklist - Role-Based Access Control

## Pre-Deployment Verification

### ✅ Code Files Verification
- [x] `app/Http/Middleware/CheckAuth.php` - Authentication middleware
- [x] `app/Http/Middleware/CheckRole.php` - Role-based authorization middleware
- [x] `app/Http/Controllers/AdminDashboardController.php` - Admin controller
- [x] `app/Http/Controllers/DoctorDashboardController.php` - Doctor controller
- [x] `app/Http/Controllers/SecretaryDashboardController.php` - Secretary controller
- [x] `app/Http/Controllers/WebAuthController.php` - Updated with role handling
- [x] `bootstrap/app.php` - Updated with middleware aliases
- [x] `routes/web.php` - Updated with role-based routes

### ✅ View Files Verification
**Admin Views (2 files)**
- [x] `resources/views/admin/dashboard.blade.php`
- [x] `resources/views/admin/settings.blade.php`

**Doctor Views (5 files)**
- [x] `resources/views/doctor/dashboard.blade.php`
- [x] `resources/views/doctor/patients.blade.php`
- [x] `resources/views/doctor/patient-profile.blade.php`
- [x] `resources/views/doctor/appointments.blade.php`
- [x] `resources/views/doctor/analytics.blade.php`

**Secretary Views (4 files)**
- [x] `resources/views/secretary/dashboard.blade.php`
- [x] `resources/views/secretary/patients.blade.php`
- [x] `resources/views/secretary/patient-profile.blade.php`
- [x] `resources/views/secretary/appointments.blade.php`

## Deployment Steps

### Step 1: Backup Current System
```bash
# Create backup of current state
cp -r . ../ent-clinic-online-backup-$(date +%Y%m%d)
```

### Step 2: Clear Laravel Caches
```bash
cd laravel-online-ent-clinic-app
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 3: Verify Database Structure
```bash
# Ensure users table has role column
php artisan migrate

# Check existing users and their roles
php artisan tinker
>>> User::select('id', 'username', 'role')->get();
```

### Step 4: Create Test User Accounts
```bash
# Generate hash for password: "password123"
php artisan tinker
>>> Hash::make('password123')
# Copy the hash output

>>> use App\Models\User;
>>> User::create([
...   'username' => 'admin_test',
...   'email' => 'admin@clinic.test',
...   'password_hash' => '$2y$10/...[paste hash]...',
...   'full_name' => 'Test Admin',
...   'role' => 'admin',
...   'is_active' => true
... ]);

# Repeat for doctor and secretary roles
```

### Step 5: Test Application Locally
```bash
php artisan serve
```

1. Navigate to `http://localhost:8000/login`
2. Test each user account
3. Verify correct dashboard redirect
4. Test unauthorized access attempts

### Step 6: Deploy to Production

#### Option A: Manual Deployment
```bash
# Upload files to server
scp -r laravel-online-ent-clinic-app/* user@server:/var/www/clinic/

# SSH into server
ssh user@server

# Navigate to application
cd /var/www/clinic

# Install dependencies (if needed)
composer install --no-dev

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Set permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 storage
sudo chmod -R 755 bootstrap/cache

# Restart PHP
sudo systemctl restart php-fpm

# Restart web server
sudo systemctl restart nginx  # or apache2
```

#### Option B: Using Git
```bash
# On server
cd /var/www/clinic
git pull origin main

# Clear caches and install
php artisan cache:clear
php artisan config:clear
php artisan route:clear
composer install --no-dev

# Set permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 storage bootstrap/cache

# Restart services
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

## Post-Deployment Testing

### ✅ Critical Tests

1. **Login Functionality**
   - [ ] Login page loads without errors
   - [ ] Valid credentials accepted
   - [ ] Invalid credentials rejected with error message
   - [ ] Session timeout works correctly

2. **Admin User Tests**
   - [ ] Admin logs in successfully
   - [ ] Redirected to `/admin/dashboard`
   - [ ] Can access `/admin/settings`
   - [ ] Receives 403 accessing `/doctor/dashboard`
   - [ ] Receives 403 accessing `/secretary/dashboard`

3. **Doctor User Tests**
   - [ ] Doctor logs in successfully
   - [ ] Redirected to `/doctor/dashboard`
   - [ ] Can view patient list `/doctor/patients`
   - [ ] Can view patient profile `/doctor/patients/{id}/profile`
   - [ ] Can view appointments `/doctor/appointments`
   - [ ] Can view analytics `/doctor/analytics`
   - [ ] Receives 403 accessing `/admin/settings`
   - [ ] Receives 403 accessing `/secretary/appointments`

4. **Secretary User Tests**
   - [ ] Secretary logs in successfully
   - [ ] Redirected to `/secretary/dashboard`
   - [ ] Can view patient list `/secretary/patients`
   - [ ] Can view patient profile `/secretary/patients/{id}/profile` (read-only)
   - [ ] Can view appointments `/secretary/appointments`
   - [ ] Receives 403 accessing `/doctor/analytics`
   - [ ] Receives 403 accessing `/admin/settings`

5. **Logout Functionality**
   - [ ] Logout clears session
   - [ ] Redirects to login page
   - [ ] Cannot access protected routes after logout

6. **Session Security**
   - [ ] Direct URL access without session redirects to login
   - [ ] Session timeout works as configured
   - [ ] Session data is properly encrypted

### 🔍 Integration Tests

```php
// routes/api.php - Add temporary test routes for verification
Route::get('/test/auth-status', function () {
    return response()->json([
        'authenticated' => session('user_id') !== null,
        'user_id' => session('user_id'),
        'user_role' => session('user_role'),
    ]);
});

Route::get('/test/role-check/{role}', function ($role) {
    if (session('user_role') === $role) {
        return response()->json(['status' => 'authorized']);
    }
    return response()->json(['status' => 'unauthorized'], 403);
});
```

## Monitoring & Maintenance

### Daily Checks
- [ ] Check application error logs: `tail -f storage/logs/laravel.log`
- [ ] Verify database connectivity
- [ ] Monitor server resources (CPU, memory, disk)
- [ ] Check for failed login attempts

### Weekly Checks
- [ ] Review user access logs
- [ ] Verify backup systems are working
- [ ] Check for security updates
- [ ] Test user account creation/modification

### Monthly Checks
- [ ] Full backup verification
- [ ] Review session management
- [ ] Analyze access patterns by role
- [ ] Security audit of new features

## Rollback Procedure (If Issues Occur)

### Quick Rollback (Files Only)
```bash
cd /var/www/clinic
git revert HEAD
php artisan cache:clear
php artisan route:clear
```

### Full Rollback (To Previous Backup)
```bash
cd /var/www
cp -r ent-clinic-online-backup-YYYYMMDD/* clinic/
cd clinic
php artisan cache:clear
php artisan route:clear
sudo systemctl restart nginx
```

## Performance Optimization (Post-Deployment)

### Cache Configuration
```bash
# Optimize composer autoloader
composer install --optimize-autoloader --no-dev

# Cache routes
php artisan route:cache

# Cache config
php artisan config:cache

# Cache views (optional)
php artisan view:cache
```

### Database Optimization
```sql
-- Add indexes for faster queries
ALTER TABLE users ADD INDEX idx_role (role);
ALTER TABLE appointments ADD INDEX idx_doctor_id (doctor_id);
ALTER TABLE appointments ADD INDEX idx_patient_id (patient_id);
```

## Common Issues & Resolutions

### Issue: 500 Error on Login
**Resolution:**
```bash
php artisan log:tail
# Check what's failing
# Verify WebAuthController exists
# Check routes/web.php syntax
```

### Issue: 403 Forbidden on All Routes
**Resolution:**
```bash
# Verify middleware is registered
php artisan route:list | grep auth.session
# Check bootstrap/app.php for middleware aliases
# Verify session is being set during login
php artisan tinker
>>> session()->all()
```

### Issue: Users Not Redirected to Correct Dashboard
**Resolution:**
```bash
# Check WebAuthController redirectToDashboard method
# Verify route names: admin.dashboard, doctor.dashboard, secretary.dashboard
# Check session data contains user_role
php artisan tinker
>>> session('user_role')
```

### Issue: Patient Profile Showing 403 for Doctor
**Resolution:**
```bash
# Verify doctor has appointment with patient
# Check DoctorDashboardController patientProfile method
# Verify doctor_id is correctly set in appointments table
```

## Communication Templates

### To Project Manager
```
[DEPLOYMENT COMPLETE]

Role-Based Access Control System has been successfully deployed.

✅ Features Implemented:
- Admin Dashboard with Settings Access
- Doctor Dashboard with Patient & Analytics Access  
- Secretary Dashboard with Patient & Appointment Access
- Middleware-based Authentication & Authorization
- Role-Based Routing

✅ Testing Status: All critical tests passed
- Login/Logout functionality working
- Role-based access control verified
- Session management operational

📊 User Access:
- Admin: System configuration & management
- Doctor: Patient care & clinical analytics
- Secretary: Administrative support & scheduling

Next Steps:
1. Grant user accounts to team members
2. Provide training on role-specific workflows
3. Monitor system for 24 hours
4. Collect user feedback for improvements
```

### To Development Team
```
[DEPLOYMENT READY]

Role-Based Access Control files have been deployed.

📁 New Components:
- 2 Middleware files (CheckAuth, CheckRole)
- 3 Dashboard controllers
- 11 Role-specific views
- Updated WebAuthController with role handling
- Updated routing configuration

🔐 Security:
- All protected routes use middleware
- Role verification at controller level
- Patient-doctor relationship verification
- Session-based authentication with timeout

📚 Documentation:
- INSTALLATION_GUIDE.md - Deployment steps
- ROLE_BASED_ACCESS_IMPLEMENTATION.md - Technical reference
- QUICK_REFERENCE.md - Common patterns
- IMPLEMENTATION_SUMMARY.md - Overview

⚠️ Support:
Contact DevOps team for:
- Cache clearing issues
- Database migration problems
- Server permission problems
- Performance monitoring setup
```

## Success Criteria

✅ **Deployment is successful when:**
1. All role-specific routes are accessible
2. Unauthorized access returns 403 Forbidden
3. Admin/Doctor/Secretary dashboards display correctly
4. Login redirects users to appropriate dashboard
5. Session management works without errors
6. All views render without PHP errors
7. Database queries execute efficiently
8. No errors in application logs

---

**Deployment Date:** [To be filled]
**Deployed By:** [To be filled]
**Environment:** [Development/Staging/Production]
**Status:** [Pending/In Progress/Complete]

For questions or issues, refer to documentation files or contact the development team.
