<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users');
            $table->dateTime('appointment_date');
            $table->string('appointment_type', 100)->nullable();
            $table->integer('duration')->nullable();
            $table->enum('status', ['Pending', 'Accepted', 'Completed', 'Cancelled', 'No-Show'])->default('Pending');
            $table->text('notes')->nullable();
            $table->string('blood_pressure', 20)->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->integer('oxygen_saturation')->nullable();
            $table->dateTime('rescheduled_from')->nullable();
            $table->dateTime('rescheduled_to')->nullable();
            $table->string('cancellation_reason', 255)->nullable();
            $table->timestamps();
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('appointment_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
