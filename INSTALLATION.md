# ENT Clinic Online - Laravel Migration

## Project Overview

This is a complete Laravel 11 migration of the ENT Clinic Online system. The application features a full RESTful API for managing patients, appointments, medical records, and clinic operations.

## Project Structure

```
laravel-online-ent-clinic-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/        # API Controllers
│   │   ├── Middleware/             # Custom middleware (CORS, session timeout)
│   ├── Models/                      # Eloquent Models
├── bootstrap/                       # Application bootstrap
├── config/                          # Configuration files
├── database/
│   ├── migrations/                  # Database migrations
│   ├── seeders/                     # Database seeders
├── routes/
│   ├── api.php                      # API routes
│   ├── web.php                      # Web routes
├── public/                          # Public-facing files (entry point)
└── storage/                         # Logs, cache, etc.
```

## Database Schema

The application includes the following tables:

- **users** - System users (admin, doctor, staff, secretary)
- **patients** - Patient information and vitals
- **appointments** - Appointment scheduling
- **patient_visits** - Medical visit records and diagnostics
- **recordings** - Medical recordings (audio, video, endoscopy, imaging)
- **medicines** - Pharmacy medicines database
- **prescription_items** - Prescription details linked to visits
- **waitlist** - Patient waitlist management
- **appointment_types** - Appointment type definitions
- **analytics** - Analytics and metrics
- **activity_logs** - Audit trail of user actions

## Installation & Setup

### Prerequisites

- PHP 8.3+ with MySQL extension (pdo_mysql)
- MySQL/MariaDB 5.7+ or 8.0+
- Composer

### Step 1: Database Setup

Ensure MySQL is running and the `ent_clinic` database exists:

```bash
mysql -u root -p
> CREATE DATABASE IF NOT EXISTS ent_clinic;
> EXIT;
```

### Step 2: Environment Configuration

Configure the `.env` file with your database credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ent_clinic
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Install Dependencies

```bash
composer install
```

### Step 4: Run Migrations & Seeding

```bash
php artisan migrate --seed
```

This will:
- Create all database tables
- Insert default medicines
- Insert default appointment types  
- Create the protected admin user account (username: `admin`, password: `admin123`)

**Note**: The admin account is automatically protected and cannot be deleted. Only the admin account can be deactivated or edited by itself. The admin must create other user accounts (secretaries, doctors, etc.) after first login.

## Default Credentials

After deployment, you can immediately log in with:

- **Username**: admin
- **Email**: admin@entclinic.com
- **Password**: admin123

⚠️ **Important**: Change the default admin password immediately after your first login in production.

## Post-Deployment Workflow

After deploying and running migrations, the system is ready to use:

1. **First Login**: Log in with the admin credentials (admin / admin123)
2. **Create Staff Accounts**: Navigate to Settings → User Management
3. **Add Secretaries**: Create secretary accounts who will manage appointments and patient records
4. **Add Doctors**: Create doctor accounts who will conduct patient visits and manage prescriptions
5. **Change Admin Password**: Update the admin password (the admin account is protected and only the current admin can edit it)

### Admin Account Protection

- The admin account is **automatically protected** during deployment
- It **cannot be deleted** by any user
- It **can only be edited by itself** (the current admin)
- Other admins can be created, but at least one protected admin must always exist
- The system prevents deletion of the last remaining admin account

## Running the Application

### Development Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

### With Apache/XAMPP

Place the project in your htdocs folder and access via:
`http://localhost/laravel-online-ent-clinic-app/public`

## API Routes

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user

### Patients
- `GET /api/patients` - List all patients (with pagination & search)
- `POST /api/patients` - Create new patient
- `GET /api/patients/{id}` - Get patient details
- `PUT /api/patients/{id}` - Update patient
- `DELETE /api/patients/{id}` - Delete patient

### Appointments
- `GET /api/appointments` - List appointments (with date range filtering)
- `GET /api/appointments/doctors` - List available doctors
- `POST /api/appointments` - Create appointment
- `GET /api/appointments/{id}` - Get appointment details
- `PUT /api/appointments/{id}` - Update appointment
- `DELETE /api/appointments/{id}` - Cancel appointment

### Patient Visits
- `GET /api/visits` - List visits (with pagination)
- `POST /api/visits` - Create visit record
- `GET /api/visits/{id}` - Get visit details with prescriptions
- `PUT /api/visits/{id}` - Update visit

### Medicines
- `GET /api/medicines` - List medicines (with search)
- `POST /api/medicines` - Add new medicine
- `GET /api/medicines/{id}` - Get medicine details
- `PUT /api/medicines/{id}` - Update medicine

### Analytics
- `GET /api/analytics/dashboard` - Get dashboard statistics
- `GET /api/analytics/metrics` - Get detailed metrics

## CORS Configuration

The application supports CORS for the following origins:
- http://localhost:5173 (Vue dev server)
- http://localhost:8000 (Laravel dev server)
- http://localhost:3000 (React dev server)
- http://localhost
- APP_URL environment variable

Customize in `.env`:
```
APP_URL=http://your-domain.com
```

## Session Management

Sessions are stored in the database with:
- **Lifetime**: 3600 seconds (1 hour) - Configurable via `SESSION_LIFETIME` in `.env`
- **Secure cookies**: Enabled in production
- **HTTP-only**: True for security
- **SameSite**: Lax (cross-site request protection)

## Security Features

- Password hashing using bcrypt
- CSRF protection (via Laravel middleware)
- SQL injection prevention (prepared statements)
- CORS middleware for cross-origin security
- Session timeout enforcement
- User role-based access (admin, doctor, staff, secretary)
- Protected admin accounts (is_protected flag) – the very first administrator created (including the default seeded user) is automatically marked as protected

## Authentication

The API uses session-based authentication:

1. User logs in via `POST /api/auth/login`
2. Session is created and stored in database
3. Subsequent requests automatically authenticated via session
4. Session timeout after configured duration of inactivity

## Development

### Creating a New Migration

```bash
php artisan make:migration create_table_name
```

### Creating a New Model

```bash
php artisan make:model ModelName
```

### Creating a New Controller

```bash
php artisan make:controller Api/ControllerName
```

### Running Tests

```bash
php artisan test
```

### Clear Application Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:cache
```

## Production Deployment

### Before Going Live

1. Update `.env`:
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   SESSION_SECURE_COOKIES=true
   ```

2. Set strong app key (already done):
   ```bash
   php artisan key:generate
   ```

3. Optimize autoloader:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   ```

4. Run migrations:
   ```bash
   php artisan migrate --force
   ```

5. Set proper file permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod -R 644 storage bootstrap/cache/*.*
   ```

6. Change default admin password immediately

## Troubleshooting

### Database Connection Issues

Ensure MySQL is running and credentials in `.env` are correct:
```bash
php artisan tinker
> DB::connection()->getPdo();
```

### Permission Denied Errors

Fix file permissions:
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage logs
```

### Missing pdo_mysql Extension

Enable in `php.ini`:
```ini
extension=pdo_mysql
```

### Session Issues

Clear session table and cache:
```bash
php artisan session:table
php artisan migrate
php artisan cache:clear
```

## Support

For issues or questions about the original application, refer to the documentation in the `docs/` directory.

## License

This project maintains the same license as the original ENT Clinic Online application.
