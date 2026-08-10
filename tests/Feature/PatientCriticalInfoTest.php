<?php

namespace Tests\Feature;

use App\Http\Controllers\PatientController;
use App\Models\Patient;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PatientCriticalInfoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('patients');
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('allergies')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('severity')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function test_critical_info_update_ignores_missing_columns(): void
    {
        $patientId = DB::table('patients')->insertGetId([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'allergies' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $patient = Patient::find($patientId);

        $request = Request::create('/patients/critical-info', 'PATCH', [
            'allergies' => 'Penicillin',
            'medical_history' => 'Asthma',
            'vaccine_history' => 'COVID-19',
        ], [], [], ['HTTP_ACCEPT' => 'application/json']);

        $response = (new PatientController())->updateCriticalInfo($request, $patient);

        $this->assertTrue($response->getData()->success);
        $this->assertSame('Penicillin', $patient->fresh()->allergies);
        $this->assertNull($patient->fresh()->medical_history);
    }
}
