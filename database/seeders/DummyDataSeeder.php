<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = User::where('role', 'doctor')->get();

        if ($doctors->isEmpty()) {
            $this->command->warn('No doctors found. Please run UserSeeder first.');
            return;
        }

        $firstNames = ['Juan', 'Maria', 'Carlos', 'Ana', 'Miguel', 'Rosa', 'Pedro', 'Sofia', 'Luis', 'Carmen', 'Diego', 'Elena', 'Adrian', 'Isabel', 'Raul', 'Mira', 'Alvin', 'Nina', 'Victor', 'Lea'];
        $lastNames = ['Santos', 'Cruz', 'Reyes', 'Garcia', 'Lopez', 'Fernandez', 'Romero', 'Morales', 'Gutierrez', 'Ramirez', 'Torres', 'Velasco', 'Navarro', 'Delgado', 'Pena', 'Dela Cruz', 'Sandoval', 'Ortiz', 'Padilla', 'Aguirre'];
        $provinces = ['Metro Manila', 'Cavite', 'Rizal', 'Laguna', 'Quezon', 'Batangas', 'Bulacan', 'Pampanga'];
        $cities = ['Manila', 'Makati', 'Quezon City', 'Pasig', 'Taguig', 'Las Piñas', 'Caloocan', 'Marikina'];
        $genders = ['male', 'female', 'other'];
        $religions = ['Catholic', 'Muslim', 'Protestant', 'Buddhist', 'Hindu', 'Agnostic'];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $occupations = ['Engineer', 'Teacher', 'Nurse', 'Business Owner', 'Student', 'Retired', 'Sales Associate', 'Office Worker'];
        $reasons = [
            'Hearing loss',
            'Ear infection',
            'Tinnitus',
            'Vertigo',
            'Nasal congestion',
            'Sinusitis',
            'Throat pain',
            'Chronic cough',
            'Ear wax buildup',
            'Sore throat',
        ];
        $classifications = ['Normal', 'Otitis Media', 'Rhinitis', 'Sinusitis', 'Pharyngitis'];
        $diagnoses = ['Otitis Media', 'Acute Sinusitis', 'Cerumen Impaction', 'Allergic Rhinitis', 'Pharyngitis'];
        $examFindings = [
            'Normal ear canals',
            'Inflamed nasal mucosa',
            'Tympanic membrane redness',
            'Fluid behind eardrum',
            'Swollen tonsils',
        ];
        $treatmentPlans = [
            'Prescribed antibiotics and follow-up in 1 week',
            'Recommended nasal sprays and rest',
            'Ear irrigation performed and advised follow-up',
            'Prescribed antihistamines and decongestants',
        ];
        $prescriptionSets = [
            ['Amoxicillin 500mg', 'Paracetamol 500mg'],
            ['Cefuroxime 250mg', 'Cetirizine 10mg'],
            ['Nasal spray 2x daily', 'Ibuprofen 400mg'],
            ['Ear drops 3x daily'],
        ];

        // Allow generating either a 60-day sample (default) or a full calendar year (Jan-Dec)
        // Set environment variables to control behavior:
        //  - DUMMY_DATA_SCOPE=year will generate for the whole year specified by DUMMY_DATA_YEAR
        //  - DUMMY_DATA_YEAR=2025 to choose a specific year (defaults to current year)
        $scope = env('DUMMY_DATA_SCOPE', '60days'); // '60days' or 'year'
        $year = (int) env('DUMMY_DATA_YEAR', Carbon::now()->year);

        if ($scope === 'year') {
            $this->command->info("Generating 40 dummy patients and appointments for entire year {$year} (Jan-Dec)...");
        } else {
            $this->command->info('Generating 40 dummy patients for 60-day appointment and visit data...');
        }

        $patients = collect();

        for ($i = 0; $i < 40; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $gender = $genders[array_rand($genders)];
            $email = strtolower("{$firstName}.{$lastName}{$i}@example.com");

            $patient = Patient::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => '09' . rand(100000000, 999999999),
                'date_of_birth' => Carbon::now()->subYears(rand(18, 75))->format('Y-m-d'),
                'gender' => $gender,
                'religion' => $religions[array_rand($religions)],
                'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                'occupation' => $occupations[array_rand($occupations)],
                'province' => $provinces[array_rand($provinces)],
                'city' => $cities[array_rand($cities)],
                'address' => rand(1, 999) . ' ' . ['Street', 'Avenue', 'Boulevard', 'Road'][rand(0, 3)],
                'allergies' => rand(0, 1) ? 'Penicillin' : null,
                'medical_history' => rand(0, 1) ? 'Hypertension' : null,
                'notes' => 'Created for 60-day dummy data set.',
            ]);

            $patients->push($patient);
            $this->command->line("✓ Created patient: {$patient->first_name} {$patient->last_name}");
        }

        if ($scope === 'year') {
            $startDate = Carbon::create($year, 1, 1)->startOfDay();
            $endDate = Carbon::create($year, 12, 31)->endOfDay();
            $this->command->info('Creating appointments across ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d') . '...');
        } else {
            $startDate = Carbon::today()->subDays(30);
            $endDate = Carbon::today()->addDays(29);
            $this->command->info('Creating appointments across 60 days...');
        }
        $appointmentCount = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $appointmentsToday = rand(1, 3);

            for ($j = 0; $j < $appointmentsToday; $j++) {
                $doctor = $doctors->random();
                $patient = $patients->random();
                $scheduledAt = $date->copy()->setTime(rand(8, 16), [0, 30][rand(0, 1)]);
                $statusOptions = $this->appointmentStatusForDate($scheduledAt);
                $status = $statusOptions[array_rand($statusOptions)];

                Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'scheduled_at' => $scheduledAt,
                    'reason' => $reasons[array_rand($reasons)],
                    'notes' => 'Scheduled visit for routine ENT evaluation.',
                    'status' => $status,
                ]);

                $appointmentCount++;
            }
        }

        $this->command->info("Created {$appointmentCount} appointments across 60 days.");
        $this->command->info('Creating visit records for past appointments...');

        $pastAppointments = Appointment::where('scheduled_at', '<=', Carbon::now())
            ->where('status', '!=', 'cancelled')
            ->get();

        $visitCount = 0;

        foreach ($pastAppointments as $appointment) {
            if (rand(0, 100) < 70) {
                $visitDate = Carbon::parse($appointment->scheduled_at)->addHours(rand(1, 3));
                if ($visitDate->isFuture()) {
                    $visitDate = Carbon::parse($appointment->scheduled_at);
                }

                $status = $this->visitStatusForDate($visitDate);
                $finalizedBy = $status === Visit::STATUS_FINALIZED ? $appointment->doctor_id : null;
                $finalizedAt = $status === Visit::STATUS_FINALIZED ? $visitDate->copy()->addHour() : null;
                $followUpDate = $status === Visit::STATUS_FINALIZED ? $visitDate->copy()->addDays(rand(7, 21)) : null;

                Visit::create([
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $appointment->doctor_id,
                    'appointment_id' => $appointment->id,
                    'visited_at' => $visitDate,
                    'chief_complaint' => $appointment->reason,
                    'ent_classification' => $classifications[array_rand($classifications)],
                    'history_of_illness' => 'Reported symptoms started several days ago and worsened over time.',
                    'physical_exam' => 'Exam revealed tenderness and mild inflammation.',
                    'exam_findings' => $examFindings[array_rand($examFindings)],
                    'diagnosis' => $diagnoses[array_rand($diagnoses)],
                    'treatment_plan' => $treatmentPlans[array_rand($treatmentPlans)],
                    'plan_instructions' => 'Patient to return if symptoms worsen or persist.',
                    'notes' => 'Visit recorded during dummy data generation.',
                    'prescriptions' => $prescriptionSets[array_rand($prescriptionSets)],
                    'follow_up_date' => $followUpDate,
                    'recorded_by' => $status === Visit::STATUS_PENDING ? 'secretary' : 'doctor',
                    'status' => $status,
                    'finalized_by' => $finalizedBy,
                    'finalized_at' => $finalizedAt,
                ]);

                $visitCount++;
            }
        }

        $this->command->info("Created {$visitCount} visits for past appointments.");
        if ($scope === 'year') {
            $this->command->info('✅ Year-long dummy data generation complete!');
        } else {
            $this->command->info('✅ 60-day dummy data generation complete!');
        }
    }

    private function appointmentStatusForDate(Carbon $scheduledAt): array
    {
        if ($scheduledAt->isFuture()) {
            return ['pending', 'accepted'];
        }

        if ($scheduledAt->isToday()) {
            return ['pending', 'accepted', 'completed'];
        }

        return ['accepted', 'completed', 'cancelled'];
    }

    private function visitStatusForDate(Carbon $visitDate): string
    {
        if ($visitDate->greaterThan(Carbon::now()->subDays(7))) {
            return rand(0, 1) ? Visit::STATUS_IN_PROGRESS : Visit::STATUS_FINALIZED;
        }

        return Visit::STATUS_FINALIZED;
    }
}
