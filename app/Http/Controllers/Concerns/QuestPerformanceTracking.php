<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use App\Models\Quest;
use App\Models\StudentPerformance;
use App\Models\SkillResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait QuestPerformanceTracking
{
    /**
     * Record quest completion performance for a student
     * 
     * @param int $studentId
     * @param int $questId
     * @param array $performanceData
     * @return StudentPerformance
     */
    public function recordQuestCompletion($studentId, $questId, $performanceData)
    {
        $quest = Quest::findOrFail($questId);
        $student = User::findOrFail($studentId);
        
        // Validate required fields
        $requiredFields = ['total_score', 'max_score', 'time_spent_minutes'];
        foreach ($requiredFields as $field) {
            if (!isset($performanceData[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }
        
        // Calculate accuracy percentage
        $accuracyPercentage = ($performanceData['total_score'] / $performanceData['max_score']) * 100;
        
        // Determine activity type based on quest type
        $activityType = $this->mapQuestTypeToActivityType($quest->type);
        
        // Prepare performance data
        $performanceRecord = array_merge($performanceData, [
            'student_id' => $studentId,
            'classroom_id' => $quest->classroom_id,
            'quest_id' => $questId,
            'activity_type' => $activityType,
            'accuracy_percentage' => round($accuracyPercentage, 2),
            'attempts_count' => $performanceData['attempts_count'] ?? 1,
            'completed_at' => now(),
        ]);
        
        // Add skill-specific metrics based on quest type
        if ($quest->type === 'pronunciation' && isset($performanceData['pronunciation_scores'])) {
            $performanceRecord['pronunciation_scores'] = $performanceData['pronunciation_scores'];
            $performanceRecord['pronunciation_accuracy'] = $this->calculatePronunciationAccuracy($performanceData['pronunciation_scores']);
        }
        
        if ($quest->type === 'reading' && isset($performanceData['reading_scores'])) {
            $performanceRecord['reading_scores'] = $performanceData['reading_scores'];
            $performanceRecord['reading_comprehension'] = $this->calculateReadingComprehension($performanceData['reading_scores']);
        }
        
        if ($quest->type === 'mixed') {
            if (isset($performanceData['pronunciation_scores'])) {
                $performanceRecord['pronunciation_scores'] = $performanceData['pronunciation_scores'];
                $performanceRecord['pronunciation_accuracy'] = $this->calculatePronunciationAccuracy($performanceData['pronunciation_scores']);
            }
            if (isset($performanceData['reading_scores'])) {
                $performanceRecord['reading_scores'] = $performanceData['reading_scores'];
                $performanceRecord['reading_comprehension'] = $this->calculateReadingComprehension($performanceData['reading_scores']);
            }
        }
        
        // Calculate improvement rate
        $performanceRecord['improvement_rate'] = $this->calculateImprovementRate($studentId, $quest->classroom_id, $activityType, $accuracyPercentage);
        
        // Record the performance
        return StudentPerformance::recordPerformance($performanceRecord);
    }
    
    /**
     * Map quest type to activity type for performance tracking
     */
    private function mapQuestTypeToActivityType($questType)
    {
        $mapping = [
            'pronunciation' => 'pronunciation',
            'reading' => 'reading',
            'mixed' => 'mixed',
            'vocabulary' => 'general',
            'grammar' => 'general',
            'comprehension' => 'reading',
            'listening' => 'general',
        ];
        
        return $mapping[$questType] ?? 'general';
    }
    
    /**
     * Calculate pronunciation accuracy from scores array
     */
    private function calculatePronunciationAccuracy($scores)
    {
        if (empty($scores)) {
            return 0;
        }
        
        $totalCorrect = collect($scores)->sum(function ($score) {
            return is_array($score) ? ($score['correct'] ?? 0) : ($score ? 1 : 0);
        });
        
        return round(($totalCorrect / count($scores)) * 100, 2);
    }
    
    /**
     * Calculate reading comprehension from scores array
     */
    private function calculateReadingComprehension($scores)
    {
        if (empty($scores)) {
            return 0;
        }
        
        $totalCorrect = collect($scores)->sum(function ($score) {
            return is_array($score) ? ($score['correct'] ?? 0) : ($score ? 1 : 0);
        });
        
        return round(($totalCorrect / count($scores)) * 100, 2);
    }
    
    /**
     * Calculate improvement rate compared to previous performances
     */
    private function calculateImprovementRate($studentId, $classroomId, $activityType, $currentAccuracy)
    {
        // Get previous performances of the same activity type
        $previousPerformances = StudentPerformance::where('student_id', $studentId)
            ->where('classroom_id', $classroomId)
            ->where('activity_type', $activityType)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->take(3) // Compare with last 3 performances
            ->get();
            
        if ($previousPerformances->isEmpty()) {
            return 0; // No previous data, no improvement rate
        }
        
        $averagePreviousAccuracy = $previousPerformances->avg('accuracy_percentage');
        
        if ($averagePreviousAccuracy == 0) {
            return 0; // Avoid division by zero
        }
        
        $improvementRate = (($currentAccuracy - $averagePreviousAccuracy) / $averagePreviousAccuracy) * 100;
        
        return round($improvementRate, 2);
    }
    
    /**
     * API endpoint for students to submit quest completion
     */
    public function submitQuestCompletion(Request $request, $questId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return response()->json(['error' => 'Only students can submit quest completions'], 403);
        }
        
        $quest = Quest::findOrFail($questId);
        
        // Verify student is enrolled in the classroom
        $studentProfile = $user->studentProfile;
        if (!$studentProfile || $studentProfile->classroom_id !== $quest->classroom_id) {
            return response()->json(['error' => 'Student not enrolled in this classroom'], 403);
        }
        
        // Validate performance data
        $validated = $request->validate([
            'total_score' => 'required|integer|min:0',
            'max_score' => 'required|integer|min:1',
            'time_spent_minutes' => 'required|integer|min:0',
            'attempts_count' => 'nullable|integer|min:1',
            'pronunciation_scores' => 'nullable|array',
            'pronunciation_scores.*.word' => 'required|string',
            'pronunciation_scores.*.correct' => 'required|boolean',
            'pronunciation_scores.*.attempts' => 'nullable|integer|min:1',
            'reading_scores' => 'nullable|array',
            'reading_scores.*.question' => 'required|string',
            'reading_scores.*.correct' => 'required|boolean',
            'reading_scores.*.response_time' => 'nullable|integer',
        ]);
        
        try {
            // Record the performance
            $performance = $this->recordQuestCompletion($user->id, $questId, $validated);
            
            // Record detailed skill responses
            $this->recordSkillResponses($user->id, $quest->classroom_id, $questId, $performance->id, $validated);
            
            // Award points to student
            $user->addPoints($quest->reward_points);
            $user->updateStreak();
            
            return response()->json([
                'success' => true,
                'performance' => [
                    'accuracy' => $performance->accuracy_percentage,
                    'improvement_rate' => $performance->improvement_rate,
                    'points_earned' => $quest->reward_points,
                    'current_level' => $user->level,
                    'current_points' => $user->points,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to record performance: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Record detailed skill responses for tracking
     */
    private function recordSkillResponses($studentId, $classroomId, $questId, $performanceId, $performanceData)
    {
        try {
            // Record pronunciation skill responses
            if (isset($performanceData['pronunciation_scores']) && is_array($performanceData['pronunciation_scores'])) {
                foreach ($performanceData['pronunciation_scores'] as $score) {
                    SkillResponse::create([
                        'student_id' => $studentId,
                        'classroom_id' => $classroomId,
                        'quest_id' => $questId,
                        'performance_id' => $performanceId,
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
                            'confidence' => $score['confidence'] ?? null,
                        ],
                        'difficulty_level' => $score['difficulty'] ?? 'medium',
                        'responded_at' => now(),
                    ]);
                }
            }
            
            // Record reading comprehension skill responses
            if (isset($performanceData['reading_scores']) && is_array($performanceData['reading_scores'])) {
                foreach ($performanceData['reading_scores'] as $score) {
                    SkillResponse::create([
                        'student_id' => $studentId,
                        'classroom_id' => $classroomId,
                        'quest_id' => $questId,
                        'performance_id' => $performanceId,
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
                            'confidence' => $score['confidence'] ?? null,
                        ],
                        'difficulty_level' => $score['difficulty'] ?? 'medium',
                        'responded_at' => now(),
                    ]);
                }
            }
            
            Log::info('Skill responses recorded for student ' . $studentId . ' in quest ' . $questId);
            
        } catch (\Exception $e) {
            Log::error('Failed to record skill responses: ' . $e->getMessage());
            // Don't throw exception here - performance recording should still succeed
        }
    }
    
    /**
     * Get skill tracking data for teacher observation
     */
    public function getSkillTrackingData($classroomId, $filters = [])
    {
        $query = SkillResponse::with(['student', 'quest'])
            ->where('classroom_id', $classroomId);
        
        // Apply filters
        if (isset($filters['skill_type'])) {
            $query->where('skill_type', $filters['skill_type']);
        }
        
        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }
        
        if (isset($filters['difficulty_level'])) {
            $query->where('difficulty_level', $filters['difficulty_level']);
        }
        
        if (isset($filters['date_from'])) {
            $query->whereDate('responded_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('responded_at', '<=', $filters['date_to']);
        }
        
        $responses = $query->orderBy('responded_at', 'desc')->get();
        
        // Calculate skill statistics
        $skillStats = $this->calculateSkillStatistics($responses);
        
        return [
            'responses' => $responses,
            'statistics' => $skillStats,
            'summary' => $this->generateSkillSummary($responses),
        ];
    }
    
    /**
     * Calculate skill statistics from responses
     */
    private function calculateSkillStatistics($responses)
    {
        $stats = [];
        
        // Group by skill type
        $bySkillType = $responses->groupBy('skill_type');
        foreach ($bySkillType as $skillType => $skillResponses) {
            $total = $skillResponses->count();
            $correct = $skillResponses->where('is_correct', true)->count();
            $avgAccuracy = $skillResponses->avg('accuracy_score');
            $avgResponseTime = $skillResponses->avg('response_time_seconds');
            
            $stats[$skillType] = [
                'total_responses' => $total,
                'correct_responses' => $correct,
                'incorrect_responses' => $total - $correct,
                'accuracy_rate' => round(($correct / $total) * 100, 2),
                'average_accuracy_score' => round($avgAccuracy, 2),
                'average_response_time' => round($avgResponseTime, 2),
                'improvement_trend' => $this->calculateImprovementTrend($skillResponses),
            ];
        }
        
        // Group by difficulty level
        $byDifficulty = $responses->groupBy('difficulty_level');
        foreach ($byDifficulty as $difficulty => $difficultyResponses) {
            $total = $difficultyResponses->count();
            $correct = $difficultyResponses->where('is_correct', true)->count();
            
            $stats['by_difficulty'][$difficulty] = [
                'total_responses' => $total,
                'correct_responses' => $correct,
                'accuracy_rate' => round(($correct / $total) * 100, 2),
            ];
        }
        
        return $stats;
    }
    
    /**
     * Calculate improvement trend for a skill
     */
    private function calculateImprovementTrend($responses)
    {
        if ($responses->count() < 2) {
            return 'insufficient_data';
        }
        
        $sortedResponses = $responses->sortBy('responded_at');
        $firstHalf = $sortedResponses->take(floor($sortedResponses->count() / 2));
        $secondHalf = $sortedResponses->skip(floor($sortedResponses->count() / 2));
        
        $firstHalfAccuracy = $firstHalf->avg('accuracy_score');
        $secondHalfAccuracy = $secondHalf->avg('accuracy_score');
        
        $difference = $secondHalfAccuracy - $firstHalfAccuracy;
        
        if ($difference > 5) {
            return 'improving';
        } elseif ($difference < -5) {
            return 'declining';
        } else {
            return 'stable';
        }
    }
    
    /**
     * Generate skill summary for teacher dashboard
     */
    private function generateSkillSummary($responses)
    {
        $totalResponses = $responses->count();
        if ($totalResponses === 0) {
            return [
                'total_responses' => 0,
                'overall_accuracy' => 0,
                'most_challenging_skill' => null,
                'most_successful_skill' => null,
                'students_needing_attention' => [],
            ];
        }
        
        $overallCorrect = $responses->where('is_correct', true)->count();
        $overallAccuracy = round(($overallCorrect / $totalResponses) * 100, 2);
        
        // Find most challenging and successful skills
        $skillAccuracy = [];
        foreach ($responses->groupBy('skill_type') as $skillType => $skillResponses) {
            $correct = $skillResponses->where('is_correct', true)->count();
            $accuracy = ($correct / $skillResponses->count()) * 100;
            $skillAccuracy[$skillType] = $accuracy;
        }
        
        $mostChallenging = $skillAccuracy ? array_keys($skillAccuracy, min($skillAccuracy))[0] : null;
        $mostSuccessful = $skillAccuracy ? array_keys($skillAccuracy, max($skillAccuracy))[0] : null;
        
        // Find students needing attention (low accuracy in recent responses)
        $studentsNeedingAttention = $responses->groupBy('student_id')
            ->map(function ($studentResponses) {
                $recentResponses = $studentResponses->take(-10); // Last 10 responses
                $accuracy = ($recentResponses->where('is_correct', true)->count() / $recentResponses->count()) * 100;
                return [
                    'student_id' => $studentResponses->first()->student_id,
                    'student_name' => $studentResponses->first()->student->name,
                    'recent_accuracy' => round($accuracy, 2),
                    'total_responses' => $studentResponses->count(),
                ];
            })
            ->filter(function ($student) {
                return $student['recent_accuracy'] < 60; // Below 60% accuracy
            })
            ->sortBy('recent_accuracy')
            ->take(5)
            ->values();
        
        return [
            'total_responses' => $totalResponses,
            'overall_accuracy' => $overallAccuracy,
            'most_challenging_skill' => $mostChallenging,
            'most_successful_skill' => $mostSuccessful,
            'students_needing_attention' => $studentsNeedingAttention,
        ];
    }
    
    /**
     * Get detailed skill analysis for a specific student
     */
    public function getStudentSkillAnalysis($studentId, $classroomId)
    {
        $responses = SkillResponse::with(['quest'])
            ->where('student_id', $studentId)
            ->where('classroom_id', $classroomId)
            ->orderBy('responded_at', 'desc')
            ->get();
        
        // Group responses by skill type
        $bySkillType = $responses->groupBy('skill_type');
        $skillAnalysis = [];
        
        foreach ($bySkillType as $skillType => $skillResponses) {
            $total = $skillResponses->count();
            $correct = $skillResponses->where('is_correct', true)->count();
            $avgAccuracy = $skillResponses->avg('accuracy_score');
            
            // Recent performance (last 10 responses)
            $recentResponses = $skillResponses->take(10);
            $recentCorrect = $recentResponses->where('is_correct', true)->count();
            $recentAccuracy = $recentResponses->count() > 0 ? ($recentCorrect / $recentResponses->count()) * 100 : 0;
            
            // Common mistakes
            $incorrectResponses = $skillResponses->where('is_correct', false);
            $commonMistakes = $incorrectResponses->groupBy('problem_content')
                ->map(function ($mistakes) {
                    return $mistakes->count();
                })
                ->sortDesc()
                ->take(5);
            
            $skillAnalysis[$skillType] = [
                'total_responses' => $total,
                'correct_responses' => $correct,
                'overall_accuracy' => round(($correct / $total) * 100, 2),
                'recent_accuracy' => round($recentAccuracy, 2),
                'average_accuracy_score' => round($avgAccuracy, 2),
                'improvement_trend' => $this->calculateImprovementTrend($skillResponses),
                'common_mistakes' => $commonMistakes,
                'difficulty_breakdown' => $this->getDifficultyBreakdown($skillResponses),
                'recent_responses' => $recentResponses->take(5), // Last 5 responses for detailed view
            ];
        }
        
        return [
            'student_id' => $studentId,
            'skill_analysis' => $skillAnalysis,
            'overall_summary' => [
                'total_responses' => $responses->count(),
                'overall_accuracy' => round(($responses->where('is_correct', true)->count() / $responses->count()) * 100, 2),
                'strongest_skill' => $skillAnalysis ? array_keys(array_column($skillAnalysis, 'overall_accuracy'), max(array_column($skillAnalysis, 'overall_accuracy')))[0] : null,
                'weakest_skill' => $skillAnalysis ? array_keys(array_column($skillAnalysis, 'overall_accuracy'), min(array_column($skillAnalysis, 'overall_accuracy')))[0] : null,
            ],
        ];
    }
    
    /**
     * Get difficulty breakdown for skill responses
     */
    private function getDifficultyBreakdown($responses)
    {
        $byDifficulty = $responses->groupBy('difficulty_level');
        $breakdown = [];
        
        foreach ($byDifficulty as $difficulty => $difficultyResponses) {
            $total = $difficultyResponses->count();
            $correct = $difficultyResponses->where('is_correct', true)->count();
            
            $breakdown[$difficulty] = [
                'total' => $total,
                'correct' => $correct,
                'accuracy' => round(($correct / $total) * 100, 2),
            ];
        }
        
        return $breakdown;
    }
    
    /**
     * Get performance data for a specific quest
     */
    public function getQuestPerformance($questId)
    {
        $teacher = Auth::user();
        
        if ($teacher->role !== 'teacher') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $quest = Quest::where('teacher_id', $teacher->id)->findOrFail($questId);
        
        $performances = StudentPerformance::where('quest_id', $questId)
            ->with('student')
            ->whereNotNull('completed_at')
            ->get()
            ->map(function ($performance) {
                return [
                    'student_name' => $performance->student->name,
                    'student_id' => $performance->student_id,
                    'accuracy_percentage' => $performance->accuracy_percentage,
                    'time_spent_minutes' => $performance->time_spent_minutes,
                    'attempts_count' => $performance->attempts_count,
                    'improvement_rate' => $performance->improvement_rate,
                    'completed_at' => $performance->completed_at->format('M d, Y H:i'),
                    'pronunciation_accuracy' => $performance->pronunciation_accuracy,
                    'reading_comprehension' => $performance->reading_comprehension,
                ];
            });
            
        return response()->json([
            'quest' => [
                'id' => $quest->id,
                'title' => $quest->title,
                'type' => $quest->type,
                'difficulty' => $quest->difficulty,
                'reward_points' => $quest->reward_points,
            ],
            'performances' => $performances,
            'statistics' => [
                'total_completions' => $performances->count(),
                'average_accuracy' => round($performances->avg('accuracy_percentage'), 2),
                'average_time_spent' => round($performances->avg('time_spent_minutes'), 2),
                'average_improvement' => round($performances->avg('improvement_rate'), 2),
            ]
        ]);
    }
}
