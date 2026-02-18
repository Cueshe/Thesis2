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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('points')->default(0)->after('status');
            $table->integer('level')->default(1)->after('points');
            $table->integer('experience')->default(0)->after('level');
            $table->integer('streak_days')->default(0)->after('experience');
            $table->date('last_activity_date')->nullable()->after('streak_days');
            $table->json('achievements')->nullable()->after('last_activity_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['points', 'level', 'experience', 'streak_days', 'last_activity_date', 'achievements']);
        });
    }
};
