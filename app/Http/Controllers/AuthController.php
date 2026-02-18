<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\TeacherRequest;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show the login/register page.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.login');
    }

    /**
     * Handle teacher account request submission.
     */
    public function requestTeacher(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teacher_requests,email|unique:users,email',
            'subject' => 'required|string|in:english,filipino',
            'grade_level' => 'required|in:7,8,9,10',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Save as a pending request, not as a user yet
        TeacherRequest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'grade_level' => $validated['grade_level'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('login', ['role' => 'teacher', 'tab' => 'signin'])
            ->with('success', 'Request sent! An administrator will review your request and create your account once approved.');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required',
            'role' => 'nullable|in:student,teacher,admin',
        ]);

        $remember = $request->boolean('remember');
        $requestedRole = $credentials['role'] ?? null;

        $authCredentials = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($authCredentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($requestedRole && $user->role !== $requestedRole) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'This account is not authorized for the selected portal.',
                ])->withInput($request->except('password'));
            }

            // Restore joined classes from database for students
            if ($user->role === 'student') {
                $this->restoreStudentClasses($user);
            }

            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|in:student,teacher',
            'password' => 'required|string|min:8|confirmed',
            'grade_level' => 'required_unless:role,teacher|in:7,8,9,10',
        ]);

        $role = $validated['role'] ?? 'student';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'status' => $role === 'teacher' ? 'pending' : 'active',
        ]);

        if ($role === 'teacher') {
            TeacherProfile::create([
                'user_id' => $user->id,
                'phone' => $request->input('phone'),
                'subject' => $request->input('subject'),
                'grade_level' => $request->input('grade_level'),
            ]);

            return redirect()->route('login')->with('success', 'Account created! An administrator will review your teacher registration.');
        }

        $profileData = [
            'user_id' => $user->id,
            'grade_level' => $validated['grade_level'] ?? null,
            'section' => $request->input('section'),
        ];

        if (Schema::hasColumn('student_profiles', 'name')) {
            $profileData['name'] = $validated['name'];
        }

        if (Schema::hasColumn('student_profiles', 'email')) {
            $profileData['email'] = $validated['email'];
        }

        StudentProfile::create($profileData);

        return redirect()->route('login', ['role' => 'student', 'tab' => 'signin'])
            ->with('success', 'Account created! Please sign in with your new credentials.');
    }

    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and log them in.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Unable to sign in with Google. Please try again.');
        }

        $email = $googleUser->getEmail();

        if (!$email) {
            return redirect()->route('login')->with('error', 'Google account does not have an email address associated.');
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: $email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'student',
                'status' => 'active',
            ]
        );

        if ($user->wasRecentlyCreated) {
            $profileData = [
                'user_id' => $user->id,
                'grade_level' => null,
                'section' => null,
            ];

            if (Schema::hasColumn('student_profiles', 'name')) {
                $profileData['name'] = $user->name;
            }

            if (Schema::hasColumn('student_profiles', 'email')) {
                $profileData['email'] = $user->email;
            }

            StudentProfile::create($profileData);
        }

        Auth::login($user, true);

        // Restore joined classes from database for students
        if ($user->role === 'student') {
            $this->restoreStudentClasses($user);
        }

        return $this->redirectToDashboard();
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect to appropriate dashboard based on user role.
     */
    protected function redirectToDashboard()
    {
        $user = Auth::user();

        if ($user->role === 'student') {
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

            if (empty($profile->grade_level)) {
                return redirect()->route('student.grade.select');
            }
        }

        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    }

    /**
     * Restore student's joined classes from database to session.
     */
    protected function restoreStudentClasses(User $user): void
    {
        if ($user->role !== 'student') {
            return;
        }

        $profile = $user->studentProfile;
        if (!$profile || !$profile->classroom_id) {
            return;
        }

        $classroom = $profile->classroom;
        if (!$classroom) {
            // Clean up orphaned classroom_id
            $profile->classroom_id = null;
            $profile->save();
            return;
        }

        // Get current session classes
        $joinedClasses = collect(session('joined_classes', []));

        // Check if class is already in session
        $exists = $joinedClasses->contains(fn ($item) => (int) ($item['id'] ?? 0) === $classroom->id);

        if (!$exists) {
            // Add the class from database to session
            $joinedClasses->push([
                'id' => $classroom->id,
                'name' => $classroom->name ?? 'Class',
                'join_code' => strtoupper($classroom->join_code ?? ''),
                'schedule' => $classroom->schedule ?? 'Schedule to be announced',
                'slug' => $classroom->slug ?? \Illuminate\Support\Str::slug($classroom->name ?? 'class'),
            ]);

            session(['joined_classes' => $joinedClasses->all()]);
        }
    }
}
