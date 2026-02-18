<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Profile - Q2L</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            --banner-success-bg: rgba(34, 197, 94, 0.1);
            --banner-success-text: #047857;
            --banner-error-bg: rgba(239, 68, 68, 0.1);
            --banner-error-text: #b91c1c;
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
            --banner-success-bg: rgba(34, 197, 94, 0.18);
            --banner-success-text: #bbf7d0;
            --banner-error-bg: rgba(248, 113, 113, 0.18);
            --banner-error-text: #fecaca;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-bg);
            color: var(--text-primary);
            min-height: 100vh;
            margin: 0;
            transition: background 0.45s ease, color 0.45s ease;
        }

        html { font-size: 15px; }
        @media (min-width: 768px) { html { font-size: 14.5px; } }
        @media (min-width: 1280px) { html { font-size: 14px; } }

        .achievements-scroller {
            -ms-overflow-style: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(99, 102, 241, 0.65) rgba(148, 163, 184, 0.18);
        }
        .achievements-scroller::-webkit-scrollbar { height: 10px; }
        .achievements-scroller::-webkit-scrollbar-track {
            background: rgba(148, 163, 184, 0.18);
            border-radius: 9999px;
        }
        .achievements-scroller::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.65);
            border-radius: 9999px;
            border: 2px solid rgba(148, 163, 184, 0.18);
        }
        .achievements-scroller::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.85);
        }

        .achievements-scroller {
            cursor: grab;
        }
        .achievements-scroller.is-dragging {
            cursor: grabbing;
            user-select: none;
        }

        .achievements-scroller::before,
        .achievements-scroller::after {
            content: "";
            flex: 0 0 0.25rem;
        }

        @media (min-width: 640px) {
            .achievements-scroller::before,
            .achievements-scroller::after {
                flex-basis: 0.75rem;
            }
        }

        .achievements-scroller-fade {
            position: relative;
        }

        .achievements-viewport {
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
            overflow: hidden;
        }

        @media (min-width: 1024px) {
            .achievements-viewport {
                max-width: calc((260px * 3) + (1rem * 2));
            }
        }
        .achievements-scroller-fade::before,
        .achievements-scroller-fade::after {
            content: "";
            pointer-events: none;
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2.5rem;
            z-index: 5;
        }
        .achievements-scroller-fade::before {
            left: 0;
            background: linear-gradient(90deg, var(--card-bg), rgba(255,255,255,0));
        }
        .achievements-scroller-fade::after {
            right: 0;
            background: linear-gradient(270deg, var(--card-bg), rgba(255,255,255,0));
        }

        .layout-shell { padding: 0.5rem 0.75rem; }
        @media (min-width: 768px) { .layout-shell { padding: 1.5rem 1rem; } }
        @media (min-width: 1024px) { .layout-shell { padding: 2rem 1.25rem; } }

        .layout-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            gap: 1.25rem;
        }
        @media (min-width: 1024px) { .layout-grid { grid-template-columns: 260px 1fr; } }

        .dashboard-sidebar {
            background: var(--sidebar-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
            transition: background-color 0.35s ease, border-color 0.35s ease, color 0.35s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--surface-border);
        }

        .sidebar-avatar { position: relative; }
        .theme-avatar { background: var(--brand-primary); color: var(--text-inverse); }
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

        .sidebar-meta { display: flex; flex-direction: column; gap: 0.25rem; }
        .role-label { color: var(--text-muted); letter-spacing: 0.12em; }
        .section-label { color: var(--text-muted); letter-spacing: 0.18em; }
        .border-divider { border-color: var(--surface-border) !important; }
        .sidebar-meta .name-line { font-size: 0.938rem; font-weight: 600; color: var(--text-primary); letter-spacing: 0.02em; }

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

        .nav-link svg { width: 1.125rem; height: 1.125rem; color: var(--text-muted); transition: color 0.15s ease; }
        .nav-link:hover { color: var(--text-primary); background: var(--surface-accent); }
        .nav-link:hover svg { color: var(--brand-primary); }
        .nav-link.active { color: var(--brand-primary); background: var(--surface-contrast); font-weight: 600; }
        .nav-link.active svg { color: var(--brand-primary); }

        .main-content { padding-bottom: 6rem; }

        .dashboard-topbar {
            background: var(--card-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
            transition: background-color 0.35s ease, border-color 0.35s ease, color 0.35s ease;
        }

        .dashboard-card {
            background: var(--card-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
            transition: background-color 0.35s ease, border-color 0.35s ease, color 0.35s ease;
        }

        .dashboard-card.compact {
            border-radius: 1.25rem;
        }

        .message-banner {
            padding: 1rem 1.2rem;
            border-radius: 1.35rem;
            border: 1px solid var(--surface-border-strong);
            background: var(--banner-success-bg);
            color: var(--banner-success-text);
            box-shadow: 0 20px 45px -32px rgba(34, 197, 94, 0.45);
        }
        .message-banner.error {
            background: var(--banner-error-bg);
            color: var(--banner-error-text);
            box-shadow: 0 20px 45px -32px rgba(239, 68, 68, 0.45);
        }

        .form-label { color: var(--text-muted); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; }

        .form-input {
            width: 100%;
            height: 44px;
            border-radius: 0.75rem;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            padding: 0 1rem;
            font-size: 0.875rem;
            color: var(--text-primary);
            transition: all 0.15s ease;
        }
        .form-input:focus {
            background: var(--input-bg-focus);
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px var(--input-ring);
            outline: none;
        }

        .primary-btn {
            height: 44px;
            border-radius: 0.75rem;
            padding: 0 1.25rem;
            background: var(--brand-primary);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.875rem;
            transition: background 0.15s ease;
        }
        .primary-btn:hover { background: var(--brand-primary-dark); }

        @media (max-width: 1023px) {
            .layout-grid { gap: 1rem; grid-template-columns: 1fr; }
            .dashboard-sidebar { display: none; }
            .main-content { width: 100%; }
        }
    </style>
</head>
<body class="min-h-screen">
@php($joinedClasses = $joinedClasses ?? [])

<div class="layout-shell">
    <div class="layout-grid">
        <aside class="dashboard-sidebar p-5 flex flex-col relative" id="sidebar">
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
                            <span data-translate="dashboard-title">Dashboard</span>
                        </a></li>
                        <li><a href="{{ route('student.calendar') }}" class="nav-link">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25m10.5-2.25V5.25M3.75 7.5h16.5M4.5 21.75h15a1.5 1.5 0 001.5-1.5v-12h-18v12a1.5 1.5 0 001.5 1.5z" />
                            </svg>
                            <span data-translate="nav-calendar">Calendar</span>
                        </a></li>
                        <li><a href="{{ route('student.profile') }}" class="nav-link active">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                            </svg>
                            <span data-translate="nav-profile">My Profile</span>
                        </a></li>
                        <li><a href="{{ route('student.quiz.attempts') }}" class="nav-link">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.25h13.5M4.5 9.75h15m-9.75 4.5h4.5m-6 4.5h7.5" />
                            </svg>
                            <span data-translate="nav-quiz">My Quiz Attempts</span>
                        </a></li>
                        <li><a href="{{ route('pronunciation.tutor') }}" class="nav-link">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3" />
                            </svg>
                            <span data-translate="nav-pronunciation">AI Pronunciation Tutor</span>
                        </a></li>
                        <li>
                            <button type="button" class="nav-link w-full text-left" data-join-class-open>
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <span data-translate="nav-join-class">Join a Class</span>
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
                            <span data-translate="nav-announcements">Announcements</span>
                        </a></li>
                        <li><a href="#" class="nav-link">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15M6 12h12M8.25 16.5h7.5" />
                            </svg>
                            <span data-translate="nav-assignments">Assignments</span>
                        </a></li>
                    </ul>
                </div>

                <div class="pt-2 border-t border-divider space-y-2">
                    <a href="{{ route('student.settings') }}" class="nav-link">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.75h4.5M12 5.25v13.5m-6.375 1.5h12.75a1.125 1.125 0 001.125-1.125V6.75a1.125 1.125 0 00-1.125-1.125H5.625A1.125 1.125 0 004.5 6.75v12.375c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span data-translate="nav-settings">Settings</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="nav-link w-full justify-start text-red-500 hover:text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-500/10">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H6.75A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21H13.5a2.25 2.25 0 002.25-2.25V15m3.75-3H9.75m9 0l-3 3m3-3l-3-3" />
                            </svg>
                            <span data-translate="nav-logout">Logout</span>
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

            <header class="dashboard-topbar px-5 py-3.5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-9 w-auto">
                        <div>
                            <div class="text-xl font-semibold" style="color: var(--text-primary);">My Profile</div>
                            <div class="text-sm" style="color: var(--text-muted);">Manage your account and view your progress</div>
                        </div>
                    </div>
                    <div class="text-sm" style="color: var(--text-muted);">
                        Signed in as <span class="font-semibold" style="color: var(--text-primary);">{{ auth()->user()->email }}</span>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <section class="dashboard-card compact p-5" style="background: var(--card-bg);">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Your Progress</h2>
                        <div class="px-4 py-1.5 rounded-full text-xs font-semibold" style="background: var(--brand-primary); color: #fff;">
                            Rank #{{ $userRank ?? 1 }}
                        </div>
                    </div>

                    <div class="flex items-center gap-7 mb-6">
                        <div class="w-24 h-24 rounded-full flex flex-col items-center justify-center" style="background: var(--brand-primary); color: #fff; box-shadow: 0 16px 34px rgba(79, 70, 229, 0.38);">
                            <div class="text-3xl font-extrabold leading-none">{{ $user->level ?? 1 }}</div>
                            <div class="text-xs tracking-[0.35em] opacity-90 mt-1">LEVEL</div>
                        </div>

                        <div>
                            <div class="text-4xl font-extrabold leading-none" style="color: var(--brand-primary);">{{ number_format($user->points ?? 0) }}</div>
                            <div class="text-sm" style="color: var(--text-muted);">Points</div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm" style="color: var(--text-primary);">Experience</div>
                            <div class="text-sm" style="color: var(--text-primary);">{{ $user->experience ?? 0 }} / {{ $user->getExperienceForNextLevel() ?? 100 }} XP</div>
                        </div>
                        <div class="h-3 rounded-full overflow-hidden" style="background: rgba(148, 163, 184, 0.22);">
                            <div class="h-full rounded-full" style="width: {{ $user->getLevelProgress() ?? 0 }}%; background: var(--brand-primary);"></div>
                        </div>
                    </div>

                    <div class="rounded-2xl p-4 flex items-center gap-4" style="background: rgba(148, 163, 184, 0.12); border: 1px solid rgba(148, 163, 184, 0.22);">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(245, 158, 11, 0.18);">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">
                                <path d="M14.5 4.5c1.5 1.5 2.5 3.5 2.5 6 0 5-4 7-4 7s-4-2-4-7c0-2.5 1-4.5 2.5-6" />
                                <path d="M12 20c2 0 4-1.5 4-4 0-2-1.5-3.5-4-5-2.5 1.5-4 3-4 5 0 2.5 2 4 4 4z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-extrabold" style="color: var(--text-primary);">{{ $user->streak_days ?? 0 }} days</div>
                            <div class="text-sm" style="color: var(--text-muted);">Daily Streak</div>
                        </div>
                    </div>
                </section>

                <div class="lg:col-span-2 rounded-xl border border-[color:var(--surface-border)] bg-[color:var(--surface-muted)] p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-sm font-semibold" style="color: var(--text-primary);">Leaderboard</div>
                            <div class="text-xs" style="color: var(--text-muted);">Top students</div>
                        </div>

                        @php($leaderboardRows = $leaderboard ?? collect())
                        @if($leaderboardRows->isEmpty())
                            <div class="rounded-lg border border-dashed border-[color:var(--surface-border)] bg-[color:var(--card-bg)] p-4 text-sm" style="color: var(--text-muted);">
                                Leaderboard is not available right now.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($leaderboardRows as $index => $student)
                                    @php($isMe = (int) ($student->id ?? 0) === (int) (auth()->user()->id ?? 0))
                                    <div class="rounded-lg bg-[color:var(--card-bg)] border border-[color:var(--surface-border)] p-4 flex flex-wrap items-center justify-between gap-3 {{ $isMe ? 'ring-2 ring-[color:var(--brand-primary)]' : '' }}">
                                        <div class="min-w-0 flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-extrabold" style="background: var(--badge-bg); color: var(--badge-text);">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-[color:var(--text-primary)] truncate">
                                                    {{ $student->name ?? 'Student' }}
                                                    @if($isMe)
                                                        <span class="text-xs" style="color: var(--text-muted);">(You)</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-[color:var(--text-muted)]">Level {{ $student->level ?? 1 }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm">
                                            <div class="px-3 py-1 rounded-full" style="background: var(--badge-bg); color: var(--badge-text);">
                                                {{ number_format($student->points ?? 0) }} pts
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="mt-4 rounded-lg border border-[color:var(--surface-border)] bg-[color:var(--card-bg)] p-4 flex items-center justify-between gap-3">
                                    <div class="text-sm font-semibold" style="color: var(--text-primary);">Your rank</div>
                                    <div class="text-sm" style="color: var(--text-muted);">#{{ $userRank ?? 1 }}</div>
                                </div>
                            </div>
                        @endif
                </div>
            </div>

            <section class="dashboard-card compact p-5">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <div class="text-lg font-semibold" style="color: var(--text-primary);">Achievements</div>
                        <div class="text-sm" style="color: var(--text-muted);">Your unlocked rewards</div>
                    </div>
                    <div class="px-3 py-1 rounded-full text-xs font-semibold" style="background: var(--badge-bg); color: var(--badge-text);">
                        {{ count($user->achievements ?? []) }}
                    </div>
                </div>

                @php($userAchievements = $user->achievements ?? [])
                <div class="achievements-viewport">
                    <div class="achievements-scroller-fade">
                    <div class="achievements-scroller flex flex-nowrap gap-4 overflow-x-auto overflow-y-hidden py-1" style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; scroll-padding-left: 0.75rem; scroll-padding-right: 0.75rem;">
                    @foreach(($achievementCatalog ?? []) as $key => $achievement)
                        @php($unlocked = in_array($key, $userAchievements))
                        <div class="w-[240px] sm:w-[260px] shrink-0 rounded-xl border p-4 flex items-start gap-3" style="border-color: var(--surface-border); background: var(--surface-muted); opacity: {{ $unlocked ? '1' : '0.55' }}; scroll-snap-align: start;">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl" style="background: var(--badge-bg); color: var(--badge-text);">
                                {{ $achievement['icon'] ?? '🏅' }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="font-semibold truncate" style="color: var(--text-primary);">{{ $achievement['name'] ?? 'Achievement' }}</div>
                                    @if($unlocked)
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background: rgba(16, 185, 129, 0.12); color: #047857; border: 1px solid rgba(16, 185, 129, 0.25);">Unlocked</span>
                                    @else
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background: rgba(148, 163, 184, 0.16); color: var(--text-muted); border: 1px solid var(--surface-border);">Locked</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-sm" style="color: var(--text-muted);">{{ $achievement['desc'] ?? '' }}</div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const joinClassModal = document.getElementById('joinClassModal');
        const joinClassOpeners = document.querySelectorAll('[data-join-class-open]');
        const joinClassClosers = document.querySelectorAll('[data-join-class-close]');

        const achievementsScroller = document.querySelector('.achievements-scroller');
        if (achievementsScroller) {
            let isDown = false;
            let startX = 0;
            let scrollLeft = 0;

            const onDown = (e) => {
                isDown = true;
                achievementsScroller.classList.add('is-dragging');
                startX = (e.pageX || 0) - achievementsScroller.offsetLeft;
                scrollLeft = achievementsScroller.scrollLeft;
            };

            const onLeaveOrUp = () => {
                isDown = false;
                achievementsScroller.classList.remove('is-dragging');
            };

            const onMove = (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = (e.pageX || 0) - achievementsScroller.offsetLeft;
                const walk = (x - startX) * 1.2;
                achievementsScroller.scrollLeft = scrollLeft - walk;
            };

            achievementsScroller.addEventListener('mousedown', onDown);
            achievementsScroller.addEventListener('mouseleave', onLeaveOrUp);
            achievementsScroller.addEventListener('mouseup', onLeaveOrUp);
            achievementsScroller.addEventListener('mousemove', onMove);
        }

        const openJoinModal = () => {
            if (!joinClassModal) return;
            joinClassModal.classList.remove('hidden');
            joinClassModal.classList.add('flex');
            const input = joinClassModal.querySelector('input[name="join_code"]');
            if (input) {
                setTimeout(() => input.focus(), 100);
            }
        };

        const closeJoinModal = () => {
            if (!joinClassModal) return;
            joinClassModal.classList.add('hidden');
            joinClassModal.classList.remove('flex');
        };

        joinClassOpeners.forEach(btn => btn.addEventListener('click', openJoinModal));
        joinClassClosers.forEach(btn => btn.addEventListener('click', closeJoinModal));

        if (joinClassModal?.dataset.shouldOpen === 'true') {
            openJoinModal();
        }
    });
</script>

<div id="joinClassModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 sm:px-6 py-6" data-should-open="{{ $errors->has('join_code') ? 'true' : 'false' }}">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" data-join-class-close></div>
    <div class="relative w-full max-w-md rounded-2xl border border-[color:var(--surface-border)] bg-[color:var(--card-bg)] p-6 shadow-2xl space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-[color:var(--text-muted)]">Join a Class</p>
                <p class="text-sm text-[color:var(--text-muted)]">Paste the code from your teacher to unlock their class dashboard.</p>
            </div>
            <button type="button" class="text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)]" data-join-class-close>
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('student.join') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-2">
                <label for="join_code" class="text-xs font-semibold tracking-wide text-[color:var(--text-primary)]">Class Code</label>
                <input type="text" id="join_code" name="join_code" value="{{ old('join_code') }}" required placeholder="e.g. EN10A-XP3" class="w-full rounded-xl border border-[color:var(--surface-border)] bg-transparent px-4 py-3 text-sm text-[color:var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-primary)]">
                @error('join_code')
                    <p class="text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full rounded-xl bg-[color:var(--brand-primary)] px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[color:var(--brand-primary)]">
                Join Class
            </button>
        </form>
    </div>
</div>
</body>
</html>
