<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/notifications/poll',          [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all',     [NotificationController::class, 'readAll'])->name('notifications.readAll');

    /*
    |------------------------------------------------------------------
    | Admin Routes
    |------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/settings',  [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // User account management
        Route::post('/users',            [SettingsController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}',      [SettingsController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}',   [SettingsController::class, 'destroyUser'])->name('users.destroy');
        Route::patch('/users/{user}/toggle', [SettingsController::class, 'toggleUser'])->name('users.toggle');

        // Analytics — admin sees all doctors
        Route::get('/analytics',             [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/doctor-data', [AnalyticsController::class, 'doctorData'])->name('analytics.doctor');
        Route::get('/analytics/clinic-data', [AnalyticsController::class, 'clinicData'])->name('analytics.clinic');
    });

    /*
    |------------------------------------------------------------------
    | Secretary Routes
    | - Can add patients, book appointments, add intake visits
    | - CANNOT accept / confirm / cancel appointments
    | - CANNOT access diagnosis, prescriptions, or SOAP fields
    |------------------------------------------------------------------
    */
    Route::middleware('role:secretary')->prefix('secretary')->name('secretary.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'secretary'])->name('dashboard');

        // Patient management
        Route::get('/patients',                [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create',         [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients',               [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}',      [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{patient}',      [PatientController::class, 'update'])->name('patients.update');
        Route::post('/patients/{patient}/note',    [PatientController::class, 'addNote'])->name('patients.note');
        Route::delete('/patients/{patient}',   [PatientController::class, 'destroy'])->name('patients.destroy');
        Route::get('/appointments',    [AppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments',   [AppointmentController::class, 'store'])->name('appointments.store');
        Route::patch('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::patch('/appointments/{appointment}/reassign',   [AppointmentController::class, 'reassign'])->name('appointments.reassign');

        // Visits — secretary can only store limited intake data (chief complaint from list, vitals)
        Route::post('/patients/{patient}/visits',          [VisitController::class, 'storeIntake'])->name('visits.store');
        Route::put('/patients/{patient}/visits/{visit}',   [VisitController::class, 'updateIntake'])->name('visits.update');
    });

    Route::get('/patients/{patient}/visits/{visit}/prescription/print',
    [VisitController::class, 'printPrescription'])
    ->name('visits.prescription.print');

    /*
    |------------------------------------------------------------------
    | Doctor Routes
    | - Full appointment control (confirm, complete, cancel)
    | - Full SOAP clinical documentation
    | - Analytics
    |------------------------------------------------------------------
    */
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'doctor'])->name('dashboard');

        // Patient management
        Route::get('/patients',                [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create',         [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients',               [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}',      [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{patient}',      [PatientController::class, 'update'])->name('patients.update');
        Route::post('/patients/{patient}/note',    [PatientController::class, 'addNote'])->name('patients.note');
        Route::delete('/patients/{patient}',   [PatientController::class, 'destroy'])->name('patients.destroy');

        // Appointments — full control
        Route::get('/appointments',    [AppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments',   [AppointmentController::class, 'store'])->name('appointments.store');
        Route::patch('/appointments/{appointment}/confirm',    [AppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::patch('/appointments/{appointment}/complete',   [AppointmentController::class, 'complete'])->name('appointments.complete');
        Route::patch('/appointments/{appointment}/cancel',     [AppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::patch('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::patch('/appointments/{appointment}/reassign',   [AppointmentController::class, 'reassign'])->name('appointments.reassign');

        // Visits — full SOAP documentation
        Route::post('/patients/{patient}/visits',                    [VisitController::class, 'store'])->name('visits.store');
        Route::get('/patients/{patient}/visits/{visit}/edit',        [VisitController::class, 'edit'])->name('visits.edit');
        Route::put('/patients/{patient}/visits/{visit}',             [VisitController::class, 'update'])->name('visits.update');
        Route::patch('/patients/{patient}/visits/{visit}/finalize',  [VisitController::class, 'finalize'])->name('visits.finalize');

        // Analytics
        Route::get('/analytics',              [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/doctor-data',  [AnalyticsController::class, 'doctorData'])->name('analytics.doctor');
        Route::get('/analytics/clinic-data',  [AnalyticsController::class, 'clinicData'])->name('analytics.clinic');
    });
});