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
        Schema::create('skill_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('quest_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('performance_id')->nullable()->constrained('student_performances')->onDelete('cascade');
            
            // Skill and problem details
            $table->string('skill_type'); // 'pronunciation', 'reading_comprehension', 'vocabulary', 'grammar'
            $table->string('problem_type'); // 'word_pronunciation', 'sentence_reading', 'multiple_choice', 'fill_blank'
            $table->text('problem_content'); // The actual problem/question
            $table->text('student_response'); // What the student answered/said
            $table->text('correct_answer'); // The expected correct answer
            
            // Response evaluation
            $table->boolean('is_correct')->default(false);
            $table->decimal('accuracy_score', 5, 2)->default(0); // 0-100 score for partial credit
            $table->integer('attempts')->default(1);
            $table->integer('response_time_seconds')->nullable(); // Time taken to respond
            
            // Skill-specific details
            $table->json('skill_details')->nullable(); // Store skill-specific data (phonetic score, comprehension level, etc.)
            $table->string('difficulty_level')->default('medium'); // easy, medium, hard
            $table->text('feedback')->nullable(); // Teacher or system feedback
            
            // Timestamps
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['student_id', 'classroom_id']);
            $table->index(['classroom_id', 'skill_type']);
            $table->index(['student_id', 'skill_type']);
            $table->index(['is_correct']);
            $table->index(['responded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_responses');
    }
};
