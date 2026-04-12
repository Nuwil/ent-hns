<?php

namespace App\Console\Commands;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class GenerateForecastData extends Command
{
    protected $signature = 'forecast:generate-data {--days=60 : Number of days to generate data for}';
    protected $description = 'Generate realistic visit data for forecasting system testing';

    public function handle()
    {
        $days = $this->option('days');
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        $patients = Patient::limit(20)->get();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            $this->error('❌ No doctors or patients found. Create them first.');
            return 1;
        }

        // ENT classifications
        $entTypes = ['ears', 'nose', 'throat', 'head_neck_tumor', 'lifestyle_medicine', 'pediatric_ent'];
        
        // Chief complaints
        $complaints = [
            'Sore throat', 'Ear pain', 'Hearing loss', 'Nasal congestion',
            'Chronic cough', 'Voice hoarseness', 'Tinnitus', 'Sinusitis',
            'Allergic rhinitis', 'Sleep apnea', 'Deviated septum', 'Earwax impaction'
        ];

        // Check if ent_classification column exists
        $hasEntClassification = \Schema::hasColumn('visits', 'ent_classification');

        $generated = 0;
        $bar = $this->output->createProgressBar($days * 7);

        // Generate visits for the past N days
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            
            // Skip Sundays (clinic closed)
            if ($date->isSunday()) continue;
            
            // Weekday multiplier (busier on Mon-Fri)
            $dayOfWeek = $date->dayOfWeek;
            $baseVisits = match($dayOfWeek) {
                0, 6 => 3,      // Sat, Sun = 3 visits
                1, 2, 3, 4 => 8, // Mon-Thu = 8 visits (busier)
                5 => 5,         // Fri = 5 visits (slower)
                default => 5
            };

            // Add randomness
            $visitCount = $baseVisits + rand(-2, 4);
            $visitCount = max(0, min($visitCount, 15));

            // Occasional spike
            if (rand(0, 10) === 0) {
                $visitCount += rand(3, 7);
            }

            for ($v = 0; $v < $visitCount; $v++) {
                $doctor = $doctors->random();
                $patient = $patients->random();

                $data = [
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'visited_at' => $date->copy()->addHours(rand(8, 17))->addMinutes(rand(0, 59)),
                    'chief_complaint' => $complaints[array_rand($complaints)],
                    'diagnosis' => 'ENT condition - ongoing assessment',
                    'status' => ['pending', 'in_progress', 'finalized'][rand(0, 2)],
                    'recorded_by' => rand(0, 1) === 0 ? 'secretary' : 'doctor',
                ];

                // Add ent_classification if column exists
                if ($hasEntClassification) {
                    $data['ent_classification'] = $entTypes[array_rand($entTypes)];
                }

                Visit::create($data);

                $generated++;
                $bar->advance();
            }
        }

        $bar->finish();

        $this->info("\n\n✅ Generated $generated realistic visit records for forecasting!");
        $this->info("📊 Data spans: " . now()->subDays($days)->format('M j, Y') . " to " . now()->format('M j, Y'));
        $this->info("👨‍⚕️ Doctors: {$doctors->count()} | 👥 Patients: {$patients->count()}");
        
        return 0;
    }
}
