<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherPdf;
use App\Models\User;
use App\Models\PronunciationStat;
use App\Models\StudentProfile;
use App\Models\Classroom;
use App\Models\ClassAnnouncement;
use App\Models\StudentPerformance;
use App\Models\SkillResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class StudentController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Real-time cleanup of deleted classes
        $this->cleanupDeletedClasses($user);
        
        // Update streak if needed
        if ($user->role === 'student') {
            try {
                $user->updateStreak();
                $user->refresh();
            } catch (\Exception $e) {
                // Continue if streak update fails
            }
        }
        
        // Get leaderboard
        $leaderboard = collect();
        $userRank = 1;
        
        try {
            $leaderboard = User::getLeaderboard(10);
            
            // Get user's rank
            $userRank = User::where('role', 'student')
                ->where(function($query) use ($user) {
                    $query->where('points', '>', $user->points ?? 0)
                        ->orWhere(function($q) use ($user) {
                            $q->where('points', '=', $user->points ?? 0)
                              ->where('level', '>', $user->level ?? 1);
                        })
                        ->orWhere(function($q) use ($user) {
                            $q->where('points', '=', $user->points ?? 0)
                              ->where('level', '=', $user->level ?? 1)
                              ->where('experience', '>', $user->experience ?? 0);
                        });
                })
                ->count() + 1;
        } catch (\Exception $e) {
            // Continue if leaderboard query fails
        }

        $recentPerformances = collect();
        $completedCount = 0;
        $perfectCount = 0;
        try {
            $recentPerformances = StudentPerformance::where('student_id', $user->id)
                ->whereNotNull('completed_at')
                ->with(['quest', 'classroom'])
                ->orderByDesc('completed_at')
                ->take(5)
                ->get();

            $completedCount = StudentPerformance::where('student_id', $user->id)
                ->whereNotNull('completed_at')
                ->count();

            $perfectCount = StudentPerformance::where('student_id', $user->id)
                ->whereNotNull('completed_at')
                ->where('accuracy_percentage', '>=', 100)
                ->count();
        } catch (\Exception $e) {
            // Continue if recent activity query fails
        }

        if ($user->role === 'student') {
            try {
                if ($completedCount >= 1) {
                    $user->addAchievement('first_steps');
                }
                if ($completedCount >= 5) {
                    $user->addAchievement('quick_learner');
                }
                if (($user->streak_days ?? 0) >= 7) {
                    $user->addAchievement('dedicated');
                }
                if (($user->level ?? 1) >= 2) {
                    $user->addAchievement('level_up_2');
                }
                if (($user->level ?? 1) >= 5) {
                    $user->addAchievement('level_up_5');
                }
                if (($user->level ?? 1) >= 10) {
                    $user->addAchievement('expert');
                }
                if ($userRank <= 10) {
                    $user->addAchievement('champion');
                }
                if ($perfectCount >= 10) {
                    $user->addAchievement('perfectionist');
                }
            } catch (\Exception $e) {
                // Don't block dashboard if achievement awarding fails
            }
        }
        
        return view('student-dashboard', [
            'user' => $user,
            'leaderboard' => $leaderboard,
            'userRank' => $userRank,
            'joinedClasses' => $this->getValidJoinedClasses($user),
            'recentPerformances' => $recentPerformances,
            'achievementCatalog' => [
                'first_steps' => ['name' => 'First Steps', 'icon' => '🎯', 'desc' => 'Complete your first assignment'],
                'quick_learner' => ['name' => 'Quick Learner', 'icon' => '⚡', 'desc' => 'Complete 5 assignments'],
                'dedicated' => ['name' => 'Dedicated', 'icon' => '🔥', 'desc' => '7-day streak'],
                'expert' => ['name' => 'Expert', 'icon' => '⭐', 'desc' => 'Reach level 10'],
                'champion' => ['name' => 'Champion', 'icon' => '🏆', 'desc' => 'Top 10 in leaderboard'],
                'perfectionist' => ['name' => 'Perfectionist', 'icon' => '💯', 'desc' => 'Score 100% on 10 quizzes'],
                'level_up_2' => ['name' => 'Level Up!', 'icon' => '⬆️', 'desc' => 'Reach level 2'],
                'level_up_5' => ['name' => 'Rising Star', 'icon' => '🌟', 'desc' => 'Reach level 5'],
            ],
            'announcements' => ClassAnnouncement::with('classroom')
                ->whereIn('classroom_id', $this->getJoinedClassroomIds($user))
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Display the calendar dashboard.
     */
    public function calendar()
    {
        $user = auth()->user();
        
        // Real-time cleanup of deleted classes
        $this->cleanupDeletedClasses($user);
        
        return view('student-calendar-dashboard', [
            'user' => $user,
            'joinedClasses' => $this->getValidJoinedClasses($user),
        ]);
    }

    public function settings()
    {
        $user = Auth::user();

        $profileDefaults = [
            'grade_level' => null,
            'section' => null,
        ];

        if (Schema::hasColumn('student_profiles', 'name')) {
            $profileDefaults['name'] = $user->name;
        }

        if (Schema::hasColumn('student_profiles', 'email')) {
            $profileDefaults['email'] = $user->email;
        }

        $profile = StudentProfile::firstOrCreate(
            ['user_id' => $user->id],
            $profileDefaults
        );

        return view('student-settings-dashboard', [
            'user' => $user,
            'profile' => $profile,
            'joinedClasses' => $this->getValidJoinedClasses($user),
        ]);
    }

    public function profile()
    {
        $user = Auth::user();

        $profileDefaults = [
            'grade_level' => null,
            'section' => null,
        ];

        if (Schema::hasColumn('student_profiles', 'name')) {
            $profileDefaults['name'] = $user->name;
        }

        if (Schema::hasColumn('student_profiles', 'email')) {
            $profileDefaults['email'] = $user->email;
        }

        $profile = StudentProfile::firstOrCreate(
            ['user_id' => $user->id],
            $profileDefaults
        );

        $leaderboard = collect();
        $userRank = 1;
        try {
            $leaderboard = User::getLeaderboard(10);

            $userRank = User::where('role', 'student')
                ->where(function($query) use ($user) {
                    $query->where('points', '>', $user->points ?? 0)
                        ->orWhere(function($q) use ($user) {
                            $q->where('points', '=', $user->points ?? 0)
                              ->where('level', '>', $user->level ?? 1);
                        })
                        ->orWhere(function($q) use ($user) {
                            $q->where('points', '=', $user->points ?? 0)
                              ->where('level', '=', $user->level ?? 1)
                              ->where('experience', '>', $user->experience ?? 0);
                        });
                })
                ->count() + 1;
        } catch (\Exception $e) {
            // Continue if leaderboard query fails
        }

        return view('student-profile-dashboard', [
            'user' => $user,
            'profile' => $profile,
            'leaderboard' => $leaderboard,
            'userRank' => $userRank,
            'achievementCatalog' => [
                'first_steps' => ['name' => 'First Steps', 'icon' => '🎯', 'desc' => 'Complete your first assignment'],
                'quick_learner' => ['name' => 'Quick Learner', 'icon' => '⚡', 'desc' => 'Complete 5 assignments'],
                'dedicated' => ['name' => 'Dedicated', 'icon' => '🔥', 'desc' => '7-day streak'],
                'expert' => ['name' => 'Expert', 'icon' => '⭐', 'desc' => 'Reach level 10'],
                'champion' => ['name' => 'Champion', 'icon' => '🏆', 'desc' => 'Top 10 in leaderboard'],
                'perfectionist' => ['name' => 'Perfectionist', 'icon' => '💯', 'desc' => 'Score 100% on 10 quizzes'],
                'level_up_2' => ['name' => 'Level Up!', 'icon' => '⬆️', 'desc' => 'Reach level 2'],
                'level_up_5' => ['name' => 'Rising Star', 'icon' => '🌟', 'desc' => 'Reach level 5'],
            ],
            'joinedClasses' => $this->getValidJoinedClasses($user),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'grade_level' => 'nullable|in:7,8,9,10',
            'section' => 'nullable|string|max:255',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        $profile = StudentProfile::firstOrNew(['user_id' => $user->id]);

        if (Schema::hasColumn('student_profiles', 'name')) {
            $profile->name = $validated['name'];
        }

        if (Schema::hasColumn('student_profiles', 'email')) {
            $profile->email = $validated['email'];
        }

        if (Schema::hasColumn('student_profiles', 'grade_level')) {
            $profile->grade_level = $validated['grade_level'] ?? null;
        }

        if (Schema::hasColumn('student_profiles', 'section')) {
            $profile->section = $validated['section'] ?? null;
        }

        $profile->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Your current password is incorrect.');
        }

        $user->password = $validated['password'];

        if (Schema::hasColumn('users', 'must_change_password')) {
            $user->must_change_password = false;
        }

        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function quizAttempts()
    {
        $user = Auth::user();

        $attempts = collect();
        try {
            $attempts = StudentPerformance::where('student_id', $user->id)
                ->whereNotNull('completed_at')
                ->with(['quest', 'classroom'])
                ->orderByDesc('completed_at')
                ->get();
        } catch (\Exception $e) {
            $attempts = collect();
        }

        return view('student-quiz-attempts', [
            'user' => $user,
            'attempts' => $attempts,
            'joinedClasses' => $this->getValidJoinedClasses($user),
        ]);
    }

    public function quizAttemptDetail($performanceId)
    {
        $user = Auth::user();

        $performance = StudentPerformance::with(['quest', 'classroom'])
            ->where('id', $performanceId)
            ->where('student_id', $user->id)
            ->firstOrFail();

        $wrongItems = [];

        foreach (($performance->reading_scores ?? []) as $row) {
            $isCorrect = (bool) ($row['correct'] ?? false);
            if ($isCorrect) {
                continue;
            }
            $wrongItems[] = [
                'type' => 'reading',
                'prompt' => $row['question'] ?? '',
                'student_answer' => $row['student_answer'] ?? ($row['student_response'] ?? ''),
                'correct_answer' => $row['correct_answer'] ?? '',
            ];
        }

        foreach (($performance->pronunciation_scores ?? []) as $row) {
            $isCorrect = (bool) ($row['correct'] ?? false);
            if ($isCorrect) {
                continue;
            }
            $wrongItems[] = [
                'type' => 'pronunciation',
                'prompt' => $row['word'] ?? '',
                'student_answer' => $row['student_response'] ?? '',
                'correct_answer' => $row['word'] ?? '',
            ];
        }

        $skillWrong = collect();
        try {
            $skillWrong = SkillResponse::where('student_id', $user->id)
                ->where('performance_id', $performance->id)
                ->where('is_correct', false)
                ->orderByDesc('responded_at')
                ->get();
        } catch (\Exception $e) {
            $skillWrong = collect();
        }

        return view('student-quiz-attempt-detail', [
            'user' => $user,
            'performance' => $performance,
            'wrongItems' => $wrongItems,
            'skillWrong' => $skillWrong,
            'joinedClasses' => $this->getValidJoinedClasses($user),
        ]);
    }

    /**
     * Real-time cleanup of deleted classes from student's session and profile
     */
    private function cleanupDeletedClasses($user)
    {
        $joinedClasses = collect(session('joined_classes', []));
        $deletedClasses = [];
        
        // Check each class if it still exists
        foreach ($joinedClasses as $class) {
            $classId = $class['id'] ?? null;
            if ($classId && !Classroom::find($classId)) {
                $deletedClasses[] = $classId;
            }
        }
        
        // Remove deleted classes from session
        if (!empty($deletedClasses)) {
            $validClasses = $joinedClasses->filter(function ($class) use ($deletedClasses) {
                $classId = $class['id'] ?? null;
                return !in_array($classId, $deletedClasses);
            });
            session(['joined_classes' => $validClasses->all()]);
            
            // Clean up student profile if it references a deleted class
            $profile = $user->studentProfile;
            if ($profile && $profile->classroom_id && in_array($profile->classroom_id, $deletedClasses)) {
                $profile->classroom_id = null;
                $profile->save();
            }
            
            // Flash message about deleted classes
            if (count($deletedClasses) === 1) {
                session()->flash('info', 'A class you joined is no longer available.');
            } else {
                session()->flash('info', count($deletedClasses) . ' classes you joined are no longer available.');
            }
        }
    }

    /**
     * Get pronunciation stats for the authenticated user
     */
    public function getPronunciationStats()
    {
        $user = Auth::user();
        
        $stats = PronunciationStat::firstOrCreate(
            ['user_id' => $user->id],
            [
                'total_practiced' => 0,
                'total_accuracy' => 0,
                'attempts' => 0,
                'streak' => 0,
            ]
        );

        return response()->json([
            'totalPracticed' => $stats->total_practiced,
            'totalAccuracy' => $stats->total_accuracy,
            'attempts' => $stats->attempts,
            'streak' => $stats->streak,
        ]);
    }

    /**
     * Save pronunciation stats for the authenticated user
     */
    public function savePronunciationStats(Request $request)
    {
        $request->validate([
            'totalPracticed' => 'required|integer|min:0',
            'totalAccuracy' => 'required|integer|min:0',
            'attempts' => 'required|integer|min:0',
            'streak' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        
        $stats = PronunciationStat::updateOrCreate(
            ['user_id' => $user->id],
            [
                'total_practiced' => $request->totalPracticed,
                'total_accuracy' => $request->totalAccuracy,
                'attempts' => $request->attempts,
                'streak' => $request->streak,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Pronunciation stats saved successfully',
            'stats' => [
                'totalPracticed' => $stats->total_practiced,
                'totalAccuracy' => $stats->total_accuracy,
                'attempts' => $stats->attempts,
                'streak' => $stats->streak,
            ]
        ]);
    }

    /**
     * Allow students to join a class using a code provided by their teacher.
     */
    public function joinClass(Request $request)
    {
        $validated = $request->validate([
            'join_code' => 'required|string|max:50',
        ]);

        $joinCode = strtoupper(trim($validated['join_code']));
        $class = Classroom::whereRaw('UPPER(join_code) = ?', [$joinCode])->first();

        if (!$class) {
            return back()->with('error', 'That class code was not recognized. Double-check with your teacher.');
        }

        $joined = collect(session('joined_classes', []));

        if ($joined->contains(fn ($item) => strtoupper($item['join_code']) === $joinCode)) {
            return back()->with('success', 'You already joined ' . ($class->name ?? 'this class') . '.');
        }

        $joined->push([
            'id' => $class->id,
            'name' => $class->name ?? 'Class',
            'join_code' => strtoupper($class->join_code ?? $joinCode),
            'schedule' => $class->schedule ?? 'Schedule to be announced',
            'slug' => $class->slug ?? Str::slug($class->join_code ?? $joinCode),
        ]);

        session(['joined_classes' => $joined->all()]);

        $profile = StudentProfile::firstOrNew(['user_id' => Auth::id()]);
        if (Schema::hasColumn('student_profiles', 'classroom_id')) {
            $profile->classroom_id = $class->id;
        }
        $profile->save();

        return back()->with('success', 'Welcome to ' . ($class->name ?? 'your new class') . '! You can find it under Your Classes.');
    }

    protected function getJoinedClassroomIds($user): array
    {
        $ids = collect(session('joined_classes', []))
            ->pluck('id')
            ->filter()
            ->values();

        if (Schema::hasColumn('student_profiles', 'classroom_id')) {
            $profileClassroomId = optional($user->studentProfile)->classroom_id;
            if ($profileClassroomId) {
                // Check if classroom still exists
                if (Classroom::find($profileClassroomId)) {
                    $ids->push($profileClassroomId);
                } else {
                    // Clean up orphaned reference
                    $profile = $user->studentProfile;
                    if ($profile) {
                        $profile->classroom_id = null;
                        $profile->save();
                    }
                }
            }
        }

        return $ids->unique()->all();
    }

    /**
     * Get valid joined classes (filter out deleted classes)
     */
    protected function getValidJoinedClasses($user): array
    {
        $joinedClasses = collect(session('joined_classes', []));
        
        // Filter out classes that no longer exist
        $validClasses = $joinedClasses->filter(function ($class) {
            $classroomId = $class['id'] ?? null;
            if (!$classroomId) {
                return false;
            }
            return Classroom::find($classroomId);
        });

        // Update session with filtered classes
        session(['joined_classes' => $validClasses->all()]);

        return $validClasses->all();
    }

    public function announcementsFeed(Request $request)
    {
        $user = Auth::user();
        $after = $request->query('after');

        $query = ClassAnnouncement::with('classroom')
            ->whereIn('classroom_id', $this->getJoinedClassroomIds($user))
            ->latest();

        if ($after) {
            try {
                $afterTime = Carbon::parse($after);
                $query->where(function ($q) use ($afterTime) {
                    $q->where('sent_at', '>', $afterTime)
                        ->orWhere(function ($inner) use ($afterTime) {
                            $inner->whereNull('sent_at')
                                ->where('created_at', '>', $afterTime);
                        });
                });
            } catch (\Exception $e) {
                // ignore invalid timestamp
            }
        }

        $announcements = $query->take(10)->get()->map(function ($announcement) {
            $timestamp = $announcement->created_at;

            return [
                'id' => $announcement->id,
                'classroom' => $announcement->classroom?->name ?? 'Classroom',
                'title' => $announcement->title,
                'body' => $announcement->message,
                'timestamp_human' => $timestamp?->diffForHumans(),
                'timestamp' => $timestamp?->toIso8601String(),
            ];
        });

        return response()->json([
            'announcements' => $announcements,
        ]);
    }

    /**
     * Show the grade selection screen for students who signed in without a grade level.
     */
    public function showGradeSelection()
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            return redirect()->route('home');
        }

        $profile = $user->studentProfile;

        if ($profile && $profile->grade_level) {
            return redirect()->route('student.dashboard');
        }

        return view('student.grade-selection', [
            'user' => $user,
            'selectedGrade' => old('grade_level', $profile?->grade_level),
        ]);
    }

    /**
     * Persist the selected grade level for the authenticated student.
     */
    public function storeGradeSelection(Request $request)
    {
        $validated = $request->validate([
            'grade_level' => 'required|in:7,8,9,10',
        ]);

        $user = Auth::user();

        if ($user->role !== 'student') {
            return redirect()->route('home');
        }

        $profile = StudentProfile::firstOrNew(['user_id' => $user->id]);
        $profile->grade_level = $validated['grade_level'];

        if (Schema::hasColumn('student_profiles', 'name') && empty($profile->name)) {
            $profile->name = $user->name;
        }

        if (Schema::hasColumn('student_profiles', 'email') && empty($profile->email)) {
            $profile->email = $user->email;
        }

        $profile->save();

        return redirect()->route('student.dashboard')->with('success', 'Grade level saved! Welcome to Q2L.');
    }

    /**
     * Display PDF reading interface for students
     */
    public function pdfReader()
    {
        $user = Auth::user();
        
        // Get PDFs from student's joined classes AND general PDFs (classroom_id is null)
        $joinedClassIds = $this->getJoinedClassroomIds($user);
        
        $pdfs = TeacherPdf::where(function($query) use ($joinedClassIds) {
                // PDFs assigned to student's classes
                $query->whereIn('classroom_id', $joinedClassIds)
                      // OR general PDFs (not assigned to any specific class)
                      ->orWhereNull('classroom_id');
            })
            ->where('is_active', true)
            ->with('classroom', 'teacher')
            ->latest()
            ->get();

        return view('student.pdf-reader', [
            'user' => $user,
            'pdfs' => $pdfs,
            'joinedClasses' => $this->getValidJoinedClasses($user),
        ]);
    }

    /**
     * Get PDF content for reading with recording
     */
    public function getPdfContent(TeacherPdf $pdf)
    {
        $user = Auth::user();
        
        // Check if student has access to this PDF
        $joinedClassIds = $this->getJoinedClassroomIds($user);
        
        // Allow access if:
        // 1. PDF is assigned to one of student's classes, OR
        // 2. PDF is general (not assigned to any specific class)
        if (!in_array($pdf->classroom_id, $joinedClassIds) && $pdf->classroom_id !== null) {
            abort(403, 'You do not have access to this PDF.');
        }

        // skill_responses.classroom_id is NOT nullable in the current schema.
        // For "General" PDFs (classroom_id = null), associate the session with one of the student's joined classrooms.
        $classroomIdForTracking = $pdf->classroom_id;
        if ($classroomIdForTracking === null) {
            $classroomIdForTracking = !empty($joinedClassIds) ? $joinedClassIds[0] : null;
        }
        if ($classroomIdForTracking === null) {
            abort(422, 'No classroom found for tracking. Please join a class first.');
        }

        return response()->json([
            'pdf' => [
                'id' => $pdf->id,
                'title' => $pdf->title,
                'description' => $pdf->description,
                'file_url' => $pdf->file_url,
                'extracted_text' => $pdf->extracted_text,
                'classroom' => $pdf->classroom?->name,
                'teacher' => $pdf->teacher?->name,
            ]
        ]);
    }

    /**
     * Save pronunciation recording data
     */
    public function savePronunciationRecording(Request $request)
    {
        $request->validate([
            'pdf_id' => 'required|exists:teacher_pdfs,id',
            'difficult_words' => 'required',
            'recording_duration' => 'required|integer|min:1',
            'attempts' => 'nullable|integer|min:0|max:10',
        ]);

        $user = Auth::user();
        
        // Verify access to PDF
        $pdf = TeacherPdf::findOrFail($request->pdf_id);
        $joinedClassIds = $this->getJoinedClassroomIds($user);
        
        // Allow access if:
        // 1. PDF is assigned to one of student's classes, OR
        // 2. PDF is general (not assigned to any specific class)
        if (!in_array($pdf->classroom_id, $joinedClassIds) && $pdf->classroom_id !== null) {
            abort(403, 'You do not have access to this PDF.');
        }

        // skill_responses.classroom_id is NOT nullable in the current schema.
        // For "General" PDFs (classroom_id = null), associate the session with one of the student's joined classrooms.
        $classroomIdForTracking = $pdf->classroom_id;
        if ($classroomIdForTracking === null) {
            $classroomIdForTracking = !empty($joinedClassIds) ? $joinedClassIds[0] : null;
        }
        if ($classroomIdForTracking === null) {
            abort(422, 'No classroom found for tracking. Please join a class first.');
        }

        // Normalize difficult_words (frontend may send JSON string or array)
        $difficultWordsInput = $request->input('difficult_words');
        if (is_string($difficultWordsInput)) {
            $decoded = json_decode($difficultWordsInput, true);
            $difficultWords = is_array($decoded) ? $decoded : [];
        } elseif (is_array($difficultWordsInput)) {
            $difficultWords = $difficultWordsInput;
        } else {
            $difficultWords = [];
        }

        // Ensure all entries are strings
        $difficultWords = array_values(array_filter(array_map(function ($w) {
            return is_scalar($w) ? (string) $w : null;
        }, $difficultWords)));

        // Analyze difficult words and provide feedback
        $attempts = $request->attempts ?? 1;
        $recordingDuration = $request->recording_duration;
        
        // Generate AI-like feedback based on performance
        $totalWords = 0;
        if (!empty($pdf->extracted_text)) {
            $totalWords = preg_match_all('/\b[\p{L}\p{N}"]+\b/u', (string) $pdf->extracted_text);
        }
        $feedback = $this->generateReadingFeedback($difficultWords, $attempts, $recordingDuration, $totalWords);

        // Save pronunciation tracking data
        $skillResponse = SkillResponse::create([
            'student_id' => $user->id,
            'skill_type' => 'pdf_reading',
            'classroom_id' => $classroomIdForTracking,
            'quest_id' => null,
            'problem_type' => 'pdf_reading',
            'problem_content' => $pdf->title,
            'student_response' => json_encode([
                'difficult_words' => $difficultWords,
                'recording_duration' => $recordingDuration,
                'pdf_id' => $pdf->id,
                'attempts' => $attempts,
                'feedback' => $feedback,
            ]),
            'correct_answer' => '',
            'is_correct' => true,
            'accuracy_score' => $feedback['accuracy_score'],
            'attempts' => $attempts,
            'response_time_seconds' => $recordingDuration,
            'responded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reading session saved successfully!',
            'feedback' => $feedback,
            'tracking_id' => $skillResponse->id,
        ]);
    }

    /**
     * Generate AI-like reading feedback based on performance
     */
    private function generateReadingFeedback($difficultWords, $attempts, $duration, $totalWords = 0)
    {
        $wordCount = count($difficultWords);

        // Accuracy scoring
        // If we know total words, compute % of words NOT marked difficult.
        // Otherwise fallback to a small penalty system.
        if (is_int($totalWords) && $totalWords > 0) {
            $accuracyScore = round(max(0, min(100, 100 - (($wordCount / $totalWords) * 100))), 2);
        } else {
            $accuracyScore = round(max(0, min(100, 100 - ($wordCount * 5))), 2);
        }
        
        $suggestions = [];
        
        // Generate suggestions based on performance
        if ($wordCount === 0) {
            $suggestions[] = 'Excellent reading! No difficult words detected.';
            $suggestions[] = 'You have great pronunciation skills!';
        } elseif ($wordCount <= 3) {
            $suggestions[] = 'Good job! Only a few words need practice.';
            $suggestions[] = 'Focus on the highlighted words for next time.';
        } elseif ($wordCount <= 6) {
            $suggestions[] = 'Keep practicing! Focus on the highlighted words.';
            $suggestions[] = 'Try reading more slowly and clearly.';
        } else {
            $suggestions[] = 'Consider reading more slowly and practicing difficult words individually.';
            $suggestions[] = 'Break down difficult words into smaller parts.';
        }

        // Add attempt-based feedback
        if ($attempts > 1) {
            if ($wordCount < $attempts * 2) {
                $suggestions[] = 'Great improvement with each attempt!';
            } else {
                $suggestions[] = 'Keep trying - practice makes perfect!';
            }
        }

        // Add duration-based feedback
        if ($duration < 30) {
            $suggestions[] = 'Try reading at a more moderate pace.';
        } elseif ($duration > 300) {
            $suggestions[] = 'Good pacing! Your reading speed is appropriate.';
        }

        return [
            'accuracy_score' => $accuracyScore,
            'difficult_words_count' => $wordCount,
            'attempts' => $attempts,
            'duration_seconds' => $duration,
            'suggestions' => $suggestions,
            'performance_level' => $this->getPerformanceLevel($accuracyScore),
        ];
    }

    /**
     * Get performance level based on accuracy score
     */
    private function getPerformanceLevel($score)
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 80) return 'Good';
        if ($score >= 70) return 'Fair';
        if ($score >= 60) return 'Needs Practice';
        return 'Keep Trying';
    }
}
