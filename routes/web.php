<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StudentClassController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\TTSController;
use App\Http\Controllers\PronunciationWordController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    $activeStudents = User::where('role', 'student')
        ->where('status', 'active')
        ->count();

    return view('welcome', [
        'activeStudents' => $activeStudents,
    ]);
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/teacher/request', [AuthController::class, 'requestTeacher'])->name('teacher.request');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
        Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');
        Route::put('/teachers/{teacher}', [AdminController::class, 'updateTeacher'])->name('teachers.update');
        Route::post('/teacher-requests/{teacherRequest}/approve', [AdminController::class, 'approveTeacher'])->name('teachers.approve');
        Route::post('/teacher-requests/{teacherRequest}/reject', [AdminController::class, 'rejectTeacher'])->name('teachers.reject');
        Route::delete('/teachers/{teacher}', [AdminController::class, 'destroyTeacher'])->name('teachers.destroy');
    });

    // Teacher routes
    Route::prefix('teacher')->name('teacher.')->middleware('role:teacher')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::post('/classes', [TeacherController::class, 'storeClass'])->name('classes.store');
        Route::get('/classes/{class}', [TeacherController::class, 'classDashboard'])->name('classes.show');
        Route::get('/classes/{class}/performance', [TeacherController::class, 'performanceAnalytics'])->name('performance.analytics');
        Route::get('/classes/{class}/students/{studentId}', [TeacherController::class, 'studentPerformanceDetail'])->name('student.performance');
        Route::get('/classes/{class}/export', [TeacherController::class, 'exportPerformanceData'])->name('performance.export');
        Route::post('/performance/record', [TeacherController::class, 'recordPerformance'])->name('performance.record');
        Route::get('/classes/{classroom}/quests/{quest}', [TeacherController::class, 'showQuest'])->name('classes.quests.show');
        Route::get('/classes/{classroom}/quests/{quest}/performance', [TeacherController::class, 'getQuestPerformance'])->name('classes.quests.performance');
        Route::post('/classes/{classroom}/generate-quest', [TeacherController::class, 'generateQuest'])->name('classes.generate.quest');
        Route::delete('/classes/{classroom}/quests/{quest}', [TeacherController::class, 'deleteQuest'])->name('classes.quests.delete');
        Route::delete('/classes/{classroom}', [TeacherController::class, 'deleteClass'])->name('classes.delete');
        Route::post('/classes/{classroom}/announcements', [AnnouncementController::class, 'store'])->name('classes.announcements.store');
        Route::delete('/classes/{classroom}/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('classes.announcements.destroy');
        Route::get('/classes/{classroom}/skill-tracking', [TeacherController::class, 'skillTracking'])->name('classes.skill-tracking');
        Route::get('/classes/{classroom}/skill-tracking/data', [TeacherController::class, 'getSkillTrackingData'])->name('classes.skill-tracking.data');
        Route::get('/classes/{classroom}/students/{student}/skill-analysis', [TeacherController::class, 'getStudentSkillAnalysis'])->name('classes.student.skill-analysis');
        
        // PDF Library routes
        Route::get('/pdf-library', [TeacherController::class, 'pdfLibrary'])->name('pdf.library');
        Route::post('/pdf-upload', [TeacherController::class, 'uploadPdf'])->name('pdf.upload');
        Route::delete('/pdfs/{pdf}', [TeacherController::class, 'deletePdf'])->name('pdf.delete');
        Route::get('/classes/{classroom}/pdfs', [TeacherController::class, 'getClassroomPdfs'])->name('classes.pdfs');
        
        Route::post('/settings', [TeacherController::class, 'updateSettings'])->name('settings.update');
    });

    // Student routes
    Route::prefix('student')->name('student.')->middleware('role:student')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/calendar', [StudentController::class, 'calendar'])->name('calendar');
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [StudentController::class, 'updatePassword'])->name('profile.password');
        Route::get('/settings', [StudentController::class, 'settings'])->name('settings');
        Route::get('/quiz-attempts', [StudentController::class, 'quizAttempts'])->name('quiz.attempts');
        Route::get('/quiz-attempts/{performanceId}', [StudentController::class, 'quizAttemptDetail'])->name('quiz.attempts.show');
        Route::get('/pronunciation-stats', [StudentController::class, 'getPronunciationStats'])->name('pronunciation.stats');
        Route::post('/pronunciation-stats', [StudentController::class, 'savePronunciationStats'])->name('pronunciation.stats.save');
        Route::get('/select-grade', [StudentController::class, 'showGradeSelection'])->name('grade.select');
        Route::post('/select-grade', [StudentController::class, 'storeGradeSelection'])->name('grade.store');
        Route::post('/join-class', [StudentController::class, 'joinClass'])->name('join');
        Route::get('/announcements/feed', [StudentController::class, 'announcementsFeed'])->name('announcements.feed');
        Route::get('/classes/{classId}', [StudentClassController::class, 'show'])->name('classes.show');
        Route::get('/classes/{classroom}/deleted', [StudentClassController::class, 'handleDeletedClass'])->name('classes.deleted');
        Route::delete('/classes/{classId}/leave', [StudentClassController::class, 'leaveClass'])->name('classes.leave');
        
        // PDF Reader routes
        Route::get('/pdf-reader', [StudentController::class, 'pdfReader'])->name('pdf.reader');
        Route::get('/pdfs/{pdf}/content', [StudentController::class, 'getPdfContent'])->name('pdf.content');
        Route::post('/pdf-recording', [StudentController::class, 'savePronunciationRecording'])->name('pdf.recording.save');
        
        // Quiz/Quest routes
        Route::get('/classes/{classId}/quests/{questId}', [StudentClassController::class, 'showQuest'])->name('classes.quests.show');
        Route::post('/classes/{classId}/quests/{questId}/submit', [StudentClassController::class, 'submitQuest'])->name('classes.quests.submit');
    });

    // Pronunciation Tutor (accessible to students)
    Route::get('/pronunciation-tutor', [PronunciationWordController::class, 'index'])->name('pronunciation.tutor');
    Route::get('/pronunciation-tutor/english', [PronunciationWordController::class, 'english'])->name('pronunciation.tutor.english');
    Route::get('/pronunciation-tutor/filipino', [PronunciationWordController::class, 'filipino'])->name('pronunciation.tutor.filipino');

    Route::post('/assistant/chat', AIChatController::class)->name('assistant.chat');
    
    // PDF testing routes removed
    
    // TTS route for pronunciation tutor
    Route::get('/api/tts/speak', [TTSController::class, 'speak'])->name('tts.speak');
    
    // API routes for class status checking
    Route::get('/api/class/{classId}/status', function($classId) {
        $classroom = \App\Models\Classroom::find($classId);
        return response()->json([
            'exists' => $classroom !== null,
            'class' => $classroom ? [
                'id' => $classroom->id,
                'name' => $classroom->name
            ] : null
        ]);
    })->middleware(['web', 'auth']);
});
