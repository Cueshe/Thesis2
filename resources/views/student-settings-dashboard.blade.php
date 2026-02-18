<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings - Q2L</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/student/settings.css') }}" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --brand-primary: #4f46e5;
            --brand-primary-dark: #4338ca;
            --page-bg: #f8fafc;
            --card-bg: #ffffff;
            --sidebar-bg: rgba(255, 255, 255, 0.94);
            --surface-border: rgba(148, 163, 184, 0.45);
            --surface-border-strong: rgba(99, 102, 241, 0.18);
            --shadow-soft: 0 24px 55px -25px rgba(15, 23, 42, 0.3);
            --shadow-hover: 0 30px 60px -30px rgba(79, 70, 229, 0.35);
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --text-subtle: #7b8aa8;
            --text-inverse: #ffffff;
            --link-primary: #4f46e5;
            --link-primary-hover: #4338ca;
            --surface-muted: rgba(248, 250, 252, 0.92);
            --surface-muted-strong: rgba(241, 245, 255, 0.9);
            --surface-contrast: rgba(79, 70, 229, 0.16);
            --surface-accent: rgba(79, 70, 229, 0.12);
            --badge-bg: rgba(79, 70, 229, 0.12);
            --badge-text: #4338ca;
            --chip-bg: rgba(79, 70, 229, 0.12);
            --chip-text: #4338ca;
            --input-bg: rgba(255, 255, 255, 0.92);
            --input-bg-focus: #ffffff;
            --input-border: rgba(148, 163, 184, 0.45);
            --input-ring: rgba(79, 70, 229, 0.22);
        }

        .dark {
            color-scheme: dark;
            --brand-primary: #6366f1;
            --brand-primary-dark: #818cf8;
            --page-bg: #0f172a;
            --card-bg: rgba(15, 23, 42, 0.88);
            --sidebar-bg: rgba(15, 23, 42, 0.92);
            --surface-border: rgba(71, 85, 105, 0.55);
            --surface-border-strong: rgba(129, 140, 248, 0.4);
            --shadow-soft: 0 24px 55px -25px rgba(2, 6, 23, 0.7);
            --shadow-hover: 0 30px 70px -30px rgba(99, 102, 241, 0.55);
            --text-primary: #e2e8f0;
            --text-muted: #cbd5f5;
            --text-subtle: #94a3b8;
            --text-inverse: #0f172a;
            --link-primary: #a5b4fc;
            --link-primary-hover: #c7d2fe;
            --surface-muted: rgba(30, 41, 59, 0.78);
            --surface-muted-strong: rgba(30, 41, 59, 0.92);
            --surface-contrast: rgba(129, 140, 248, 0.24);
            --surface-accent: rgba(129, 140, 248, 0.22);
            --badge-bg: rgba(99, 102, 241, 0.22);
            --badge-text: #e0e7ff;
            --chip-bg: rgba(129, 140, 248, 0.22);
            --chip-text: #e0e7ff;
            --input-bg: rgba(15, 23, 42, 0.7);
            --input-bg-focus: rgba(15, 23, 42, 0.85);
            --input-border: rgba(71, 85, 105, 0.65);
            --input-ring: rgba(129, 140, 248, 0.35);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-bg);
            color: var(--text-primary);
            min-height: 100vh;
            margin: 0;
            transition: background 0.45s ease, color 0.45s ease;
        }

        .layout-shell {
            padding: 0.5rem 0.75rem;
        }
        @media (min-width: 768px) {
            .layout-shell {
                padding: 2rem 1rem;
            }
        }
        @media (min-width: 1024px) {
            .layout-shell {
                padding: 2.5rem 1.5rem;
            }
        }

        .dashboard-sidebar {
            background: var(--sidebar-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
            box-shadow: var(--shadow-soft);
            transition: background-color 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease, color 0.35s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--surface-border);
        }

        .theme-avatar {
            background: var(--brand-primary);
            color: var(--text-inverse);
        }

        .sidebar-avatar {
            position: relative;
        }

        .sidebar-avatar .status-dot {
            position: absolute;
            right: -0.35rem;
            bottom: -0.35rem;
            width: 0.95rem;
            height: 0.95rem;
            border-radius: 9999px;
            border: 2px solid #ffffff;
            background: #34d399;
        }

        .sidebar-meta {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .role-label {
            color: var(--text-muted);
            letter-spacing: 0.12em;
        }

        .section-label {
            color: var(--text-muted);
            letter-spacing: 0.18em;
        }

        .border-divider {
            border-color: var(--surface-border) !important;
        }

        .sidebar-meta .name-line {
            font-size: 0.938rem;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: 0.02em;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-subtle);
            transition: all 0.15s ease;
        }

        .nav-link svg {
            width: 1.125rem;
            height: 1.125rem;
            color: var(--text-muted);
            transition: color 0.15s ease;
        }

        .nav-link:hover {
            color: var(--text-primary);
            background: var(--surface-accent);
        }

        .nav-link:hover svg {
            color: var(--brand-primary);
        }

        .nav-link.active {
            color: var(--brand-primary);
            background: var(--surface-contrast);
            font-weight: 600;
        }

        .nav-link.active svg {
            color: var(--brand-primary);
        }

        .main-content {
            padding-bottom: 6rem;
        }

        @media (max-width: 1023px) {
            .settings-shell {
                gap: 1rem;
                grid-template-columns: 1fr;
            }
            .dashboard-sidebar {
                display: none;
            }
            .main-content {
                width: 100%;
            }
        }

        .dashboard-topbar {
            background: var(--card-bg);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--surface-border);
        }

        .message-banner {
            border-radius: 1rem;
            padding: 0.85rem 1rem;
            border: 1px solid var(--surface-border);
            box-shadow: var(--shadow-soft);
        }
        .message-banner.success {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.25);
            color: #047857;
        }
        .dark .message-banner.success {
            color: #6ee7b7;
        }
        .message-banner.error {
            background: rgba(244, 63, 94, 0.12);
            border-color: rgba(244, 63, 94, 0.25);
            color: #be123c;
        }
        .dark .message-banner.error {
            color: #fda4af;
        }
    </style>
</head>
<body class="min-h-screen">
    @php($joinedClasses = $joinedClasses ?? [])

    <div class="layout-shell">
        <div class="settings-shell">
            <aside class="dashboard-sidebar p-6 flex flex-col relative" id="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-avatar">
                        <div class="w-14 h-14 rounded-full theme-avatar flex items-center justify-center text-xl font-semibold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="status-dot"></span>
                    </div>
                    <div class="sidebar-meta">
                        <span class="text-xs uppercase tracking-wide role-label">Student</span>
                        <span class="name-line">{{ ucwords(strtolower(auth()->user()->name ?? 'Student')) }}</span>
                    </div>
                </div>

                <nav class="mt-6 space-y-6 pr-1">
                    <div>
                        <p class="text-xs font-semibold section-label uppercase tracking-wide mb-2">Student</p>
                        <ul class="space-y-1.5">
                            <li><a href="{{ route('student.dashboard') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75M4.5 10.5V21h15V10.5" />
                                </svg>
                                <span>Dashboard</span>
                            </a></li>
                            <li><a href="{{ route('student.calendar') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25m10.5-2.25V5.25M3.75 7.5h16.5M4.5 21.75h15a1.5 1.5 0 001.5-1.5v-12h-18v12a1.5 1.5 0 001.5 1.5z" />
                                </svg>
                                <span>Calendar</span>
                            </a></li>
                            <li><a href="{{ route('student.profile') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                                </svg>
                                <span>My Profile</span>
                            </a></li>
                            <li><a href="{{ route('student.quiz.attempts') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.25h13.5M4.5 9.75h15m-9.75 4.5h4.5m-6 4.5h7.5" />
                                </svg>
                                <span>My Quiz Attempts</span>
                            </a></li>
                            <li><a href="{{ route('pronunciation.tutor') }}" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3" />
                                </svg>
                                <span>AI Pronunciation Tutor</span>
                            </a></li>
                            <li>
                                <button type="button" class="nav-link w-full text-left" data-join-class-open>
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>Join a Class</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="text-xs font-semibold section-label uppercase tracking-wide mb-2">Instructor</p>
                        <ul class="space-y-1.5">
                            @php($firstClassId = collect($joinedClasses ?? [])->pluck('id')->filter()->first())
                            <li>
                                <a href="{{ $firstClassId ? route('student.classes.show', $firstClassId) : '#' }}"
                                   class="nav-link {{ $firstClassId ? '' : 'opacity-70 cursor-not-allowed pointer-events-none' }}"
                                   title="{{ $firstClassId ? '' : 'Join a class to see announcements' }}">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5h7.5M6 9.75h12M5.25 15h13.5M8.25 19.5h7.5" />
                                </svg>
                                <span>Announcements</span>
                            </a></li>
                            <li><a href="#" class="nav-link">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15M6 12h12M8.25 16.5h7.5" />
                                </svg>
                                <span>Assignments</span>
                            </a></li>
                        </ul>
                    </div>

                    <div class="pt-2 border-t border-divider space-y-2">
                        <a href="{{ route('student.settings') }}" class="nav-link active">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.75h4.5M12 5.25v13.5m-6.375 1.5h12.75a1.125 1.125 0 001.125-1.125V6.75a1.125 1.125 0 00-1.125-1.125H5.625A1.125 1.125 0 004.5 6.75v12.375c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            <span>Settings</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="nav-link w-full justify-start text-red-500 hover:text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-500/10">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H6.75A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21H13.5a2.25 2.25 0 002.25-2.25V15m3.75-3H9.75m9 0l-3 3m3-3l-3-3" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            <main class="space-y-6 main-content">
                @if(session('success'))
                    <div class="message-banner success" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="message-banner error" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="message-banner error" role="alert">
                        <div class="font-semibold mb-1">Please fix the highlighted errors.</div>
                        <ul class="text-sm space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <header class="dashboard-topbar px-6 py-4">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-9 w-auto">
                            <div>
                                <div class="text-xl font-semibold" style="color: var(--text-primary);">Settings</div>
                                <div class="text-sm" style="color: var(--text-muted);">Manage your student account preferences</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-translation-toggle />
                            <x-theme-toggle />
                        </div>
                    </div>
                </header>

                <section class="student-settings-card">
                    <div class="settings-section">
                        <div class="settings-row">
                            <div>
                                <div class="settings-section-title">Account &amp; Security</div>
                                <div class="settings-section-subtitle">Keep your account safe and updated.</div>
                            </div>
                            <span class="settings-chip">Student</span>
                        </div>

                        <form action="{{ route('student.profile.password') }}" method="POST" class="mt-4">
                            @csrf
                            @method('PUT')

                            <div class="settings-grid two">
                                <div class="settings-field">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" class="settings-input" required>
                                </div>
                                <div class="settings-field"></div>
                                <div class="settings-field">
                                    <label for="password">New Password</label>
                                    <input type="password" id="password" name="password" class="settings-input" required>
                                </div>
                                <div class="settings-field">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="settings-input" required>
                                </div>
                            </div>

                            <div class="settings-actions">
                                <button type="submit" class="settings-btn settings-btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>

                    <div class="settings-section">
                        <div class="settings-section-title">Profile Preferences</div>
                        <div class="settings-section-subtitle">Update your profile information shown across the student dashboards.</div>

                        <form action="{{ route('student.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="settings-grid two">
                                <div class="settings-field">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" class="settings-input" value="{{ old('name', $user->name ?? '') }}" required>
                                </div>
                                <div class="settings-field">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" class="settings-input" value="{{ old('email', $user->email ?? '') }}" required>
                                </div>
                                <div class="settings-field">
                                    <label for="grade_level">Grade Level</label>
                                    <select id="grade_level" name="grade_level" class="settings-input">
                                        @php($gradeValue = old('grade_level', $profile->grade_level ?? null))
                                        <option value="" {{ $gradeValue ? '' : 'selected' }}>Not set</option>
                                        <option value="7" {{ (string)$gradeValue === '7' ? 'selected' : '' }}>Grade 7</option>
                                        <option value="8" {{ (string)$gradeValue === '8' ? 'selected' : '' }}>Grade 8</option>
                                        <option value="9" {{ (string)$gradeValue === '9' ? 'selected' : '' }}>Grade 9</option>
                                        <option value="10" {{ (string)$gradeValue === '10' ? 'selected' : '' }}>Grade 10</option>
                                    </select>
                                </div>
                                <div class="settings-field">
                                    <label for="section">Section</label>
                                    <input type="text" id="section" name="section" class="settings-input" value="{{ old('section', $profile->section ?? '') }}" placeholder="e.g. Rizal">
                                </div>
                            </div>

                            <div class="settings-actions">
                                <button type="submit" class="settings-btn settings-btn-primary">Save Profile</button>
                            </div>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="{{ asset('js/student/settings.js') }}"></script>
</body>
</html>
