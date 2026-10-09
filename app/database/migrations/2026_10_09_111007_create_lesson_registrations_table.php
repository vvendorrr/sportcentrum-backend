<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lesson_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['enrolled', 'waitlisted', 'cancelled'])->default('enrolled');
            $table->dateTime('registered_at')->useCurrent();
            $table->enum('attendance', ['attended', 'absent'])->nullable();
            $table->dateTime('canceled_at')->nullable();

            $table->unique(['lesson_id', 'user_id']);
            $table->index(['lesson_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_registrations');
    }
};
