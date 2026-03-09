<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->dateTime('visit_date');
            $table->string('visit_type', 100)->nullable();
            $table->enum('ent_type', ['ear', 'nose', 'throat', 'head_neck_tumor', 'lifestyle_medicine', 'misc'])->default('ear');
            $table->text('chief_complaint')->nullable();
            $table->text('history')->nullable();
            $table->text('physical_exam')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->text('prescription')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('blood_pressure', 20)->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->integer('oxygen_saturation')->nullable();
            $table->text('vitals_notes')->nullable();
            $table->foreignId('doctor_id')->nullable()->constrained('users');
            $table->string('doctor_name', 150)->nullable();
            $table->timestamps();
            $table->index('patient_id');
            $table->index('appointment_id');
            $table->index('visit_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_visits');
    }
};
