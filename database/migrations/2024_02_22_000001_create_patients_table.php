<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('email', 100)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();
            $table->text('medical_history')->nullable();
            $table->text('current_medications')->nullable();
            $table->text('allergies')->nullable();
            $table->text('vaccine_history')->nullable();
            $table->string('insurance_provider', 100)->nullable();
            $table->string('insurance_id', 100)->nullable();
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relationship', 50)->nullable()->comment('Relationship to patient (e.g., Spouse, Parent, Sibling)');
            $table->decimal('height', 5, 2)->nullable()->comment('Height in cm');
            $table->decimal('weight', 5, 2)->nullable()->comment('Weight in kg');
            $table->decimal('bmi', 5, 2)->nullable()->comment('Body Mass Index (kg/m2)');
            $table->string('blood_pressure', 20)->nullable()->comment('e.g., 120/80');
            $table->decimal('temperature', 4, 1)->nullable()->comment('Temperature in Celsius');
            $table->timestamp('vitals_updated_at')->nullable()->comment('Last update of vitals');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->index('patient_id');
            $table->index('email');
            $table->index('phone');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
