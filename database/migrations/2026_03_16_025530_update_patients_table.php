<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Address breakdown — Philippines: Province > City > Street
            $table->string('province', 100)->nullable()->after('address');
            $table->string('city', 100)->nullable()->after('province');
            // address column becomes street address

            // New patient info fields
            $table->string('occupation', 150)->nullable()->after('phone');
            $table->string('insurance_info', 500)->nullable()->after('allergies');
            $table->text('medical_history')->nullable()->after('insurance_info');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['province', 'city', 'occupation', 'insurance_info', 'medical_history']);
        });
    }
};