<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate name and email in teacher_profiles from users table
        DB::statement('
            UPDATE teacher_profiles 
            INNER JOIN users ON teacher_profiles.user_id = users.id
            SET teacher_profiles.name = users.name,
                teacher_profiles.email = users.email
            WHERE teacher_profiles.name IS NULL OR teacher_profiles.email IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear name and email from teacher_profiles
        DB::table('teacher_profiles')->update([
            'name' => null,
            'email' => null,
        ]);
    }
};
