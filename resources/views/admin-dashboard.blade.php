<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Q2L</title>
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
            --page-bg: linear-gradient(140deg, #e8edff 0%, #f5f7ff 100%);
            --surface-bg: #ffffff;
            --surface-border: rgba(148, 163, 184, 0.55);
            --surface-hover-border: rgba(79, 70, 229, 0.4);
            --surface-shadow: 0 18px 46px -30px rgba(15, 23, 42, 0.35);
            --surface-hover-shadow: 0 24px 60px -28px rgba(79, 70, 229, 0.35);
            --surface-soft-bg: rgba(241, 245, 255, 0.92);
            --surface-soft-border: rgba(148, 163, 184, 0.35);
            --text-primary: #0f172a;
            --text-muted: #1f2937;
            --text-subtle: #334155;
            --divider: rgba(148, 163, 184, 0.3);
            --field-bg: #ffffff;
            --field-border: rgba(148, 163, 184, 0.55);
            --field-border-focus: rgba(79, 70, 229, 0.5);
            --field-ring: rgba(99, 102, 241, 0.2);
            --pill-bg: rgba(79, 70, 229, 0.12);
            --badge-bg: rgba(148, 163, 184, 0.22);
            --table-header-bg: rgba(241, 245, 255, 0.92);
        }

        .dark {
            color-scheme: dark;
            --page-bg: linear-gradient(140deg, #0f172a 0%, #111827 45%, #020617 100%);
            --surface-bg: rgba(15, 23, 42, 0.9);
            --surface-border: rgba(71, 85, 105, 0.65);
            --surface-hover-border: rgba(129, 140, 248, 0.45);
            --surface-shadow: 0 30px 80px -36px rgba(8, 15, 35, 0.7);
            --surface-hover-shadow: 0 34px 90px -34px rgba(99, 102, 241, 0.55);
            --surface-soft-bg: rgba(30, 41, 59, 0.65);
            --surface-soft-border: rgba(51, 65, 85, 0.4);
            --text-primary: #e2e8f0;
            --text-muted: #cbd5f5;
            --text-subtle: #94a3b8;
            --divider: rgba(71, 85, 105, 0.4);
            --field-bg: rgba(15, 23, 42, 0.78);
            --field-border: rgba(71, 85, 105, 0.6);
            --field-border-focus: rgba(129, 140, 248, 0.55);
            --field-ring: rgba(79, 70, 229, 0.24);
            --pill-bg: rgba(99, 102, 241, 0.24);
            --badge-bg: rgba(148, 163, 184, 0.22);
            --table-header-bg: rgba(15, 23, 42, 0.75);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-bg);
            color: var(--text-primary);
            transition: background 0.45s ease, color 0.45s ease;
            min-height: 100vh;
        }

        .theme-body {
            color: var(--text-primary);
            transition: background 0.45s ease, color 0.45s ease;
        }

        .theme-surface {
            background-color: var(--surface-bg);
            border-color: var(--surface-border);
            box-shadow: var(--surface-shadow);
            backdrop-filter: blur(16px);
            transition: background-color 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease, color 0.35s ease;
        }

        .theme-surface:hover,
        .theme-surface:focus-within {
            border-color: var(--surface-hover-border);
            box-shadow: var(--surface-hover-shadow);
        }

        .theme-soft-surface {
            background-color: var(--surface-soft-bg);
            border-color: var(--surface-soft-border);
        }

        .theme-text-primary {
            color: var(--text-primary);
            transition: color 0.3s ease;
        }

        .theme-text-subtle {
            color: var(--text-subtle);
            transition: color 0.3s ease;
        }

        .theme-text-muted {
            color: var(--text-muted);
            transition: color 0.3s ease;
        }

        .theme-divider {
            border-color: var(--divider);
        }

        .theme-input {
            background-color: var(--field-bg);
            border: 1px solid var(--field-border);
            color: var(--text-primary);
            transition: background-color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease, color 0.25s ease;
        }

        .theme-input:focus {
            background-color: var(--field-bg);
            border-color: var(--field-border-focus);
            box-shadow: 0 0 0 4px var(--field-ring);
            outline: none;
        }

        .theme-input::placeholder {
            color: var(--text-subtle);
            opacity: 0.75;
        }

        .stat-icon {
            border-radius: 9999px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: #ffffff;
            box-shadow: 0 12px 30px -16px rgba(15, 23, 42, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-icon svg {
            stroke-width: 2;
            filter: drop-shadow(0 4px 10px rgba(15, 23, 42, 0.25));
        }

        .stat-icon--neutral {
            color: #1e293b;
        }

        .dark .stat-icon--neutral {
            color: #cbd5f5;
            background: rgba(148, 163, 184, 0.2);
            border-color: rgba(148, 163, 184, 0.4);
        }

        .stat-icon--success {
            color: #065f46;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(59, 130, 246, 0.05));
            border-color: rgba(16, 185, 129, 0.35);
        }

        .stat-icon--warning {
            color: #92400e;
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.18), rgba(248, 250, 252, 0.8));
            border-color: rgba(251, 191, 36, 0.35);
        }

        .dark .stat-icon {
            border-color: rgba(148, 163, 184, 0.35);
            background: rgba(15, 23, 42, 0.75);
            box-shadow: 0 18px 36px -20px rgba(2, 6, 23, 0.85);
        }

        .dark .stat-icon svg {
            filter: none;
        }

        .dark .stat-icon--success {
            color: #bbf7d0;
            background: rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.45);
        }

        .dark .stat-icon--warning {
            color: #fde68a;
            background: rgba(251, 191, 36, 0.24);
            border-color: rgba(251, 191, 36, 0.45);
        }

        .pending-badge {
            background: rgba(251, 191, 36, 0.12);
            color: #92400e;
            border: 1px solid rgba(251, 191, 36, 0.4);
            letter-spacing: 0.02em;
        }

        .dark .pending-badge {
            background: rgba(251, 191, 36, 0.2);
            color: #fde68a;
            border-color: rgba(251, 191, 36, 0.45);
        }

        .form-reset-btn {
            border: 1px solid rgba(148, 163, 184, 0.65);
            color: #1f2937;
            background: rgba(248, 250, 252, 0.95);
            box-shadow: 0 10px 30px -20px rgba(15, 23, 42, 0.4);
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-reset-btn:hover {
            background: #ffffff;
            border-color: rgba(79, 70, 229, 0.4);
            color: #111827;
            box-shadow: 0 15px 35px -22px rgba(79, 70, 229, 0.35);
        }

        .dark .form-reset-btn {
            border-color: rgba(148, 163, 184, 0.45);
            color: #e2e8f0;
            background: rgba(30, 41, 59, 0.88);
            box-shadow: 0 14px 30px -18px rgba(2, 6, 23, 0.9);
        }

        .dark .form-reset-btn:hover {
            background: rgba(30, 41, 59, 1);
            border-color: rgba(129, 140, 248, 0.55);
            color: #f8fafc;
        }

        .modal-check {
            color: #059669;
            filter: drop-shadow(0 16px 22px rgba(5, 150, 105, 0.35));
        }

        .dark .modal-check {
            color: #6ee7b7;
            filter: drop-shadow(0 22px 28px rgba(15, 118, 110, 0.5));
        }

        .review-label {
            color: #1f2937;
            letter-spacing: 0.25em;
            text-transform: uppercase;
        }

        .dark .review-label {
            color: #e2e8f0;
        }

        .review-note {
            background: rgba(253, 230, 138, 0.25);
            border: 1px solid rgba(251, 191, 36, 0.6);
            color: #92400e;
            box-shadow: 0 18px 38px -22px rgba(146, 64, 14, 0.35);
        }

        .dark .review-note {
            background: rgba(251, 191, 36, 0.22);
            color: #fde68a;
            border-color: rgba(251, 191, 36, 0.55);
            box-shadow: 0 22px 36px -22px rgba(15, 23, 42, 0.75);
        }

        .theme-pill {
            background-color: var(--pill-bg);
            color: var(--text-primary);
        }

        .theme-badge {
            background-color: var(--badge-bg);
            color: var(--text-primary);
        }

        .theme-table thead {
            background-color: var(--table-header-bg);
        }

        .theme-table tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.08);
        }

        .dark .theme-table tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.18);
        }

        .theme-modal {
            background-color: var(--surface-bg);
            color: var(--text-primary);
            border-color: var(--surface-border);
            box-shadow: var(--surface-shadow);
        }

        .theme-overlay {
            backdrop-filter: blur(8px);
        }

        .theme-heading {
            color: var(--text-primary);
        }
    </style>
</head>
<body class="theme-body min-h-screen">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-4 sm:py-6 md:py-8 space-y-4 sm:space-y-6 md:space-y-8">
        <!-- Header -->
        <header class="theme-surface border rounded-xl sm:rounded-2xl px-4 sm:px-6 py-4 sm:py-5 flex flex-col gap-3 sm:gap-4 md:flex-row md:items-center md:justify-between relative z-10">
            <div class="flex items-center gap-3 sm:gap-4">
                <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-10 sm:h-12 w-auto">
                <div>
                    <h1 class="text-lg sm:text-xl md:text-2xl font-semibold theme-text-primary">Administrator Console</h1>
                    <p class="text-xs sm:text-sm theme-text-subtle">Track teams, approve access, and onboard new teachers.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <x-theme-toggle class="shrink-0" />
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs sm:text-sm theme-text-muted">Signed in as</span>
                    <span class="text-sm sm:text-base font-semibold theme-text-primary">{{ auth()->user()->name ?? 'Administrator' }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 sm:gap-2 rounded-lg border border-red-200/70 bg-white dark:bg-transparent px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-red-600 dark:text-red-400 transition hover:bg-red-50 dark:hover:bg-red-500/10 hover:border-red-300 dark:hover:border-red-400/60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Sign Out</span>
                        <span class="sm:hidden">Out</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/40 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-200 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl border border-rose-200 dark:border-rose-500/40 bg-rose-50 dark:bg-rose-500/10 px-4 py-3 text-sm text-rose-700 dark:text-rose-200 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- KPI Summary -->
        <section class="grid grid-cols-1 gap-3 sm:gap-4 md:grid-cols-3" id="statsCards">
            <article class="theme-surface rounded-xl sm:rounded-2xl border p-4 sm:p-5" data-stat="total_teachers">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium theme-text-muted">Total Teachers</p>
                        <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-semibold theme-text-primary stat-value">{{ $stats['total_teachers'] ?? 0 }}</p>
                    </div>
                    <div class="stat-icon stat-icon--neutral p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c-1.833 0-3.5.667-4.5 1.5S6 14.5 6 16m12 0c0-1.5-.5-2.5-1.5-3.5S13.833 11 12 11m0 0c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4Zm0 0c2.21 0 4-1.79 4-4" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-xs theme-text-subtle">All registered teacher accounts in the system.</p>
            </article>
            <article class="theme-surface rounded-2xl border p-5" data-stat="active_teachers">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium theme-text-muted">Active Teachers</p>
                        <p class="mt-2 text-3xl font-semibold theme-text-primary stat-value">{{ $stats['active_teachers'] ?? 0 }}</p>
                    </div>
                    <div class="stat-icon stat-icon--success p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 11-18 0 9 9 0 0118 0Z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-xs theme-text-subtle">Teachers with an active session in the last {{ config('session.lifetime', 120) }} minutes.</p>
            </article>
            <article class="theme-surface rounded-2xl border p-5" data-stat="pending_approvals">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium theme-text-muted">Pending Approvals</p>
                        <p class="mt-2 text-3xl font-semibold theme-text-primary stat-value">{{ $stats['pending_approvals'] ?? 0 }}</p>
                    </div>
                    <div class="stat-icon stat-icon--warning p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374L10.052 4.126c.866-1.5 3.03-1.5 3.897 0l7.353 12.376Z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-3 text-xs theme-text-subtle">Teachers awaiting activation. Review their details below.</p>
            </article>
        </section>

        <section class="grid grid-cols-1 gap-8 xl:grid-cols-3">
            <div class="space-y-8 xl:col-span-2">
                <!-- Pending Approvals -->
                <div class="theme-surface rounded-2xl border p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold theme-text-primary">Pending Teacher Approvals</h2>
                            <p class="text-sm theme-text-subtle">Verify details before activating accounts.</p>
                        </div>
                        <span class="rounded-full pending-badge px-3 py-1 text-xs font-semibold uppercase tracking-wide" id="pendingBadge">{{ $stats['pending_approvals'] ?? 0 }} awaiting</span>
                    </div>
                    <div class="mt-6 divide-y divide-slate-100 dark:divide-slate-700/60" id="pendingList">
                        @forelse($pendingTeachers as $teacher)
                            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center md:justify-between" data-id="{{ $teacher->id }}">
                                <div>
                                    <p class="text-sm font-semibold theme-text-primary">{{ $teacher->name }}</p>
                                    <p class="text-xs theme-text-subtle">Subject: {{ ucfirst($teacher->subject ?? 'Not provided') }}</p>
                                    <p class="text-xs theme-text-subtle">Grade Level: {{ $teacher->grade_level ? 'Grade ' . $teacher->grade_level : 'Not provided' }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-xs theme-text-muted">Requested {{ $teacher->created_at_diff }}</span>
                                    <button
                                        class="rounded-lg border border-slate-200 dark:border-slate-600/60 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-800/60"
                                        data-action="review"
                                        data-teacher-id="{{ $teacher->id }}"
                                        data-teacher-name="{{ $teacher->name }}"
                                        data-teacher-email="{{ $teacher->email }}"
                                        data-teacher-subject="{{ $teacher->subject ?? '' }}"
                                        data-teacher-grade="{{ $teacher->grade_level ?? '' }}"
                                        data-teacher-requested="{{ $teacher->created_at_diff }}"
                                        data-teacher-notes="{{ $teacher->notes ?? '' }}"
                                    >Review</button>
                                </div>
                            </div>
                        @empty
                            <p class="py-6 text-center text-sm theme-text-subtle">No pending approvals right now.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Registrations -->
                <div class="theme-surface rounded-2xl border p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold theme-text-primary">Recently Added Teachers</h2>
                            <p class="text-sm theme-text-subtle">Latest registrations and their current status.</p>
                        </div>
                        <span class="rounded-full theme-badge px-3 py-1 text-xs font-medium">Last 6 records</span>
                    </div>
                    <div class="mt-4 sm:mt-6 overflow-x-auto -mx-4 sm:mx-0">
                        <table class="theme-table min-w-full divide-y divide-slate-100 dark:divide-slate-700/60 text-left text-xs sm:text-sm">
                            <thead class="text-xs uppercase tracking-wider theme-text-muted">
                                <tr>
                                    <th class="py-2 px-2 sm:px-4">Name</th>
                                    <th class="py-2 px-2 sm:px-4 hidden sm:table-cell">Email</th>
                                    <th class="py-2 px-2 sm:px-4 hidden md:table-cell">Subject</th>
                                    <th class="py-2 px-2 sm:px-4 text-center">Status</th>
                                    <th class="py-2 px-2 sm:px-4 text-right hidden lg:table-cell">Joined</th>
                                    <th class="py-2 px-2 sm:px-4 text-right">Manage</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60" id="recentTableBody">
                                @forelse($recentTeachers as $teacher)
                                    <tr class="transition-colors" data-id="{{ $teacher->id }}">
                                        <td class="py-2 sm:py-3 px-2 sm:px-4 font-medium theme-text-primary">
                                            <div class="sm:hidden text-xs text-slate-500 mb-1">{{ $teacher->email }}</div>
                                            {{ $teacher->name }}
                                        </td>
                                        <td class="py-2 sm:py-3 px-2 sm:px-4 theme-text-subtle hidden sm:table-cell">{{ $teacher->email }}</td>
                                        <td class="py-2 sm:py-3 px-2 sm:px-4 theme-text-subtle hidden md:table-cell">{{ ucfirst($teacher->subject ?? '—') }}</td>
                                        <td class="py-2 sm:py-3 px-2 sm:px-4 text-center">
                                            <span class="inline-flex items-center rounded-full px-2 sm:px-3 py-0.5 sm:py-1 text-xs font-semibold status-badge {{ isset($teacher->display_status_class) ? $teacher->display_status_class : ($teacher->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700') }}">
                                                {{ $teacher->display_status ?? ucfirst($teacher->status ?? 'unknown') }}
                                            </span>
                                        </td>
                                        <td class="py-2 sm:py-3 px-2 sm:px-4 text-right theme-text-subtle hidden lg:table-cell">{{ $teacher->created_at?->format('M d, Y') }}</td>
                                        <td class="py-2 sm:py-3 px-2 sm:px-4 text-right">
                                            <div class="flex justify-end gap-1 sm:gap-2">
                                                <button type="button"
                                                    class="rounded-lg border border-slate-200 dark:border-slate-600/60 px-2 sm:px-3 py-1 sm:py-1.5 text-xs font-medium text-slate-600 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-800/60"
                                                    data-action="edit-teacher"
                                                    data-teacher-id="{{ $teacher->id }}"
                                                    data-teacher-name="{{ $teacher->name }}"
                                                    data-teacher-email="{{ $teacher->email }}"
                                                    data-teacher-phone="{{ $teacher->phone }}"
                                                    data-teacher-subject="{{ $teacher->subject }}"
                                                    data-teacher-grade="{{ $teacher->grade_level }}"
                                                ><span class="hidden sm:inline">Update</span><span class="sm:hidden">Edit</span></button>
                                                <button type="button"
                                                    class="rounded-lg border border-rose-200 dark:border-rose-500/40 px-2 sm:px-3 py-1 sm:py-1.5 text-xs font-medium text-rose-600 dark:text-rose-300 transition hover:bg-rose-50 dark:hover:bg-rose-500/10"
                                                    data-action="delete-teacher"
                                                    data-teacher-id="{{ $teacher->id }}"
                                                    data-teacher-name="{{ $teacher->name }}"
                                                ><span class="hidden sm:inline">Delete</span><span class="sm:hidden">Del</span></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="6" class="py-6 text-center text-sm theme-text-subtle">No teachers registered yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Registration Sidebar -->
            <div class="xl:col-span-1">
                <div class="theme-surface rounded-xl sm:rounded-2xl border p-4 sm:p-6">
                    <h2 class="text-base sm:text-lg font-semibold theme-text-primary">Register New Teacher</h2>
                    <p class="mt-1 text-xs sm:text-sm theme-text-subtle">Create an account and assign initial details. They'll be prompted to update their password on first sign-in.</p>

                    <form id="teacherForm" action="{{ route('admin.teachers.store') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="mb-1 block text-xs font-medium theme-text-muted">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="theme-input w-full rounded-lg px-3 py-2 text-sm focus:outline-none @error('first_name') border-rose-300 @enderror">
                                @error('first_name')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium theme-text-muted">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="theme-input w-full rounded-lg px-3 py-2 text-sm focus:outline-none @error('last_name') border-rose-300 @enderror">
                                @error('last_name')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium theme-text-muted">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="theme-input w-full rounded-lg px-3 py-2 text-sm focus:outline-none @error('email') border-rose-300 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-medium theme-text-muted">Phone</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required class="theme-input w-full rounded-lg px-3 py-2 text-sm focus:outline-none @error('phone') border-rose-300 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium theme-text-muted">Subject</label>
                                <select name="subject" required class="theme-input w-full rounded-lg px-3 py-2 text-sm focus:outline-none @error('subject') border-rose-300 @enderror">
                                    <option value="">Select subject</option>
                                    <option value="english" {{ old('subject') == 'english' ? 'selected' : '' }}>English</option>
                                    <option value="filipino" {{ old('subject') == 'filipino' ? 'selected' : '' }}>Filipino</option>
                                </select>
                                @error('subject')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium theme-text-muted">Grade Level</label>
                            <select name="grade_level" required class="theme-input w-full rounded-lg px-3 py-2 text-sm focus:outline-none @error('grade_level') border-rose-300 @enderror">
                                <option value="">Select grade level</option>
                                <option value="7" {{ old('grade_level') == '7' ? 'selected' : '' }}>Grade 7</option>
                                <option value="8" {{ old('grade_level') == '8' ? 'selected' : '' }}>Grade 8</option>
                                <option value="9" {{ old('grade_level') == '9' ? 'selected' : '' }}>Grade 9</option>
                                <option value="10" {{ old('grade_level') == '10' ? 'selected' : '' }}>Grade 10</option>
                            </select>
                            @error('grade_level')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium theme-text-muted">Password</label>
                            <input type="password" name="password" required class="theme-input w-full rounded-lg px-3 py-2 text-sm focus:outline-none @error('password') border-rose-300 @enderror">
                            @error('password')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" id="teacherFormReset" class="rounded-lg px-4 py-2 text-xs font-semibold form-reset-btn">Clear</button>
                            <button type="submit" class="rounded-lg bg-slate-900 dark:bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-700 dark:hover:bg-indigo-500">Register Teacher</button>
                        </div>
                    </form>
                </div>

                <!-- Activity Timeline Placeholder -->
                <div class="theme-surface mt-8 rounded-2xl border p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold theme-text-primary">Recent Activity</h2>
                        <span class="rounded-full theme-badge px-3 py-1 text-xs font-medium">System log</span>
                    </div>
                    <div class="mt-5 space-y-4">
                        @forelse($recent_activities ?? [] as $activity)
                            <div class="relative pl-6">
                                <span class="absolute left-0 top-1.5 h-2 w-2 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                <p class="text-sm theme-text-subtle">{{ $activity->description }}</p>
                                <span class="text-xs theme-text-muted">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-sm theme-text-subtle">No recent activity logged yet. Actions performed here will appear once activity tracking is implemented.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black/40 dark:bg-black/70 hidden items-center justify-center p-4 z-50 theme-overlay">
        <div class="theme-modal rounded-xl p-6 max-w-md w-full">
            <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 modal-check" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-xl font-bold theme-text-primary mb-2">Teacher Registered Successfully!</h3>
                <p class="theme-text-subtle mb-6">The teacher account has been created and an email with login credentials will be sent.</p>
                <button onclick="closeSuccessModal()" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Review Pending Teacher Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 hidden items-center justify-center px-3 sm:px-4 py-4 sm:py-6 theme-overlay">
        <div class="w-full max-w-2xl rounded-xl sm:rounded-2xl theme-modal shadow-2xl max-h-[90vh] overflow-y-auto review-modal-panel">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700/60 px-4 sm:px-6 py-3 sm:py-4 sticky top-0 review-modal-bar">
                <div>
                    <p class="text-xs font-semibold review-label">Teacher request</p>
                    <h3 class="text-lg font-semibold theme-text-primary" id="reviewTeacherName">Review Request</h3>
                </div>
                <button id="reviewModalClose" class="rounded-full bg-slate-100 dark:bg-slate-800/70 p-2 text-slate-500 dark:text-slate-300 transition hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-700" type="button" aria-label="Close">
                    ✕
                </button>
            </div>

            <div class="grid gap-4 sm:gap-6 px-4 sm:px-6 py-4 sm:py-6 md:grid-cols-[2fr_3fr]">
                <aside class="space-y-3 sm:space-y-4 rounded-xl theme-soft-surface p-3 sm:p-4">
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-xs font-medium theme-text-muted">Name</dt>
                            <dd class="font-medium theme-text-primary" id="reviewTeacherNameDetail">—</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium theme-text-muted">Email</dt>
                            <dd class="font-medium theme-text-primary" id="reviewTeacherEmail">—</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium theme-text-muted">Subject</dt>
                            <dd class="font-medium theme-text-primary" id="reviewTeacherSubject">—</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium theme-text-muted">Grade Level</dt>
                            <dd class="font-medium theme-text-primary" id="reviewTeacherGrade">—</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium theme-text-muted">Requested</dt>
                            <dd class="font-medium theme-text-primary" id="reviewTeacherRequested">—</dd>
                        </div>
                        <div id="reviewTeacherNotesSection" class="hidden">
                            <dt class="text-xs font-medium theme-text-muted">Notes from Teacher</dt>
                            <dd class="text-sm theme-text-subtle mt-1 whitespace-pre-wrap" id="reviewTeacherNotes">—</dd>
                        </div>
                    </dl>
                </aside>

                <div class="space-y-4 sm:space-y-6">
                    <div class="rounded-xl border border-slate-200 dark:border-slate-600/60 p-3 sm:p-4 theme-soft-surface">
                        <h4 class="text-xs sm:text-sm font-semibold theme-text-primary">Approval details</h4>
                        <p class="mt-1 text-xs theme-text-subtle">Set a temporary password for the teacher. They must change it upon first login.</p>
                        <form id="reviewApproveForm" method="POST" class="mt-3 sm:mt-4 space-y-2 sm:space-y-3" action="#">
                            @csrf
                            <div>
                                <label for="reviewPassword" class="text-xs font-medium theme-text-muted">Temporary Password <span class="text-red-500">*</span></label>
                                <input type="text" id="reviewPassword" name="password" required minlength="8" placeholder="Enter password (minimum 8 characters)" class="mt-1 w-full theme-input rounded-lg px-3 py-2 text-xs sm:text-sm">
                                <p class="mt-1 text-xs theme-text-subtle">Minimum 8 characters required</p>
                            </div>
                            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-3">
                                <button type="button" id="reviewRejectButton" class="w-full sm:w-auto rounded-lg border border-rose-200 dark:border-rose-500/40 px-3 sm:px-4 py-2 text-xs font-semibold text-rose-600 dark:text-rose-300 transition hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                    Reject Request
                                </button>
                    <button type="submit" class="w-full sm:w-auto rounded-lg bg-emerald-600 px-3 sm:px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                    Approve &amp; Activate
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="rounded-xl review-note p-4 text-xs">
                        Approved teachers will receive the temporary password you provide. They must change it upon first login.
                    </div>
                </div>
            </div>
            <form id="reviewRejectForm" method="POST" class="hidden" action="#">
                @csrf
            </form>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div id="rejectConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 px-4 theme-overlay">
        <div class="w-full max-w-md rounded-2xl theme-modal p-6 shadow-xl">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 rounded-full bg-rose-100 dark:bg-rose-500/15 p-3">
                    <svg class="h-6 w-6 text-rose-600 dark:text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold theme-text-primary">Decline Teacher Request</h3>
                    <p class="mt-2 text-sm theme-text-subtle">Are you sure you want to decline this teacher request? This action cannot be undone.</p>
                    <p class="mt-1 text-sm font-medium theme-text-primary" id="rejectTeacherName"></p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" id="rejectConfirmCancel" class="rounded-lg border border-slate-200 dark:border-slate-600/60 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                    Cancel
                </button>
                <button type="button" id="rejectConfirmOk" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">
                    Yes, Decline Request
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Teacher Modal -->
    <div id="deleteTeacherModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 px-4 theme-overlay">
        <div class="w-full max-w-lg rounded-2xl theme-modal shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700/60 px-6 py-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide theme-text-muted">Remove teacher</p>
                    <h3 class="text-lg font-semibold theme-text-primary" id="deleteTeacherHeading">Delete Teacher</h3>
                </div>
                <button id="deleteTeacherClose" class="rounded-full bg-slate-100 dark:bg-slate-800/70 p-2 text-slate-500 dark:text-slate-300 transition hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-700" type="button" aria-label="Close">✕</button>
            </div>
            <form id="deleteTeacherForm" method="POST" action="#" class="space-y-5 px-6 py-6">
                @csrf
                @method('DELETE')
                <p class="text-sm theme-text-subtle">This will permanently remove <span class="font-semibold theme-text-primary" id="deleteTeacherName">this teacher</span> and all associated data. This action cannot be undone.</p>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" id="deleteTeacherCancel" class="rounded-lg border border-slate-200 dark:border-slate-600/60 px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-800/60">Cancel</button>
                    <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-rose-700">Delete Teacher</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Teacher Modal -->
    <div id="editTeacherModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 px-4 theme-overlay">
        <div class="w-full max-w-2xl rounded-2xl theme-modal shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700/60 px-6 py-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide theme-text-muted">Update teacher</p>
                    <h3 class="text-lg font-semibold theme-text-primary" id="editTeacherHeading">Edit Teacher</h3>
                </div>
                <button id="editTeacherClose" class="rounded-full bg-slate-100 dark:bg-slate-800/70 p-2 text-slate-500 dark:text-slate-300 transition hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-700" type="button" aria-label="Close">✕</button>
            </div>
            <form id="editTeacherForm" method="POST" action="#" class="px-6 py-6 space-y-5">
                @csrf
                @method('PUT')
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="editTeacherName" class="block text-xs font-medium theme-text-muted">Full Name</label>
                        <input type="text" id="editTeacherName" name="name" required class="mt-1 w-full theme-input rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label for="editTeacherEmail" class="block text-xs font-medium theme-text-muted">Email</label>
                        <input type="email" id="editTeacherEmail" name="email" required class="mt-1 w-full theme-input rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label for="editTeacherPhone" class="block text-xs font-medium theme-text-muted">Phone</label>
                        <input type="tel" id="editTeacherPhone" name="phone" class="mt-1 w-full theme-input rounded-lg px-3 py-2 text-sm" placeholder="Optional">
                    </div>
                    <div>
                        <label for="editTeacherSubject" class="block text-xs font-medium theme-text-muted">Subject</label>
                        <select id="editTeacherSubject" name="subject" class="mt-1 w-full theme-input rounded-lg px-3 py-2 text-sm">
                            <option value="">Not set</option>
                            <option value="english">English</option>
                            <option value="filipino">Filipino</option>
                        </select>
                    </div>
                    <div>
                        <label for="editTeacherGrade" class="block text-xs font-medium theme-text-muted">Grade Level</label>
                        <select id="editTeacherGrade" name="grade_level" class="mt-1 w-full theme-input rounded-lg px-3 py-2 text-sm">
                            <option value="">Not set</option>
                            <option value="7">Grade 7</option>
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                            <option value="10">Grade 10</option>
                        </select>
                    </div>
                    <div>
                        <label for="editTeacherStatus" class="block text-xs font-medium theme-text-muted">Status</label>
                        <select id="editTeacherStatus" name="status" class="mt-1 w-full theme-input rounded-lg px-3 py-2 text-sm">
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" id="editTeacherCancel" class="rounded-lg border border-slate-200 dark:border-slate-600/60 px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-200 transition hover:bg-slate-50 dark:hover:bg-slate-800/60">Cancel</button>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Generate a random password
        function generateRandomPassword() {
            const length = 10;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let password = "";
            for (let i = 0; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            return password;
        }

        // Generate password button
        const generatePasswordButton = document.getElementById('generatePassword');
        if (generatePasswordButton) {
            generatePasswordButton.addEventListener('click', function() {
                const passwordInput = document.querySelector('input[name="password"]');
                if (!passwordInput) return;
                passwordInput.value = generateRandomPassword();
                passwordInput.type = 'text';
                setTimeout(() => {
                    passwordInput.type = 'password';
                }, 3000);
            });
        }

        // Close success modal
        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            document.getElementById('successModal').classList.remove('flex');
        }

        // Show success modal if session has success message
        @if(session('success'))
            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('successModal').classList.add('flex');
        @endif

        // Live stats polling
        const statsEndpoint = "{{ route('admin.stats') }}";
        const teacherForm = document.getElementById('teacherForm');
        const teacherFormReset = document.getElementById('teacherFormReset');
        const statsCards = document.querySelectorAll('#statsCards [data-stat]');
        const pendingBadge = document.getElementById('pendingBadge');
        const pendingList = document.getElementById('pendingList');
        const recentTableBody = document.getElementById('recentTableBody');
        const reviewModal = document.getElementById('reviewModal');
        const reviewModalClose = document.getElementById('reviewModalClose');
        const reviewTeacherName = document.getElementById('reviewTeacherName');
        const reviewTeacherNameDetail = document.getElementById('reviewTeacherNameDetail');
        const reviewTeacherEmail = document.getElementById('reviewTeacherEmail');
        const reviewTeacherSubject = document.getElementById('reviewTeacherSubject');
        const reviewTeacherGrade = document.getElementById('reviewTeacherGrade');
        const reviewTeacherRequested = document.getElementById('reviewTeacherRequested');
        const reviewTeacherNotes = document.getElementById('reviewTeacherNotes');
        const reviewTeacherNotesSection = document.getElementById('reviewTeacherNotesSection');
        const reviewApproveForm = document.getElementById('reviewApproveForm');
        const reviewRejectButton = document.getElementById('reviewRejectButton');
        const reviewRejectForm = document.getElementById('reviewRejectForm');
        const reviewPasswordInput = document.getElementById('reviewPassword');
        const rejectConfirmModal = document.getElementById('rejectConfirmModal');
        const rejectConfirmCancel = document.getElementById('rejectConfirmCancel');
        const rejectConfirmOk = document.getElementById('rejectConfirmOk');
        const rejectTeacherName = document.getElementById('rejectTeacherName');
        const approveUrlTemplate = "{{ route('admin.teachers.approve', ['teacherRequest' => 'REPLACE_ID']) }}";
        const rejectUrlTemplate = "{{ route('admin.teachers.reject', ['teacherRequest' => 'REPLACE_ID']) }}";
        const deleteTeacherModal = document.getElementById('deleteTeacherModal');
        const deleteTeacherClose = document.getElementById('deleteTeacherClose');
        const deleteTeacherCancel = document.getElementById('deleteTeacherCancel');
        const deleteTeacherName = document.getElementById('deleteTeacherName');
        const deleteTeacherHeading = document.getElementById('deleteTeacherHeading');
        const deleteTeacherForm = document.getElementById('deleteTeacherForm');
        const deleteUrlTemplate = "{{ route('admin.teachers.destroy', ['teacher' => 'REPLACE_ID']) }}";
        const editTeacherModal = document.getElementById('editTeacherModal');
        const editTeacherClose = document.getElementById('editTeacherClose');
        const editTeacherCancel = document.getElementById('editTeacherCancel');
        const editTeacherHeading = document.getElementById('editTeacherHeading');
        const editTeacherForm = document.getElementById('editTeacherForm');
        const editTeacherName = document.getElementById('editTeacherName');
        const editTeacherEmail = document.getElementById('editTeacherEmail');
        const editTeacherPhone = document.getElementById('editTeacherPhone');
        const editTeacherSubject = document.getElementById('editTeacherSubject');
        const editTeacherGrade = document.getElementById('editTeacherGrade');
        const editUrlTemplate = "{{ route('admin.teachers.update', ['teacher' => 'REPLACE_ID']) }}";

        let currentReviewTeacherId = null;
        let currentDeleteTeacherId = null;
        let currentEditTeacherId = null;

        function updateStatsBoard(data) {
            statsCards.forEach(card => {
                const key = card.dataset.stat;
                if (key && data[key] !== undefined) {
                    const valueEl = card.querySelector('.stat-value');
                    if (valueEl) {
                        valueEl.textContent = data[key];
                    }
                }
            });

            if (pendingBadge && data.pending_approvals !== undefined) {
                pendingBadge.textContent = `${data.pending_approvals} awaiting`;
            }
        }

        teacherFormReset?.addEventListener('click', () => {
            if (!teacherForm) return;
            teacherForm.querySelectorAll('input, textarea').forEach(input => {
                if (input.type === 'hidden' || input.name === '_token') return;
                input.value = '';
                input.classList.remove('border-rose-300');
            });
            teacherForm.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
                select.classList.remove('border-rose-300');
            });
            teacherForm.querySelectorAll('.text-rose-500').forEach(msg => {
                msg.classList.add('hidden');
            });
        });

        function renderPendingList(items = []) {
            if (!pendingList) return;
            if (items.length === 0) {
                pendingList.innerHTML = '<p class="py-6 text-center text-sm theme-text-subtle">No pending approvals right now.</p>';
                return;
            }

            pendingList.innerHTML = items.map(item => `
                <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center md:justify-between" data-id="${item.id}">
                    <div>
                        <p class="text-sm font-semibold theme-text-primary">${item.name}</p>
                        <p class="text-xs theme-text-subtle">Subject: ${item.subject ? capitalize(item.subject) : 'Not provided'}</p>
                        <p class="text-xs theme-text-subtle">Grade Level: ${item.grade_level ? `Grade ${item.grade_level}` : 'Not provided'}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs theme-text-muted">Requested ${item.created_at_diff ?? ''}</span>
                        <button
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium theme-text-primary transition hover:bg-slate-50"
                            data-action="review"
                            data-teacher-id="${item.id}"
                            data-teacher-name="${item.name}"
                            data-teacher-email="${item.email}"
                            data-teacher-subject="${item.subject ?? ''}"
                            data-teacher-grade="${item.grade_level ?? ''}"
                            data-teacher-requested="${item.created_at_diff ?? ''}"
                            data-teacher-notes="${item.notes ?? ''}"
                        >Review</button>
                    </div>
                </div>
            `).join('');

            attachReviewHandlers();
        }

        function renderRecentTeachers(items = []) {
            if (!recentTableBody) return;
            if (items.length === 0) {
                recentTableBody.innerHTML = '<tr class="empty-row"><td colspan="6" class="py-6 text-center text-sm theme-text-subtle">No teachers registered yet.</td></tr>';
                return;
            }

            recentTableBody.innerHTML = items.map(item => {
                const statusClass = item.status === 'active'
                    ? 'bg-emerald-100 text-emerald-700'
                    : item.status === 'pending'
                        ? 'bg-amber-100 text-amber-700'
                        : 'bg-slate-200 text-slate-700';

                return `
                    <tr class="hover:bg-slate-50/60" data-id="${item.id}">
                        <td class="py-3 font-medium theme-text-primary">${item.name}</td>
                        <td class="py-3 theme-text-subtle">${item.email}</td>
                        <td class="py-3 theme-text-subtle">${item.subject ? capitalize(item.subject) : '—'}</td>
                        <td class="py-3 text-center">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs/plain font-semibold status-badge ${statusClass}">
                                ${item.status ? capitalize(item.status) : 'Unknown'}
                            </span>
                        </td>
                        <td class="py-3 text-right theme-text-subtle">${item.joined_at ?? ''}</td>
                        <td class="py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button type="button"
                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium theme-text-primary transition hover:bg-slate-50"
                                    data-action="edit-teacher"
                                    data-teacher-id="${item.id}"
                                    data-teacher-name="${item.name}"
                                    data-teacher-email="${item.email}"
                                    data-teacher-phone="${item.phone ?? ''}"
                                    data-teacher-subject="${item.subject ?? ''}"
                                    data-teacher-grade="${item.grade_level ?? ''}"
                                >Update</button>
                                <button type="button"
                                    class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50"
                                    data-action="delete-teacher"
                                    data-teacher-id="${item.id}"
                                    data-teacher-name="${item.name}"
                                >Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            attachDeleteHandlers();
            attachEditHandlers();
        }

        function capitalize(value = '') {
            return value.charAt(0).toUpperCase() + value.slice(1);
        }

        async function pollStats() {
            try {
                const response = await fetch(statsEndpoint, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error('Failed to fetch stats');
                const payload = await response.json();
                if (payload?.stats) {
                    updateStatsBoard(payload.stats);
                }
                if (payload?.pending_teachers) {
                    renderPendingList(payload.pending_teachers);
                }
                if (payload?.recent_teachers) {
                    renderRecentTeachers(payload.recent_teachers);
                }
            } catch (error) {
                console.error('Stats polling error:', error);
            }
        }

        function attachReviewHandlers() {
            if (!pendingList) return;
            const buttons = pendingList.querySelectorAll('button[data-action="review"]');
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    const teacherData = {
                        id: button.dataset.teacherId,
                        name: button.dataset.teacherName,
                        email: button.dataset.teacherEmail,
                        subject: button.dataset.teacherSubject,
                        grade_level: button.dataset.teacherGrade,
                        requested: button.dataset.teacherRequested,
                        notes: button.dataset.teacherNotes,
                    };
                    openReviewModal(teacherData);
                });
            });
        }

        function openReviewModal(teacher) {
            if (!reviewModal || !reviewApproveForm || !reviewRejectForm) return;

            currentReviewTeacherId = teacher.id;

            reviewTeacherName.textContent = `Review ${teacher.name}`;
            reviewTeacherNameDetail.textContent = teacher.name;
            reviewTeacherEmail.textContent = teacher.email;
            reviewTeacherSubject.textContent = teacher.subject ? capitalize(teacher.subject) : 'Not provided';
            reviewTeacherGrade.textContent = teacher.grade_level ? `Grade ${teacher.grade_level}` : 'Not provided';
            reviewTeacherRequested.textContent = teacher.requested || '—';
            
            // Show/hide notes section
            if (teacher.notes && teacher.notes.trim() !== '') {
                reviewTeacherNotes.textContent = teacher.notes;
                reviewTeacherNotesSection.classList.remove('hidden');
            } else {
                reviewTeacherNotesSection.classList.add('hidden');
            }
            
            reviewPasswordInput.value = '';

            const approveUrl = approveUrlTemplate.replace('REPLACE_ID', teacher.id);
            const rejectUrl = rejectUrlTemplate.replace('REPLACE_ID', teacher.id);
            reviewApproveForm.action = approveUrl;
            reviewRejectForm.action = rejectUrl;

            reviewModal.classList.remove('hidden');
            reviewModal.classList.add('flex');
        }

        function closeReviewModal() {
            if (!reviewModal) return;
            reviewModal.classList.add('hidden');
            reviewModal.classList.remove('flex');
            currentReviewTeacherId = null;
        }

        reviewModalClose?.addEventListener('click', closeReviewModal);

        reviewModal?.addEventListener('click', (event) => {
            if (event.target === reviewModal) {
                closeReviewModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeReviewModal();
            }
        });

        reviewRejectButton?.addEventListener('click', () => {
            if (!reviewRejectForm || !rejectConfirmModal) return;
            // Show reject confirmation modal
            const teacherName = reviewTeacherNameDetail.textContent;
            rejectTeacherName.textContent = teacherName;
            rejectConfirmModal.classList.remove('hidden');
            rejectConfirmModal.classList.add('flex');
        });

        // Handle reject confirmation cancel
        rejectConfirmCancel?.addEventListener('click', () => {
            if (!rejectConfirmModal) return;
            rejectConfirmModal.classList.add('hidden');
            rejectConfirmModal.classList.remove('flex');
        });

        // Handle reject confirmation OK
        rejectConfirmOk?.addEventListener('click', () => {
            if (!reviewRejectForm || !rejectConfirmModal) return;
            rejectConfirmModal.classList.add('hidden');
            rejectConfirmModal.classList.remove('flex');
            reviewRejectForm.submit();
        });

        // Close reject modal on backdrop click
        rejectConfirmModal?.addEventListener('click', (e) => {
            if (e.target === rejectConfirmModal) {
                rejectConfirmModal.classList.add('hidden');
                rejectConfirmModal.classList.remove('flex');
            }
        });

        function attachDeleteHandlers() {
            if (!recentTableBody) return;
            const buttons = recentTableBody.querySelectorAll('button[data-action="delete-teacher"]');
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    openDeleteModal({
                        id: button.dataset.teacherId,
                        name: button.dataset.teacherName,
                    });
                });
            });
        }

        function openDeleteModal(teacher) {
            if (!deleteTeacherModal || !deleteTeacherForm) return;

            currentDeleteTeacherId = teacher.id;
            deleteTeacherHeading.textContent = `Delete ${teacher.name}`;
            deleteTeacherName.textContent = teacher.name;

            const deleteUrl = deleteUrlTemplate.replace('REPLACE_ID', teacher.id);
            deleteTeacherForm.action = deleteUrl;

            deleteTeacherModal.classList.remove('hidden');
            deleteTeacherModal.classList.add('flex');
        }

        function closeDeleteModal() {
            if (!deleteTeacherModal) return;
            deleteTeacherModal.classList.add('hidden');
            deleteTeacherModal.classList.remove('flex');
            currentDeleteTeacherId = null;
        }

        deleteTeacherClose?.addEventListener('click', closeDeleteModal);
        deleteTeacherCancel?.addEventListener('click', closeDeleteModal);
        deleteTeacherModal?.addEventListener('click', (event) => {
            if (event.target === deleteTeacherModal) {
                closeDeleteModal();
            }
        });

        function attachEditHandlers() {
            if (!recentTableBody) return;
            const buttons = recentTableBody.querySelectorAll('button[data-action="edit-teacher"]');
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    openEditModal({
                        id: button.dataset.teacherId,
                        name: button.dataset.teacherName,
                        email: button.dataset.teacherEmail,
                        phone: button.dataset.teacherPhone,
                        subject: button.dataset.teacherSubject,
                        grade_level: button.dataset.teacherGrade,
                    });
                });
            });
        }

        function openEditModal(teacher) {
            if (!editTeacherModal || !editTeacherForm) return;

            currentEditTeacherId = teacher.id;
            editTeacherHeading.textContent = `Edit ${teacher.name}`;
            editTeacherName.value = teacher.name || '';
            editTeacherEmail.value = teacher.email || '';
            editTeacherPhone.value = teacher.phone || '';
            editTeacherSubject.value = teacher.subject || '';
            editTeacherGrade.value = teacher.grade_level || '';

            const editUrl = editUrlTemplate.replace('REPLACE_ID', teacher.id);
            editTeacherForm.action = editUrl;

            editTeacherModal.classList.remove('hidden');
            editTeacherModal.classList.add('flex');
        }

        function closeEditModal() {
            if (!editTeacherModal) return;
            editTeacherModal.classList.add('hidden');
            editTeacherModal.classList.remove('flex');
            currentEditTeacherId = null;
        }

        editTeacherClose?.addEventListener('click', closeEditModal);
        editTeacherCancel?.addEventListener('click', closeEditModal);
        editTeacherModal?.addEventListener('click', (event) => {
            if (event.target === editTeacherModal) {
                closeEditModal();
            }
        });

        attachReviewHandlers();
        attachDeleteHandlers();
        attachEditHandlers();

        setInterval(pollStats, 15000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                pollStats();
            }
        });
        pollStats();
    </script>
</body>
</html>
