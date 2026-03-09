<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_types', function (Blueprint $table) {
            $table->string('key', 50)->primary();
            $table->string('label', 100);
            $table->integer('duration_minutes');
            $table->integer('buffer_minutes');
            $table->integer('daily_max')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_types');
    }
};
