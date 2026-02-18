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
        Schema::create('student_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('quest_id')->nullable()->constrained()->onDelete('cascade');
            
            // Performance Metrics
            $table->string('activity_type'); // 'pronunciation', 'reading', 'mixed', 'general'
            $table->integer('total_score')->default(0);
            $table->integer('max_score')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0);
            $table->integer('time_spent_minutes')->default(0);
            $table->integer('attempts_count')->default(1);
            
            // Skill-specific metrics
            $table->json('pronunciation_scores')->nullable(); // Store individual word scores
            $table->json('reading_scores')->nullable(); // Store reading comprehension scores
            $table->decimal('pronunciation_accuracy', 5, 2)->nullable();
            $table->decimal('reading_comprehension', 5, 2)->nullable();
            
            // Progress tracking
            $table->decimal('improvement_rate', 5, 2)->default(0); // Percentage improvement over time
            $table->integer('streak_bonus')->default(0);
            $table->json('difficulty_levels')->nullable(); // Track performance by difficulty
            
            // Timestamps
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance queries
            $table->index(['student_id', 'classroom_id']);
            $table->index(['classroom_id', 'activity_type']);
            $table->index(['student_id', 'activity_type']);
            $table->index(['completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_performances');
    }
};
