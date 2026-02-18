<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attempt Details - Q2L</title>
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
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --text-subtle: #7b8aa8;
            --surface-muted: rgba(248, 250, 252, 0.92);
            --surface-accent: rgba(79, 70, 229, 0.12);
            --badge-bg: rgba(79, 70, 229, 0.12);
            --badge-text: #4338ca;
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
            --text-primary: #e2e8f0;
            --text-muted: #cbd5f5;
            --text-subtle: #94a3b8;
            --surface-muted: rgba(30, 41, 59, 0.78);
            --surface-accent: rgba(129, 140, 248, 0.22);
            --badge-bg: rgba(99, 102, 241, 0.22);
            --badge-text: #e0e7ff;
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
        }

        .layout-shell { padding: 0.5rem 0.75rem; }
        @media (min-width: 768px) { .layout-shell { padding: 2rem 1rem; } }
        @media (min-width: 1024px) { .layout-shell { padding: 2.5rem 1.5rem; } }

        .layout-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) { .layout-grid { grid-template-columns: 260px 1fr; } }

        .dashboard-sidebar {
            background: var(--sidebar-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
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
        .nav-link:hover { color: var(--text-primary); background: var(--surface-accent); }
        .nav-link.active { color: var(--brand-primary); background: rgba(79, 70, 229, 0.16); font-weight: 600; }

        .dashboard-card {
            background: var(--card-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
        }

        .message-banner {
            padding: 1rem 1.2rem;
            border-radius: 1.35rem;
            border: 1px solid var(--surface-border-strong);
            background: var(--banner-success-bg);
            color: var(--banner-success-text);
        }
        .message-banner.error {
            background: var(--banner-error-bg);
            color: var(--banner-error-text);
        }

        @media (max-width: 1023px) {
            .layout-grid { grid-template-columns: 1fr; }
            .dashboard-sidebar { display: none; }
        }
    </style>
</head>
<body class="min-h-screen">
@php($joinedClasses = $joinedClasses ?? [])

<div class="layout-shell">
    <div class="layout-grid">
        <aside class="dashboard-sidebar p-6 flex flex-col relative" id="sidebar">
            <div class="sidebar-header" style="display:flex; align-items:center; gap:0.875rem; padding-bottom:1.25rem; border-bottom:1px solid var(--surface-border);">
                <div class="sidebar-avatar" style="position:relative;">
                    <div class="w-14 h-14 rounded-full" style="background: var(--brand-primary); color: var(--text-inverse); display:flex; align-items:center; justify-content:center; font-weight:600; font-size:1.25rem;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                    </div>
                    <span class="status-dot" style="position:absolute; right:-0.35rem; bottom:-0.35rem; width:0.95rem; height:0.95rem; border-radius:9999px; border:2px solid #ffffff; background:#34d399;"></span>
                </div>
                <div class="sidebar-meta" style="display:flex; flex-direction:column; gap:0.25rem;">
                    <span class="text-xs uppercase tracking-wide" style="color: var(--text-muted); letter-spacing:0.12em;">Student</span>
                    <span class="name-line" style="font-size:0.938rem; font-weight:600; color: var(--text-primary);">{{ ucwords(strtolower(auth()->user()->name ?? 'Student')) }}</span>
                </div>
            </div>

            <nav class="mt-6 space-y-6 pr-1">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color: var(--text-muted); letter-spacing:0.18em;">Student</p>
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
                        <li><a href="{{ route('student.quiz.attempts') }}" class="nav-link active">
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
                    <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color: var(--text-muted); letter-spacing:0.18em;">Instructor</p>
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

                <div class="pt-2 border-t" style="border-color: var(--surface-border);">
                    <div class="space-y-2">
                        <a href="{{ route('student.settings') }}" class="nav-link">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.75h4.5M12 5.25v13.5m-6.375 1.5h12.75a1.125 1.125 0 001.125-1.125V6.75a1.125 1.125 0 00-1.125-1.125H5.625A1.125 1.125 0 004.5 6.75v12.375c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            <span>Settings</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link w-full justify-start" style="color:#ef4444;">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H6.75A2.25 2.25 0 004.5 5.25v13.5A2.25 2.25 0 006.75 21H13.5a2.25 2.25 0 002.25-2.25V15m3.75-3H9.75m9 0l-3 3m3-3l-3-3" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        </aside>

        <main class="space-y-7 main-content">
            <header class="dashboard-card px-6 py-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-xl font-semibold" style="color: var(--text-primary);">Attempt Details</div>
                        <div class="text-sm" style="color: var(--text-muted);">
                            {{ $performance->quest?->title ?? 'Activity' }}
                            <span class="mx-2">•</span>
                            {{ $performance->completed_at?->format('M d, Y h:i A') ?? '' }}
                        </div>
                    </div>
                    <a href="{{ route('student.quiz.attempts') }}" class="px-4 py-2 rounded-xl text-sm font-semibold" style="background: var(--surface-muted); border: 1px solid var(--surface-border); color: var(--text-primary);">Back</a>
                </div>
            </header>

            <section class="dashboard-card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="text-sm" style="color: var(--text-muted);">Score</div>
                    <div class="text-sm font-semibold" style="color: var(--text-primary);">
                        {{ (int) ($performance->total_score ?? 0) }} / {{ (int) ($performance->max_score ?? 0) }}
                        <span class="mx-2">•</span>
                        {{ number_format((float) ($performance->accuracy_percentage ?? 0), 0) }}%
                    </div>
                </div>
            </section>

            <section class="dashboard-card p-6">
                <div class="text-lg font-semibold" style="color: var(--text-primary);">Wrong Answers</div>
                <div class="text-sm" style="color: var(--text-muted);">Items you missed in this attempt</div>

                @php($wrong = $wrongItems ?? [])
                @php($skillWrongItems = $skillWrong ?? collect())

                @if(empty($wrong) && $skillWrongItems->isEmpty())
                    <div class="mt-4 rounded-lg border border-dashed" style="border-color: var(--surface-border); background: var(--surface-muted); padding: 1rem; color: var(--text-muted);">
                        No wrong answers found.
                    </div>
                @else
                    <div class="mt-5 space-y-4">
                        @foreach($wrong as $item)
                            <div class="rounded-xl border p-4" style="border-color: var(--surface-border); background: var(--surface-muted);">
                                <div class="text-xs uppercase tracking-wide" style="color: var(--text-muted);">{{ ucfirst($item['type'] ?? 'item') }}</div>
                                <div class="mt-2 font-semibold" style="color: var(--text-primary);">{{ $item['prompt'] ?? '' }}</div>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="rounded-lg p-3" style="background: var(--card-bg); border: 1px solid var(--surface-border);">
                                        <div class="text-xs" style="color: var(--text-muted);">Your answer</div>
                                        <div class="mt-1" style="color: var(--banner-error-text);">{{ $item['student_answer'] ?? '' }}</div>
                                    </div>
                                    <div class="rounded-lg p-3" style="background: var(--card-bg); border: 1px solid var(--surface-border);">
                                        <div class="text-xs" style="color: var(--text-muted);">Correct answer</div>
                                        <div class="mt-1" style="color: var(--banner-success-text);">{{ $item['correct_answer'] ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @foreach($skillWrongItems as $row)
                            <div class="rounded-xl border p-4" style="border-color: var(--surface-border); background: var(--surface-muted);">
                                <div class="text-xs uppercase tracking-wide" style="color: var(--text-muted);">{{ $row->skill_type_display_name ?? 'Skill' }}</div>
                                <div class="mt-2 font-semibold" style="color: var(--text-primary);">{{ $row->problem_content ?? '' }}</div>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="rounded-lg p-3" style="background: var(--card-bg); border: 1px solid var(--surface-border);">
                                        <div class="text-xs" style="color: var(--text-muted);">Your answer</div>
                                        <div class="mt-1" style="color: var(--banner-error-text);">{{ $row->student_response ?? '' }}</div>
                                    </div>
                                    <div class="rounded-lg p-3" style="background: var(--card-bg); border: 1px solid var(--surface-border);">
                                        <div class="text-xs" style="color: var(--text-muted);">Correct answer</div>
                                        <div class="mt-1" style="color: var(--banner-success-text);">{{ $row->correct_answer ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const joinClassModal = document.getElementById('joinClassModal');
        const joinClassOpeners = document.querySelectorAll('[data-join-class-open]');
        const joinClassClosers = document.querySelectorAll('[data-join-class-close]');

        const openJoinModal = () => {
            if (!joinClassModal) return;
            joinClassModal.classList.remove('hidden');
            joinClassModal.classList.add('flex');
            const input = joinClassModal.querySelector('input[name="join_code"]');
            if (input) setTimeout(() => input.focus(), 100);
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
    <div class="relative w-full max-w-md rounded-2xl border" style="border-color: var(--surface-border); background: var(--card-bg);">
        <div class="flex items-start justify-between gap-4 p-6 pb-0">
            <div>
                <p class="text-xs uppercase tracking-[0.4em]" style="color: var(--text-muted);">Join a Class</p>
                <p class="text-sm" style="color: var(--text-muted);">Paste the code from your teacher to unlock their class dashboard.</p>
            </div>
            <button type="button" class="p-6 pt-0 text-sm" style="color: var(--text-muted);" data-join-class-close>Close</button>
        </div>

        <form action="{{ route('student.join') }}" method="POST" class="space-y-4 p-6 pt-2">
            @csrf
            <div class="space-y-2">
                <label for="join_code" class="text-xs font-semibold tracking-wide" style="color: var(--text-primary);">Class Code</label>
                <input type="text" id="join_code" name="join_code" value="{{ old('join_code') }}" required placeholder="e.g. EN10A-XP3" class="w-full rounded-xl border bg-transparent px-4 py-3 text-sm" style="border-color: var(--surface-border); color: var(--text-primary);">
                @error('join_code')
                    <p class="text-xs" style="color:#ef4444;">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-white" style="background: var(--brand-primary);">Join Class</button>
        </form>
    </div>
</div>
</body>
</html>
