<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('visited_at');
            $table->string('chief_complaint', 500);
            $table->text('diagnosis');
            $table->text('notes')->nullable();
            $table->json('prescriptions')->nullable();
            $table->date('follow_up_date')->nullable();
            // Tracks who created the record: 'secretary' (intake only) or 'doctor' (full SOAP)
            $table->enum('recorded_by', ['secretary', 'doctor'])->default('doctor');
            $table->timestamps();

            $table->index(['doctor_id', 'visited_at']);
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};