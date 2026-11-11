<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['member', 'trainer', 'admin'])
                ->default('member')
                ->change();
        });

        DB::table('users')
            ->where('role', 'admin')
            ->whereIn('id', DB::table('lessons')->select('instructor_id')->whereNotNull('instructor_id'))
            ->update(['role' => 'trainer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('role', 'trainer')
            ->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['member', 'admin'])
                ->default('member')
                ->change();
        });
    }
};
