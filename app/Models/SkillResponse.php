<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'classroom_id',
        'quest_id',
        'performance_id',
        'skill_type',
        'problem_type',
        'problem_content',
        'student_response',
        'correct_answer',
        'is_correct',
        'accuracy_score',
        'attempts',
        'response_time_seconds',
        'skill_details',
        'difficulty_level',
        'feedback',
        'responded_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'accuracy_score' => 'decimal:2',
        'skill_details' => 'json',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the student who made this response.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the classroom for this response.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get the quest for this response.
     */
    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }

    /**
     * Get the performance record for this response.
     */
    public function performance(): BelongsTo
    {
        return $this->belongsTo(StudentPerformance::class, 'performance_id');
    }

    /**
     * Scope for correct responses.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope for incorrect responses.
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    /**
     * Scope for specific skill type.
     */
    public function scopeForSkill($query, string $skillType)
    {
        return $query->where('skill_type', $skillType);
    }

    /**
     * Scope for specific difficulty level.
     */
    public function scopeForDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    /**
     * Get skill type display name.
     */
    public function getSkillTypeDisplayNameAttribute(): string
    {
        return match($this->skill_type) {
            'pronunciation' => 'Pronunciation',
            'reading_comprehension' => 'Reading Comprehension',
            'vocabulary' => 'Vocabulary',
            'grammar' => 'Grammar',
            'listening' => 'Listening',
            default => ucfirst($this->skill_type),
        };
    }

    /**
     * Get problem type display name.
     */
    public function getProblemTypeDisplayNameAttribute(): string
    {
        return match($this->problem_type) {
            'word_pronunciation' => 'Word Pronunciation',
            'sentence_reading' => 'Sentence Reading',
            'multiple_choice' => 'Multiple Choice',
            'fill_blank' => 'Fill in the Blank',
            'true_false' => 'True/False',
            'short_answer' => 'Short Answer',
            default => ucfirst($this->problem_type),
        };
    }

    /**
     * Get difficulty level color.
     */
    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty_level) {
            'easy' => 'green',
            'medium' => 'yellow',
            'hard' => 'red',
            default => 'gray',
        };
    }

    /**
     * Create a skill response from quest completion data.
     */
    public static function createFromQuestCompletion($studentId, $classroomId, $questId, $performanceData): array
    {
        $responses = [];
        
        // Handle pronunciation responses
        if (isset($performanceData['pronunciation_scores']) && is_array($performanceData['pronunciation_scores'])) {
            foreach ($performanceData['pronunciation_scores'] as $score) {
                $responses[] = static::create([
                    'student_id' => $studentId,
                    'classroom_id' => $classroomId,
                    'quest_id' => $questId,
                    'skill_type' => 'pronunciation',
                    'problem_type' => 'word_pronunciation',
                    'problem_content' => $score['word'] ?? '',
                    'student_response' => $score['student_response'] ?? $score['word'] ?? '',
                    'correct_answer' => $score['word'] ?? '',
                    'is_correct' => $score['correct'] ?? false,
                    'accuracy_score' => $score['accuracy'] ?? ($score['correct'] ? 100 : 0),
                    'attempts' => $score['attempts'] ?? 1,
                    'skill_details' => [
                        'phonetic_score' => $score['phonetic_score'] ?? null,
                        'pronunciation_quality' => $score['quality'] ?? 'unknown',
                    ],
                    'difficulty_level' => $score['difficulty'] ?? 'medium',
                    'responded_at' => now(),
                ]);
            }
        }
        
        // Handle reading comprehension responses
        if (isset($performanceData['reading_scores']) && is_array($performanceData['reading_scores'])) {
            foreach ($performanceData['reading_scores'] as $score) {
                $responses[] = static::create([
                    'student_id' => $studentId,
                    'classroom_id' => $classroomId,
                    'quest_id' => $questId,
                    'skill_type' => 'reading_comprehension',
                    'problem_type' => $score['question_type'] ?? 'multiple_choice',
                    'problem_content' => $score['question'] ?? '',
                    'student_response' => $score['student_answer'] ?? '',
                    'correct_answer' => $score['correct_answer'] ?? '',
                    'is_correct' => $score['correct'] ?? false,
                    'accuracy_score' => $score['accuracy'] ?? ($score['correct'] ? 100 : 0),
                    'attempts' => $score['attempts'] ?? 1,
                    'response_time_seconds' => $score['response_time'] ?? null,
                    'skill_details' => [
                        'comprehension_level' => $score['comprehension_level'] ?? 'basic',
                        'question_category' => $score['category'] ?? 'general',
                    ],
                    'difficulty_level' => $score['difficulty'] ?? 'medium',
                    'responded_at' => now(),
                ]);
            }
        }
        
        return $responses;
    }
}
