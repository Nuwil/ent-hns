<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ─────────────────────────────────────────────────
        $admin = User::create([
            'username'      => 'admin',
            'full_name'     => 'Admin User',
            'email'         => 'admin@entclinic.com',
            'password_hash' => Hash::make('password'),
            'role'          => 'admin',
        ]);

        $secretary = User::create([
            'username'      => 'secretary',
            'full_name'     => 'Maria Santos',
            'email'         => 'secretary@entclinic.com',
            'password_hash' => Hash::make('password'),
            'role'          => 'secretary',
        ]);

        $doctor = User::create([
            'username'      => 'doctor',
            'full_name'     => 'Dr. Juan Reyes',
            'email'         => 'doctor@entclinic.com',
            'password_hash' => Hash::make('password'),
            'role'          => 'doctor',
        ]);

        // ── Patients ──────────────────────────────────────────────
        $patients = [
            ['first_name' => 'Ana',     'last_name' => 'dela Cruz',  'date_of_birth' => '1985-03-14', 'gender' => 'female', 'phone' => '09171234567', 'blood_type' => 'O+'],
            ['first_name' => 'Carlos',  'last_name' => 'Mendoza',    'date_of_birth' => '1972-07-22', 'gender' => 'male',   'phone' => '09189876543', 'blood_type' => 'A+'],
            ['first_name' => 'Sofia',   'last_name' => 'Ramos',      'date_of_birth' => '1990-11-05', 'gender' => 'female', 'phone' => '09201112222', 'blood_type' => 'B+'],
            ['first_name' => 'Roberto', 'last_name' => 'Garcia',     'date_of_birth' => '1968-01-30', 'gender' => 'male',   'phone' => '09173334444', 'blood_type' => 'AB+', 'allergies' => 'Penicillin'],
            ['first_name' => 'Elena',   'last_name' => 'Torres',     'date_of_birth' => '2001-09-18', 'gender' => 'female', 'phone' => '09175556666', 'blood_type' => 'O-'],
        ];

        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }

        // ── Sample Appointments ───────────────────────────────────
        $patientIds = Patient::pluck('id');

        Appointment::create([
            'patient_id'   => $patientIds[0],
            'doctor_id'    => $doctor->id,
            'scheduled_at' => now()->addHours(2),
            'reason'       => 'Ear pain and hearing difficulty',
            'status'       => 'pending',
        ]);

        Appointment::create([
            'patient_id'   => $patientIds[1],
            'doctor_id'    => $doctor->id,
            'scheduled_at' => now()->addHours(4),
            'reason'       => 'Follow-up for sinusitis',
            'status'       => 'accepted',
        ]);

        Appointment::create([
            'patient_id'   => $patientIds[2],
            'doctor_id'    => $doctor->id,
            'scheduled_at' => now()->subDay(),
            'reason'       => 'Throat irritation',
            'status'       => 'completed',
        ]);

        // ── Sample Visit ──────────────────────────────────────────
        Visit::create([
            'patient_id'      => $patientIds[2],
            'doctor_id'       => $doctor->id,
            'visited_at'      => now()->subDay(),
            'chief_complaint' => 'Sore throat for 3 days',
            'diagnosis'       => 'Acute pharyngitis',
            'notes'           => 'No fever. Recommended rest and hydration.',
            'prescriptions'   => [
                ['drug' => 'Amoxicillin 500mg', 'dosage' => 'TID x 7 days'],
                ['drug' => 'Paracetamol 500mg', 'dosage' => 'PRN for pain/fever'],
            ],
            'follow_up_date'  => now()->addWeek()->toDateString(),
        ]);

        $this->command->info('✅ Seeder complete. Login credentials:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',     'admin@entclinic.com',     'password'],
                ['Secretary', 'secretary@entclinic.com', 'password'],
                ['Doctor',    'doctor@entclinic.com',    'password'],
            ]
        );
    }
}