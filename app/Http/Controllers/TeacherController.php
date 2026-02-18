<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\QuestPerformanceTracking;
use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Quest;
use App\Models\SkillResponse;
use App\Models\StudentPerformance;
use App\Models\StudentProfile;
use App\Models\TeacherPdf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class TeacherController extends Controller
{
    /**
     * Display the teacher dashboard.
     */
    public function dashboard()
    {
        $classes = Classroom::where('teacher_id', Auth::id())->latest()->get();

        return view('teacher-dashboard', [
            'classes' => $classes,
        ]);
    }

    /**
     * Display the teacher's class dashboard.
     */
    public function classDashboard($class)
    {
        $teacher = Auth::user();
        $teacherName = $teacher->name ?? 'Teacher';

        $classroom = Classroom::where('teacher_id', $teacher->id)
            ->where('id', $class)
            ->orWhere('slug', $class)
            ->firstOrFail();

        // Get actual enrolled students from database
        $studentProfiles = StudentProfile::where('classroom_id', $classroom->id)
            ->with('user')
            ->get()
            ->filter(function ($profile) {
                return !is_null($profile->user);
            });

        $studentUsers = $studentProfiles->map(function ($profile) {
            return $profile->user;
        });

        $classQuestXp = StudentPerformance::where('classroom_id', $classroom->id)
            ->whereNotNull('completed_at')
            ->with('quest')
            ->get()
            ->groupBy('student_id')
            ->map(function ($performances) {
                return $performances->sum(function ($performance) {
                    if (isset($performance->earned_xp)) {
                        return $performance->earned_xp;
                    }

                    return $performance->quest?->reward_points ?? 0;
                });
            });

        $enrolledStudents = $studentUsers
            ->map(function ($user) use ($classQuestXp) {
                $streakDays = max(0, $user->streak_days ?? 0);
                $classXp = $classQuestXp->get($user->id, 0);
                $classStats = $this->calculateClassLevelStats($classXp);

                return [
                    'id' => $user->id,
                    'name' => $user->name ?? 'Unknown Hero',
                    'avatar' => strtoupper(substr($user->name ?? 'H', 0, 1)),
                    'level' => $classStats['level'],
                    'experience' => $classStats['xp_total'],
                    'xp' => $classStats['xp_total'],
                    'level_progress' => $classStats['progress_percent'],
                    'points' => $classStats['xp_total'],
                    'rank' => 0, // Will be calculated after sorting
                    'streak' => $streakDays,
                    'streak_days' => $streakDays,
                    'achievements' => $user->achievements ?? [],
                    'status' => $this->determineStudentStatus($streakDays),
                ];
            })
            ->sort(function ($a, $b) {
                $levelComparison = $b['level'] <=> $a['level'];
                if ($levelComparison !== 0) {
                    return $levelComparison;
                }

                $experienceComparison = $b['experience'] <=> $a['experience'];
                if ($experienceComparison !== 0) {
                    return $experienceComparison;
                }

                $pointsComparison = $b['points'] <=> $a['points'];
                if ($pointsComparison !== 0) {
                    return $pointsComparison;
                }

                return strcmp($a['name'], $b['name']);
            })
            ->values();

        // Assign ranks based on level-focused ordering
        $enrolledStudents = $enrolledStudents->map(function ($student, $index) {
            $student['rank'] = $index + 1;
            return $student;
        });

        // Get actual quests from database
        $quests = Quest::where('classroom_id', $classroom->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($quest) {
                return [
                    'id' => $quest->id,
                    'title' => $quest->title,
                    'type' => ucfirst($quest->type),
                    'difficulty' => ucfirst($quest->difficulty),
                    'reward' => $quest->reward_points . ' XP',
                    'status' => ucfirst($quest->status),
                    'created_at' => $quest->created_at->format('M d, Y'),
                    'estimated_time' => $quest->estimated_time,
                ];
            })
            ->toArray();

        // Get sample data for other sections
        $sampleClasses = collect(config('classrooms.classes', []));
        $sample = $sampleClasses->firstWhere(fn ($class) => ($class['slug'] ?? null) === $classroom->slug)
            ?? $sampleClasses->first();

        $classData = [
            'name' => $classroom->name,
            'join_code' => $classroom->join_code,
            'schedule' => $classroom->schedule ?? ($sample['schedule'] ?? 'Schedule coming soon'),
            'streak_days' => $sample['streak_days'] ?? 0,
            'coins' => $sample['coins'] ?? 0,
            'xp' => $sample['xp'] ?? 0,
            'leaderboard' => $enrolledStudents->take(10)->toArray(), // Top 10 students
            'quests' => $quests, // Use actual quests from database
            'students' => $enrolledStudents->toArray(), // All enrolled students
            'announcements' => $sample['announcements'] ?? [],
            'live_buff' => $classroom->live_buff ?? ($sample['live_buff'] ?? 'Momentum Aura'),
            'coin_bonus' => $classroom->coin_bonus ?? ($sample['coin_bonus'] ?? 0),
            'mentor' => $teacherName,
            'slug' => $classroom->slug,
            // Gamification statistics
            'rank' => $this->getClassRank($classroom),
            'avg_level' => $this->getAverageLevel($enrolledStudents),
            'active_streaks' => $this->getActiveStreaks($studentUsers),
            'total_achievements' => $this->getTotalAchievements($studentUsers),
        ];

        return view('teacher.class-dashboard', [
            'classData' => $classData,
            'classroom' => $classroom,
            'researchMetrics' => $this->getResearchFocusMetrics($classroom),
        ]);
    }

    /**
     * Show individual quest content
     */
    public function showQuest(Classroom $classroom, Quest $quest)
    {
        // Verify the quest belongs to this classroom
        if ($quest->classroom_id !== $classroom->id) {
            abort(404);
        }

        // Verify the teacher owns this classroom
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'quest' => [
                'id' => $quest->id,
                'title' => $quest->title,
                'description' => $quest->description,
                'type' => ucfirst($quest->type),
                'difficulty' => ucfirst($quest->difficulty),
                'content' => $quest->content,
                'estimated_time' => $quest->estimated_time,
                'reward_points' => $quest->reward_points,
                'status' => ucfirst($quest->status),
                'created_at' => $quest->created_at->format('M d, Y'),
            ]
        ]);
    }

    /**
     * Delete a quest
     */
    public function deleteQuest(Classroom $classroom, Quest $quest)
    {
        // Verify the quest belongs to this classroom
        if ($quest->classroom_id !== $classroom->id) {
            abort(404);
        }

        // Verify the teacher owns this classroom
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403);
        }

        // Delete the quest
        $quest->delete();

        return response()->json([
            'message' => 'Quest deleted successfully',
            'status' => 'success'
        ]);
    }

    /**
     * Store a new classroom for the authenticated teacher.
     */
    public function storeClass(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'live_buff' => ['nullable', 'string', 'max:255'],
            'coin_bonus' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $classroom = Classroom::create([
            'teacher_id' => $teacher->id,
            'name' => $validated['name'],
            'schedule' => $validated['schedule'] ?? null,
            'live_buff' => $validated['live_buff'] ?? null,
            'coin_bonus' => $validated['coin_bonus'] ?? 0,
        ]);

        return redirect()
            ->route('teacher.dashboard')
            ->with('success', 'Class "' . $classroom->name . '" created! Share the join code ' . $classroom->join_code . ' with your students.');
    }

    /**
     * Update the authenticated teacher's settings.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->subject = $validated['subject'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->must_change_password = false;
        }

        $user->save();

        return response()->json([
            'message' => 'Settings updated successfully.',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'subject' => $user->subject,
            ],
        ]);
    }

    /**
     * Delete a class and remove all students.
     */
    public function deleteClass(Classroom $classroom)
    {
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403);
        }

        // Remove all students from this class
        StudentProfile::where('classroom_id', $classroom->id)->update(['classroom_id' => null]);

        $className = $classroom->name;
        $classroom->delete();

        return redirect()
            ->route('teacher.dashboard')
            ->with('success', "Class \"{$className}\" has been deleted. All students have been removed.");
    }

    /**
     * Generate a quest from form content (simplified version)
     */
    public function generateQuest(Request $request, Classroom $classroom)
    {
        $request->validate([
            'quest_title' => 'required|string|max:255',
            'quest_content' => 'required|string',
            'quest_type' => 'required|in:pronunciation,reading,mixed,pdf',
            'difficulty' => 'required|in:easy,medium,hard',
            'reward_points' => 'required|integer|min:10|max:200',
            'estimated_time' => 'required|string|max:50',
        ]);

        try {
            $title = $request->input('quest_title');
            $content = $request->input('quest_content');
            $questType = $request->input('quest_type');
            $difficulty = $request->input('difficulty');
            $rewardPoints = $request->input('reward_points');
            $estimatedTime = $request->input('estimated_time');

            if ($questType === 'pdf') {
                $request->validate([
                    'quest_pdf' => 'required|file|mimes:pdf|max:20480',
                    'pdf_activity_type' => 'required|in:read,pronunciation',
                ]);

                $pdfData = $this->processPdfQuest($request);
                $pdfData['pdf_activity_type'] = $request->input('pdf_activity_type');
                $content = json_encode($pdfData);
            }

            if ($questType === 'pronunciation' || $questType === 'mixed') {
                $request->validate([
                    'pronunciation_images_*' => 'nullable|image|max:5120',
                ]);
                $content = $this->processPronunciationImages($content, $request);
            }
            
            Log::info('Creating quest with type: ' . $questType . ' and title: ' . $title);
            
            // Create structured content based on quest type
            $structuredContent = $this->createStructuredContent($content, $questType, $difficulty);
            Log::info('Created structured content: ' . json_encode($structuredContent));

            // Create quest from content
            $quest = $this->createQuestFromStructuredContent($classroom->id, $title, $structuredContent, $questType, $difficulty, $rewardPoints, $estimatedTime);
            
            Log::info('Quest created successfully with ID: ' . $quest['id']);

            return response()->json([
                'success' => true,
                'quest' => $quest,
                'message' => 'Quest created successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Quest creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quest: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processPdfQuest(Request $request)
    {
        $pdf = $request->file('quest_pdf');
        if (!$pdf || !$pdf->isValid()) {
            throw new \RuntimeException('Invalid PDF upload.');
        }

        $path = $pdf->store('quest-pdfs', 'public');
        $pdfUrl = asset('storage/' . $path);

        try {
            $parser = new Parser();
            $document = $parser->parseFile($pdf->getRealPath());
            $text = trim($document->getText() ?? '');
        } catch (\Throwable $e) {
            throw new \RuntimeException('Failed to read PDF content.');
        }

        if ($text === '') {
            throw new \RuntimeException('The uploaded PDF has no readable text.');
        }

        return [
            'type' => 'pdf',
            'pdf_url' => $pdfUrl,
            'pdf_text' => $text,
        ];
    }

    private function createPdfContent($content, $difficulty)
    {
        $structuredContent = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($structuredContent)) {
            return [
                'type' => 'pdf',
                'pdf_url' => '',
                'pdf_text' => (string) $content,
                'difficulty' => $difficulty,
                'estimated_time' => '15 minutes',
            ];
        }

        return [
            'type' => 'pdf',
            'pdf_url' => $structuredContent['pdf_url'] ?? '',
            'pdf_text' => $structuredContent['pdf_text'] ?? '',
            'difficulty' => $difficulty,
            'estimated_time' => '15 minutes',
        ];
    }

    private function processPronunciationImages($content, Request $request)
    {
        $structuredItems = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($structuredItems)) {
            return $content;
        }

        foreach ($structuredItems as $index => &$item) {
            $imageKey = "pronunciation_images_{$index}";
            if (!$request->hasFile($imageKey)) {
                continue;
            }

            $image = $request->file($imageKey);
            if (!$image || !$image->isValid()) {
                continue;
            }

            $path = $image->store('pronunciation-images', 'public');
            $item['image'] = asset('storage/' . $path);
        }

        return json_encode($structuredItems);
    }
    
    /**
     * Create structured content based on quest type
     */
    private function createStructuredContent($content, $questType, $difficulty)
    {
        switch ($questType) {
            case 'pronunciation':
                return $this->createPronunciationContent($content, $difficulty);
                
            case 'reading':
                return $this->createReadingContent($content, $difficulty);
                
            case 'mixed':
                return $this->createMixedContent($content, $difficulty);

            case 'pdf':
                return $this->createPdfContent($content, $difficulty);
                
            default:
                return [
                    'type' => 'general',
                    'content' => $content,
                    'difficulty' => $difficulty,
                    'estimated_time' => '15 minutes'
                ];
        }
    }
    
    /**
     * Create pronunciation practice content
     */
    private function createPronunciationContent($content, $difficulty)
    {
        $structuredItems = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($structuredItems)) {
            $pronunciationExercises = [];

            foreach ($structuredItems as $item) {
                $word = trim($item['word'] ?? '');
                $phonetic = trim($item['phonetic'] ?? '');
                $practiceSentence = trim($item['practice_sentence'] ?? '');
                $image = $item['image'] ?? '';

                if (empty($word)) {
                    continue;
                }

                $pronunciationExercises[] = [
                    'word' => $word,
                    'phonetic' => $phonetic ?: $this->generatePhonetic($word),
                    'practice_sentence' => $practiceSentence ?: $this->generatePracticeSentence($word),
                    'image' => $image,
                    'difficulty' => $difficulty
                ];
            }

            if (!empty($pronunciationExercises)) {
                return [
                    'type' => 'pronunciation',
                    'pronunciation_exercises' => $pronunciationExercises,
                    'difficulty' => $difficulty,
                    'estimated_time' => count($pronunciationExercises) * 2 . ' minutes'
                ];
            }
        }

        // Fallback: derive items from free-form content
        $lines = preg_split('/[\n\r]+/', $content);
        $words = [];
        
        foreach ($lines as $line) {
            $lineWords = preg_split('/[\s,;:]+/', trim($line));
            foreach ($lineWords as $word) {
                $word = trim($word);
                if (strlen($word) > 2 && !preg_match('/[^a-zA-Z]/', $word)) {
                    $words[] = $word;
                }
            }
        }
        
        $words = array_unique($words);
        $words = array_slice($words, 0, 10);
        
        $pronunciationExercises = [];
        foreach ($words as $word) {
            $pronunciationExercises[] = [
                'word' => $word,
                'phonetic' => $this->generatePhonetic($word),
                'practice_sentence' => $this->generatePracticeSentence($word),
                'difficulty' => $difficulty
            ];
        }
        
        return [
            'type' => 'pronunciation',
            'pronunciation_exercises' => $pronunciationExercises,
            'difficulty' => $difficulty,
            'estimated_time' => count($words) * 2 . ' minutes'
        ];
    }
    
    /**
     * Create reading comprehension content
     */
    private function createReadingContent($content, $difficulty)
    {
        $structuredContent = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($structuredContent)) {
            $passage = trim($structuredContent['passage'] ?? $structuredContent['reading_passage'] ?? '');
            $questions = $structuredContent['questions'] ?? $structuredContent['reading_questions'] ?? [];

            $readingExercises = [];

            foreach ($questions as $question) {
                $prompt = trim($question['question'] ?? '');
                $options = $question['options'] ?? [];
                $answer = strtoupper($question['answer'] ?? '');

                if (empty($prompt) || count($options) < 2) {
                    continue;
                }

                $readingExercises[] = [
                    'question' => $prompt,
                    'options' => array_values($options),
                    'answer' => $answer ?: 'A',
                    'difficulty' => $question['difficulty'] ?? $difficulty
                ];
            }

            if (!empty($passage) && !empty($readingExercises)) {
                return [
                    'type' => 'reading',
                    'reading_passage' => $passage,
                    'reading_exercises' => $readingExercises,
                    'difficulty' => $difficulty,
                    'estimated_time' => '15 minutes'
                ];
            }
        }

        // Fallback to auto-generated content when no structured data provided
        $passage = $this->createReadingPassage($content);
        $questions = $this->generateComprehensionQuestions($passage, $difficulty);
        
        return [
            'type' => 'reading',
            'reading_passage' => $passage,
            'reading_exercises' => $questions,
            'difficulty' => $difficulty,
            'estimated_time' => '15 minutes'
        ];
    }
    
    /**
     * Create mixed practice content
     */
    private function createMixedContent($content, $difficulty)
    {
        $structuredContent = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($structuredContent)) {
            $pronunciationPayload = $structuredContent['pronunciation_items'] ?? $structuredContent['pronunciation_exercises'] ?? null;
            $readingPassage = $structuredContent['reading_passage'] ?? null;
            $readingQuestions = $structuredContent['reading_questions'] ?? $structuredContent['reading_exercises'] ?? null;

            $pronunciationExercises = [];
            if ($pronunciationPayload) {
                $encodedPronunciation = json_encode($pronunciationPayload);
                $pronunciationContent = $this->createPronunciationContent($encodedPronunciation, $difficulty);
                $pronunciationExercises = $pronunciationContent['pronunciation_exercises'] ?? [];
            }

            $readingExercises = [];
            $readingPassageValue = '';
            if ($readingPassage && $readingQuestions) {
                $encodedReading = json_encode([
                    'passage' => $readingPassage,
                    'questions' => $readingQuestions,
                ]);
                $readingContent = $this->createReadingContent($encodedReading, $difficulty);
                $readingExercises = $readingContent['reading_exercises'] ?? [];
                $readingPassageValue = $readingContent['reading_passage'] ?? '';
            } elseif ($readingQuestions) {
                $encodedReading = json_encode(['questions' => $readingQuestions]);
                $readingContent = $this->createReadingContent($encodedReading, $difficulty);
                $readingExercises = $readingContent['reading_exercises'] ?? [];
                $readingPassageValue = $readingContent['reading_passage'] ?? '';
            }

            if (!empty($pronunciationExercises) || !empty($readingExercises)) {
                return [
                    'type' => 'mixed',
                    'pronunciation_exercises' => $pronunciationExercises,
                    'reading_passage' => $readingPassageValue,
                    'reading_exercises' => $readingExercises,
                    'difficulty' => $difficulty,
                    'estimated_time' => (count($pronunciationExercises) + count($readingExercises)) * 2 . ' minutes',
                ];
            }
        }

        $fallbackPronunciation = $this->createPronunciationContent($content, $difficulty);
        $fallbackReading = $this->createReadingContent($content, $difficulty);

        return [
            'type' => 'mixed',
            'pronunciation_exercises' => $fallbackPronunciation['pronunciation_exercises'] ?? [],
            'reading_passage' => $fallbackReading['reading_passage'] ?? '',
            'reading_exercises' => $fallbackReading['reading_exercises'] ?? [],
            'difficulty' => $difficulty,
            'estimated_time' => '20 minutes'
        ];
    }
    
    /**
     * Create quest from structured content
     */
    private function createQuestFromStructuredContent($classroomId, $title, $content, $type, $difficulty, $rewardPoints, $estimatedTime)
    {
        $quest = Quest::create([
            'classroom_id' => $classroomId,
            'teacher_id' => Auth::id(),
            'title' => $title,
            'description' => "Teacher-created {$type} exercises",
            'type' => $type,
            'difficulty' => $difficulty,
            'content' => $content,
            'estimated_time' => $estimatedTime,
            'reward_points' => $rewardPoints,
            'status' => 'active',
        ]);
        
        return [
            'id' => $quest->id,
            'classroom_id' => $quest->classroom_id,
            'title' => $quest->title,
            'type' => ucfirst($quest->type),
            'difficulty' => $quest->difficulty,
            'reward' => $quest->reward_points . ' XP',
            'status' => ucfirst($quest->status),
            'content' => $quest->content,
            'created_at' => $quest->created_at->format('M d, Y')
        ];
    }
    
    /**
     * Generate phonetic representation (simplified)
     */
    private function generatePhonetic($word)
    {
        // Simple phonetic approximation
        return '[' . strtoupper($word) . ']';
    }
    
    /**
     * Generate practice sentence
     */
    private function generatePracticeSentence($word)
    {
        $sentences = [
            "Please practice saying the word: {$word}.",
            "Can you pronounce {$word} clearly?",
            "Let's focus on the word {$word}.",
            "Repeat after me: {$word}."
        ];
        
        return $sentences[array_rand($sentences)];
    }
    
    /**
     * Create reading passage
     */
    private function createReadingPassage($content)
    {
        // Clean and format content as a passage
        $lines = preg_split('/[\n\r]+/', $content);
        $sentences = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) > 10) {
                // Ensure it ends with proper punctuation
                if (!preg_match('/[.!?]$/', $line)) {
                    $line .= '.';
                }
                $sentences[] = ucfirst($line);
            }
        }
        
        if (empty($sentences)) {
            $sentences[] = "This is a reading passage to help improve comprehension skills.";
        }
        
        return implode(' ', $sentences);
    }
    
    /**
     * Generate comprehension questions
     */
    private function generateComprehensionQuestions($passage, $difficulty)
    {
        $questions = [];
        
        // Extract key concepts from passage
        $words = str_word_count($passage, 1);
        $importantWords = array_slice($words, 0, min(5, count($words)));
        
        foreach ($importantWords as $index => $word) {
            if (strlen($word) > 3) {
                $question = "What is the main idea related to " . strtolower($word) . "?";
                $options = [
                    "The concept of " . strtolower($word),
                    "Something unrelated to " . strtolower($word),
                    "The opposite of " . strtolower($word),
                    "None of the above"
                ];
                
                $questions[] = [
                    'question' => $question,
                    'options' => $options,
                    'answer' => 'A',
                    'difficulty' => $difficulty
                ];
                
                if (count($questions) >= 3) break;
            }
        }
        
        // Add a general question if no specific questions were created
        if (empty($questions)) {
            $questions[] = [
                'question' => "What is the main topic of this passage?",
                'options' => [
                    "The main subject discussed",
                    "Something completely different",
                    "No clear topic",
                    "Multiple unrelated topics"
                ],
                'answer' => 'A',
                'difficulty' => $difficulty
            ];
        }
        
        return $questions;
    }
    
    /**
     * Display skill tracking dashboard for teacher
     */
    public function skillTracking(Classroom $classroom)
    {
        // Verify teacher owns this classroom
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get initial skill tracking data
        $skillData = $this->getInitialSkillTrackingData($classroom->id);
        
        // Get students list for filtering
        $students = User::where('role', 'student')
            ->whereHas('studentProfile', function ($query) use ($classroom) {
                $query->where('classroom_id', $classroom->id);
            })
            ->orderBy('name')
            ->get();
        
        // Load classroom with relationships to avoid null errors
        $classroom->load(['students', 'quests']);
        
        return view('teacher.skill-tracking', [
            'classroom' => $classroom,
            'skillData' => $skillData,
            'students' => $students,
        ]);
    }
    
    /**
     * Get skill tracking data via AJAX
     */
    public function getSkillTrackingData(Request $request, Classroom $classroom)
    {
        // Verify teacher owns this classroom
        if ($classroom->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $filters = $request->only(['skill_type', 'student_id', 'difficulty_level', 'date_from', 'date_to']);
        
        // Build the base query for skill responses
        $query = SkillResponse::where('classroom_id', $classroom->id)
            ->with(['student', 'quest']);
        
        // Apply filters
        if (!empty($filters['skill_type'])) {
            $query->where('skill_type', $filters['skill_type']);
        }
        
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }
        
        if (!empty($filters['difficulty_level'])) {
            $query->where('difficulty_level', $filters['difficulty_level']);
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        $skillResponses = $query->orderBy('created_at', 'desc')->get();
        
        // Ensure we always have a valid collection
        if ($skillResponses === null) {
            $skillResponses = collect();
        }
        
        // Process the data for the skill tracking dashboard
        $skillData = [
            'summary' => [
                'total_responses' => $skillResponses->count(),
                'unique_students' => $skillResponses->isNotEmpty() ? $skillResponses->pluck('student_id')->unique()->count() : 0,
                'overall_accuracy' => $skillResponses->isNotEmpty() ? round($skillResponses->avg('accuracy'), 1) : 0,
                'skill_types' => $skillResponses->isNotEmpty() ? $skillResponses->pluck('skill_type')->unique()->values()->toArray() : [],
                'most_challenging_skill' => $this->getMostChallengingSkill($skillResponses),
                'most_successful_skill' => $this->getMostSuccessfulSkill($skillResponses),
                'students_needing_attention' => $this->getStudentsNeedingAttention($skillResponses),
            ],
            'skills_breakdown' => [],
            'student_progress' => [],
            'responses' => $skillResponses, // For the recent responses table
            'recent_responses' => $skillResponses->take(10)->map(function ($response) {
                return [
                    'student_name' => $response->student ? $response->student->name : 'Unknown Student',
                    'skill_type' => $response->skill_type ?? 'Unknown',
                    'accuracy' => $response->accuracy ?? 0,
                    'difficulty_level' => $response->difficulty_level ?? 'Unknown',
                    'created_at' => $response->created_at ? $response->created_at->format('Y-m-d H:i:s') : 'Unknown',
                ];
            })->toArray(),
        ];
        
        // Group by skill type for breakdown
        if ($skillResponses->isNotEmpty()) {
            $groupedBySkill = $skillResponses->groupBy('skill_type');
            foreach ($groupedBySkill as $skillType => $responses) {
                if ($responses && $responses->count() > 0) {
                    $skillData['skills_breakdown'][] = [
                        'skill_type' => $skillType ?? 'Unknown',
                        'total_responses' => $responses->count(),
                        'average_accuracy' => round($responses->avg('accuracy') ?? 0, 2),
                        'students_involved' => $responses->pluck('student_id')->unique()->count(),
                    ];
                }
            }
            
            // Group by student for progress tracking
            $groupedByStudent = $skillResponses->groupBy('student_id');
            foreach ($groupedByStudent as $studentId => $responses) {
                if ($responses && $responses->count() > 0) {
                    $firstResponse = $responses->first();
                    $student = $firstResponse ? $firstResponse->student : null;
                    $skillData['student_progress'][] = [
                        'student_id' => $studentId ?? 0,
                        'student_name' => $student ? $student->name : 'Unknown Student',
                        'total_responses' => $responses->count(),
                        'average_accuracy' => round($responses->avg('accuracy') ?? 0, 2),
                        'skill_types_covered' => $responses->pluck('skill_type')->unique()->count(),
                        'latest_response' => $firstResponse && $firstResponse->created_at ? $firstResponse->created_at->format('Y-m-d H:i:s') : 'Unknown',
                    ];
                }
            }
        }
        
        return response()->json($skillData);
    }
    
    /**
     * Get the most challenging skill based on average accuracy
     */
    private function getMostChallengingSkill($skillResponses)
    {
        if ($skillResponses->isEmpty()) {
            return null;
        }
        
        $groupedBySkill = $skillResponses->groupBy('skill_type');
        $skillAccuracies = [];
        
        foreach ($groupedBySkill as $skillType => $responses) {
            if ($responses->isNotEmpty()) {
                $skillAccuracies[$skillType] = $responses->avg('accuracy');
            }
        }
        
        if (empty($skillAccuracies)) {
            return null;
        }
        
        return array_keys($skillAccuracies, min($skillAccuracies))[0] ?? null;
    }
    
    /**
     * Get the most successful skill based on average accuracy
     */
    private function getMostSuccessfulSkill($skillResponses)
    {
        if ($skillResponses->isEmpty()) {
            return null;
        }
        
        $groupedBySkill = $skillResponses->groupBy('skill_type');
        $skillAccuracies = [];
        
        foreach ($groupedBySkill as $skillType => $responses) {
            if ($responses->isNotEmpty()) {
                $skillAccuracies[$skillType] = $responses->avg('accuracy');
            }
        }
        
        if (empty($skillAccuracies)) {
            return null;
        }
        
        return array_keys($skillAccuracies, max($skillAccuracies))[0] ?? null;
    }
    
    /**
     * Get students who need attention (low accuracy)
     */
    private function getStudentsNeedingAttention($skillResponses)
    {
        if ($skillResponses->isEmpty()) {
            return collect();
        }
        
        $groupedByStudent = $skillResponses->groupBy('student_id');
        $needingAttention = collect();
        
        foreach ($groupedByStudent as $studentId => $responses) {
            if ($responses->isNotEmpty()) {
                $avgAccuracy = $responses->avg('accuracy');
                if ($avgAccuracy < 60) { // Students with less than 60% accuracy
                    $firstResponse = $responses->first();
                    $student = $firstResponse ? $firstResponse->student : null;
                    
                    if ($student) {
                        $needingAttention->push((object)[
                            'student_id' => $studentId,
                            'student_name' => $student->name,
                            'average_accuracy' => round($avgAccuracy, 1),
                            'total_responses' => $responses->count(),
                        ]);
                    }
                }
            }
        }
        
        return $needingAttention->sortBy('average_accuracy')->take(10);
    }
    
    /**
     * Get initial skill tracking data for the view
     */
    private function getInitialSkillTrackingData($classroomId)
    {
        // Build the base query for skill responses
        $query = SkillResponse::where('classroom_id', $classroomId)
            ->with(['student', 'quest']);
        
        $skillResponses = $query->orderBy('created_at', 'desc')->get();
        
        // Ensure we always have a valid collection
        if ($skillResponses === null) {
            $skillResponses = collect();
        }
        
        // Process the data for the skill tracking dashboard
        return [
            'summary' => [
                'total_responses' => $skillResponses->count(),
                'unique_students' => $skillResponses->isNotEmpty() ? $skillResponses->pluck('student_id')->unique()->count() : 0,
                'overall_accuracy' => $skillResponses->isNotEmpty() ? round($skillResponses->avg('accuracy'), 1) : 0,
                'skill_types' => $skillResponses->isNotEmpty() ? $skillResponses->pluck('skill_type')->unique()->values()->toArray() : [],
                'most_challenging_skill' => $this->getMostChallengingSkill($skillResponses),
                'most_successful_skill' => $this->getMostSuccessfulSkill($skillResponses),
                'students_needing_attention' => $this->getStudentsNeedingAttention($skillResponses),
            ],
            'skills_breakdown' => [],
            'student_progress' => [],
            'responses' => $skillResponses, // For the recent responses table
            'recent_responses' => $skillResponses->take(10)->map(function ($response) {
                return [
                    'student_name' => $response->student ? $response->student->name : 'Unknown Student',
                    'skill_type' => $response->skill_type ?? 'Unknown',
                    'accuracy' => $response->accuracy ?? 0,
                    'difficulty_level' => $response->difficulty_level ?? 'Unknown',
                    'created_at' => $response->created_at ? $response->created_at->format('Y-m-d H:i:s') : 'Unknown',
                ];
            })->toArray(),
        ];
    }
    
    /**
     * Get detailed skill analysis for a specific student
     */
    public function getStudentSkillAnalysis(Classroom $classroom, User $student)
    {
        // Verify teacher owns this classroom
        if ($classroom->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Verify student is in this classroom
        if (!$student->studentProfile || $student->studentProfile->classroom_id !== $classroom->id) {
            return response()->json(['error' => 'Student not found in this classroom'], 404);
        }
        
        $analysis = $this->getStudentSkillAnalysis($student->id, $classroom->id);
        
        return response()->json($analysis);
    }

    /**
     * Extract text from PDF file
     */
    private function extractTextFromPDF($filePath)
    {
        try {
            $fullPath = storage_path('app/public/' . $filePath);
            
            Log::info('Attempting to extract text from PDF: ' . $fullPath);
            
            if (!file_exists($fullPath)) {
                Log::error('PDF file not found: ' . $fullPath);
                throw new \Exception('PDF file not found: ' . $fullPath);
            }
            
            // Check file size
            $fileSize = filesize($fullPath);
            Log::info('PDF file size: ' . $fileSize . ' bytes');
            
            if ($fileSize === 0) {
                throw new \Exception('PDF file is empty');
            }
            
            // Check if it's actually a PDF
            $fileInfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fullPath);
            Log::info('PDF file MIME type: ' . $fileInfo);
            
            if ($fileInfo !== 'application/pdf') {
                throw new \Exception('File is not a valid PDF. Detected MIME type: ' . $fileInfo);
            }
            
            // Try multiple extraction methods
            $text = $this->extractTextWithMultipleMethods($fullPath);
            
            // Clean up the text while preserving formatting
            $text = $this->cleanExtractedText($text);
            $text = trim($text);
            
            // Format text better - preserve paragraph breaks
            $text = $this->formatExtractedText($text);
            
            // Limit text length to avoid API limits
            if (strlen($text) > 15000) {
                $text = substr($text, 0, 15000) . '...';
                Log::info('PDF text truncated to 15000 characters for AI processing');
            }
            
            if (empty($text) || strlen($text) < 10) {
                Log::warning('No meaningful text extracted from PDF: ' . $filePath . '. Extracted length: ' . strlen($text));
                return $this->getPdfExtractionHelpMessage();
            }
            
            Log::info('Successfully extracted ' . strlen($text) . ' characters from PDF');
            return $text;
            
        } catch (\Exception $e) {
            Log::error('PDF extraction failed: ' . $e->getMessage());
            return "Failed to extract text from PDF: " . $e->getMessage() . ". " . $this->getPdfExtractionHelpMessage();
        }
    }
    
    /**
     * Try multiple methods to extract text from PDF
     */
    private function extractTextWithMultipleMethods($fullPath)
    {
        $methods = [
            'Standard Parser' => function($path) {
                $parser = new Parser();
                $pdf = $parser->parseFile($path);
                return $pdf->getText();
            },
            'Page by Page Extraction' => function($path) {
                $parser = new Parser();
                $pdf = $parser->parseFile($path);
                $pages = $pdf->getPages();
                $text = '';
                foreach ($pages as $index => $page) {
                    $pageText = $page->getText();
                    $text .= "Page " . ($index + 1) . ":\n" . $pageText . "\n\n";
                }
                return $text;
            },
            'Detailed Extraction' => function($path) {
                $parser = new Parser();
                $pdf = $parser->parseFile($path);
                $pages = $pdf->getPages();
                $text = '';
                
                foreach ($pages as $pageIndex => $page) {
                    $pageText = $page->getText();
                    
                    // Try to extract text with different encodings
                    if (!empty($pageText)) {
                        $text .= "--- PAGE " . ($pageIndex + 1) . " ---\n";
                        $text .= $pageText . "\n\n";
                    }
                    
                    // Try to get text details
                    try {
                        $details = $page->getDetails();
                        if (!empty($details)) {
                            $text .= "Details Page " . ($pageIndex + 1) . ":\n";
                            $text .= print_r($details, true) . "\n\n";
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not get details for page ' . ($pageIndex + 1) . ': ' . $e->getMessage());
                    }
                }
                
                return $text;
            }
        ];
        
        $bestText = '';
        $bestLength = 0;
        
        foreach ($methods as $methodName => $method) {
            try {
                Log::info('Trying PDF extraction method: ' . $methodName);
                $text = $method($fullPath);
                
                // Log the first 200 characters for debugging
                $preview = substr($text, 0, 200);
                Log::info('Method ' . $methodName . ' extracted (preview): ' . $preview);
                
                if (!empty($text) && strlen($text) > $bestLength) {
                    $bestText = $text;
                    $bestLength = strlen($text);
                    Log::info('New best method found: ' . $methodName . ' with ' . $bestLength . ' characters');
                }
            } catch (\Exception $e) {
                Log::warning('Method ' . $methodName . ' failed: ' . $e->getMessage());
                continue;
            }
        }
        
        if (empty($bestText)) {
            throw new \Exception('All PDF extraction methods failed to extract meaningful text');
        }
        
        return $bestText;
    }
    
    /**
     * Clean extracted text to remove artifacts and improve readability
     */
    private function cleanExtractedText($text)
    {
        if (empty($text)) {
            return $text;
        }
        
        Log::info('Cleaning extracted text. Original length: ' . strlen($text));
        
        // Step 1: Remove common PDF artifacts and special characters
        $patterns = [
            // Remove binary/compressed data artifacts
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/',
            // Remove excessive special characters and symbols
            '/[^\w\s\.\,\!\?\;\:\-\(\)\"\'\/\n\r@#$%&*+=<>\[\]{}|\\\\]/u',
            // Remove multiple special characters in a row
            '/[^\w\s]{3,}/u',
            // Remove common PDF stream markers
            '/stream[\s\S]*?endstream/i',
            // Remove object references
            '/\d+\s+\d+\s+obj[\s\S]*?endobj/i',
            // Remove font definitions
            '/BT[\s\S]*?ET/i',
            // Remove array brackets and excessive numbers
            '/\[\s*\d+\s*\]/',
            // Remove coordinate patterns
            '/\d+\.\d+\s+\d+\.\d+\s+\d+\.\d+\s+\d+\.\d+\s+cm/',
        ];
        
        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, ' ', $text);
        }
        
        // Step 2: Fix common encoding issues
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        
        // Step 3: Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Step 4: Remove sentences that are mostly special characters or numbers
        $sentences = preg_split('/[.!?]+/', $text);
        $cleanSentences = [];
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 5) {
                // Check if sentence has enough readable characters
                $letterCount = preg_match_all('/[a-zA-Z]/', $sentence);
                $totalChars = strlen($sentence);
                $readableRatio = $letterCount / $totalChars;
                
                // Keep sentences with at least 30% readable characters
                if ($readableRatio >= 0.3) {
                    $cleanSentences[] = $sentence;
                }
            }
        }
        
        // Step 5: Reassemble clean sentences
        $text = implode('. ', $cleanSentences);
        
        // Step 6: Final cleanup
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/[.]{2,}/', '.', $text);
        $text = trim($text);
        
        Log::info('Text cleaning complete. Clean length: ' . strlen($text));
        
        // If cleaning removed too much, fall back to basic cleaning
        if (strlen($text) < 50) {
            Log::warning('Aggressive cleaning removed too much text, using basic cleaning');
            $originalText = $text;
            $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);
            
            if (strlen($text) < 10) {
                return $originalText; // Return original if basic cleaning also fails
            }
        }
        
        return $text;
    }
    
    /**
     * Format extracted text to improve readability
     */
    private function formatExtractedText($text)
    {
        if (empty($text)) {
            return $text;
        }
        
        // Normalize multiple spaces to single space (but preserve line breaks)
        $text = preg_replace('/[ \t]+/', ' ', $text);
        
        // Normalize multiple line breaks (2+ newlines = paragraph break)
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Ensure sentences end with proper punctuation followed by space
        $text = preg_replace('/([.!?])([A-Z])/', '$1 $2', $text);
        
        // Clean up spacing around punctuation
        $text = preg_replace('/\s+([,.!?;:])/', '$1', $text);
        $text = preg_replace('/([,.!?;:])\s*/', '$1 ', $text);
        
        // Normalize multiple spaces again
        $text = preg_replace('/ +/', ' ', $text);
        
        // Ensure proper paragraph breaks
        $text = preg_replace('/\n /', "\n", $text);
        $text = preg_replace('/ \n/', "\n", $text);
        
        return trim($text);
    }
    
    // PDF chat removed

    /**
     * Get helpful message for PDF extraction issues
     */
    private function getPdfExtractionHelpMessage()
    {
        return "The PDF could not be read properly. Common solutions:\n" .
               "1. Ensure the PDF contains actual text (not just images)\n" .
               "2. Try saving the PDF as 'Text' or 'Optimized for web'\n" .
               "3. Check if the PDF is password-protected\n" .
               "4. Try a different PDF with clear text content\n" .
               "5. Make sure the PDF is not corrupted or damaged";
    }

    /**
     * Generate lesson content using AI
     */
    private function generateLessonContent($pdfText, $questType, $title)
    {
        try {
            // Prepare the prompt based on quest type
            $prompt = $this->buildAIPrompt($pdfText, $questType, $title);
            
            // Call Gemini API
            $response = $this->callGemini($prompt);
            
            // Parse the AI response
            return $this->parseAIResponse($response, $title, $questType);
            
        } catch (\Exception $e) {
            \Log::error('AI generation failed: ' . $e->getMessage());
            
            // Fallback to placeholder if AI fails
            return $this->getFallbackContent($title, $questType);
        }
    }

    /**
     * Build AI prompt based on quest type
     */
    private function buildAIPrompt($pdfText, $questType, $title)
    {
        $basePrompt = "You are an expert educational content creator. Based on the following educational content, generate learning exercises for students. ";
        
        switch ($questType) {
            case 'pronunciation':
                return $basePrompt . "
                
                Title: {$title}
                
                Content: {$pdfText}
                
                Create a pronunciation practice lesson based on the provided text. Generate exactly 5 pronunciation exercises focusing on challenging words:
                
                For each exercise:
                1. **Word Selection**: Choose words from the text that are:
                   - Difficult to pronounce
                   - Important to the content
                   - Age-appropriate for practice
                2. **Phonetic Transcription**: Provide accurate IPA transcription
                3. **Practice Sentence**: Create a meaningful sentence using the word in context
                4. **Difficulty**: Rate based on pronunciation complexity (easy/medium/hard)
                
                Return ONLY valid JSON format:
                {
                    \"pronunciation_exercises\": [
                        {
                            \"word\": \"example\",
                            \"phonetic\": \"/ɪɡˈzæmpəl/\",
                            \"difficulty\": \"medium\",
                            \"practice_sentence\": \"This is an example sentence.\"
                        }
                    ]
                }";
                
            case 'reading':
                return $basePrompt . "
                
                Title: {$title}
                
                Content: {$pdfText}
                
                IMPORTANT: Do NOT summarize the content. Instead, ANALYZE the provided text and create meaningful reading comprehension exercises.
                
                Create exactly 5 reading comprehension questions that test understanding of the ACTUAL content in the text:
                
                For each exercise:
                1. **Question Type**: Vary between these specific types:
                   - Main idea identification (What is the primary topic/theme?)
                   - Detail retrieval (According to the text, what/when/where/who?)
                   - Inference making (What can be inferred from the passage?)
                   - Vocabulary in context (What does [specific word] mean in this context?)
                   - Cause and effect (What caused [event] or what was the result of [action]?)
                2. **Question**: Base questions DIRECTLY on the provided text content
                3. **Options**: Create 4 plausible multiple-choice options (A, B, C, D) with only ONE correct answer
                4. **Answer**: Indicate the correct letter (A, B, C, or D)
                5. **Difficulty**: Rate based on cognitive complexity (easy/medium/hard)
                
                CRITICAL: All questions must be answerable ONLY by reading the provided text. Do not use external knowledge.
                
                Return ONLY valid JSON format:
                {
                    \"reading_exercises\": [
                        {
                            \"question\": \"Based on the text, what is the main purpose of [specific topic mentioned]?\",
                            \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
                            \"answer\": \"A\",
                            \"difficulty\": \"medium\"
                        }
                    ]
                }";
                
            case 'mixed':
                return $basePrompt . "
                
                Title: {$title}
                
                Content: {$pdfText}
                
                Create a comprehensive mixed-skills lesson combining pronunciation and reading comprehension. Generate:
                - 3 pronunciation exercises (focus on key vocabulary from the text)
                - 3 reading comprehension questions (vary question types)
                
                Follow the same quality standards as individual quest types.
                
                Return ONLY valid JSON format:
                {
                    \"pronunciation_exercises\": [
                        {
                            \"word\": \"example\",
                            \"phonetic\": \"/ɪɡˈzæmpəl/\",
                            \"difficulty\": \"medium\",
                            \"practice_sentence\": \"This is an example sentence.\"
                        }
                    ],
                    \"reading_exercises\": [
                        {
                            \"question\": \"What is the main topic?\",
                            \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
                            \"answer\": \"A\",
                            \"difficulty\": \"medium\"
                        }
                    ]
                }";
                
            default:
                return $basePrompt . "Generate general learning exercises from: " . $pdfText;
        }
    }

    /**
     * Call Gemini API
     */
    private function callGemini($prompt)
    {
        $apiKey = config('services.gemini.api_key');
        
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured');
        }
        
        $client = new Client();
        
        // Use the correct model name that's available
        $model = 'gemini-2.0-flash-exp';
        
        $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2000
                ]
            ]
        ]);
        
        $body = json_decode($response->getBody(), true);
        
        // Better error handling
        if (isset($body['error'])) {
            throw new \Exception('Gemini API error: ' . $body['error']['message'] ?? 'Unknown error');
        }
        
        if (!isset($body['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception('Invalid Gemini response: ' . json_encode($body));
        }
        
        return $body['candidates'][0]['content']['parts'][0]['text'];
    }

    /**
     * Parse AI response
     */
    private function parseAIResponse($aiResponse, $title, $questType)
    {
        try {
            Log::info('Raw AI response: ' . $aiResponse);
            
            // Try to extract JSON from the response
            $jsonStart = strpos($aiResponse, '{');
            $jsonEnd = strrpos($aiResponse, '}');
            
            if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
                $jsonString = substr($aiResponse, $jsonStart, $jsonEnd - $jsonStart + 1);
                $data = json_decode($jsonString, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('JSON decode error: ' . json_last_error_msg());
                    throw new \Exception('Invalid JSON in AI response: ' . json_last_error_msg());
                }
            } else {
                // If no JSON found, try parsing the whole response
                $data = json_decode($aiResponse, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('JSON decode error on full response: ' . json_last_error_msg());
                    throw new \Exception('AI response is not valid JSON: ' . json_last_error_msg());
                }
            }
            
            Log::info('Parsed AI data: ' . json_encode($data));
            
            // Validate the structure based on quest type
            if ($questType === 'reading') {
                if (!isset($data['reading_exercises']) || !is_array($data['reading_exercises'])) {
                    throw new \Exception('AI response missing reading_exercises array');
                }
                
                return [
                    'reading_exercises' => $data['reading_exercises'],
                    'pronunciation_exercises' => [],
                    'difficulty' => 'medium',
                    'estimated_time' => $this->calculateEstimatedTime($data)
                ];
            }
            
            if ($questType === 'pronunciation') {
                if (!isset($data['pronunciation_exercises']) || !is_array($data['pronunciation_exercises'])) {
                    throw new \Exception('AI response missing pronunciation_exercises array');
                }
                
                return [
                    'pronunciation_exercises' => $data['pronunciation_exercises'],
                    'reading_exercises' => [],
                    'difficulty' => 'medium',
                    'estimated_time' => $this->calculateEstimatedTime($data)
                ];
            }
            
            if ($questType === 'mixed') {
                if ((!isset($data['pronunciation_exercises']) || !is_array($data['pronunciation_exercises'])) ||
                    (!isset($data['reading_exercises']) || !is_array($data['reading_exercises']))) {
                    throw new \Exception('AI response missing required exercise arrays for mixed quest');
                }
                
                return [
                    'pronunciation_exercises' => $data['pronunciation_exercises'],
                    'reading_exercises' => $data['reading_exercises'],
                    'difficulty' => 'medium',
                    'estimated_time' => $this->calculateEstimatedTime($data)
                ];
            }
            
            throw new \Exception('Unknown quest type: ' . $questType);
            
        } catch (\Exception $e) {
            Log::error('AI response parsing failed: ' . $e->getMessage());
            
            // Return fallback structure
            return [
                'reading_exercises' => $questType === 'reading' || $questType === 'mixed' ? [
                    [
                        'question' => 'What is the main topic of the text?',
                        'options' => ['Topic A', 'Topic B', 'Topic C', 'Topic D'],
                        'answer' => 'A',
                        'difficulty' => 'medium'
                    ]
                ] : [],
                'pronunciation_exercises' => $questType === 'pronunciation' || $questType === 'mixed' ? [
                    [
                        'word' => 'example',
                        'phonetic' => '/ɪɡˈzæmpəl/',
                        'difficulty' => 'medium',
                        'practice_sentence' => 'This is an example sentence.'
                    ]
                ] : [],
                'difficulty' => 'medium',
                'estimated_time' => '15 minutes'
            ];
        }
    }

    /**
     * Calculate estimated time for quest completion
     */
    private function calculateEstimatedTime($data)
    {
        $exerciseCount = count($data['pronunciation_exercises'] ?? []) + count($data['reading_exercises'] ?? []);
        $baseTime = 5; // 5 minutes base
        $timePerExercise = 2; // 2 minutes per exercise
        
        return ($baseTime + ($exerciseCount * $timePerExercise)) . ' minutes';
    }

    /**
     * Fallback content if AI fails
     */
    private function getFallbackContent($title, $questType)
    {
        return [
            'title' => $title,
            'type' => $questType,
            'content' => "Generated content based on PDF analysis (AI service temporarily unavailable)",
            'pronunciation_exercises' => [
                ['word' => 'example', 'phonetic' => '/ɪɡˈzæmpəl/', 'difficulty' => 'medium', 'practice_sentence' => 'This is an example sentence.'],
                ['word' => 'pronunciation', 'phonetic' => '/prəˌnʌnsiˈeɪʃən/', 'difficulty' => 'hard', 'practice_sentence' => 'Practice pronunciation daily.'],
            ],
            'reading_exercises' => [
                ['question' => 'What is the main topic?', 'options' => ['Option A', 'Option B', 'Option C', 'Option D'], 'answer' => 'A', 'difficulty' => 'medium'],
                ['question' => 'Which statement is true?', 'options' => ['True', 'False'], 'answer' => 'True', 'difficulty' => 'easy'],
            ],
            'difficulty' => 'medium',
            'estimated_time' => '15 minutes',
            'ai_generated' => false
        ];
    }

    /**
     * Calculate difficulty based on exercises
     */
    private function calculateDifficulty($data)
    {
        $exercises = array_merge(
            $data['pronunciation_exercises'] ?? [],
            $data['reading_exercises'] ?? []
        );
        
        if (empty($exercises)) return 'medium';
        
        $difficulties = array_column($exercises, 'difficulty');
        $easy = count(array_filter($difficulties, fn($d) => $d === 'easy'));
        $medium = count(array_filter($difficulties, fn($d) => $d === 'medium'));
        $hard = count(array_filter($difficulties, fn($d) => $d === 'hard'));
        
        if ($hard > $medium && $hard > $easy) return 'hard';
        if ($easy > $medium) return 'easy';
        return 'medium';
    }

    /**
     * Estimate completion time
     */
    private function estimateTime($data)
    {
        $exerciseCount = count($data['pronunciation_exercises'] ?? []) + count($data['reading_exercises'] ?? []);
        $baseTime = 5; // 5 minutes base
        $timePerExercise = 2; // 2 minutes per exercise
        
        return ($baseTime + ($exerciseCount * $timePerExercise)) . ' minutes';
    }

    /**
     * Create quest from generated content
     */
    private function createQuestFromContent($classroomId, $title, $content, $type)
    {
        $quest = Quest::create([
            'classroom_id' => $classroomId,
            'teacher_id' => Auth::id(),
            'title' => $title,
            'description' => "AI-generated {$type} exercises based on PDF analysis",
            'type' => $type,
            'difficulty' => $content['difficulty'] ?? 'medium',
            'content' => $content,
            'estimated_time' => $content['estimated_time'] ?? '15 minutes',
            'reward_points' => 50,
            'status' => 'active',
        ]);
        
        return [
            'id' => $quest->id,
            'classroom_id' => $quest->classroom_id,
            'title' => $quest->title,
            'type' => ucfirst($quest->type),
            'difficulty' => $quest->difficulty,
            'reward' => $quest->reward_points . ' XP',
            'status' => ucfirst($quest->status),
            'content' => $quest->content,
            'created_at' => $quest->created_at->format('M d, Y')
        ];
    }

    /**
     * Get class ranking based on total points
     */
    private function getClassRank(Classroom $classroom): int
    {
        try {
            $classPoints = StudentProfile::where('classroom_id', $classroom->id)
                ->join('users', 'student_profiles.user_id', '=', 'users.id')
                ->sum('users.points') ?? 0;

            $allClassPoints = StudentProfile::select('classroom_id')
                ->with('user')
                ->get()
                ->groupBy('classroom_id')
                ->map(function ($students) {
                    return $students->sum(function ($student) {
                        return $student->user->points ?? 0;
                    });
                })
                ->sortDesc()
                ->values();

            $rank = $allClassPoints->search($classPoints) + 1;
            return $rank ?: 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Determine gamified status label based on streak days
     */
    private function determineStudentStatus(int $streakDays): string
    {
        if ($streakDays >= 7) {
            return 'On fire 🔥';
        }

        if ($streakDays >= 1) {
            return 'Needs boost ⚡';
        }

        return 'Cooling ❄️';
    }

    private function calculateClassLevelStats(int $xpTotal): array
    {
        $level = 1;
        $xpNeeded = 100;
        $remainingXp = $xpTotal;

        while ($remainingXp >= $xpNeeded) {
            $remainingXp -= $xpNeeded;
            $level++;
            $xpNeeded = $level * 100;
        }

        $progressPercent = $xpNeeded > 0 ? ($remainingXp / $xpNeeded) * 100 : 0;

        return [
            'level' => $level,
            'xp_total' => $xpTotal,
            'xp_into_level' => $remainingXp,
            'xp_for_next_level' => $xpNeeded,
            'progress_percent' => round($progressPercent, 1),
        ];
    }

    private function getResearchFocusMetrics(Classroom $classroom): array
    {
        $metrics = [];

        $readingPerformances = StudentPerformance::where('classroom_id', $classroom->id)
            ->where('activity_type', 'reading')
            ->whereNotNull('reading_comprehension')
            ->orderBy('completed_at')
            ->get();

        $readingBaseline = $readingPerformances->take(3)->avg('reading_comprehension') ?? 0;
        $readingCurrent = $readingPerformances->count()
            ? $readingPerformances->sortByDesc('completed_at')->take(3)->avg('reading_comprehension')
            : 0;
        $readingChange = $readingBaseline > 0
            ? (($readingCurrent - $readingBaseline) / $readingBaseline) * 100
            : $readingCurrent;

        $metrics[] = [
            'key' => 'reading',
            'badge' => 'Reading Skills',
            'title' => 'Reading Skill Gain',
            'value' => round($readingCurrent ?? 0, 1),
            'unit' => '%',
            'change' => round($readingChange ?? 0, 1),
            'change_label' => 'vs baseline',
            'subtext' => sprintf('Baseline %.1f%% → Current %.1f%%', $readingBaseline ?? 0, $readingCurrent ?? 0),
            'description' => 'Tracks comprehension gains before and after using the quest system.',
        ];

        $pronPerformances = StudentPerformance::where('classroom_id', $classroom->id)
            ->whereNotNull('pronunciation_accuracy')
            ->orderBy('completed_at')
            ->get();

        $pronAvg = $pronPerformances->avg('pronunciation_accuracy') ?? 0;
        $pronEarly = $pronPerformances->take(3)->avg('pronunciation_accuracy') ?? 0;
        $pronChange = $pronEarly > 0 ? (($pronAvg - $pronEarly) / $pronEarly) * 100 : $pronAvg;

        $metrics[] = [
            'key' => 'pronunciation',
            'badge' => 'Pronunciation',
            'title' => 'Pronunciation Accuracy',
            'value' => round($pronAvg, 1),
            'unit' => '%',
            'change' => round($pronChange ?? 0, 1),
            'change_label' => 'class average',
            'subtext' => sprintf('%d drills tracked', $pronPerformances->count()),
            'description' => 'Averages pronunciation drill scores and highlights growth over early attempts.',
        ];

        $vocabResponses = SkillResponse::where('classroom_id', $classroom->id)
            ->where('skill_type', 'vocabulary')
            ->get();
        $vocabTotal = $vocabResponses->count();
        $vocabCorrect = $vocabResponses->where('is_correct', true)->count();
        $vocabAccuracy = $vocabTotal > 0 ? ($vocabCorrect / $vocabTotal) * 100 : 0;

        $metrics[] = [
            'key' => 'vocabulary',
            'badge' => 'Vocabulary',
            'title' => 'Definitions Mastered',
            'value' => round($vocabAccuracy, 1),
            'unit' => '%',
            'change' => round($vocabAccuracy, 1),
            'change_label' => 'accuracy',
            'subtext' => sprintf('%d accurate out of %d attempts', $vocabCorrect, $vocabTotal),
            'description' => 'Measures how well students define or recall vocabulary introduced in quests.',
        ];

        $totalStudents = StudentProfile::where('classroom_id', $classroom->id)->count();
        $activeStudents = StudentPerformance::where('classroom_id', $classroom->id)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subDays(7))
            ->distinct('student_id')
            ->count('student_id');
        $weeklyQuests = StudentPerformance::where('classroom_id', $classroom->id)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subDays(7))
            ->count();
        $engagementPercent = $totalStudents > 0 ? ($activeStudents / $totalStudents) * 100 : 0;

        $metrics[] = [
            'key' => 'engagement',
            'badge' => 'Engagement',
            'title' => 'Lesson Engagement',
            'value' => round($engagementPercent, 0),
            'unit' => '%',
            'change' => round($weeklyQuests, 0),
            'change_label' => 'quests this week',
            'subtext' => sprintf('%d of %d students active in last 7 days', $activeStudents, $totalStudents),
            'description' => 'Compares active quest participation against total enrollment for the week.',
        ];

        return $metrics;
    }

    /**
     * Get average level of enrolled students
     */
    private function getAverageLevel($students): float
    {
        try {
            $totalLevel = $students->sum(function ($student) {
                return $student->level ?? 1;
            });
            return $students->count() > 0 ? round($totalLevel / $students->count(), 1) : 1.0;
        } catch (\Exception $e) {
            return 1.0;
        }
    }

    /**
     * Get number of students with active streaks
     */
    private function getActiveStreaks($students): int
    {
        try {
            return $students->filter(function ($student) {
                return ($student->streak_days ?? 0) >= 3;
            })->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get total achievements earned by students
     */
    private function getTotalAchievements($students): int
    {
        try {
            return $students->sum(function ($student) {
                return count($student->achievements ?? []);
            });
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Display student performance analytics for a classroom
     */
    public function performanceAnalytics($class)
    {
        $teacher = Auth::user();
        
        $classroom = Classroom::where('teacher_id', $teacher->id)
            ->where('id', $class)
            ->orWhere('slug', $class)
            ->firstOrFail();

        // Get classroom performance summary
        $performanceSummary = StudentPerformance::getClassroomPerformanceSummary($classroom->id);
        
        // Get performance trends
        $performanceTrends = StudentPerformance::getPerformanceTrends($classroom->id, 30);
        
        // Get individual student performances
        $students = StudentProfile::where('classroom_id', $classroom->id)
            ->with('user')
            ->get()
            ->map(function ($profile) use ($classroom) {
                $performance = StudentPerformance::getStudentOverallPerformance(
                    $profile->user_id, 
                    $classroom->id
                );
                
                return [
                    'student_id' => $profile->user_id,
                    'name' => $profile->user->name ?? 'Unknown Student',
                    'avatar' => strtoupper(substr($profile->user->name ?? 'S', 0, 1)),
                    'performance' => $performance,
                    'last_activity' => $profile->user->last_activity_date,
                    'current_level' => $profile->user->level ?? 1,
                    'points' => $profile->user->points ?? 0,
                ];
            })
            ->sortByDesc('performance.average_accuracy')
            ->values();

        return view('teacher.performance-analytics', [
            'classroom' => $classroom,
            'performanceSummary' => $performanceSummary,
            'performanceTrends' => $performanceTrends,
            'students' => $students,
        ]);
    }

    /**
     * Get detailed performance data for a specific student
     */
    public function studentPerformanceDetail($class, $studentId)
    {
        $teacher = Auth::user();
        
        $classroom = Classroom::where('teacher_id', $teacher->id)
            ->where('id', $class)
            ->orWhere('slug', $class)
            ->firstOrFail();

        $student = User::findOrFail($studentId);
        $studentProfile = StudentProfile::where('user_id', $studentId)
            ->where('classroom_id', $classroom->id)
            ->firstOrFail();

        // Get student's performance history
        $performanceHistory = StudentPerformance::where('student_id', $studentId)
            ->where('classroom_id', $classroom->id)
            ->whereNotNull('completed_at')
            ->with('quest')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function ($performance) {
                return [
                    'id' => $performance->id,
                    'activity_type' => $performance->activity_type,
                    'total_score' => $performance->total_score,
                    'max_score' => $performance->max_score,
                    'accuracy_percentage' => $performance->accuracy_percentage,
                    'time_spent_minutes' => $performance->time_spent_minutes,
                    'attempts_count' => $performance->attempts_count,
                    'improvement_rate' => $performance->improvement_rate,
                    'completed_at' => $performance->completed_at->format('M d, Y H:i'),
                    'quest_title' => $performance->quest?->title ?? 'General Activity',
                    'pronunciation_accuracy' => $performance->pronunciation_accuracy,
                    'reading_comprehension' => $performance->reading_comprehension,
                ];
            });

        // Get overall performance summary
        $overallPerformance = StudentPerformance::getStudentOverallPerformance($studentId, $classroom->id);

        // Get skill-specific analytics
        $skillAnalytics = $this->getStudentSkillAnalytics($studentId, $classroom->id);

        return view('teacher.student-performance-detail', [
            'classroom' => $classroom,
            'student' => $student,
            'studentProfile' => $studentProfile,
            'performanceHistory' => $performanceHistory,
            'overallPerformance' => $overallPerformance,
            'skillAnalytics' => $skillAnalytics,
        ]);
    }

    /**
     * Get skill-specific analytics for a student
     */
    private function getStudentSkillAnalytics($studentId, $classroomId): array
    {
        $performances = StudentPerformance::where('student_id', $studentId)
            ->where('classroom_id', $classroomId)
            ->whereNotNull('completed_at')
            ->get();

        if ($performances->isEmpty()) {
            return [
                'pronunciation' => ['average' => 0, 'trend' => 'no_data', 'total_activities' => 0],
                'reading' => ['average' => 0, 'trend' => 'no_data', 'total_activities' => 0],
                'mixed' => ['average' => 0, 'trend' => 'no_data', 'total_activities' => 0],
            ];
        }

        $analytics = [];
        
        foreach (['pronunciation', 'reading', 'mixed'] as $skill) {
            $skillPerformances = $performances->where('activity_type', $skill);
            
            if ($skillPerformances->isEmpty()) {
                $analytics[$skill] = ['average' => 0, 'trend' => 'no_data', 'total_activities' => 0];
                continue;
            }

            $average = $skillPerformances->avg('accuracy_percentage');
            $totalActivities = $skillPerformances->count();
            
            // Calculate trend (last 3 vs previous 3)
            $recent = $skillPerformances->take(3)->avg('accuracy_percentage');
            $previous = $skillPerformances->skip(3)->take(3)->avg('accuracy_percentage');
            
            $trend = 'stable';
            if ($recent > $previous + 5) {
                $trend = 'improving';
            } elseif ($recent < $previous - 5) {
                $trend = 'declining';
            }

            $analytics[$skill] = [
                'average' => round($average, 2),
                'trend' => $trend,
                'total_activities' => $totalActivities,
            ];
        }

        return $analytics;
    }

    /**
     * Record student performance (API endpoint for AJAX calls)
     */
    public function recordPerformance(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'quest_id' => 'nullable|exists:quests,id',
            'activity_type' => 'required|in:pronunciation,reading,mixed,general',
            'total_score' => 'required|integer|min:0',
            'max_score' => 'required|integer|min:1',
            'time_spent_minutes' => 'required|integer|min:0',
            'pronunciation_accuracy' => 'nullable|numeric|min:0|max:100',
            'reading_comprehension' => 'nullable|numeric|min:0|max:100',
            'pronunciation_scores' => 'nullable|array',
            'reading_scores' => 'nullable|array',
        ]);

        // Verify teacher has access to this classroom
        $classroom = Classroom::findOrFail($validated['classroom_id']);
        if ($classroom->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Calculate accuracy percentage
        $accuracyPercentage = ($validated['total_score'] / $validated['max_score']) * 100;

        $performanceData = array_merge($validated, [
            'accuracy_percentage' => round($accuracyPercentage, 2),
            'attempts_count' => 1,
        ]);

        $performance = StudentPerformance::recordPerformance($performanceData);

        return response()->json([
            'success' => true,
            'performance_id' => $performance->id,
            'accuracy_percentage' => $performance->accuracy_percentage,
            'improvement_rate' => $performance->improvement_rate,
        ]);
    }

    /**
     * Export classroom performance data
     */
    public function exportPerformanceData($class)
    {
        $teacher = Auth::user();
        
        $classroom = Classroom::where('teacher_id', $teacher->id)
            ->where('id', $class)
            ->orWhere('slug', $class)
            ->firstOrFail();

        $students = StudentProfile::where('classroom_id', $classroom->id)
            ->with('user')
            ->get();

        $csvData = [];
        $csvData[] = ['Student Name', 'Total Activities', 'Average Accuracy', 'Total Time (min)', 'Improvement Rate', 'Strongest Area', 'Weakest Area'];

        foreach ($students as $profile) {
            $performance = StudentPerformance::getStudentOverallPerformance(
                $profile->user_id, 
                $classroom->id
            );
            
            $csvData[] = [
                $profile->user->name ?? 'Unknown',
                $performance['total_activities'],
                $performance['average_accuracy'] . '%',
                $performance['total_time_spent'],
                $performance['improvement_rate'] . '%',
                $performance['strongest_area'],
                $performance['weakest_area'],
            ];
        }

        $filename = "classroom_{$classroom->slug}_performance_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show PDF test page for teacher to upload and process PDF files
     */
    // PDF Test handlers removed

    /**
     * Generate summary with activity detection using Gemini API
     */
    private function generatePDFSummaryWithActivities($text)
    {
        try {
            $apiKey = config('services.gemini.api_key');

            if (!$apiKey) {
                return [
                    'summary' => 'AI summary not available: Gemini API key not configured.',
                    'activities' => []
                ];
            }

            // Truncate text if too long (Gemini has token limits)
            $textForSummary = $text;
            if (strlen($textForSummary) > 20000) {
                $textForSummary = substr($text, 0, 20000) . '... [truncated]';
            }

            $prompt = "Analyze the following document and provide:
1. A clear, detailed summary of key points
2. Any activities, exercises, or tasks related to reading comprehension and/or pronunciation

For the summary, organize it with clear sections if the document has multiple topics. Use paragraphs, bullet points, or numbered lists where appropriate.

For activities, specifically identify:
- Reading comprehension activities (questions, exercises, comprehension tasks, reading passages with questions)
- Pronunciation activities (word pronunciation exercises, phonetic practice, speaking tasks, pronunciation drills)
- Mixed activities that combine both reading and pronunciation

Format your response as JSON with this structure:
{
  \"summary\": \"Your detailed summary here with proper formatting\",
  \"activities\": {
    \"reading_comprehension\": [
      {
        \"title\": \"Activity title\",
        \"description\": \"Brief description\",
        \"type\": \"reading\",
        \"difficulty\": \"easy/medium/hard\"
      }
    ],
    \"pronunciation\": [
      {
        \"title\": \"Activity title\",
        \"description\": \"Brief description\",
        \"type\": \"pronunciation\",
        \"difficulty\": \"easy/medium/hard\"
      }
    ],
    \"mixed\": [
      {
        \"title\": \"Activity title\",
        \"description\": \"Brief description\",
        \"type\": \"mixed\",
        \"difficulty\": \"easy/medium/hard\"
      }
    ]
  }
}

If there are no activities, return empty arrays for the activity types.

Document content:

" . $textForSummary;

            $model = config('services.gemini.model', 'gemini-2.0-flash-exp');
            $endpoint = sprintf(
                'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                $model,
                urlencode($apiKey)
            );

            $response = Http::timeout(30)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 4000
                ]
            ]);

            if ($response->successful()) {
                $body = $response->json();

                if (isset($body['error'])) {
                    throw new \Exception('Gemini API error: ' . ($body['error']['message'] ?? 'Unknown error'));
                }

                if (isset($body['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiResponse = $body['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Try to parse JSON from response
                    $jsonStart = strpos($aiResponse, '{');
                    $jsonEnd = strrpos($aiResponse, '}');
                    
                    if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
                        $jsonString = substr($aiResponse, $jsonStart, $jsonEnd - $jsonStart + 1);
                        $parsedData = json_decode($jsonString, true);
                        
                        if (json_last_error() === JSON_ERROR_NONE && isset($parsedData['summary'])) {
                            return [
                                'summary' => $parsedData['summary'],
                                'activities' => $parsedData['activities'] ?? ['reading_comprehension' => [], 'pronunciation' => [], 'mixed' => []]
                            ];
                        }
                    }
                    
                    // Fallback: return response as summary if JSON parsing fails
                    return [
                        'summary' => $aiResponse,
                        'activities' => ['reading_comprehension' => [], 'pronunciation' => [], 'mixed' => []]
                    ];
                }

                throw new \Exception('Invalid Gemini response structure');
            }

            throw new \Exception('API request failed with status: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('Summary generation failed: ' . $e->getMessage());
            
            // Fallback to basic summary without activities
            $basicSummary = $this->generatePDFSummary($text);
            
            return [
                'summary' => $basicSummary . "\n\n[Note: Activity detection failed - " . $e->getMessage() . "]",
                'activities' => ['reading_comprehension' => [], 'pronunciation' => [], 'mixed' => []]
            ];
        }
    }

    /**
     * Generate summary of PDF content using Gemini API (fallback method)
     */
    private function generatePDFSummary($text)
    {
        try {
            $apiKey = config('services.gemini.api_key');

            if (!$apiKey) {
                return 'AI summary not available: Gemini API key not configured.';
            }

            // Truncate text if too long (Gemini has token limits)
            $textForSummary = $text;
            if (strlen($textForSummary) > 20000) {
                $textForSummary = substr($text, 0, 20000) . '... [truncated]';
            }

            $prompt = "Please provide a clear, detailed, and well-structured summary of the key points from the following document. 

Requirements:
1. Focus on the main ideas, important details, and key takeaways
2. Organize the summary with clear sections if the document has multiple topics
3. Use bullet points or numbered lists where appropriate
4. Highlight important concepts, dates, names, or figures mentioned
5. Maintain the logical flow of the document
6. Keep it comprehensive but concise (aim for 3-7 paragraphs or bullet points)
7. Use clear, professional language that's easy to understand
8. If the document contains specific data, statistics, or facts, include them in the summary

Format the summary with:
- Paragraphs separated by blank lines
- Bullet points (•) or numbered lists for key items
- Bold formatting for important concepts (using **text**)

Document content:

" . $textForSummary;

            $model = config('services.gemini.model', 'gemini-2.0-flash-exp');
            $endpoint = sprintf(
                'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                $model,
                urlencode($apiKey)
            );

            $response = Http::timeout(30)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 3000
                ]
            ]);

            if ($response->successful()) {
                $body = $response->json();

                if (isset($body['error'])) {
                    throw new \Exception('Gemini API error: ' . ($body['error']['message'] ?? 'Unknown error'));
                }

                if (isset($body['candidates'][0]['content']['parts'][0]['text'])) {
                    return $body['candidates'][0]['content']['parts'][0]['text'];
                }

                throw new \Exception('Invalid Gemini response structure');
            }

            throw new \Exception('API request failed with status: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('Summary generation failed: ' . $e->getMessage());
            return 'Summary generation failed: ' . $e->getMessage();
        }
    }

    /**
     * Display PDF library for teacher
     */
    public function pdfLibrary()
    {
        $pdfs = TeacherPdf::where('teacher_id', Auth::id())
            ->with('classroom')
            ->latest()
            ->get();

        $classes = Classroom::where('teacher_id', Auth::id())->get();

        // Reading analytics (based on student pdf_reading skill responses)
        $pdfIds = $pdfs->pluck('id')->values()->all();
        $teacherClassroomIds = $classes->pluck('id')->values()->all();

        $pdfReadingResponses = SkillResponse::query()
            ->where('skill_type', 'pdf_reading')
            ->where(function ($q) use ($teacherClassroomIds) {
                // pdf can be classroom-specific or general (null classroom_id)
                $q->whereIn('classroom_id', $teacherClassroomIds)->orWhereNull('classroom_id');
            })
            ->with('student')
            ->latest('responded_at')
            ->get();

        $pdfAnalytics = [];
        $latestByStudentAndPdf = [];

        foreach ($pdfReadingResponses as $response) {
            $payload = [];
            try {
                $payload = is_array($response->student_response)
                    ? $response->student_response
                    : json_decode((string) $response->student_response, true);
            } catch (\Throwable $e) {
                $payload = [];
            }

            if (!is_array($payload)) {
                continue;
            }

            $pdfId = $payload['pdf_id'] ?? null;
            if (!$pdfId || !in_array($pdfId, $pdfIds, true)) {
                continue;
            }

            $feedback = $payload['feedback'] ?? [];
            $accuracy = is_array($feedback) && isset($feedback['accuracy_score']) ? (float) $feedback['accuracy_score'] : null;
            if ($accuracy === null && isset($response->accuracy_score)) {
                $accuracy = (float) $response->accuracy_score;
            }

            $difficultWords = $payload['difficult_words'] ?? [];
            if (!is_array($difficultWords)) {
                $difficultWords = [];
            }

            $attempts = (int) ($payload['attempts'] ?? ($response->attempts ?? 1));
            $duration = (int) ($payload['recording_duration'] ?? ($feedback['duration_seconds'] ?? 0));

            if (!isset($pdfAnalytics[$pdfId])) {
                $pdfAnalytics[$pdfId] = [
                    'pdf_id' => $pdfId,
                    'attempt_count' => 0,
                    'student_count' => 0,
                    'avg_accuracy' => null,
                    'word_frequency' => [],
                    'students' => [],
                ];
            }

            $pdfAnalytics[$pdfId]['attempt_count'] += 1;

            foreach ($difficultWords as $w) {
                if (!is_string($w) || $w === '') {
                    continue;
                }
                $word = mb_strtolower(trim($w));
                if ($word === '') {
                    continue;
                }
                $pdfAnalytics[$pdfId]['word_frequency'][$word] = ($pdfAnalytics[$pdfId]['word_frequency'][$word] ?? 0) + 1;
            }

            // Latest session per student per PDF (for the teacher table)
            $studentId = $response->student_id;
            $key = $pdfId . ':' . $studentId;
            if (!isset($latestByStudentAndPdf[$key])) {
                $latestByStudentAndPdf[$key] = [
                    'student_id' => $studentId,
                    'student_name' => $response->student?->name ?? 'Student #' . $studentId,
                    'accuracy' => $accuracy,
                    'difficult_words_count' => count($difficultWords),
                    'attempts' => $attempts,
                    'duration_seconds' => $duration,
                    'responded_at' => optional($response->responded_at)->toDateTimeString(),
                ];
            }

            // Track accuracy average
            if ($accuracy !== null) {
                $pdfAnalytics[$pdfId]['_accuracy_sum'] = ($pdfAnalytics[$pdfId]['_accuracy_sum'] ?? 0) + $accuracy;
                $pdfAnalytics[$pdfId]['_accuracy_count'] = ($pdfAnalytics[$pdfId]['_accuracy_count'] ?? 0) + 1;
            }
        }

        // Finalize aggregates
        foreach ($pdfAnalytics as $pdfId => &$stats) {
            $accuracyCount = $stats['_accuracy_count'] ?? 0;
            $accuracySum = $stats['_accuracy_sum'] ?? 0;
            $stats['avg_accuracy'] = $accuracyCount > 0 ? round($accuracySum / $accuracyCount, 2) : null;
            unset($stats['_accuracy_sum'], $stats['_accuracy_count']);

            // Students list
            $students = [];
            foreach ($latestByStudentAndPdf as $k => $row) {
                if (str_starts_with($k, $pdfId . ':')) {
                    $students[] = $row;
                }
            }
            $stats['students'] = $students;
            $stats['student_count'] = count($students);

            // Top difficult words
            arsort($stats['word_frequency']);
            $top = [];
            foreach ($stats['word_frequency'] as $word => $count) {
                $top[] = ['word' => $word, 'count' => $count];
                if (count($top) >= 15) {
                    break;
                }
            }
            $stats['top_words'] = $top;
        }
        unset($stats);

        return view('teacher.pdf-library', [
            'pdfs' => $pdfs,
            'classes' => $classes,
            'pdfAnalytics' => $pdfAnalytics,
        ]);
    }

    /**
     * Upload a new PDF
     */
    public function uploadPdf(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        try {
            $pdf = $request->file('pdf_file');
            $path = $pdf->store('teacher-pdfs', 'public');

            // Extract text from PDF
            $extractedText = '';
            try {
                $parser = new Parser();
                $document = $parser->parseFile($pdf->getRealPath());
                $extractedText = trim($document->getText() ?? '');
            } catch (\Throwable $e) {
                Log::warning('Failed to extract text from PDF: ' . $e->getMessage());
            }

            $teacherPdf = TeacherPdf::create([
                'teacher_id' => Auth::id(),
                'classroom_id' => $request->classroom_id,
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $path,
                'original_filename' => $pdf->getClientOriginalName(),
                'file_size' => $pdf->getSize(),
                'extracted_text' => $extractedText,
                'metadata' => [
                    'upload_date' => now()->toISOString(),
                    'page_count' => $this->estimatePageCount($pdf->getSize()),
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF uploaded successfully!',
                'pdf' => $teacherPdf,
            ]);

        } catch (\Exception $e) {
            Log::error('PDF upload failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a PDF
     */
    public function deletePdf(TeacherPdf $pdf)
    {
        if ($pdf->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Delete file from storage
            if ($pdf->file_path && Storage::disk('public')->exists($pdf->file_path)) {
                Storage::disk('public')->delete($pdf->file_path);
            }

            // Delete database record
            $pdf->delete();

            return response()->json([
                'success' => true,
                'message' => 'PDF deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('PDF deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PDFs available for students in a classroom
     */
    public function getClassroomPdfs(Classroom $classroom)
    {
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $pdfs = TeacherPdf::where('classroom_id', $classroom->id)
            ->where('is_active', true)
            ->latest()
            ->get();

        return response()->json([
            'pdfs' => $pdfs,
        ]);
    }

    /**
     * Estimate page count from file size (rough approximation)
     */
    private function estimatePageCount($fileSize)
    {
        // Rough estimate: ~50KB per page
        return max(1, intval($fileSize / 50000));
    }
}
