<?php

namespace App\Http\Controllers;

use App\Models\TeacherProfile;
use App\Models\TeacherRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $activeTeacherIds = $this->activeTeacherIds();
        $stats = $this->getTeacherStats($activeTeacherIds);

        $recentTeachers = $this->recentTeachers()->map(function ($teacher) use ($activeTeacherIds) {
            $teacher->display_status = in_array($teacher->id, $activeTeacherIds, true) ? 'Active' : 'Offline';
            $teacher->display_status_class = in_array($teacher->id, $activeTeacherIds, true)
                ? 'bg-emerald-100 text-emerald-700'
                : 'bg-slate-200 text-slate-700';
            return $teacher;
        });

        $pendingTeachers = $this->pendingTeachers()->map(function ($teacher) {
            $teacher->created_at_diff = optional($teacher->created_at)->diffForHumans();
            return $teacher;
        });

        // Get recent activities (you can create an Activity model later)
        $recent_activities = collect([]); // Empty for now, implement Activity model if needed

        return view('admin-dashboard', compact('stats', 'recent_activities', 'recentTeachers', 'pendingTeachers'));
    }

    public function stats()
    {
        $activeTeacherIds = $this->activeTeacherIds();

        $recentTeachers = $this->recentTeachers()->map(function ($teacher) use ($activeTeacherIds) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'phone' => $teacher->phone,
                'subject' => $teacher->subject,
                'grade_level' => $teacher->grade_level,
                'status' => in_array($teacher->id, $activeTeacherIds, true) ? 'active' : 'offline',
                'joined_at' => optional($teacher->created_at)->format('M d, Y'),
            ];
        });

        $pendingTeachers = $this->pendingTeachers()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'subject' => $teacher->subject,
                'grade_level' => $teacher->grade_level,
                'notes' => $teacher->notes,
                'created_at_diff' => optional($teacher->created_at)->diffForHumans(),
            ];
        });

        return response()->json([
            'stats' => $this->getTeacherStats($activeTeacherIds),
            'recent_teachers' => $recentTeachers,
            'pending_teachers' => $pendingTeachers,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Compute teacher-related statistics.
     */
    protected function getTeacherStats(array $activeTeacherIds = null): array
    {
        $sessionLifetime = config('session.lifetime', 120);
        $activeTeacherIds = $activeTeacherIds ?? $this->activeTeacherIds();
        $activeTeacherCount = count($activeTeacherIds);

        return [
            'total_teachers' => User::where('role', 'teacher')->count(),
            'active_teachers' => $activeTeacherCount,
            'pending_approvals' => TeacherRequest::count(),
            'session_window_minutes' => $sessionLifetime,
        ];
    }

    protected function activeTeacherIds(): array
    {
        $sessionLifetime = config('session.lifetime', 120);

        return DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('users.role', 'teacher')
            ->whereNotNull('sessions.user_id')
            ->where('sessions.last_activity', '>=', now()->subMinutes($sessionLifetime)->timestamp)
            ->pluck('sessions.user_id')
            ->unique()
            ->values()
            ->all();
    }

    protected function recentTeachers()
    {
        return User::where('users.role', 'teacher')
            ->where('users.status', 'active')
            ->join('teacher_profiles', 'users.id', '=', 'teacher_profiles.user_id')
            ->orderByDesc('users.created_at')
            ->select(
                'users.id', 
                'users.name', 
                'users.email', 
                'teacher_profiles.phone', 
                'teacher_profiles.subject', 
                'teacher_profiles.grade_level', 
                'users.status', 
                'users.created_at'
            )
            ->take(6)
            ->get();
    }

    protected function pendingTeachers()
    {
        return TeacherRequest::orderByDesc('created_at')
            ->select('id', 'name', 'email', 'subject', 'grade_level', 'notes', 'created_at')
            ->take(5)
            ->get();
    }

    /**
     * Store a newly created teacher.
     */
    public function storeTeacher(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|in:english,filipino',
            'grade_level' => 'required|integer|in:7,8,9,10',
            'password' => 'required|string|min:8',
        ]);

        try {
            // Create the teacher user
            $teacher = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'teacher',
                'status' => 'active',
                'must_change_password' => true,
            ]);

            TeacherProfile::updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'phone' => $validated['phone'],
                    'subject' => $validated['subject'],
                    'grade_level' => (string) $validated['grade_level'],
                ]
            );

            // TODO: Send email notification to teacher with credentials

            return redirect()->route('admin.dashboard')
                ->with('success', 'Teacher registered successfully! An email has been sent with login credentials.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to register teacher. Please try again.');
        }
    }

    public function approveTeacher(Request $request, TeacherRequest $teacherRequest)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $temporaryPassword = $validated['password'];

        try {
            DB::beginTransaction();

            // Create the actual user account
            $teacher = User::create([
                'name' => $teacherRequest->name,
                'email' => $teacherRequest->email,
                'password' => $temporaryPassword, // Will be hashed by the model
                'role' => 'teacher',
                'status' => 'active',
                'must_change_password' => true,
            ]);

            // Create teacher profile with data from the request
            TeacherProfile::create([
                'user_id' => $teacher->id,
                'phone' => null,
                'subject' => $teacherRequest->subject,
                'grade_level' => $teacherRequest->grade_level ? (string) $teacherRequest->grade_level : null,
            ]);

            // Delete the teacher request after successful approval
            $teacherRequest->delete();

            DB::commit();

            return redirect()->route('admin.dashboard')
                ->with('success', "Teacher approved successfully! Temporary password: {$temporaryPassword}");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to approve teacher: ' . $e->getMessage());
        }
    }

    public function rejectTeacher(Request $request, TeacherRequest $teacherRequest)
    {
        // Delete the rejected request
        $teacherRequest->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Teacher request has been declined and removed.');
    }

    public function destroyTeacher(User $teacher)
    {
        if ($teacher->role !== 'teacher') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only teacher accounts can be deleted.');
        }

        $teacher->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Teacher account removed.');
    }

    public function updateTeacher(Request $request, User $teacher)
    {
        if ($teacher->role !== 'teacher') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only teacher accounts can be updated.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|in:english,filipino',
            'grade_level' => 'nullable|integer|in:7,8,9,10',
        ]);

        $teacher->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ])->save();

        $teacher->refresh();

        TeacherProfile::updateOrCreate(
            ['user_id' => $teacher->id],
            [
                'phone' => $validated['phone'] ?? null,
                'subject' => $validated['subject'] ?? null,
                'grade_level' => $validated['grade_level'] ? (string) $validated['grade_level'] : null,
            ]
        );

        return redirect()->route('admin.dashboard')
            ->with('success', 'Teacher details updated successfully.');
    }
}
