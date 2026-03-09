<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlist');
    }
};
