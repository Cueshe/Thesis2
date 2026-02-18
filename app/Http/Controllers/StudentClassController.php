<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\QuestPerformanceTracking;
use App\Models\Classroom;
use App\Models\ClassAnnouncement;
use App\Models\Quest;
use App\Models\StudentProfile;
use App\Models\StudentPerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentClassController extends Controller
{
    use QuestPerformanceTracking;
    public function show($classId)
    {
        try {
            // Debug: Log the method entry
            \Log::info('show called', ['classId' => $classId]);
            
            $student = Auth::user();
            
            // Debug: Log authentication status
            \Log::info('Authentication check in show', ['student' => $student ? 'authenticated' : 'not authenticated']);
            
            if (!$student) {
                return redirect()->route('login')
                    ->with('error', 'Please log in to access your classes.');
            }

            // Try to find the classroom
            $classroom = Classroom::find($classId);
            
            // Real-time check if classroom exists
            if (!$classroom) {
                // Clean up any references to this deleted class
                $this->cleanupDeletedClassReferences($student, $classId);
                
                return redirect()->route('student.dashboard')
                    ->with('error', 'This class is already deleted.');
            }

            // Real-time check if student is still enrolled
            if (!$this->studentIsInClass($student, $classroom)) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'This class is already deleted or you are no longer enrolled.');
            }

            $announcements = $classroom->announcements()->latest()->take(10)->get();

            // Get available quests for this classroom
            \Log::info('Loading quests for classroom', ['classroom_id' => $classroom->id]);
            $quests = Quest::where('classroom_id', $classroom->id)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();
            
            \Log::info('Quests loaded', [
                'count' => $quests->count(),
                'quests' => $quests->map(function($q) { return ['id' => $q->id, 'title' => $q->title, 'type' => $q->type]; })->toArray()
            ]);

            // Get student's performance data for completed quests
            $studentId = $student->id; // Store ID to avoid potential scope issues
            $completedQuests = StudentPerformance::where('student_id', $studentId)
                ->where('classroom_id', $classroom->id)
                ->whereNotNull('completed_at')
                ->pluck('quest_id')
                ->toArray();

            // Mark quests as completed or available
            $questsData = $quests->map(function ($quest) use ($completedQuests, $studentId) {
                $questData = $quest->toArray();
                $questData['is_completed'] = in_array($quest->id, $completedQuests);
                $questData['can_attempt'] = !$questData['is_completed'];
                
                // Get student's best performance for this quest if completed
                if ($questData['is_completed']) {
                    $bestPerformance = StudentPerformance::where('student_id', $studentId)
                        ->where('quest_id', $quest->id)
                        ->orderBy('accuracy_percentage', 'desc')
                        ->first();
                    
                    $questData['best_accuracy'] = $bestPerformance ? $bestPerformance->accuracy_percentage : 0;
                    $questData['attempts'] = StudentPerformance::where('student_id', $studentId)
                        ->where('quest_id', $quest->id)
                        ->count();
                }
                
                return $questData;
            });

            // Debug: Log before rendering view
            \Log::info('About to render class dashboard view', [
                'classroom_id' => $classroom->id,
                'quests_count' => $questsData->count()
            ]);

            $classProgress = $this->getClassProgress($student->id, $classroom->id);

            return view('student.class-dashboard', [
                'classroom' => $classroom,
                'announcements' => $announcements,
                'quests' => $questsData,
                'classProgress' => $classProgress,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in show: ' . $e->getMessage(), [
                'classId' => $classId,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a user-friendly error
            return redirect()->route('student.dashboard')
                ->with('error', 'An error occurred while loading the class. Please try again.');
        }
    }

    /**
     * Clean up references to deleted class from student's session and profile
     */
    private function cleanupDeletedClassReferences($student, $classId)
    {
        // Remove from session
        $joinedClasses = collect(session('joined_classes', []));
        $filteredClasses = $joinedClasses->filter(fn ($item) => (int) ($item['id'] ?? 0) !== (int) $classId);
        session(['joined_classes' => $filteredClasses->all()]);
        
        // Remove from student profile
        $profile = $student->studentProfile;
        if ($profile && $profile->classroom_id == $classId) {
            $profile->classroom_id = null;
            $profile->save();
        }
    }

    /**
     * Handle deleted class access and cleanup
     */
    public function handleDeletedClass($classId)
    {
        $student = Auth::user();
        
        if (!$student) {
            return redirect()->route('login')
                ->with('error', 'Please log in to access your classes.');
        }
        
        // Remove from session
        $joinedClasses = collect(session('joined_classes', []));
        $filteredClasses = $joinedClasses->filter(fn ($item) => (int) ($item['id'] ?? 0) !== (int) $classId);
        session(['joined_classes' => $filteredClasses->all()]);
        
        // Remove from student profile
        $profile = $student->studentProfile;
        if ($profile && $profile->classroom_id == $classId) {
            $profile->classroom_id = null;
            $profile->save();
        }
        
        return redirect()->route('student.dashboard')
            ->with('error', 'This class has been deleted by the teacher.');
    }

    protected function studentIsInClass($student, Classroom $classroom): bool
    {
        \Log::info('Checking if student is in class', [
            'student_id' => $student->id,
            'classroom_id' => $classroom->id,
            'classroom_name' => $classroom->name
        ]);
        
        $joinedClasses = collect(session('joined_classes', []));
        \Log::info('Joined classes from session', ['joined_classes' => $joinedClasses->toArray()]);

        $byId = $joinedClasses->contains(fn ($item) => (int) ($item['id'] ?? 0) === $classroom->id);
        \Log::info('Check by session ID', ['byId' => $byId, 'classroom_id' => $classroom->id]);

        if ($byId) {
            return true;
        }

        $profile = $student->studentProfile;
        \Log::info('Student profile check', [
            'profile_exists' => $profile ? 'yes' : 'no',
            'profile_classroom_id' => $profile ? $profile->classroom_id : 'null',
            'expected_classroom_id' => $classroom->id
        ]);
        
        $profileMatch = $profile && (int) $profile->classroom_id === $classroom->id;
        \Log::info('Profile match result', ['profileMatch' => $profileMatch]);
        
        if ($profileMatch) {
            // Add to session if valid
            if (!$byId) {
                $joinedClasses->push([
                    'id' => $classroom->id,
                    'name' => $classroom->name ?? 'Class',
                    'join_code' => strtoupper($classroom->join_code ?? ''),
                    'schedule' => $classroom->schedule ?? 'Schedule to be announced',
                    'slug' => $classroom->slug ?? \Illuminate\Support\Str::slug($classroom->name ?? 'class'),
                ]);
                session(['joined_classes' => $joinedClasses->all()]);
                \Log::info('Added class to session', ['classroom_id' => $classroom->id]);
            }
            return true;
        }
        
        \Log::info('Student not in class', [
            'student_id' => $student->id,
            'classroom_id' => $classroom->id,
            'reason' => 'No session match and no profile match'
        ]);
        
        return false;
    }

    /**
     * Leave a class - remove from session and database.
     */
    public function leaveClass($classId)
    {
        $student = Auth::user();
        
        if (!$student) {
            return redirect()->route('login')
                ->with('error', 'Please log in to access your classes.');
        }

        // Try to find the classroom
        $classroom = Classroom::find($classId);
        
        if (!$classroom) {
            return redirect()->route('student.dashboard')
                ->with('error', 'This class no longer exists.');
        }

        abort_unless($this->studentIsInClass($student, $classroom), 403);

        // Remove from session
        $joinedClasses = collect(session('joined_classes', []));
        $filteredClasses = $joinedClasses->filter(fn ($item) => (int) ($item['id'] ?? 0) !== $classroom->id);
        session(['joined_classes' => $filteredClasses->all()]);

        // Remove from database
        $profile = $student->studentProfile;
        if ($profile && (int) $profile->classroom_id === $classroom->id) {
            $profile->classroom_id = null;
            $profile->save();
        }

        return redirect()->route('student.dashboard')->with('success', 'You have left ' . ($classroom->name ?? 'the class') . '.');
    }

    /**
     * Show quiz/quest attempt page
     */
    public function showQuest($classId, $questId)
    {
        try {
            // Simple test to identify the issue
            \Log::info('showQuest called', ['classId' => $classId, 'questId' => $questId]);
            
            // Check authentication
            if (!Auth::check()) {
                \Log::error('User not authenticated');
                return redirect()->route('login')->with('error', 'Please log in.');
            }
            
            $student = Auth::user();
            \Log::info('Student authenticated', ['student_id' => $student->id, 'student_name' => $student->name]);
            
            // Simple classroom check
            $classroom = Classroom::find($classId);
            if (!$classroom) {
                \Log::error('Classroom not found', ['classId' => $classId]);
                return redirect()->route('student.dashboard')->with('error', 'Class not found.');
            }
            
            // Simple quest check
            $quest = Quest::find($questId);
            if (!$quest) {
                \Log::error('Quest not found', ['questId' => $questId]);
                return redirect()->route('student.dashboard')->with('error', 'Quest not found.');
            }
            
            // Verify quest belongs to classroom
            if ($quest->classroom_id != $classId) {
                \Log::error('Quest does not belong to classroom', ['quest_id' => $questId, 'classroom_id' => $classId, 'quest_classroom_id' => $quest->classroom_id]);
                return redirect()->route('student.dashboard')->with('error', 'Quest not available for this class.');
            }
            
            \Log::info('All checks passed, rendering view');
            
            // Render the actual quest attempt view
            return view('student.quest-attempt', [
                'classroom' => $classroom,
                'quest' => $quest,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in showQuest: ' . $e->getMessage(), [
                'classId' => $classId,
                'questId' => $questId,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('student.dashboard')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Submit quiz/quest answers
     */
    public function submitQuest(Request $request, $classId, $questId)
    {
        $student = Auth::user();
        
        if (!$student) {
            return redirect()->route('login')
                ->with('error', 'Please log in to access your classes.');
        }
        
        // Verify classroom exists and student is enrolled
        $classroom = Classroom::findOrFail($classId);
        if (!$this->studentIsInClass($student, $classroom)) {
            abort(403, 'You are not enrolled in this class.');
        }

        // Get the quest
        $quest = Quest::where('id', $questId)
            ->where('classroom_id', $classId)
            ->where('status', 'active')
            ->firstOrFail();

        // Check if student has already completed this quest
        $studentId = $student->id; // Store ID to avoid potential scope issues
        $completedQuest = StudentPerformance::where('student_id', $studentId)
            ->where('quest_id', $questId)
            ->whereNotNull('completed_at')
            ->first();

        if ($completedQuest) {
            return response()->json([
                'success' => false,
                'message' => 'You have already completed this quest.'
            ], 400);
        }

        // Validate the submission
        $validated = $request->validate([
            'answers' => 'required|array',
            'time_spent_minutes' => 'required|integer|min:1',
        ]);

        try {
            // Process answers based on quest type
            $results = $this->processQuestAnswers($quest, $validated['answers'], $student);
            
            // Record performance using the trait method
            $performanceData = array_merge($results, [
                'total_score' => $results['correct_count'],
                'max_score' => $results['total_questions'],
                'time_spent_minutes' => $validated['time_spent_minutes'],
                'attempts_count' => 1,
            ]);

            // Use the QuestPerformanceTracking trait to record performance
            $this->recordQuestCompletion($student->id, $questId, $performanceData);

            // Award scaled points based on accuracy
            $earnedPoints = $this->calculateQuestXp(
                $quest->reward_points,
                $results['correct_count'],
                $results['total_questions']
            );

            if ($earnedPoints > 0) {
                $student->addPoints($earnedPoints);
            }

            $student->updateStreak();

            try {
                $completedCount = StudentPerformance::where('student_id', $student->id)
                    ->whereNotNull('completed_at')
                    ->count();

                $perfectCount = StudentPerformance::where('student_id', $student->id)
                    ->whereNotNull('completed_at')
                    ->where('accuracy_percentage', '>=', 100)
                    ->count();

                if ($completedCount >= 1) {
                    $student->addAchievement('first_steps');
                }
                if ($completedCount >= 5) {
                    $student->addAchievement('quick_learner');
                }
                if (($student->streak_days ?? 0) >= 7) {
                    $student->addAchievement('dedicated');
                }
                if (($student->level ?? 1) >= 2) {
                    $student->addAchievement('level_up_2');
                }
                if (($student->level ?? 1) >= 5) {
                    $student->addAchievement('level_up_5');
                }
                if (($student->level ?? 1) >= 10) {
                    $student->addAchievement('expert');
                }
                if ($perfectCount >= 10) {
                    $student->addAchievement('perfectionist');
                }

                $rank = User::where('role', 'student')
                    ->where(function ($query) use ($student) {
                        $query->where('points', '>', $student->points ?? 0)
                            ->orWhere(function ($q) use ($student) {
                                $q->where('points', '=', $student->points ?? 0)
                                    ->where('level', '>', $student->level ?? 1);
                            })
                            ->orWhere(function ($q) use ($student) {
                                $q->where('points', '=', $student->points ?? 0)
                                    ->where('level', '=', $student->level ?? 1)
                                    ->where('experience', '>', $student->experience ?? 0);
                            });
                    })
                    ->count() + 1;

                if ($rank <= 10) {
                    $student->addAchievement('champion');
                }
            } catch (\Exception $e) {
                // Don't block quest submission if achievement awarding fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Quest completed successfully!',
                'results' => [
                    'correct' => $results['correct_count'],
                    'total' => $results['total_questions'],
                    'accuracy' => round(($results['correct_count'] / $results['total_questions']) * 100, 2),
                    'points_earned' => $earnedPoints,
                    'new_level' => $student->level,
                    'new_points' => $student->points,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Quest submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit quest. Please try again.'
            ], 500);
        }
    }

    /**
     * Process quest answers based on quest type
     */
    private function processQuestAnswers($quest, $answers, $student)
    {
        $content = $quest->content;
        if (!is_array($content)) {
            $content = json_decode($content, true) ?: [];
        }

        switch ($quest->type) {
            case 'pronunciation':
                if (isset($content['pronunciation_exercises'])) {
                    return $this->processPronunciationAnswers($content, $answers);
                }
                return $this->processSimplePronunciation($content, $answers);
            case 'reading':
                if (isset($content['reading_exercises'])) {
                    return $this->processReadingAnswers($content, $answers);
                }
                return $this->processSimpleQA($content, $answers);
            case 'mixed':
                if (isset($content['pronunciation_exercises']) || isset($content['reading_exercises'])) {
                    $pronunciationAnswers = $answers['pronunciation'] ?? $answers;
                    $readingAnswers = $answers['reading'] ?? $answers;

                    if (!is_array($pronunciationAnswers)) {
                        $pronunciationAnswers = $answers;
                    }

                    if (!is_array($readingAnswers)) {
                        $readingAnswers = $answers;
                    }

                    $pronunciationResults = $this->processPronunciationAnswers($content, $pronunciationAnswers);
                    $readingResults = $this->processReadingAnswers($content, $readingAnswers);

                    return [
                        'correct_count' => $pronunciationResults['correct_count'] + $readingResults['correct_count'],
                        'total_questions' => $pronunciationResults['total_questions'] + $readingResults['total_questions'],
                        'pronunciation_scores' => $pronunciationResults['pronunciation_scores'],
                        'reading_scores' => $readingResults['reading_scores'],
                    ];
                }
                return $this->processSimpleQA($content, $answers);
            case 'pdf':
                $pdfActivityType = $content['pdf_activity_type'] ?? 'read';
                if ($pdfActivityType === 'pronunciation') {
                    // Generate pronunciation exercises from PDF text
                    $pronunciationExercises = $this->generatePronunciationFromPdfText($content['pdf_text'] ?? '');
                    return [
                        'correct_count' => 0,
                        'total_questions' => count($pronunciationExercises),
                        'pronunciation_scores' => [],
                        'pdf_pronunciation_exercises' => $pronunciationExercises,
                    ];
                }
                return [
                    'correct_count' => 0,
                    'total_questions' => 0,
                    'reading_scores' => [],
                ];
            default:
                return $this->processSimpleQA($content, $answers);
        }
    }

    /**
     * Generate pronunciation exercises from PDF text
     */
    private function generatePronunciationFromPdfText($pdfText)
    {
        // Extract words from PDF text (simple approach)
        $words = preg_split('/[\s,;:.\-]+/', $pdfText);
        $pronunciationExercises = [];
        
        foreach ($words as $word) {
            $word = trim($word);
            // Filter: keep only alphabetic words, 3-15 characters
            if (strlen($word) >= 3 && strlen($word) <= 15 && preg_match('/^[a-zA-Z]+$/', $word)) {
                $pronunciationExercises[] = [
                    'word' => $word,
                    'phonetic' => '[' . strtoupper($word) . ']',
                    'practice_sentence' => "Please practice saying the word: {$word}.",
                    'difficulty' => 'medium'
                ];
                
                // Limit to 10 exercises
                if (count($pronunciationExercises) >= 10) {
                    break;
                }
            }
        }
        
        return $pronunciationExercises;
    }

    private function processSimpleQA($content, $answers)
    {
        $question = $content['question'] ?? '';
        $correctAnswer = $content['answer'] ?? '';
        $studentAnswer = $answers[0] ?? '';
        $isCorrect = strtolower(trim($studentAnswer)) === strtolower(trim($correctAnswer));

        return [
            'correct_count' => $isCorrect ? 1 : 0,
            'total_questions' => 1,
            'reading_scores' => [[
                'question' => $question,
                'question_type' => 'short_answer',
                'student_answer' => $studentAnswer,
                'correct_answer' => $correctAnswer,
                'correct' => $isCorrect,
                'accuracy' => $isCorrect ? 100 : 0,
                'attempts' => 1,
                'difficulty' => 'medium',
                'response_time' => null,
            ]],
        ];
    }

    private function processPronunciationAnswers($content, $answers)
    {
        $pronunciationExercises = $content['pronunciation_exercises'] ?? [];
        $correctCount = 0;
        $scores = [];

        foreach ($pronunciationExercises as $index => $exercise) {
            $word = $exercise['word'] ?? '';
            $studentAnswer = $answers[$index] ?? '';
            $isCorrect = strtolower(trim($studentAnswer)) === strtolower(trim($word));

            if ($isCorrect) {
                $correctCount++;
            }

            $scores[] = [
                'word' => $word,
                'student_response' => $studentAnswer,
                'correct' => $isCorrect,
                'accuracy' => $isCorrect ? 100 : 0,
                'attempts' => 1,
                'difficulty' => $exercise['difficulty'] ?? 'medium',
            ];
        }

        return [
            'correct_count' => $correctCount,
            'total_questions' => count($pronunciationExercises),
            'pronunciation_scores' => $scores,
        ];
    }

    private function processReadingAnswers($content, $answers)
    {
        $readingExercises = $content['reading_exercises'] ?? [];
        $correctCount = 0;
        $scores = [];

        foreach ($readingExercises as $index => $exercise) {
            $question = $exercise['question'] ?? '';
            $correctAnswer = $exercise['answer'] ?? 'A';
            $studentAnswer = $answers[$index] ?? '';
            $isCorrect = strtoupper(trim($studentAnswer)) === strtoupper(trim($correctAnswer));

            if ($isCorrect) {
                $correctCount++;
            }

            $scores[] = [
                'question' => $question,
                'question_type' => 'multiple_choice',
                'student_answer' => $studentAnswer,
                'correct_answer' => $correctAnswer,
                'correct' => $isCorrect,
                'accuracy' => $isCorrect ? 100 : 0,
                'attempts' => 1,
                'difficulty' => $exercise['difficulty'] ?? 'medium',
                'response_time' => null,
            ];
        }

        return [
            'correct_count' => $correctCount,
            'total_questions' => count($readingExercises),
            'reading_scores' => $scores,
        ];
    }

    private function processSimplePronunciation($content, $answers)
    {
        $word = $content['word'] ?? ($content['answer'] ?? '');
        $correctAnswer = $content['answer'] ?? $word;
        $studentAnswer = $answers[0] ?? '';
        $isCorrect = strtolower(trim($studentAnswer)) === strtolower(trim($correctAnswer));

        return [
            'correct_count' => $isCorrect ? 1 : 0,
            'total_questions' => 1,
            'pronunciation_scores' => [[
                'word' => $word,
                'student_response' => $studentAnswer,
                'correct' => $isCorrect,
                'accuracy' => $isCorrect ? 100 : 0,
                'attempts' => 1,
                'difficulty' => 'medium',
            ]],
        ];
    }

    private function getClassProgress(int $studentId, int $classroomId): array
    {
        $performances = StudentPerformance::where('student_id', $studentId)
            ->where('classroom_id', $classroomId)
            ->whereNotNull('completed_at')
            ->with('quest')
            ->get();

        $totalXp = $performances->sum(function ($performance) {
            if (isset($performance->earned_xp)) {
                return $performance->earned_xp;
            }

            $reward = $performance->quest?->reward_points ?? 0;
            return $reward;
        });

        $levelStats = $this->calculateLevelStats($totalXp);

        return array_merge($levelStats, [
            'xp_total' => $totalXp,
            'completed_quests' => $performances->count(),
        ]);
    }

    private function calculateLevelStats(int $totalXp): array
    {
        $level = 1;
        $xpNeeded = 100;
        $remainingXp = $totalXp;

        while ($remainingXp >= $xpNeeded) {
            $remainingXp -= $xpNeeded;
            $level++;
            $xpNeeded = $level * 100;
        }

        $progressPercent = $xpNeeded > 0 ? ($remainingXp / $xpNeeded) * 100 : 0;

        return [
            'level' => $level,
            'xp_into_level' => $remainingXp,
            'xp_for_next_level' => $xpNeeded,
            'progress_percent' => round($progressPercent, 1),
        ];
    }

    private function calculateQuestXp(int $rewardPoints, int $correctCount, int $totalQuestions): int
    {
        if ($rewardPoints <= 0) {
            return 0;
        }

        if ($totalQuestions > 0) {
            $ratio = max(0, min(1, $correctCount / $totalQuestions));
            return (int) round($rewardPoints * $ratio);
        }

        return $rewardPoints;
    }
}
