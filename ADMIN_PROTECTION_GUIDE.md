# Admin Account Protection & Deployment Guide

## How It Works

When you deploy the application and run `php artisan migrate --seed`, these things happen automatically:

1. **Database tables are created** from all migrations
2. **Default medicines and appointment types are seeded** into the database
3. **Admin account is automatically created** with:
   - Username: `admin`
   - Password: `admin123`
   - Email: `admin@entclinic.com`
   - Status: **Protected** (cannot be deleted)

## Protection Mechanism

The admin account protection is built into the application code in three places:

### 1. User Model (`app/Models/User.php`)
When an admin account is created, the model automatically:
- Sets `is_protected = true` for the **first admin only**
- This happens regardless of whether it's created via seeder or UI

### 2. UserController (`app/Http/Controllers/Admin/UserController.php`)

**On Creation:**
- First admin to be created is automatically marked as protected

**On Update:**
- Prevents demoting the last remaining admin to a different role
- Allows admins to edit themselves (update username, email, password)

**On Deletion:**
- **Cannot delete any user with `is_protected = true`**
- **Cannot delete the last remaining admin account**

These checks prevent accidental system lockout.

### 3. User Privileges
- Protected admin account can **only be edited by itself** (not by other admins)
- This ensures the initial admin maintains control
- Other admins can be created, but the first protected admin remains special

## Deployment Process

```bash
# Step 1: Clone/prepare the application
git clone [repo-url]
cd laravel-online-ent-clinic-app

# Step 2: Install dependencies
composer install

# Step 3: Configure .env (database credentials, etc.)
cp .env.example .env
# Edit .env with your settings

# Step 4: Run migrations + seeding (THAT'S IT!)
php artisan migrate --seed
```

After this, the system is **fully ready** with:
- ✓ Database schema
- ✓ Default medicines
- ✓ Default appointment types
- ✓ Protected admin account

## First Login

```
URL: http://your-domain.com
Username: admin
Password: admin123
```

## Creating Other Accounts

After logging in as admin:

1. Go to **Settings → User Management**
2. Click **Add New User**
3. Select role: **Secretary** or **Doctor**
4. Fill in credentials and click **Create**

These accounts:
- ✓ Can be edited by admin
- ✓ Can be edited by themselves
- ✓ Can be deleted by admin
- ✓ Are NOT protected

## Key Points

| Feature | Protected Admin | Other Users |
|---------|-----------------|-------------|
| Can be created | Yes (during deployment) | Yes (by admin) |
| Can be edited by admin | Yes | Yes |
| Can be edited by themselves | Yes | Yes |
| Can be deleted | **NO** | Yes |
| Can be deactivated | NO (only role can change) | Yes |
| Role can be changed away from "admin" | NO (if last admin) | N/A |
| Can change password | Yes | Yes |

## Important Notes

1. **Never** lose access to the admin account - it's your system gateway
2. **Never** delete the last admin - the system prevents this
3. Change the default password immediately in production
4. The protected admin account ensures system integrity even if other admins are compromised

## If You Need to Reset Admin Password

(This is rarely needed if you have system access)

```bash
php artisan admin:reset-password [new-password]
```

This command creates a fresh admin account if needed, but normally you won't need it since the admin account is already seeded with known credentials.
