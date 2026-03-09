<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->enum('recording_type', ['audio', 'video', 'endoscopy', 'imaging']);
            $table->string('recording_title', 255);
            $table->text('recording_description')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->integer('file_size')->nullable();
            $table->integer('duration')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->dateTime('recorded_at')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processed', 'archived'])->default('pending');
            $table->timestamps();
            $table->index('patient_id');
            $table->index('recorded_at');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recordings');
    }
};
