<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SampleAppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing users and patients
        $doctor = DB::table('users')->where('role', 'doctor')->first();
        $secretary = DB::table('users')->where('role', 'secretary')->first();
        $patient = DB::table('patients')->first();

        if (!$doctor || !$patient) {
            echo "No doctor or patient found. Please run DefaultDataSeeder first.\n";
            return;
        }

        // Create sample appointments for the current month
        $appointments = [
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => Carbon::now()->startOfMonth()->addDays(5)->setTime(10, 0, 0),
                'appointment_type' => 'Consultation',
                'notes' => 'Patient reports sudden hearing loss in left ear',
                'duration' => 30,
                'status' => 'Accepted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => Carbon::now()->startOfMonth()->addDays(12)->setTime(14, 30, 0),
                'appointment_type' => 'Follow-up',
                'notes' => 'Follow-up appointment for previous ear infection',
                'duration' => 15,
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => Carbon::now()->startOfMonth()->addDays(20)->setTime(11, 15, 0),
                'appointment_type' => 'Surgery',
                'notes' => 'Scheduled tonsillectomy surgery',
                'duration' => 60,
                'status' => 'Accepted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($appointments as $appointment) {
            DB::table('appointments')->insert($appointment);
        }

        echo "Sample appointments created successfully!\n";
    }
}