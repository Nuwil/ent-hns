<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_type', 100);
            $table->string('metric_name', 255);
            $table->integer('metric_value')->default(0);
            $table->date('measurement_date');
            $table->json('additional_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('metric_type');
            $table->index('measurement_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
