<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;

class SecretaryViewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure migrations are run when using RefreshDatabase
        $this->artisan('migrate', ['--database' => 'sqlite']);

        // in local tests the project is served from a subfolder, override
        // the app.url to avoid route() generating that prefix
        $this->app['config']->set('app.url', 'http://localhost');
    }

    private function actingAsSecretary()
    {
        $secretary = User::factory()->create([
            'role' => 'secretary',
            'password_hash' => bcrypt('password'),
        ]);

        return $this->withSession([
            'user_id' => $secretary->id,
            'user_name' => $secretary->full_name ?? $secretary->username,
            'user_role' => $secretary->role,
        ]);
    }

    public function test_dashboard_is_accessible()
    {
        $generatedPath = route('secretary.dashboard', [], false); // path only
        dump('generated path: '.$generatedPath);
        $response = $this->withoutMiddleware([
            \App\Http\Middleware\CheckAuth::class,
            \App\Http\Middleware\CheckRole::class,
        ])->actingAsSecretary()->get($generatedPath);

        $response->assertStatus(200);
        $response->assertSee('Secretary Dashboard');
        $response->assertSee('Total Patients');
    }

    public function test_patient_list_and_profile_links()
    {
        $patient = Patient::create([
            'patient_id' => 'P'.uniqid(),
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'other',
            'email' => 'jane@example.com',
        ]);

        $response = $this->withoutMiddleware([
            \App\Http\Middleware\CheckAuth::class,
            \App\Http\Middleware\CheckRole::class,
        ])->actingAsSecretary()->get('/secretary/patients');

        $response->assertStatus(200);
        $response->assertSee((string) $patient->id);
        $response->assertSee('Jane');
        // ensure link to profile exists
        $response->assertSee(route('secretary.patient-profile', $patient->id));
        // table should use admin-like settings-table for styling
        $response->assertSee('class="settings-table"');

        // visit profile page directly
        $response2 = $this->withoutMiddleware([
            \App\Http\Middleware\CheckAuth::class,
            \App\Http\Middleware\CheckRole::class,
        ])->actingAsSecretary()->get('/secretary/patients/'.$patient->id.'/profile');
        $response2->assertStatus(200);
        $response2->assertSee('Patient Profile');
        $response2->assertSee('Jane');
    }

    public function test_appointments_page_shows_records()
    {
        $patient = Patient::create([
            'patient_id' => 'P'.uniqid(),
            'first_name' => 'Jim',
            'last_name' => 'Beam',
            'gender' => 'other',
            'email' => 'jim@example.com',
        ]);

        $doctor = User::factory()->create([
            'role' => 'doctor',
            'password_hash' => bcrypt('password'),
        ]);

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay(),
            'status' => 'Pending',
        ]);

        $response = $this->withoutMiddleware([
            \App\Http\Middleware\CheckAuth::class,
            \App\Http\Middleware\CheckRole::class,
        ])->actingAsSecretary()->get('/secretary/appointments');
        $response->assertStatus(200);
        $response->assertSee('Appointments Management');
        $response->assertSee('Jim');
        // ensure table uses settings-table class for consistent styling
        $response->assertSee('class="settings-table"');
    }
}
