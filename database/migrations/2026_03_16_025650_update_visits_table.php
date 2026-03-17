<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Vitals — secretary collects weight, height, BP during intake
            $table->string('blood_pressure', 20)->nullable()->after('chief_complaint');
            $table->decimal('weight', 5, 1)->nullable()->after('blood_pressure');
            $table->decimal('height', 5, 1)->nullable()->after('weight');

            // Clinical fields — doctor fills during completion
            $table->text('history')->nullable()->after('ent_classification');       // further explanation of CC
            $table->text('physical_exam')->nullable()->after('exam_findings');      // renamed from exam_findings but kept both
            $table->text('plan_instructions')->nullable()->after('treatment_plan'); // doctor's instructions alongside Rx
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['blood_pressure', 'weight', 'height', 'history', 'physical_exam', 'plan_instructions']);
        });
    }
};