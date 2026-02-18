<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calendar - Q2L</title>
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
        .layout-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .layout-grid {
                grid-template-columns: 260px 1fr;
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
        .sidebar-avatar {
            position: relative;
        }
        .theme-avatar {
            background: var(--brand-primary);
            color: var(--text-inverse);
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
        .dashboard-card {
            background: var(--card-bg);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--surface-border);
            transition: background-color 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease, color 0.35s ease;
        }
        .calendar-card {
            padding: 1.75rem;
        }
        @media (max-width: 767px) {
            .calendar-card {
                padding: 1.25rem;
            }
        }
        .calendar-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .calendar-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .month-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }
        .month-nav button {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            border: 1px solid var(--surface-border);
            background: var(--card-bg);
            color: var(--text-subtle);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .month-nav button:hover {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
            background: rgba(79, 70, 229, 0.12);
        }
        .month-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            min-width: 140px;
            text-align: center;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.5rem;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        .calendar-cell {
            min-height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            color: var(--text-primary);
            background: var(--surface-muted);
            transition: all 0.15s ease;
        }
        .calendar-cell:hover {
            background: var(--surface-contrast);
            color: var(--brand-primary);
        }
        .calendar-cell.muted {
            color: rgba(148, 163, 184, 0.6);
            background: transparent;
        }
        .calendar-cell.today {
            background: var(--brand-primary);
            color: var(--text-inverse);
            font-weight: 600;
        }
        .calendar-cell.has-assignment::after {
            content: '';
            position: absolute;
            bottom: 0.375rem;
            width: 0.375rem;
            height: 0.375rem;
            border-radius: 9999px;
            background: var(--brand-primary);
        }
        .calendar-cell.today.has-assignment::after {
            background: #ffffff;
        }
        .assignments-list {
            margin-top: 2rem;
        }
        .assignments-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        .assignment-item {
            padding: 0.875rem;
            background: var(--surface-muted);
            border-radius: 0.75rem;
            border: 1px solid var(--surface-border);
            margin-bottom: 0.75rem;
        }
        .assignment-item .title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        .assignment-item .subject {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .assignment-item .deadline {
            font-size: 0.75rem;
            color: var(--brand-primary);
            margin-top: 0.5rem;
        }
        @media (max-width: 1023px) {
            .layout-grid {
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
    </style>
</head>
<body class="min-h-screen">
    @php($joinedClasses = $joinedClasses ?? [])

    <div class="layout-shell">
        <div class="layout-grid">
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
                            <li><a href="{{ route('student.calendar') }}" class="nav-link active">
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
                        <a href="{{ route('student.settings') }}" class="nav-link">
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

            <main class="main-content">
                <section class="dashboard-card calendar-card">
                    <div class="calendar-head">
                        <div>
                            <h2 class="calendar-title">Calendar</h2>
                            <p class="text-sm" style="color: var(--text-muted);">Track your assignments, deadlines, and learning streak.</p>
                        </div>
                        <div class="month-nav">
                            <button id="prevMonth" aria-label="Previous month">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                </svg>
                            </button>
                            <div id="monthLabel" class="month-label"></div>
                            <button id="nextMonth" aria-label="Next month">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="calendar-weekdays">
                        <span>Sun</span>
                        <span>Mon</span>
                        <span>Tue</span>
                        <span>Wed</span>
                        <span>Thu</span>
                        <span>Fri</span>
                        <span>Sat</span>
                    </div>
                    <div id="calendarGrid" class="calendar-grid"></div>
                </section>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const monthLabel = document.getElementById('monthLabel');
            const calendarGrid = document.getElementById('calendarGrid');
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');

            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            const today = new Date();
            let viewDate = new Date(today.getFullYear(), today.getMonth(), 1);

            function formatDate(date) {
                return date.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
            }

            function formatKey(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function buildCell(dateObj, { muted = false } = {}) {
                const cell = document.createElement('div');
                cell.className = 'calendar-cell';
                if (muted) {
                    cell.classList.add('muted');
                }

                if (
                    dateObj.getFullYear() === today.getFullYear() &&
                    dateObj.getMonth() === today.getMonth() &&
                    dateObj.getDate() === today.getDate()
                ) {
                    cell.classList.add('today');
                }

                cell.textContent = dateObj.getDate();
                calendarGrid.appendChild(cell);
            }

            function renderCalendar() {
                monthLabel.textContent = `${monthNames[viewDate.getMonth()]} ${viewDate.getFullYear()}`;
                calendarGrid.innerHTML = '';

                const firstDayOfMonth = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1);
                const startingWeekday = firstDayOfMonth.getDay();
                const daysInMonth = new Date(viewDate.getFullYear(), viewDate.getMonth() + 1, 0).getDate();
                const daysInPrevMonth = new Date(viewDate.getFullYear(), viewDate.getMonth(), 0).getDate();

                for (let i = startingWeekday; i > 0; i -= 1) {
                    const day = daysInPrevMonth - i + 1;
                    buildCell(new Date(viewDate.getFullYear(), viewDate.getMonth() - 1, day), { muted: true });
                }

                for (let day = 1; day <= daysInMonth; day += 1) {
                    buildCell(new Date(viewDate.getFullYear(), viewDate.getMonth(), day));
                }

                const totalFilled = calendarGrid.childElementCount;
                const totalCells = 42;
                for (let day = 1; totalFilled + day <= totalCells; day += 1) {
                    buildCell(new Date(viewDate.getFullYear(), viewDate.getMonth() + 1, day), { muted: true });
                }
            }

            prevMonthBtn?.addEventListener('click', () => {
                viewDate.setMonth(viewDate.getMonth() - 1);
                renderCalendar();
            });

            nextMonthBtn?.addEventListener('click', () => {
                viewDate.setMonth(viewDate.getMonth() + 1);
                renderCalendar();
            });

            renderCalendar();

            const joinClassModal = document.getElementById('joinClassModal');
            const joinClassOpeners = document.querySelectorAll('[data-join-class-open]');
            const joinClassClosers = document.querySelectorAll('[data-join-class-close]');

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

