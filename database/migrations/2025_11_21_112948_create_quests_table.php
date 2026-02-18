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
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['pronunciation', 'reading', 'mixed']);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->json('content'); // Stores the AI-generated exercises
            $table->string('estimated_time')->default('15 minutes');
            $table->integer('reward_points')->default(50);
            $table->enum('status', ['draft', 'active', 'archived'])->default('active');
            $table->timestamp('available_until')->nullable();
            $table->timestamps();
            
            $table->index(['classroom_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quests');
    }
};
