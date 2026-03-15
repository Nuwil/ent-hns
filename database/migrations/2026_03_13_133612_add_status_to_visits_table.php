<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Workflow status
            // pending    = secretary created, awaiting doctor
            // in_progress = doctor opened and saved partial notes
            // finalized  = doctor confirmed — locked forever
            $table->enum('status', ['pending', 'in_progress', 'finalized'])
                  ->default('pending')
                  ->after('recorded_by');

            // ENT classification stored separately for filtering
            $table->string('ent_classification', 100)->nullable()->after('chief_complaint');

            // Doctor-only fields stored separately for clarity
            $table->text('history_of_illness')->nullable()->after('ent_classification');
            $table->text('exam_findings')->nullable()->after('history_of_illness');
            $table->text('treatment_plan')->nullable()->after('exam_findings');

            // Who finalized and when
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'status', 'ent_classification', 'history_of_illness',
                'exam_findings', 'treatment_plan', 'finalized_by', 'finalized_at',
            ]);
        });
    }
};