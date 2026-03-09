<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // Insert default admin user
        DB::table('users')->insertOrIgnore([
            'username' => 'admin',
            'email' => 'admin@entclinic.com',
            'password_hash' => '$2y$12$XOKg8KRFwGny0WOE8qoTj.I7cgoaVmFDwY6pPw9hyW3PxqnyRMzda', // password: admin123
            'full_name' => 'Administrator',
            'role' => 'admin',
            'is_active' => true,
            'is_protected' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert default medicines
        $medicines = [
            ['name' => 'Amoxicillin', 'dosage' => '500', 'unit' => 'mg'],
            ['name' => 'Ibuprofen', 'dosage' => '200', 'unit' => 'mg'],
            ['name' => 'Paracetamol', 'dosage' => '500', 'unit' => 'mg'],
            ['name' => 'Cetirizine', 'dosage' => '10', 'unit' => 'mg'],
            ['name' => 'Omeprazole', 'dosage' => '20', 'unit' => 'mg'],
            ['name' => 'Metronidazole', 'dosage' => '400', 'unit' => 'mg'],
            ['name' => 'Cephalexin', 'dosage' => '500', 'unit' => 'mg'],
            ['name' => 'Aspirin', 'dosage' => '81', 'unit' => 'mg'],
            ['name' => 'Loratadine', 'dosage' => '10', 'unit' => 'mg'],
            ['name' => 'Dexamethasone', 'dosage' => '0.5', 'unit' => 'mg'],
            ['name' => 'Ambroxol', 'dosage' => '30', 'unit' => 'mg'],
            ['name' => 'Diphenhydramine', 'dosage' => '25', 'unit' => 'mg'],
            ['name' => 'Fluconazole', 'dosage' => '150', 'unit' => 'mg'],
            ['name' => 'Hydrocodone', 'dosage' => '5', 'unit' => 'mg'],
            ['name' => 'Itraconazole', 'dosage' => '100', 'unit' => 'mg'],
            ['name' => 'Ketoconazole', 'dosage' => '200', 'unit' => 'mg'],
            ['name' => 'Levofloxacin', 'dosage' => '500', 'unit' => 'mg'],
            ['name' => 'Mometasone', 'dosage' => '50', 'unit' => 'mcg'],
            ['name' => 'Nifedipine', 'dosage' => '30', 'unit' => 'mg'],
            ['name' => 'Oxymetazoline', 'dosage' => '0.05', 'unit' => '%'],
        ];

        foreach ($medicines as $medicine) {
            DB::table('medicines')->insertOrIgnore(array_merge($medicine, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Insert appointment types
        $appointmentTypes = [
            ['key' => 'new_patient', 'label' => 'New Patient', 'duration_minutes' => 30, 'buffer_minutes' => 10, 'daily_max' => 4],
            ['key' => 'follow_up', 'label' => 'Follow-up', 'duration_minutes' => 15, 'buffer_minutes' => 5, 'daily_max' => 10],
            ['key' => 'procedure', 'label' => 'Procedure', 'duration_minutes' => 45, 'buffer_minutes' => 15, 'daily_max' => 2],
            ['key' => 'emergency', 'label' => 'Emergency', 'duration_minutes' => 0, 'buffer_minutes' => 0, 'daily_max' => null],
        ];

        foreach ($appointmentTypes as $appointmentType) {
            DB::table('appointment_types')->insertOrIgnore($appointmentType);
        }
    }
}
