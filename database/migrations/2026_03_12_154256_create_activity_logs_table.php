<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name', 150)->nullable();  // snapshot in case user deleted
            $table->string('user_role', 50)->nullable();
            $table->string('action', 100);                 // e.g. 'patient.viewed'
            $table->string('description');                 // human-readable
            $table->string('subject_type', 100)->nullable(); // e.g. 'Patient'
            $table->unsignedBigInteger('subject_id')->nullable(); // e.g. patient id
            $table->string('subject_label')->nullable();   // e.g. patient name snapshot
            $table->enum('severity', ['info', 'warning', 'danger'])->default('info');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};