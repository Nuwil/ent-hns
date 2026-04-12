<?php

namespace App\Console\Commands;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class GenerateForecastData extends Command
{
    protected $signature = 'forecast:generate-data {--days=60 : Number of days to generate data for}';
    protected $description = 'Generate realistic visit data for forecasting system testing';

    // In Carbon: 0 = Sunday, 1 = Monday, ..., 6 = Saturday
    private const DAILY_BASE_VISITS = [
        0 => 0,  // Sunday  — clinic closed (skipped entirely)
        1 => 8,  // Monday  — busy
        2 => 8,  // Tuesday — busy
        3 => 8,  // Wednesday — busy
        4 => 8,  // Thursday — busy
        5 => 5,  // Friday  — slower
        6 => 3,  // Saturday — light
    ];

    private const ENT_TYPES = [
        'ears', 'nose', 'throat',
        'head_neck_tumor', 'lifestyle_medicine', 'pediatric_ent',
    ];

    private const COMPLAINTS = [
        'Sore throat', 'Ear pain', 'Hearing loss', 'Nasal congestion',
        'Chronic cough', 'Voice hoarseness', 'Tinnitus', 'Sinusitis',
        'Allergic rhinitis', 'Sleep apnea', 'Deviated septum', 'Earwax impaction',
    ];

    public function handle(): int
    {
        $days     = (int) $this->option('days');
        $doctors  = User::where('role', 'doctor')->where('is_active', true)->get();
        $patients = Patient::limit(20)->get();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            $this->error('❌ No doctors or patients found. Create them first.');
            return 1;
        }

        $hasEntClassification = Schema::hasColumn('visits', 'ent_classification');
        $generated = 0;
        $bar       = $this->output->createProgressBar($days);

        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();

            // Skip Sundays — clinic is closed
            if ($date->isSunday()) {
                $bar->advance();
                continue;
            }

            $base        = self::DAILY_BASE_VISITS[$date->dayOfWeek] ?? 5;
            $visitCount  = max(0, min($base + rand(-2, 4), 15));

            // Occasional spike day (roughly 1 in 10 days)
            if (rand(0, 9) === 0) {
                $visitCount += rand(3, 7);
            }

            for ($v = 0; $v < $visitCount; $v++) {
                $doctor  = $doctors->random();
                $patient = $patients->random();

                $data = [
                    'patient_id'    => $patient->id,
                    'doctor_id'     => $doctor->id,
                    'visited_at'    => $date->copy()->addHours(rand(8, 17))->addMinutes(rand(0, 59)),
                    'chief_complaint' => self::COMPLAINTS[array_rand(self::COMPLAINTS)],
                    'diagnosis'     => 'ENT condition — ongoing assessment',
                    'prescriptions' => [],   // required by Visit model cast
                    'status'        => ['pending', 'in_progress', 'finalized'][rand(0, 2)],
                    'recorded_by'   => rand(0, 1) === 0 ? 'secretary' : 'doctor',
                ];

                if ($hasEntClassification) {
                    $data['ent_classification'] = self::ENT_TYPES[array_rand(self::ENT_TYPES)];
                }

                Visit::create($data);
                $generated++;
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info("✅ Generated {$generated} realistic visit records for forecasting!");
        $this->info('📊 Data spans: ' . now()->subDays($days)->format('M j, Y') . ' to ' . now()->format('M j, Y'));
        $this->info("👨‍⚕️ Doctors: {$doctors->count()} | 👥 Patients: {$patients->count()}");

        return 0;
    }
}