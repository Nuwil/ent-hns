<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics', function (Blueprint $table) {
            // store which doctor the analytics record belongs to
            $table->unsignedBigInteger('doctor_id')->nullable()->after('id');
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::table('analytics', function (Blueprint $table) {
            $table->dropIndex(['doctor_id']);
            $table->dropColumn('doctor_id');
        });
    }
};
