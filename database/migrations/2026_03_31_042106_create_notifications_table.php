<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // recipient
            $table->string('type');          // appointment.booked, visit.intake, etc.
            $table->string('title');
            $table->string('message');
            $table->string('icon')->default('bi-bell');
            $table->string('color')->default('primary');
            $table->string('url')->nullable(); // where to go when clicked
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};