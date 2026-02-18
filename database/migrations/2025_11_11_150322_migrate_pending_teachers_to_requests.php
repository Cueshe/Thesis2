<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\TeacherRequest;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Move existing pending teacher accounts to teacher_requests table
        $pendingTeachers = User::where('role', 'teacher')
            ->where('status', 'pending')
            ->get();

        foreach ($pendingTeachers as $teacher) {
            TeacherRequest::create([
                'name' => $teacher->name,
                'email' => $teacher->email,
                'subject' => $teacher->subject,
                'grade_level' => $teacher->grade_level,
                'notes' => null,
                'created_at' => $teacher->created_at,
                'updated_at' => $teacher->updated_at,
            ]);

            // Delete the user and their teacher profile
            $teacher->teacherProfile()->delete();
            $teacher->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration
        // The data has been moved and it should stay in the new structure
    }
};
