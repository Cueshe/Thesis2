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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('grade_level')->nullable();
            $table->string('section')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->string('grade_level')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        // Backfill existing users into their respective profile tables
        $existingStudents = DB::table('users')->where('role', 'student')->get();
        foreach ($existingStudents as $student) {
            DB::table('student_profiles')->insert([
                'user_id' => $student->id,
                'grade_level' => $student->grade_level,
                'section' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $existingTeachers = DB::table('users')->where('role', 'teacher')->get();
        foreach ($existingTeachers as $teacher) {
            DB::table('teacher_profiles')->insert([
                'user_id' => $teacher->id,
                'phone' => $teacher->phone,
                'subject' => $teacher->subject,
                'grade_level' => $teacher->grade_level,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
        Schema::dropIfExists('student_profiles');
    }
};
