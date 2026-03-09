<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\SecretaryDashboardController;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login'])->name('login.post');

// Debug: test endpoint to manually set session data
Route::get('/test-session-set', function (\Illuminate\Http\Request $request) {
    $request->session()->put('user_id', 999);
    $request->session()->put('test_key', 'test_value');
    $request->session()->save();
    
    $sessionId = $request->session()->getId();
    
    // Check if it was saved
    $dbSession = DB::table('sessions')->where('id', $sessionId)->first();
    
    return response()->json([
        'session_id' => $sessionId,
        'session_all' => $request->session()->all(),
        'in_database' => $dbSession ? true : false,
        'db_payload' => $dbSession ? unserialize(base64_decode($dbSession->payload)) : null,
    ]);
});

// Protected routes - All authenticated users
Route::middleware(['auth.session'])->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [WebAuthController::class, 'dashboard'])->name('dashboard');
});

// Admin routes
Route::middleware(['auth.session', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    // Admin user management routes
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['create', 'show']);
    Route::patch('users/{user}/toggle', [App\Http\Controllers\Admin\UserController::class, 'toggle'])->name('users.toggle');
});

// Doctor routes
Route::middleware(['auth.session', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/patients', [DoctorDashboardController::class, 'patients'])->name('patients');
    Route::post('/patients', [DoctorDashboardController::class, 'storePatient'])->name('patients.store');
    Route::get('/patients/{patient_id}/profile', [DoctorDashboardController::class, 'patientProfile'])->name('patient-profile');
    Route::get('/patients/{patient_id}/edit', [DoctorDashboardController::class, 'editPatient'])->name('patients.edit');
    Route::put('/patients/{patient_id}', [DoctorDashboardController::class, 'updatePatient'])->name('patients.update');
    Route::get('/appointments', [DoctorDashboardController::class, 'appointments'])->name('appointments');
    Route::get('/appointments/create', [DoctorDashboardController::class, 'createAppointment'])->name('appointments.create');
    Route::post('/appointments', [DoctorDashboardController::class, 'storeAppointment'])->name('appointments.store');
    Route::get('/visits/create', [DoctorDashboardController::class, 'createVisit'])->name('visits.create');
    Route::post('/visits', [DoctorDashboardController::class, 'storeVisit'])->name('visits.store');
    Route::get('/analytics', [DoctorDashboardController::class, 'analytics'])->name('analytics');
});

// Secretary routes
Route::middleware(['auth.session', 'role:secretary'])->prefix('secretary')->name('secretary.')->group(function () {
    Route::get('/dashboard', [SecretaryDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/patients', [SecretaryDashboardController::class, 'patients'])->name('patients');
    Route::post('/patients', [SecretaryDashboardController::class, 'storePatient'])->name('patients.store');
    Route::get('/patients/{patient_id}/profile', [SecretaryDashboardController::class, 'patientProfile'])->name('patient-profile');
    Route::get('/patients/{patient_id}/edit', [SecretaryDashboardController::class, 'editPatient'])->name('patients.edit');
    Route::put('/patients/{patient_id}', [SecretaryDashboardController::class, 'updatePatient'])->name('patients.update');
    Route::get('/appointments', [SecretaryDashboardController::class, 'appointments'])->name('appointments');
    Route::get('/appointments/create', [SecretaryDashboardController::class, 'createAppointment'])->name('appointments.create');
    Route::post('/appointments', [SecretaryDashboardController::class, 'storeAppointment'])->name('appointments.store');
    Route::get('/visits/create', [SecretaryDashboardController::class, 'createVisit'])->name('visits.create');
    Route::post('/visits', [SecretaryDashboardController::class, 'storeVisit'])->name('visits.store');
});
