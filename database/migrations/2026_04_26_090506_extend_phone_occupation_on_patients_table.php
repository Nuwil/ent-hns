<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Extend phone to accommodate international formats and extensions
            $table->string('phone', 50)->change();
            // Extend occupation for longer job titles
            $table->string('occupation', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('phone', 20)->change();
            $table->string('occupation', 150)->nullable()->change();
        });
    }
};