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
        Schema::dropIfExists('pronunciation_words');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('pronunciation_words', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('phonetic')->nullable();
            $table->json('tips')->nullable();
            $table->enum('language', ['en-US', 'tl-PH'])->default('en-US');
            $table->enum('mode', ['word', 'phrase', 'sentence'])->default('word');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
};
