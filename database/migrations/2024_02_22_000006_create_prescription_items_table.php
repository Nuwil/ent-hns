<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->foreign('visit_id')->references('id')->on('patient_visits')->onDelete('set null');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medicine_id')->nullable()->constrained('medicines');
            $table->string('medicine_name', 255);
            $table->text('instruction')->nullable();
            $table->foreignId('doctor_id')->nullable()->constrained('users');
            $table->timestamp('created_at')->useCurrent();
            $table->index('patient_id');
            $table->index('visit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
