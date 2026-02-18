<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('name')->nullable()->after('user_id');
            $table->string('email')->nullable()->after('name');
        });

        DB::table('student_profiles')
            ->join('users', 'student_profiles.user_id', '=', 'users.id')
            ->update([
                'student_profiles.name' => DB::raw('users.name'),
                'student_profiles.email' => DB::raw('users.email'),
            ]);
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn(['name', 'email']);
        });
    }
};
