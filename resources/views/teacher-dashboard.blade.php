<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quest2Learn · Teacher Dashboard</title>
    <x-theme-script />
    <!-- Abandon Class Modal -->
    <div id="abandonClassModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>
        <div class="relative z-10 max-w-xl w-full mx-auto px-4 py-10 min-h-full flex items-center">
            <div class="w-full rounded-3xl border border-purple-600/40 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white shadow-2xl overflow-hidden">
                <div class="px-8 py-6 bg-gradient-to-r from-red-500 via-purple-600 to-indigo-600 flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.45em] text-white/75">Warning</p>
                        <h3 class="text-3xl font-black mt-1">Abandon Questline?</h3>
                    </div>
                    <div class="h-14 w-14 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center text-2xl">🛑</div>
                </div>
                <div class="px-8 py-8 space-y-6">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-indigo-200">Class Adventure</p>
                        <p id="abandonClassTitle" class="text-2xl font-bold mt-1">Class Name</p>
                        <p id="abandonClassLevel" class="text-sm text-slate-300">Level 1 Quest</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/5 p-5 text-sm text-slate-200 leading-relaxed">
                        <p>Leaving this questline will purge all buffs, progress logs, and tracking boosts tied to this class. Students must rejoin via the summoning code. This action cannot be undone.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" id="cancelAbandonClass" class="rounded-2xl border border-white/20 bg-white/10 py-3 font-semibold text-white hover:bg-white/15 transition-colors">Stay in Class</button>
                        <button type="button" id="confirmAbandonClass" class="rounded-2xl bg-gradient-to-r from-red-500 via-purple-500 to-indigo-500 py-3 font-semibold text-white shadow-lg shadow-red-500/40 hover:brightness-110 transition-all">Yes, abandon</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            --page-bg: linear-gradient(140deg, #e8edff 0%, #f4f7ff 100%);
            --card-bg: #ffffff;
            --surface-border: rgba(148, 163, 184, 0.35);
            --surface-border-strong: rgba(99, 102, 241, 0.24);
            --shadow-soft: 0 24px 55px -25px rgba(15, 23, 42, 0.25);
            --shadow-hover: 0 30px 60px -30px rgba(79, 70, 229, 0.35);
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --text-soft: #94a3b8;
            --surface-accent: rgba(79, 70, 229, 0.12);
            --surface-contrast: rgba(79, 70, 229, 0.18);
            --badge-bg: rgba(79, 70, 229, 0.12);
            --badge-text: #4338ca;
        }

        .dark {
            color-scheme: dark;
            --brand-primary: #6366f1;
            --brand-primary-dark: #818cf8;
            --page-bg: linear-gradient(160deg, #0f172a 0%, #1e293b 50%, #020617 100%);
            --card-bg: rgba(15, 23, 42, 0.88);
            --surface-border: rgba(71, 85, 105, 0.55);
            --surface-border-strong: rgba(129, 140, 248, 0.4);
            --shadow-soft: 0 24px 55px -25px rgba(2, 6, 23, 0.65);
            --shadow-hover: 0 30px 70px -30px rgba(99, 102, 241, 0.45);
            --text-primary: #e2e8f0;
            --text-muted: #cbd5f5;
            --text-soft: #94a3b8;
            --surface-accent: rgba(129, 140, 248, 0.18);
            --surface-contrast: rgba(129, 140, 248, 0.28);
            --badge-bg: rgba(99, 102, 241, 0.24);
            --badge-text: #e0e7ff;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: var(--text-primary);
            background: var(--page-bg);
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--surface-border);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-soft);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 0.2s ease, background-color 0.2s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: var(--surface-border-strong);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .progress-track {
            height: 0.75rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.25);
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f97316 0%, #facc15 45%, #22c55e 100%);
            box-shadow: 0 3px 12px rgba(34, 197, 94, 0.35);
            transition: width 260ms ease;
        }

        .glass-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            color: #ffffff;
            box-shadow: 0 12px 24px -14px rgba(79, 70, 229, 0.75);
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .glass-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 28px -12px rgba(99, 102, 241, 0.8);
        }

        .glass-button.secondary {
            background: rgba(255, 255, 255, 0.92);
            color: var(--brand-primary);
            box-shadow: 0 12px 24px -18px rgba(79, 70, 229, 0.3);
            border: 1px solid var(--surface-border);
        }

        .glass-button.secondary:hover {
            box-shadow: 0 16px 28px -16px rgba(148, 163, 184, 0.35);
        }

        .theme-name-accent {
            color: var(--brand-primary);
            transition: color 0.25s ease;
        }

        .logout-button {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 1.1rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            color: var(--brand-primary);
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--surface-border);
            box-shadow: 0 8px 18px -14px rgba(79, 70, 229, 0.35);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .logout-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -16px rgba(79, 70, 229, 0.4);
        }

        .logout-button svg {
            height: 1rem;
            width: 1rem;
        }

        .theme-text-primary {
            color: var(--text-primary);
        }

        .theme-text-muted {
            color: var(--text-muted);
        }

        .theme-text-soft {
            color: var(--text-soft);
        }

        .surface-item {
            border: 1px solid var(--surface-border);
            border-radius: 1rem;
            background: var(--card-bg);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .surface-item:hover {
            border-color: var(--surface-border-strong);
            box-shadow: 0 18px 40px -32px rgba(79, 70, 229, 0.35);
        }

        .tone-emerald {
            background: rgba(16, 185, 129, 0.16);
            color: #047857;
        }

        .tone-amber {
            background: rgba(245, 158, 11, 0.16);
            color: #92400e;
        }

        .tone-rose {
            background: rgba(244, 63, 94, 0.18);
            color: #be123c;
        }

        .tone-indigo {
            background: rgba(99, 102, 241, 0.18);
            color: #4338ca;
        }

        .tone-sky {
            background: rgba(56, 189, 248, 0.18);
            color: #075985;
        }

        .dark .surface-item {
            background: rgba(15, 23, 42, 0.9);
        }

        .dark .tone-emerald {
            background: rgba(16, 185, 129, 0.25);
            color: #bbf7d0;
        }

        .dark .tone-amber {
            background: rgba(245, 158, 11, 0.28);
            color: #fcd34d;
        }

        .dark .tone-rose {
            background: rgba(244, 63, 94, 0.28);
            color: #f9a8d4;
        }

        .dark .tone-indigo {
            background: rgba(99, 102, 241, 0.32);
            color: #c7d2fe;
        }

        .dark .tone-sky {
            background: rgba(56, 189, 248, 0.32);
            color: #bae6fd;
        }

        .timeline-dot {
            position: absolute;
            left: 0;
            top: 0.55rem;
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.6);
        }

        .timeline-dot.active {
            background: var(--brand-primary);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .timeline-line {
            position: absolute;
            left: 5px;
            top: 1.9rem;
            bottom: -1.2rem;
            width: 1px;
            background: var(--surface-border);
        }

        @media (prefers-reduced-motion: reduce) {
            .card:hover {
                transform: none;
                box-shadow: var(--shadow-soft);
            }
            .glass-button,
            .glass-button.secondary {
                transition: none;
            }
            .progress-fill {
                transition: none;
            }
            .surface-item {
                transition: none;
            }
            .surface-item:hover {
                box-shadow: none;
            }
        }
        
        /* Light mode readability fixes for Mentor Level card */
        :root:not(.dark) .mentor-card {
            background: linear-gradient(135deg, rgba(224, 231, 255, 0.96), rgba(233, 213, 255, 0.96));
            border-color: rgba(129, 140, 248, 0.55);
        }

        :root:not(.dark) .mentor-card .text-indigo-300 {
            color: #4338ca !important;
        }

        :root:not(.dark) .mentor-card .text-white {
            color: #111827 !important;
        }

        :root:not(.dark) .mentor-card .text-xs.text-indigo-300,
        :root:not(.dark) .mentor-card p.text-xs {
            color: #4b5563 !important;
        }

        :root:not(.dark) .mentor-card .bg-indigo-900\/50 {
            background-color: rgba(191, 219, 254, 0.9);
        }

        :root:not(.dark) .mentor-card .border-indigo-700\/30 {
            border-color: rgba(129, 140, 248, 0.45);
        }

        :root:not(.dark) .mentor-progress-track {
            background: rgba(99, 102, 241, 0.15) !important;
            border: 1px solid rgba(99, 102, 241, 0.35);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.75);
        }

        :root:not(.dark) .mentor-progress-fill {
            background: linear-gradient(90deg, #fbbf24, #f97316, #ec4899) !important;
            box-shadow: 0 4px 16px rgba(236, 72, 153, 0.45);
        }

        /* Light mode header text visibility fixes */
        :root:not(.dark) header .text-white {
            color: #111827 !important;
        }

        :root:not(.dark) header .text-gray-300 {
            color: #4b5563 !important;
        }

        :root:not(.dark) header .text-amber-400,
        :root:not(.dark) header .text-purple-400 {
            color: #6d28d9 !important;
        }

        :root:not(.dark) header .text-red-400,
        :root:not(.dark) header .text-blue-400,
        :root:not(.dark) header .text-green-400 {
            color: #1f2937 !important;
        }

        :root:not(.dark) header .bg-gradient-to-r {
            background: linear-gradient(90deg, #6d28d9, #4338ca, #1e40af) !important;
            background-clip: text !important;
            -webkit-background-clip: text !important;
            color: transparent !important;
        }

        /* Light mode Active Quests section text visibility fixes */
        :root:not(.dark) section .text-white {
            color: #111827 !important;
        }

        :root:not(.dark) section .text-gray-300 {
            color: #4b5563 !important;
        }

        :root:not(.dark) section .text-purple-400,
        :root:not(.dark) section .text-purple-300 {
            color: #6d28d9 !important;
        }

        :root:not(.dark) section .text-indigo-300 {
            color: #4338ca !important;
        }

        :root:not(.dark) section .text-amber-400,
        :root:not(.dark) section .text-amber-300 {
            color: #b45309 !important;
        }

        :root:not(.dark) section .text-emerald-300 {
            color: #047857 !important;
        }

        :root:not(.dark) section .bg-gradient-to-br {
            opacity: 0.85 !important;
        }

        :root:not(.dark) section .bg-purple-900\/30,
        :root:not(.dark) section .bg-indigo-900\/30,
        :root:not(.dark) section .bg-blue-900\/30 {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.15), rgba(99, 102, 241, 0.15)) !important;
        }

        :root:not(.dark) section .bg-purple-900\/40,
        :root:not(.dark) section .bg-indigo-900\/40 {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(99, 102, 241, 0.2)) !important;
        }

        /* Light mode Class Pulse section text visibility fixes */
        :root:not(.dark) .card .text-white {
            color: #111827 !important;
        }

        :root:not(.dark) .card .text-gray-300 {
            color: #4b5563 !important;
        }

        :root:not(.dark) .card .text-purple-400,
        :root:not(.dark) .card .text-purple-300 {
            color: #6d28d9 !important;
        }

        :root:not(.dark) .card .text-indigo-300 {
            color: #4338ca !important;
        }

        :root:not(.dark) .card .text-amber-400,
        :root:not(.dark) .card .text-amber-300 {
            color: #b45309 !important;
        }

        :root:not(.dark) .card .text-emerald-300 {
            color: #047857 !important;
        }

        :root:not(.dark) .card .text-red-300 {
            color: #b91c1c !important;
        }

        .hero-overlay,
        .quest-overlay {
            pointer-events: none;
        }

        .hero-stat {
            position: relative;
        }

        :root:not(.dark) .teacher-dashboard .hero-overlay {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(167, 139, 250, 0.12), rgba(248, 250, 252, 0.95));
        }

        :root:not(.dark) .teacher-dashboard .hero-card {
            background: rgba(255, 255, 255, 0.92);
            border-color: rgba(148, 163, 184, 0.4);
            box-shadow: 0 30px 80px -45px rgba(15, 23, 42, 0.45);
        }

        :root:not(.dark) .teacher-dashboard .hero-stat {
            background: linear-gradient(140deg, rgba(250, 250, 255, 0.95), rgba(237, 242, 255, 0.95));
            border-color: rgba(148, 163, 184, 0.45);
        }

        :root:not(.dark) .teacher-dashboard .hero-stat-icon {
            background: rgba(248, 250, 255, 0.85) !important;
            color: var(--brand-primary);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        :root:not(.dark) .teacher-dashboard .hero-stat--health .hero-stat-icon {
            color: #dc2626;
            background: rgba(254, 226, 226, 0.85) !important;
        }

        :root:not(.dark) .teacher-dashboard .hero-stat--wisdom .hero-stat-icon {
            color: #2563eb;
            background: rgba(219, 234, 254, 0.85) !important;
        }

        :root:not(.dark) .teacher-dashboard .hero-stat--power .hero-stat-icon {
            color: #059669;
            background: rgba(209, 250, 229, 0.85) !important;
        }

        :root:not(.dark) .teacher-dashboard .hero-stat .text-white,
        :root:not(.dark) .teacher-dashboard .hero-stat .text-gray-300 {
            color: var(--text-primary) !important;
        }

        :root:not(.dark) .teacher-dashboard .hero-stat .text-xs.text-red-400,
        :root:not(.dark) .teacher-dashboard .hero-stat .text-xs.text-blue-400,
        :root:not(.dark) .teacher-dashboard .hero-stat .text-xs.text-green-400 {
            color: var(--text-muted) !important;
        }

        :root:not(.dark) .teacher-dashboard .hero-actions .bg-white\/10 {
            background: rgba(255, 255, 255, 0.9);
            color: var(--brand-primary);
            border: 1px solid rgba(148, 163, 184, 0.4);
        }

        :root:not(.dark) .teacher-dashboard .quest-section .quest-overlay {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(147, 51, 234, 0.07), rgba(59, 130, 246, 0.06));
        }

        :root:not(.dark) .teacher-dashboard .quest-card {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 255, 0.92));
            border-color: rgba(148, 163, 184, 0.35);
            box-shadow: 0 25px 70px -45px rgba(30, 64, 175, 0.6);
        }

        :root:not(.dark) .teacher-dashboard .quest-card .text-white,
        :root:not(.dark) .teacher-dashboard .quest-card .text-gray-300 {
            color: var(--text-primary) !important;
        }

        :root:not(.dark) .teacher-dashboard .quest-card .text-purple-300,
        :root:not(.dark) .teacher-dashboard .quest-card .text-purple-400 {
            color: #6d28d9 !important;
        }

        :root:not(.dark) .teacher-dashboard .quest-card .text-amber-300,
        :root:not(.dark) .teacher-dashboard .quest-card .text-amber-400 {
            color: #b45309 !important;
        }

        :root:not(.dark) .teacher-dashboard .quest-card__code {
            background: linear-gradient(135deg, rgba(224, 231, 255, 0.95), rgba(233, 213, 255, 0.9));
            border-color: rgba(129, 140, 248, 0.45);
        }

        :root:not(.dark) .teacher-dashboard .quest-card__code p,
        :root:not(.dark) .teacher-dashboard .quest-card__code span {
            color: var(--text-primary) !important;
        }

        :root:not(.dark) .teacher-dashboard .quest-card__code .text-amber-400 {
            color: #b45309 !important;
        }

        :root:not(.dark) .teacher-dashboard .quest-card__badges > div {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(148, 163, 184, 0.45);
        }

        :root:not(.dark) .teacher-dashboard .quest-create-panel {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(241, 245, 249, 0.95));
            border-color: rgba(148, 163, 184, 0.45);
            color: var(--text-primary);
        }

        :root:not(.dark) .teacher-dashboard .quest-create-panel .text-white {
            color: var(--text-primary) !important;
        }

        :root:not(.dark) .teacher-dashboard .quest-create-panel .text-gray-300 {
            color: var(--text-muted) !important;
        }

        :root:not(.dark) .teacher-dashboard .quest-create-panel input,
        :root:not(.dark) .teacher-dashboard .quest-create-panel label {
            color: var(--text-primary);
        }

        :root:not(.dark) .teacher-dashboard .quest-create-panel input {
            background: rgba(248, 250, 252, 0.9);
            border-color: rgba(148, 163, 184, 0.45);
            color: var(--text-primary);
        }

        :root:not(.dark) .teacher-dashboard .quest-create-panel input::placeholder {
            color: var(--text-muted);
        }

        /* Calendar Styles */
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
            color: var(--text-soft);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .month-nav button:hover {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
            background: var(--surface-accent);
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
            background: rgba(148, 163, 184, 0.1);
            transition: all 0.15s ease;
        }
        .calendar-cell:hover {
            background: var(--surface-contrast);
            color: var(--brand-primary);
        }
        .calendar-cell.muted {
            color: var(--text-soft);
            background: transparent;
        }
        .calendar-cell.today {
            background: var(--brand-primary);
            color: white;
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
            background: white;
        }
    </style>
</head>
<body class="min-h-screen relative teacher-dashboard">
    <nav class="relative bg-[color:var(--card-bg)] border-b border-[color:var(--surface-border)] shadow-[0_20px_40px_-34px_rgba(15,23,42,0.35)]">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
            <div class="h-14 sm:h-16 flex items-center justify-between">
                <div class="flex items-center gap-2 sm:gap-3">
                    <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-9 sm:h-11 w-auto rounded-xl sm:rounded-2xl shadow-lg shadow-indigo-500/40">
                    <div>
                        <p class="text-[0.65rem] sm:text-xs uppercase tracking-widest text-[color:var(--text-soft)]">Quest2Learn</p>
                        <h1 class="text-sm sm:text-base md:text-lg font-semibold text-[color:var(--text-primary)]" data-translate="teacher-command-center">Teacher Command Center</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <x-translation-toggle class="hidden sm:flex" />
                    <x-theme-toggle class="hidden sm:flex" />
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs sm:text-sm font-medium text-[color:var(--text-primary)]">{{ \Illuminate\Support\Str::title(auth()->user()->name ?? 'Teacher') }}</span>
                        <span class="text-[0.65rem] sm:text-xs text-[color:var(--text-muted)]" data-translate="teacher-level-guide">Level Guide</span>
                    </div>
                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button type="button" @click="open = !open" @click.outside="open = false"
                                class="flex items-center justify-center rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                                aria-haspopup="true" :aria-expanded="open ? 'true' : 'false'">
                            <span class="sr-only">Open user menu</span>
                            <div class="h-9 w-9 sm:h-11 sm:w-11 rounded-full bg-gradient-to-br from-indigo-200 to-purple-200 text-indigo-600 flex items-center justify-center font-semibold shadow-inner text-sm sm:text-base">
                                {{ strtoupper(substr(auth()->user()->name ?? 'T', 0, 1)) }}
                            </div>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute right-0 mt-2 w-60 rounded-2xl border border-[color:var(--surface-border)] bg-[color:var(--card-bg)] shadow-lg shadow-indigo-500/20 z-50"
                             style="display: none;">
                            <div class="px-4 py-3 border-b border-[color:var(--surface-border)]">
                                <p class="text-[0.65rem] font-semibold uppercase tracking-widest text-[color:var(--text-soft)]" data-translate="teacher-account-menu-heading">Account</p>
                                <p class="mt-1 text-sm font-semibold text-[color:var(--text-primary)]">{{ \Illuminate\Support\Str::title(auth()->user()->name ?? 'Teacher') }}</p>
                                <p class="mt-1 text-xs text-[color:var(--text-muted)]">
                                    <span data-translate="teacher-signed-in-as">Signed in as</span>
                                    <span class="ml-1 font-medium text-[color:var(--text-primary)]">{{ auth()->user()->email ?? 'teacher@example.com' }}</span>
                                </p>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex w-full items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 dark:text-red-400 dark:hover:bg-red-500/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H3" />
                                        </svg>
                                        <span data-translate="teacher-log-out">Log out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="relative max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-6 sm:py-8 md:py-12 space-y-6 sm:space-y-8 md:py-12">
        <!-- Gamified Header with Character Profile -->
        <header class="relative overflow-hidden hero-section">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-900/20 via-indigo-900/20 to-blue-900/20 rounded-3xl hero-overlay"></div>
            <div class="relative card hero-card p-6 sm:p-8 md:p-12 flex flex-col lg:flex-row gap-8 lg:items-center lg:justify-between">
                <!-- Character Profile Section -->
                <div class="flex-1 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-orange-500/40 border-4 border-white/20">
                                {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 1)) }}
                            </div>
                            <div class="absolute -bottom-1 -right-1 h-6 w-6 rounded-full bg-green-500 border-2 border-white flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ $classes->count() ?? 0 }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-400 bg-amber-900/30 px-2 py-1 rounded-full">Master Mentor</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-purple-400 bg-purple-900/30 px-2 py-1 rounded-full">Level {{ $classes->count() + 1 }}</span>
                            </div>
                            <h1 class="text-3xl sm:text-4xl font-black text-white">
                                <span class="bg-gradient-to-r from-amber-400 via-orange-400 to-red-400 bg-clip-text text-transparent">{{ \Illuminate\Support\Str::title(auth()->user()->name ?? 'Game Master') }}</span>
                            </h1>
                            <p class="text-gray-300 text-sm mt-1">Legendary Quest Guide • Class Builder</p>
                        </div>
                    </div>

                    <!-- Stats Bar -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="hero-stat hero-stat--wisdom bg-gradient-to-br from-blue-900/30 to-blue-800/30 rounded-xl p-3 border border-blue-700/30">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center hero-stat-icon">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-blue-400 text-xs font-semibold">Wisdom</p>
                                    <p class="text-white font-bold">{{ $classes->count() * 127 }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="hero-stat hero-stat--power bg-gradient-to-br from-green-900/30 to-green-800/30 rounded-xl p-3 border border-green-700/30">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center hero-stat-icon">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-green-400 text-xs font-semibold">Power</p>
                                    <p class="text-white font-bold">{{ $classes->count() * 42 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons (Start New Quest and Quick Battle removed) -->
                    <div class="flex flex-wrap gap-3 hero-actions">
                        <a href="{{ route('teacher.pdf.library') }}" class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-6 py-3 rounded-xl font-semibold transform transition hover:scale-105 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            PDF Library
                        </a>
                        <button type="button" class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-6 py-3 rounded-xl font-semibold transform transition hover:scale-105 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            View Stats
                        </button>
                    </div>
                </div>

                <!-- Experience & Level Progress -->
                <div class="w-full lg:w-96">
                    <div class="mentor-card bg-gradient-to-br from-indigo-900/40 to-purple-900/40 rounded-2xl p-6 border border-indigo-700/30 backdrop-blur-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-indigo-300 text-xs font-bold uppercase tracking-wider">Mentor Level</p>
                                <p class="text-white font-black text-3xl">{{ $classes->count() + 1 }}</p>
                            </div>
                            <div class="h-14 w-14 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold shadow-lg shadow-orange-500/40 animate-pulse">
                                ⭐
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-indigo-300">Experience</span>
                                    <span class="text-white font-bold">{{ $classes->count() * 250 }} / {{ ($classes->count() + 1) * 500 }} XP</span>
                                </div>
                                <div class="mentor-progress-track h-3 bg-indigo-900/50 rounded-full overflow-hidden">
                                    <div class="mentor-progress-fill h-full bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full transition-all duration-1000 ease-out" style="width: {{ min(100, ($classes->count() * 250) / (($classes->count() + 1) * 500) * 100) }}%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-indigo-300">Quest Progress</span>
                                    <span class="text-white font-bold">{{ $classes->count() }} Active</span>
                                </div>
                                <div class="mentor-progress-track h-2 bg-indigo-900/50 rounded-full overflow-hidden">
                                    <div class="mentor-progress-fill h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full transition-all duration-1000 ease-out" style="width: {{ min(100, $classes->count() * 20) }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-indigo-700/30">
                            <p class="text-xs text-indigo-300 leading-relaxed">
                                🎯 Complete daily quests to earn bonus XP and unlock legendary mentor badges!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @php($classCollection = collect($classes ?? []))
        <!-- Gamified Class Quests Section -->
        <section class="relative quest-section">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-900/10 via-indigo-900/10 to-blue-900/10 rounded-3xl quest-overlay"></div>
            <div class="relative card p-6 sm:p-8 md:p-10 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg shadow-purple-500/40">
                                ⚔️
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-purple-400">Active Quests</p>
                                <h2 class="text-2xl sm:text-3xl font-black text-white">Your Class Adventures</h2>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm max-w-2xl">Guide your heroes through epic learning quests. Each class is a unique adventure waiting to unfold!</p>
                    </div>
                    <button type="button" class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-emerald-500/30 transform transition hover:scale-105 flex items-center gap-2" data-class-form-toggle>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create New Class
                    </button>
                </div>

                <div class="rounded-2xl border border-indigo-700/30 bg-indigo-900/20 px-5 py-4 text-sm text-gray-200 backdrop-blur-sm">
                    <div class="flex items-start gap-3">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg shadow-sky-500/30">i</div>
                        <div class="space-y-1">
                            <p class="font-bold text-white">Quick guide</p>
                            <p class="text-gray-300">Create a class, then share the <span class="font-semibold text-amber-300">Class Code</span> with students so they can join. Click <span class="font-semibold">Open Class</span> to manage lessons and view progress.</p>
                        </div>
                    </div>
                </div>

            <div id="createClassPanel" class="quest-create-panel bg-gradient-to-br from-indigo-900/30 to-purple-900/30 rounded-2xl border border-indigo-700/30 px-6 py-6 space-y-4 hidden backdrop-blur-sm" style="display: none;">
                <div class="text-center space-y-2">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold shadow-lg shadow-orange-500/40 mx-auto">
                        ✨
                    </div>
                    <h3 class="text-xl font-bold text-white">Create New Class</h3>
                    <p class="text-gray-300 text-sm">Forge a new learning adventure for your heroes!</p>
                </div>
                @if ($errors->any())
                    <div class="rounded-xl border border-red-500/30 bg-red-900/30 px-4 py-3 text-sm text-red-300">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('teacher.classes.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-indigo-300 block mb-2">Class Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-indigo-700/30 bg-indigo-900/20 text-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400" placeholder="Enter epic quest name...">
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-indigo-300 block mb-2">Schedule (optional)</label>
                        <input type="text" name="schedule" value="{{ old('schedule') }}" placeholder="Mon • Wed • Fri · 9–10 AM" class="w-full rounded-xl border border-indigo-700/30 bg-indigo-900/20 text-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400">
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-indigo-300 block mb-2">Class Label (optional)</label>
                        <input type="text" name="live_buff" value="{{ old('live_buff') }}" placeholder="Momentum Aura" class="w-full rounded-xl border border-indigo-700/30 bg-indigo-900/20 text-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400">
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-indigo-300 block mb-2">Reward Coins (optional)</label>
                        <input type="number" min="0" max="9999" name="coin_bonus" value="{{ old('coin_bonus', 0) }}" class="w-full rounded-xl border border-indigo-700/30 bg-indigo-900/20 text-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-gray-400">
                    </div>
                    <div class="sm:col-span-2 flex flex-wrap gap-3 pt-2">
                        <button type="submit" class="bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-amber-500/30 transform transition hover:scale-105 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Forge Quest
                        </button>
                        <button type="button" class="bg-white/10 backdrop-blur-sm border border-white/20 text-white px-6 py-3 rounded-xl font-semibold transform transition hover:scale-105" data-class-form-toggle>Cancel</button>
                    </div>
                </form>
            </div>

            @if($classCollection->isEmpty())
                <div class="text-center py-12">
                    <div class="h-20 w-20 rounded-full bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center text-white text-3xl mx-auto mb-4">
                        🗺️
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">No Active Quests</h3>
                    <p class="text-gray-300 text-sm max-w-md mx-auto">Your adventure awaits! Create your first quest to begin guiding heroes through their learning journey.</p>
                </div>
            @else
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($classCollection as $class)
                    <article class="group relative quest-card bg-gradient-to-br from-indigo-900/40 to-purple-900/40 rounded-2xl border border-indigo-700/30 backdrop-blur-sm overflow-hidden transform transition-all duration-300 hover:scale-105 hover:border-indigo-500/50">
                        <!-- Quest Card Header -->
                        <div class="absolute top-0 right-0 bg-gradient-to-l from-amber-500/90 to-transparent px-3 py-1 rounded-bl-xl">
                            <span class="text-amber-200 text-xs font-bold">LEVEL {{ $loop->index + 1 }}</span>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <!-- Quest Title & Info -->
                            <div class="space-y-2">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                ⚔️
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wider text-purple-300">Active Quest</p>
                                                <h3 class="text-lg font-bold text-white">{{ $class->name ?? 'Untitled Quest' }}</h3>
                                            </div>
                                        </div>
                                        <p class="text-gray-300 text-sm">{{ $class->schedule ?? 'Schedule: To be announced' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Quest Code Section -->
                            <div class="quest-card__code bg-gradient-to-r from-indigo-800/30 to-purple-800/30 rounded-xl p-4 border border-indigo-600/30">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider text-indigo-300 mb-1">Class Code</p>
                                        <p class="text-xl font-black text-amber-400">{{ strtoupper($class->join_code ?? '-----') }}</p>
                                    </div>
                                    <button type="button" class="bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-4 py-2 rounded-lg font-semibold shadow-lg shadow-amber-500/30 transform transition hover:scale-105 text-xs" data-copy-code="{{ strtoupper($class->join_code ?? '') }}">
                                        Copy Code
                                    </button>
                                </div>
                                <p class="text-xs text-gray-300 mt-2">Students will use this code to join your class.</p>
                            </div>

                            <!-- Quest Stats -->
                            <div class="flex flex-wrap gap-2 quest-card__badges">
                                <div class="bg-gradient-to-r from-emerald-900/30 to-green-900/30 px-3 py-1 rounded-lg border border-emerald-700/30">
                                    <span class="text-emerald-300 text-xs font-bold">{{ $class['live_buff'] ?? 'Power Aura' }}</span>
                                </div>
                                <div class="bg-gradient-to-r from-amber-900/30 to-orange-900/30 px-3 py-1 rounded-lg border border-amber-700/30">
                                    <span class="text-amber-300 text-xs font-bold">+{{ $class->coin_bonus ?? 0 }} Coins</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 pt-2">
                                <a href="{{ route('teacher.classes.show', $class->slug ?? \Illuminate\Support\Str::slug($class->join_code ?? 'class')) }}"
                                   class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-4 py-2 rounded-xl font-semibold shadow-lg shadow-purple-500/30 transform transition hover:scale-105 text-center text-sm">
                                    Open Class
                                </a>
                                <form action="{{ route('teacher.classes.delete', $class) }}" method="POST" class="abandon-class-form" data-class-name="{{ $class->name }}" data-class-level="{{ $class->level ?? 'Level 1 Quest' }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white px-4 py-2 rounded-xl font-semibold shadow-lg shadow-red-500/30 transform transition hover:scale-105 text-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Magical Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 via-indigo-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                    </article>
                @endforeach
            </div>
            @endif
        </section>

        <div class="flex items-center justify-between mt-8 mb-3">
            <h3 class="text-base font-semibold text-[color:var(--text-primary)]">Advanced</h3>
            <button type="button" id="toggleAdvanced" class="glass-button">Show</button>
        </div>

        <div id="advancedPanel" class="hidden" style="display: none;">
        <section>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-[color:var(--text-primary)] flex items-center gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 11a1 1 0 011-1h1.586l1.707-1.707a1 1 0 01.707-.293H11a1 1 0 011 1v1h3a3 3 0 013 3v2a1 1 0 01-1.447.894l-2.764-1.382-2.382 2.382a1 1 0 01-.707.293H5a3 3 0 01-3-3v-3z" />
                                <path d="M7 3a3 3 0 016 0v1h1a2 2 0 012 2v1a1 1 0 11-2 0V6h-2V4a1 1 0 10-2 0v1H8v7a1 1 0 11-2 0V3z" />
                            </svg>
                        </span>
                        <span data-translate="teacher-class-pulse">Class Pulse</span>
                    </h3>
                    <p class="text-sm text-[color:var(--text-muted)] mt-1" data-translate="teacher-class-pulse-desc">Live metrics that show how your classrooms are feeling right now.</p>
                </div>
                <button type="button" class="glass-button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582M20 20v-5h-.581M4 15v5h.582M20 9V4h-.581M9 4h6M9 20h6M4 9h16M4 15h16" />
                    </svg>
                    <span data-translate="teacher-generate-report">Generate Report</span>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 md:gap-5">
                <article class="card p-4 sm:p-5 md:p-6 flex flex-col gap-3 sm:gap-4">
                    <div class="flex items-center justify-between">
                        <div class="pill bg-gradient-to-r from-violet-100 to-purple-100 text-violet-700 text-[0.65rem] sm:text-xs px-2 sm:px-3 py-1 sm:py-1.5" data-translate="teacher-engagement">Engagement</div>
                        <span class="inline-flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-violet-500/10 text-violet-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 6a9 9 0 100 12 9 9 0 000-12z" />
                            </svg>
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl sm:text-4xl font-black text-[color:var(--text-primary)] leading-none" id="statActiveClasses">0</span>
                        <span class="text-xs sm:text-sm text-[color:var(--text-soft)]" data-translate="teacher-active-classes">active classes</span>
                    </div>
                    <p class="text-xs sm:text-sm text-[color:var(--text-muted)]" data-translate="teacher-engagement-desc">Classes you guided this week. Keep the energy flowing to maintain streaks.</p>
                </article>
                <article class="card p-6 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="pill bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700" data-translate="teacher-performance">Performance</div>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l-2 2m6 11V6l-2 2m6 11V6l-2 2" />
                            </svg>
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-[color:var(--text-primary)] leading-none" id="statAverageScore">0%</span>
                        <span class="text-sm text-[color:var(--text-soft)]" data-translate="teacher-class-average">class average</span>
                    </div>
                    <p class="text-sm text-[color:var(--text-muted)]" data-translate="teacher-performance-desc">Average mastery across all quests this week. Highlight moments to celebrate.</p>
                </article>
                <article class="card p-6 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="pill bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700" data-translate="teacher-attendance">Attendance</div>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-500/10 text-amber-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-12 8h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-[color:var(--text-primary)] leading-none" id="statAttendance">0%</span>
                        <span class="text-sm text-[color:var(--text-soft)]" data-translate="teacher-check-in-rate">check-in rate</span>
                    </div>
                    <p class="text-sm text-[color:var(--text-muted)]" data-translate="teacher-attendance-desc">Percent of learners who showed up and checked in. Send a pulse if this drops.</p>
                </article>
                <article class="card p-6 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="pill bg-gradient-to-r from-sky-100 to-cyan-100 text-sky-700" data-translate="teacher-focus">Focus</div>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-500/10 text-sky-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 002.828 2.828L18 9.828V7h-2.828z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 5l3 3m-1 5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-[color:var(--text-primary)] leading-none" id="statFocusStudents">0</span>
                        <span class="text-sm text-[color:var(--text-soft)]" data-translate="teacher-students-watch">students on watch</span>
                    </div>
                    <p class="text-sm text-[color:var(--text-muted)]" data-translate="teacher-focus-desc">Learners needing extra XP boosts this week. Give them spotlight moments.</p>
                </article>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-5 md:gap-6">
            <article class="card p-4 sm:p-5 md:p-7 space-y-4 sm:space-y-5 md:space-y-6 xl:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-[color:var(--text-primary)] flex items-center gap-2">
                            <span class="inline-flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500/15 to-purple-500/15 text-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                                </svg>
                            </span>
                            <span data-translate="teacher-quest-board">Quest Board</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-[color:var(--text-muted)] mt-1" data-translate="teacher-quest-board-desc">Suggested moves to keep your class story vibrant today.</p>
                    </div>
                    <span class="pill bg-gradient-to-r from-indigo-100 to-sky-100 text-indigo-700 text-[0.65rem] sm:text-xs px-2 sm:px-3 py-1 sm:py-1.5" data-translate="teacher-today">Today</span>
                </div>
                <ul id="questList" class="space-y-4"></ul>
            </article>

            <aside class="card p-4 sm:p-5 md:p-7 space-y-4 sm:space-y-5 md:space-y-6">
                <div class="flex items-start justify-between gap-2 sm:gap-3">
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-[color:var(--text-primary)] flex items-center gap-2">
                            <span class="inline-flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-amber-500/10 text-amber-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8m1.5 7.5L12 21l2.5 1.5-.5-3 2.5-2.5-3-.5L12 13l-1.5 3.5-3 .5 2.5 2.5-.5 3z" />
                                </svg>
                            </span>
                            <span data-translate="teacher-badges">Badges & Celebrations</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-[color:var(--text-muted)] mt-1" data-translate="teacher-badges-desc">The signals your learners respond to the most. Showcase them proudly.</p>
                    </div>
                </div>
                <ul id="achievementList" class="space-y-3"></ul>
                <div class="rounded-xl border border-dashed border-[color:var(--surface-border-strong)] bg-[color:var(--surface-accent)]/75 p-5 space-y-2">
                    <h4 class="text-sm font-semibold text-[color:var(--text-primary)] uppercase tracking-wide" data-translate="teacher-next-unlock">Next Unlock</h4>
                    <p class="text-sm text-[color:var(--text-muted)]" id="nextUnlock">Complete 3 focus quests to unlock the “Strategist” frame.</p>
                </div>
            </aside>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <article x-data="{ openQuestModal: false }" class="card p-7 space-y-6 lg:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-[color:var(--text-primary)] flex items-center gap-2">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[color:var(--surface-accent)] text-[color:var(--brand-primary)]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                                </svg>
                            </span>
                            <span data-translate="teacher-classrooms">Classrooms</span>
                        </h3>
                        <p class="text-sm text-[color:var(--text-muted)] mt-1" data-translate="teacher-classrooms-desc">Snapshot of ongoing Quest2Learn rooms with their current momentum.</p>
                    </div>
                    <button class="glass-button" type="button" @click="openQuestModal = true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span data-translate="teacher-launch-class-quest">Launch Class Quest</span>
                    </button>
                </div>
                <div class="space-y-4" id="classList"></div>

                <div x-show="openQuestModal" x-transition.opacity x-transition.scale
                     class="fixed inset-0 z-50 flex items-center justify-center px-4 sm:px-6"
                     style="display: none;">
                    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur" @click="openQuestModal = false"></div>
                    <div class="relative w-full max-w-2xl bg-[color:var(--card-bg)] border border-[color:var(--surface-border-strong)] rounded-3xl shadow-xl shadow-indigo-500/20 overflow-hidden">
                        <div class="flex items-start justify-between gap-3 border-b border-[color:var(--surface-border)] px-6 py-5">
                            <div>
                                <p class="uppercase text-[0.65rem] sm:text-xs font-semibold tracking-[0.18em] text-[color:var(--text-soft)]">Quest Launcher</p>
                                <h4 class="mt-2 text-xl font-bold text-[color:var(--text-primary)]">Choose a class quest to deploy</h4>
                                <p class="text-sm text-[color:var(--text-muted)] mt-1">Pick a class and a template. Rewards sync with Mentor XP and Class Coins.</p>
                            </div>
                            <button type="button" class="logout-button bg-transparent border-none shadow-none text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)]" @click="openQuestModal = false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div class="px-6 py-5 space-y-5">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold tracking-wide uppercase text-[color:var(--text-soft)]">Select class</span>
                                    <select id="questClassSelect" class="rounded-2xl border border-[color:var(--surface-border)] bg-transparent px-4 py-2.5 text-sm text-[color:var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                    </select>
                                </label>
                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold tracking-wide uppercase text-[color:var(--text-soft)]">Quest intensity</span>
                                    <div class="flex items-center gap-3 text-[0.8rem] text-[color:var(--text-muted)]">
                                        <div class="flex items-center gap-1">
                                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                            <span>Boost</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                            <span>Challenge</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="h-2 w-2 rounded-full bg-rose-400"></span>
                                            <span>Recovery</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="space-y-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[color:var(--text-soft)]">Recommended templates</p>
                                <ul id="questTemplates" class="space-y-3"></ul>
                            </div>

                            <div id="questLaunchStatus" class="hidden rounded-2xl border border-[color:var(--surface-border-strong)] bg-[color:var(--surface-accent)] px-4 py-3 text-sm font-semibold text-[color:var(--brand-primary)]">
                                Quest queued successfully.
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <aside class="card p-7 space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-[color:var(--text-primary)] flex items-center gap-2">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-500/10 text-rose-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-.638-1.277-2.055-2-3.5-2A3.5 3.5 0 005 9.5c0 4.556 7 8.5 7 8.5s7-3.944 7-8.5A3.5 3.5 0 0015.5 6c-1.445 0-2.862.723-3.5 2z" />
                            </svg>
                        </span>
                        <span data-translate="teacher-focus-learners">Focus Learners</span>
                    </h3>
                    <span class="pill bg-gradient-to-r from-rose-100 to-pink-100 text-rose-700" id="focusTag">0 flagged</span>
                </div>
                <ul id="focusLearners" class="space-y-3"></ul>
                <div>
                    <h4 class="text-sm font-semibold text-[color:var(--text-muted)] uppercase tracking-wide mb-2" data-translate="teacher-action-tip">Action Tip</h4>
                    <p class="text-sm text-[color:var(--text-muted)]" id="focusTip">
                        Schedule a 15-minute micro-conference to unlock their “Confidence” buff.
                    </p>
                </div>
            </aside>
        </section>

        <section class="card p-7 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-[color:var(--text-primary)] flex items-center gap-2">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-12 8h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-6 4h3" />
                            </svg>
                        </span>
                        <span data-translate="teacher-weekly-highlights">Weekly Highlights</span>
                    </h3>
                    <p class="text-sm text-[color:var(--text-muted)] mt-1" data-translate="teacher-weekly-highlights-desc">Keep track of the story beats unfolding across your classes this week.</p>
                </div>
                <span class="pill bg-gradient-to-r from-emerald-100 to-lime-100 text-emerald-700" data-translate="teacher-story-log">Story Log</span>
            </div>
            <ol id="timeline" class="relative space-y-6">
                <!-- timeline items -->
            </ol>
        </section>

        </div>
        <!-- Calendar Section -->
        <section class="card calendar-card">
            <div class="calendar-head">
                <h2 class="calendar-title" data-translate="calendar-title">Calendar</h2>
                <div class="month-nav">
                    <button id="prevMonth" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <div id="monthLabel" class="month-label"></div>
                    <button id="nextMonth" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5L15.75 12l-7.5 7.5" />
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleAdvanced = document.getElementById('toggleAdvanced');
            const advancedPanel = document.getElementById('advancedPanel');
            if (toggleAdvanced && advancedPanel) {
                toggleAdvanced.addEventListener('click', () => {
                    const isHidden = advancedPanel.style.display === 'none' || advancedPanel.classList.contains('hidden');
                    if (isHidden) {
                        advancedPanel.style.display = '';
                        advancedPanel.classList.remove('hidden');
                        toggleAdvanced.textContent = 'Hide';
                    } else {
                        advancedPanel.style.display = 'none';
                        advancedPanel.classList.add('hidden');
                        toggleAdvanced.textContent = 'Show';
                    }
                });
            }

            // Abandon class modal logic
            const abandonClassModal = document.getElementById('abandonClassModal');
            const abandonClassTitle = document.getElementById('abandonClassTitle');
            const abandonClassLevel = document.getElementById('abandonClassLevel');
            const cancelAbandonClassBtn = document.getElementById('cancelAbandonClass');
            const confirmAbandonClassBtn = document.getElementById('confirmAbandonClass');
            let abandonFormState = { form: null, submitting: false };

            document.querySelectorAll('.abandon-class-form').forEach(form => {
                form.addEventListener('submit', (event) => {
                    if (abandonFormState.submitting) return;
                    event.preventDefault();
                    abandonFormState.form = form;
                    abandonClassTitle.textContent = form.dataset.className || 'This class';
                    abandonClassLevel.textContent = form.dataset.classLevel || 'Questline';
                    abandonClassModal.classList.remove('hidden');
                    abandonClassModal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                });
            });

            function closeAbandonClassModal() {
                abandonClassModal.classList.add('hidden');
                abandonClassModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = 'auto';
                abandonFormState.submitting = false;
                confirmAbandonClassBtn.disabled = false;
                confirmAbandonClassBtn.innerHTML = 'Yes, abandon';
            }

            cancelAbandonClassBtn.addEventListener('click', closeAbandonClassModal);
            abandonClassModal.addEventListener('click', (event) => {
                if (event.target === abandonClassModal) {
                    closeAbandonClassModal();
                }
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !abandonClassModal.classList.contains('hidden')) {
                    closeAbandonClassModal();
                }
            });

            confirmAbandonClassBtn.addEventListener('click', () => {
                if (!abandonFormState.form || abandonFormState.submitting) return;
                abandonFormState.submitting = true;
                confirmAbandonClassBtn.disabled = true;
                confirmAbandonClassBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin inline-block mr-2"></div> Abandoning...';
                abandonFormState.form.submit();
            });

            const dashboard = {
                teacherName: "{{ auth()->user()->name ?? 'Coach' }}",
                stats: {
                    activeClasses: {{ $classCollection->count() }},
                    averageScore: 87,
                    attendance: 92,
                    focusStudents: 5,
                },
                xp: {
                    current: 3260,
                    next: 4000,
                    level: 6,
                    note: "Reach 4,000 XP to move into Level 7 and unlock the “Master Mentor” avatar glow.",
                },
                quests: [
                    {
                        title: "Launch a Reading Sprint",
                        description: "Assign the 10-minute narrative comprehension quest. Tag at least two focus learners for bonus XP.",
                        reward: "+150 XP • 3 Class Coins",
                        type: "Focus Quest",
                        tone: "rose",
                    },
                    {
                        title: "Celebrate Small Wins",
                        description: "Collect three shout-outs from classmates for peers who completed yesterday’s module.",
                        reward: "+90 XP • Unlock “High Five” reaction",
                        type: "Community Quest",
                        tone: "indigo",
                    },
                    {
                        title: "Update Skill Tracks",
                        description: "Record the latest mastery score for each learner to refresh their personal quest map.",
                        reward: "+120 XP • 1 Mentor Token",
                        type: "Mastery Quest",
                        tone: "sky",
                    },
                ],
                achievements: [
                    {
                        name: "Momentum Keeper",
                        detail: "Maintained >85% attendance for 3 consecutive weeks.",
                        tone: "emerald",
                    },
                    {
                        name: "Narrative Builder",
                        detail: "Crafted 5 story-driven feedback notes last week.",
                        tone: "amber",
                    },
                    {
                        name: "Signal Booster",
                        detail: "Scheduled three micro-conferences with focus learners.",
                        tone: "sky",
                    },
                ],
                classes: [
                    {
                        name: "Quest English 10A",
                        schedule: "Mon • Wed • Thu · 10:00 – 11:00",
                        status: "On-fire streak",
                        tone: "emerald",
                        progress: 84,
                        code: "EN10A-XP3",
                        streakDays: 9,
                        coinBonus: 35,
                        liveBuff: "Double XP Hour"
                    },
                    {
                        name: "Quest English 9B",
                        schedule: "Tue • Fri · 13:30 – 14:30",
                        status: "Needs nudges",
                        tone: "amber",
                        progress: 71,
                        code: "EN9B-QUEST",
                        streakDays: 4,
                        coinBonus: 20,
                        liveBuff: "Lucky Loot Drops"
                    },
                    {
                        name: "Quest English 8C",
                        schedule: "Mon • Wed · 14:45 – 15:45",
                        status: "Rebuilding",
                        tone: "rose",
                        progress: 63,
                        code: "EN8C-RISE",
                        streakDays: 2,
                        coinBonus: 15,
                        liveBuff: "Focus Shield"
                    },
                ],
                questTemplates: [
                    {
                        title: "Momentum Booster",
                        description: "Short reflective journaling to celebrate yesterday's wins and stack streak XP.",
                        reward: "+120 Mentor XP • +2 Class Coins",
                        duration: "15 mins",
                        intensity: "Boost",
                        tone: "emerald",
                    },
                    {
                        title: "Challenge Gauntlet",
                        description: "Assign a timed quiz remix with cooperative hints unlocked every 3 correct answers.",
                        reward: "+180 Mentor XP • Unlock “Guidance Beacon”",
                        duration: "25 mins",
                        intensity: "Challenge",
                        tone: "amber",
                    },
                    {
                        title: "Recovery Rally",
                        description: "Host a micro-conference carousel focused on learners who missed their streak yesterday.",
                        reward: "+140 Mentor XP • +1 Mentor Token",
                        duration: "20 mins",
                        intensity: "Recovery",
                        tone: "rose",
                    },
                ],
                focusLearners: [
                    {
                        name: "Santos, Althea",
                        quest: "Vocabulary Quest",
                        note: "2 of 3 check-ins complete. Ready for celebration.",
                        badge: "Celebrate",
                        tone: "emerald",
                    },
                    {
                        name: "Reyes, Julian",
                        quest: "Pronunciation Quest",
                        note: "Missed yesterday’s quest. Offer a micro-quest.",
                        badge: "Needs Attention",
                        tone: "amber",
                    },
                    {
                        name: "Tan, Miguel",
                        quest: "Reading Quest",
                        note: "On cooldown. Needs feedback to resume streak.",
                        badge: "Follow-up",
                        tone: "rose",
                    },
                ],
                timeline: [
                    {
                        title: "Focus Quest Completed",
                        time: "Today · 09:15",
                        detail: "Two learners cleared the Reading Sprint quest in 8 minutes.",
                    },
                    {
                        title: "Attendance Boost",
                        time: "Yesterday · 14:20",
                        detail: "Quest English 9B climbed from 78% to 96% check-ins after gamified reminders.",
                    },
                    {
                        title: "Achievement Unlocked",
                        time: "Monday · 11:40",
                        detail: "Unlocked “Momentum Keeper” after keeping streaks positive for all classes.",
                    },
                ],
                nextUnlock: "Complete 3 focus quests to unlock the “Strategist” frame.",
                focusTip: "Start today’s story with a quick win recap before guiding focus learners into the next quest.",
            };

            const toneClassMap = {
                emerald: 'tone-emerald',
                amber: 'tone-amber',
                rose: 'tone-rose',
                indigo: 'tone-indigo',
                sky: 'tone-sky',
            };

            // Stats
            document.getElementById('statActiveClasses').textContent = dashboard.stats.activeClasses;
            document.getElementById('statAverageScore').textContent = `${dashboard.stats.averageScore}%`;
            document.getElementById('statAttendance').textContent = `${dashboard.stats.attendance}%`;
            document.getElementById('statFocusStudents').textContent = dashboard.stats.focusStudents;

            // XP
            document.getElementById('xpCurrent').textContent = dashboard.xp.current;
            document.getElementById('xpNext').textContent = dashboard.xp.next;
            document.getElementById('xpLevel').textContent = dashboard.xp.level;
            document.getElementById('xpNote').textContent = dashboard.xp.note;
            const xpPercent = Math.min(100, Math.round((dashboard.xp.current / dashboard.xp.next) * 100));
            document.getElementById('xpBar').style.width = `${xpPercent}%`;

            // Quests
            const questList = document.getElementById('questList');
            questList.innerHTML = dashboard.quests.map(quest => {
                const badgeClass = toneClassMap[quest.tone] ?? 'tone-indigo';
                return `
                    <li class="surface-item p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <span class="pill ${badgeClass}">${quest.type}</span>
                                <h4 class="mt-3 text-base font-semibold theme-text-primary">${quest.title}</h4>
                                <p class="text-sm theme-text-muted mt-2 leading-relaxed">${quest.description}</p>
                            </div>
                            <span class="text-xs font-semibold theme-text-soft uppercase tracking-wide">${quest.reward}</span>
                        </div>
                    </li>
                `;
            }).join('');

            const questClassSelect = document.getElementById('questClassSelect');
            if (questClassSelect) {
                questClassSelect.innerHTML = dashboard.classes.map((cls, index) => `
                    <option value="${cls.name}" ${index === 0 ? 'selected' : ''}>${cls.name}</option>
                `).join('');
            }

            const questTemplates = document.getElementById('questTemplates');
            if (questTemplates) {
                questTemplates.innerHTML = dashboard.questTemplates.map(template => {
                    const badgeClass = toneClassMap[template.tone] ?? 'tone-indigo';
                    return `
                        <li class="surface-item p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <span class="pill ${badgeClass}">${template.intensity}</span>
                                    <h5 class="mt-3 text-base font-semibold theme-text-primary">${template.title}</h5>
                                    <p class="text-sm theme-text-muted mt-2 leading-relaxed">${template.description}</p>
                                </div>
                                <div class="text-right text-xs font-semibold theme-text-soft uppercase tracking-wide">
                                    <p>${template.duration}</p>
                                    <p class="mt-2">${template.reward}</p>
                                </div>
                            </div>
                            <button type="button" class="glass-button mt-4 w-full justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="ml-2">Queue this quest</span>
                            </button>
                        </li>
                    `;
                }).join('');
            }

            // Achievements
            const achievementList = document.getElementById('achievementList');
            achievementList.innerHTML = dashboard.achievements.map(item => {
                const badgeClass = toneClassMap[item.tone] ?? 'tone-indigo';
                return `
                    <li class="surface-item flex items-start gap-3 px-4 py-3">
                        <div class="pill ${badgeClass}">${item.name}</div>
                        <p class="text-sm theme-text-muted flex-1">${item.detail}</p>
                    </li>
                `;
            }).join('');

            document.getElementById('nextUnlock').textContent = dashboard.nextUnlock;

            // Classes
            const classList = document.getElementById('classList');
            classList.innerHTML = dashboard.classes.map(cls => {
                const badgeClass = toneClassMap[cls.tone] ?? 'tone-emerald';
                return `
                    <div class="surface-item p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h4 class="text-base font-semibold theme-text-primary">${cls.name}</h4>
                                <p class="text-sm theme-text-muted">${cls.schedule}</p>
                            </div>
                            <span class="pill ${badgeClass}">${cls.status}</span>
                        </div>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-dashed border-[color:var(--surface-border)] px-4 py-3">
                                <p class="text-[0.7rem] uppercase tracking-[0.25em] theme-text-soft">Join Code</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="font-mono text-lg font-semibold tracking-wide text-[color:var(--brand-primary)]">${cls.code}</span>
                                    <button type="button" class="glass-button secondary px-3 py-1 text-[0.7rem]" data-copy-code="${cls.code}">
                                        Copy
                                    </button>
                                </div>
                                <p class="text-xs theme-text-muted mt-2">Share with students so they can enter the class hub.</p>
                            </div>
                            <div class="rounded-2xl border border-[color:var(--surface-border)] px-4 py-3">
                                <p class="text-[0.7rem] uppercase tracking-[0.25em] theme-text-soft">Live Buff</p>
                                <p class="text-base font-semibold theme-text-primary mt-1">${cls.liveBuff}</p>
                                <div class="mt-3 flex items-center gap-3 text-xs font-semibold theme-text-soft uppercase tracking-wide">
                                    <span>+${cls.coinBonus} Class Coins</span>
                                    <span>${cls.streakDays}-day streak</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs font-semibold theme-text-soft">
                                <span>Quest Progress</span>
                                <span>${cls.progress}%</span>
                            </div>
                            <div class="progress-track mt-2">
                                <div class="progress-fill" style="width: ${cls.progress}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // Copy join codes
            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-copy-code]');
                if (!button) return;

                const code = button.getAttribute('data-copy-code');
                if (!code) return;

                const original = button.textContent;
                const notifyCopied = () => {
                    button.textContent = 'Copied!';
                    button.classList.add('bg-[color:var(--brand-primary)]', 'text-white');
                    setTimeout(() => {
                        button.textContent = original || 'Copy';
                        button.classList.remove('bg-[color:var(--brand-primary)]', 'text-white');
                    }, 1500);
                };

                if (navigator.clipboard?.writeText) {
                    navigator.clipboard.writeText(code).then(notifyCopied).catch(() => notifyCopied());
                } else {
                    notifyCopied();
                }
            });

            // Toggle create-class form
            const classFormPanel = document.getElementById('createClassPanel');
            const classFormToggles = document.querySelectorAll('[data-class-form-toggle]');
            
            console.log('createClassPanel found:', !!classFormPanel);
            console.log('toggle buttons found:', classFormToggles.length);
            
            const toggleClassForm = () => {
                if (!classFormPanel) {
                    console.log('createClassPanel not found');
                    return;
                }
                console.log('Toggling form panel, currently hidden:', classFormPanel.classList.contains('hidden'));
                console.log('Current display style:', classFormPanel.style.display);
                
                // Toggle both class and style
                if (classFormPanel.classList.contains('hidden')) {
                    classFormPanel.classList.remove('hidden');
                    classFormPanel.style.display = 'block';
                } else {
                    classFormPanel.classList.add('hidden');
                    classFormPanel.style.display = 'none';
                }
                
                console.log('After toggle - hidden:', classFormPanel.classList.contains('hidden'));
                console.log('After toggle - display:', classFormPanel.style.display);
                
                if (!classFormPanel.classList.contains('hidden')) {
                    // Small delay to ensure the form is visible before scrolling
                    setTimeout(() => {
                        classFormPanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        const input = classFormPanel.querySelector('input[name="name"]');
                        input?.focus();
                    }, 100);
                }
            };
            
            classFormToggles.forEach((btn, index) => {
                console.log(`Adding click listener to button ${index}`);
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('Button clicked:', e.target);
                    toggleClassForm();
                });
            });
            
            // Fallback: Make sure form is properly hidden initially
            if (classFormPanel) {
                classFormPanel.classList.add('hidden');
                classFormPanel.style.display = 'none';
            }
            @if ($errors->any())
                if (classFormPanel) {
                    classFormPanel.classList.remove('hidden');
                    classFormPanel.style.display = 'block';
                }
            @endif

            // Focus Learners
            document.getElementById('focusTag').textContent = `${dashboard.focusLearners.length} flagged`;
            const learners = document.getElementById('focusLearners');
            learners.innerHTML = dashboard.focusLearners.map(learner => {
                const badgeClass = toneClassMap[learner.tone] ?? 'tone-amber';
                return `
                    <li class="surface-item px-4 py-3">
                        <div class="flex items-baseline justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold theme-text-primary">${learner.name}</p>
                                <p class="text-xs uppercase tracking-wide theme-text-soft">${learner.quest}</p>
                            </div>
                            <span class="pill ${badgeClass}">${learner.badge}</span>
                        </div>
                        <p class="text-xs theme-text-muted mt-2">${learner.note}</p>
                    </li>
                `;
            }).join('');
            document.getElementById('focusTip').textContent = dashboard.focusTip;

            // Timeline
            const timeline = document.getElementById('timeline');
            timeline.innerHTML = dashboard.timeline.map((item, index) => `
                <li class="relative pl-8">
                    <span class="timeline-dot ${index === 0 ? 'active' : ''}"></span>
                    <h4 class="text-sm font-semibold theme-text-primary">${item.title}</h4>
                    <p class="text-xs theme-text-soft">${item.time}</p>
                    <p class="text-sm theme-text-muted mt-2">${item.detail}</p>
                    ${index < dashboard.timeline.length - 1 ? '<span class="timeline-line"></span>' : ''}
                </li>
            `).join('');
        });

        // Translation functionality
        window.changeLanguage = function(lang) {
                localStorage.setItem('selectedLanguage', lang);
                const langText = lang === 'fil' ? 'Filipino' : lang === 'bis' ? 'Bisaya' : 'English';
                document.querySelectorAll('.translation-current-lang').forEach(el => {
                    el.textContent = langText;
                });
                
                const translations = {
                    'en': {
                        'teacher-command-center': 'Teacher Command Center',
                        'teacher-level-guide': 'Level Guide',
                        'teacher-log-out': 'Log out',
                        'teacher-daily-story': 'Daily Story',
                        'teacher-welcome': 'Welcome back',
                        'teacher-welcome-desc': "Keep guiding your learners through their quest. Check today's class pulse, launch quests, and celebrate milestones — all in one shimmering view tailored for learning heroes.",
                        'teacher-continue-story': 'Continue Story Mode',
                        'teacher-continue': 'Continue',
                        'teacher-quick-launch': 'Quick Launch',
                        'teacher-launch': 'Launch',
                        'teacher-mentor-xp': 'Mentor XP',
                        'teacher-level-label': 'Level',
                        'teacher-xp-note': 'Complete today’s quests to earn bonus Mentor XP and unlock the “Growth Guide” badge.',
                        'teacher-account-menu-heading': 'Account',
                        'teacher-signed-in-as': 'Signed in as',
                        'teacher-class-pulse': 'Class Pulse',
                        'teacher-class-pulse-desc': 'Live metrics that show how your classrooms are feeling right now.',
                        'teacher-generate-report': 'Generate Report',
                        'teacher-engagement': 'Engagement',
                        'teacher-active-classes': 'active classes',
                        'teacher-engagement-desc': 'Classes you guided this week. Keep the energy flowing to maintain streaks.',
                        'teacher-performance': 'Performance',
                        'teacher-class-average': 'class average',
                        'teacher-performance-desc': 'Average mastery across all quests this week. Highlight moments to celebrate.',
                        'teacher-attendance': 'Attendance',
                        'teacher-check-in-rate': 'check-in rate',
                        'teacher-attendance-desc': 'Percent of learners who showed up and checked in. Send a pulse if this drops.',
                        'teacher-focus': 'Focus',
                        'teacher-students-watch': 'students on watch',
                        'teacher-focus-desc': 'Learners needing extra XP boosts this week. Give them spotlight moments.',
                        'teacher-quest-board': 'Quest Board',
                        'teacher-quest-board-desc': 'Suggested moves to keep your class story vibrant today.',
                        'teacher-today': 'Today',
                        'teacher-badges': 'Badges & Celebrations',
                        'teacher-badges-desc': 'The signals your learners respond to the most. Showcase them proudly.',
                        'teacher-next-unlock': 'Next Unlock',
                        'teacher-classrooms': 'Classrooms',
                        'teacher-classrooms-desc': 'Snapshot of ongoing Quest2Learn rooms with their current momentum.',
                        'teacher-launch-class-quest': 'Launch Class Quest',
                        'teacher-focus-learners': 'Focus Learners',
                        'teacher-action-tip': 'Action Tip',
                        'teacher-weekly-highlights': 'Weekly Highlights',
                        'teacher-weekly-highlights-desc': 'Keep track of the story beats unfolding across your classes this week.',
                        'teacher-story-log': 'Story Log'
                    },
                    'fil': {
                        'teacher-command-center': 'Command Center ng Guro',
                        'teacher-level-guide': 'Gabay sa Antas',
                        'teacher-log-out': 'Mag-logout',
                        'teacher-daily-story': 'Araw-araw na Kwento',
                        'teacher-welcome': 'Maligayang pagbabalik',
                        'teacher-welcome-desc': 'Patuloy na gabayan ang iyong mga mag-aaral sa kanilang quest. Tingnan ang pulso ng klase, maglunsad ng mga quest, at ipagdiwang ang mga tagumpay — lahat sa isang makintab na view para sa mga bayani sa pagkatuto.',
                        'teacher-continue-story': 'Ipagpatuloy ang Story Mode',
                        'teacher-continue': 'Ipagpatuloy',
                        'teacher-quick-launch': 'Mabilis na Paglunsad',
                        'teacher-launch': 'Ilunsad',
                        'teacher-mentor-xp': 'Mentor XP',
                        'teacher-level-label': 'Antas',
                        'teacher-xp-note': 'Kumpletuhin ang mga quest ngayong araw upang kumita ng dagdag na Mentor XP at ma-unlock ang badge na “Growth Guide”.',
                        'teacher-account-menu-heading': 'Account',
                        'teacher-signed-in-as': 'Naka-sign in bilang',
                        'teacher-class-pulse': 'Pulsong Klase',
                        'teacher-class-pulse-desc': 'Mga live na sukatan na nagpapakita kung ano ang nararamdaman ng iyong mga silid-aralan ngayon.',
                        'teacher-generate-report': 'Gumawa ng Ulat',
                        'teacher-engagement': 'Pakikilahok',
                        'teacher-active-classes': 'aktibong klase',
                        'teacher-engagement-desc': 'Mga klaseng ginabayan mo ngayong linggo. Panatilihing umaagos ang enerhiya para mapanatili ang streaks.',
                        'teacher-performance': 'Pagganap',
                        'teacher-class-average': 'karaniwang marka ng klase',
                        'teacher-performance-desc': 'Karaniwang mastery sa lahat ng quest ngayong linggo. Itampok ang mga sandaling dapat ipagdiwang.',
                        'teacher-attendance': 'Pagdalo',
                        'teacher-check-in-rate': 'antas ng pag-check in',
                        'teacher-attendance-desc': 'Porsiyento ng mga mag-aaral na dumalo at nag-check in. Magpadala ng pulse kapag bumaba ito.',
                        'teacher-focus': 'Pokos',
                        'teacher-students-watch': 'mga estudyanteng binabantayan',
                        'teacher-focus-desc': 'Mga mag-aaral na kailangan ng dagdag na XP ngayong linggo. Bigyan sila ng spotlight moments.',
                        'teacher-quest-board': 'Pisara ng Mga Quest',
                        'teacher-quest-board-desc': 'Mga mungkahing hakbang para panatilihing masigla ang kwento ng iyong klase ngayong araw.',
                        'teacher-today': 'Ngayon',
                        'teacher-badges': 'Mga Badge at Pagdiriwang',
                        'teacher-badges-desc': 'Ang mga signal na pinaka-tinatanggap ng iyong mga mag-aaral. Ipakita ang mga ito nang buong giliw.',
                        'teacher-next-unlock': 'Susunod na Pag-unlock',
                        'teacher-classrooms': 'Mga Silid-aralan',
                        'teacher-classrooms-desc': 'Snapshot ng mga Quest2Learn room na kasalukuyang may momentum.',
                        'teacher-launch-class-quest': 'Ilunsad ang Quest ng Klase',
                        'teacher-focus-learners': 'Mga Mag-aaral na Pokus',
                        'teacher-action-tip': 'Tip sa Aksyon',
                        'teacher-weekly-highlights': 'Lingguhang Highlight',
                        'teacher-weekly-highlights-desc': 'Subaybayan ang mga beat ng kwento na nagaganap sa iyong mga klase ngayong linggo.',
                        'teacher-story-log': 'Tala ng Kuwento'
                    },
                    'bis': {
                        'teacher-command-center': 'Command Center sa Magtutudlo',
                        'teacher-level-guide': 'Giyaa sa Level',
                        'teacher-log-out': 'Pag-logout',
                        'teacher-daily-story': 'Adlaw-adlaw nga Istorya',
                        'teacher-welcome': 'Maayong pagbalik',
                        'teacher-welcome-desc': 'Padayona ang paggiya sa imong mga tinun-an sa ilang quest. Tan-awa ang pulse sa klase, lansara ang mga quest, ug pagsaulog sa mga milestone — tanan sa usa ka hayag nga pagtan-aw para sa mga bayani sa pagkat-on.',
                        'teacher-continue-story': 'Padayona ang Story Mode',
                        'teacher-continue': 'Padayon',
                        'teacher-quick-launch': 'Dali nga Paglansad',
                        'teacher-launch': 'Lansar',
                        'teacher-mentor-xp': 'Mentor XP',
                        'teacher-level-label': 'Level',
                        'teacher-xp-note': 'Humana sa mga quest karong adlawa aron makadugang og Mentor XP ug ma-unlock ang “Growth Guide” nga badge.',
                        'teacher-account-menu-heading': 'Account',
                        'teacher-signed-in-as': 'Naka-sign in isip',
                        'teacher-class-pulse': 'Pulse sa Klase',
                        'teacher-class-pulse-desc': 'Mga live nga sukdanan kung giunsa ang pagbati sa imong mga silid sa klase karon.',
                        'teacher-generate-report': 'Paghimo og Report',
                        'teacher-engagement': 'Pag-apil',
                        'teacher-active-classes': 'aktibong klase',
                        'teacher-engagement-desc': 'Mga klase nga imong giya karong semana. Padayona ang kusog aron magpabilin ang mga streak.',
                        'teacher-performance': 'Pagpasundayag',
                        'teacher-class-average': 'kasagarang grado sa klase',
                        'teacher-performance-desc': 'Kasagarang mastery sa tanang quest karong semana. I-highlight ang mga higayon sa pagsaulog.',
                        'teacher-attendance': 'Pag-apil',
                        'teacher-check-in-rate': 'rate sa check-in',
                        'teacher-attendance-desc': 'Porsyento sa mga tinun-an nga misulod ug nag-check in. Pagpadala ug pulse kon mokunhod kini.',
                        'teacher-focus': 'Pokus',
                        'teacher-students-watch': 'mga estudyanteng bantayan',
                        'teacher-focus-desc': 'Mga tinun-an nga nanginahanglan og dugang nga XP karong semana. Hatagi sila og spotlight moments.',
                        'teacher-quest-board': 'Quest Board',
                        'teacher-quest-board-desc': 'Gisugyot nga mga lihok aron mapadayon ang kasadya sa istorya sa klase karong adlawa.',
                        'teacher-today': 'Karon',
                        'teacher-badges': 'Mga Badge ug Selebrasyon',
                        'teacher-badges-desc': 'Ang mga signal nga labing motubag ang imong mga tinun-an. Ipasigarbo kini.',
                        'teacher-next-unlock': 'Sunod nga Pag-unlock',
                        'teacher-classrooms': 'Mga Silid sa Klase',
                        'teacher-classrooms-desc': 'Snapshot sa nagpadayong mga Quest2Learn room uban sa ilang momentum.',
                        'teacher-launch-class-quest': 'Lansara ang Class Quest',
                        'teacher-focus-learners': 'Mga Tinun-an nga Pokus',
                        'teacher-action-tip': 'Tip sa Aksyon',
                        'teacher-weekly-highlights': 'Linggohan nga Highlights',
                        'teacher-weekly-highlights-desc': 'Subaya ang mga nahitabong beat sa imong mga klase ning semana.',
                        'teacher-story-log': 'Story Log'
                    }
                };
                
                const langData = translations[lang] || translations['en'];
                document.querySelectorAll('[data-translate]').forEach(el => {
                    const key = el.getAttribute('data-translate');
                    if (langData[key]) {
                        el.textContent = langData[key];
                    }
                });
        }

        // Load saved language on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('selectedLanguage') || 'en';
            const langText = savedLang === 'fil' ? 'Filipino' : savedLang === 'bis' ? 'Bisaya' : 'English';
            document.querySelectorAll('.translation-current-lang').forEach(el => {
                el.textContent = langText;
            });
            if (savedLang !== 'en') {
                window.changeLanguage(savedLang);
            }
        });

        // Calendar functionality
        document.addEventListener('DOMContentLoaded', () => {
            const monthLabel = document.getElementById('monthLabel');
            const calendarGrid = document.getElementById('calendarGrid');
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');

            if (!monthLabel || !calendarGrid || !prevMonthBtn || !nextMonthBtn) return;

            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            const today = new Date();
            let viewDate = new Date(today.getFullYear(), today.getMonth(), 1);

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

                // You can add assignment markers here if needed
                // const key = formatKey(dateObj);
                // if (assignmentMap[key]) {
                //     cell.classList.add('has-assignment');
                // }

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

            prevMonthBtn.addEventListener('click', () => {
                viewDate.setMonth(viewDate.getMonth() - 1);
                renderCalendar();
            });

            nextMonthBtn.addEventListener('click', () => {
                viewDate.setMonth(viewDate.getMonth() + 1);
                renderCalendar();
            });

            renderCalendar();
        });
    </script>
    
    <!-- Fallback script for form toggle -->
    <script>
        // Simple fallback for class form toggle
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Fallback script: DOM loaded');
            const toggleButtons = document.querySelectorAll('[data-class-form-toggle]');
            const formPanel = document.getElementById('createClassPanel');
            
            console.log('Fallback script: toggleButtons found:', toggleButtons.length);
            console.log('Fallback script: formPanel found:', !!formPanel);
            
            if (toggleButtons.length > 0 && formPanel) {
                toggleButtons.forEach((button, index) => {
                    console.log(`Fallback script: Adding listener to button ${index}`);
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Fallback script: Button clicked!');
                        
                        const isVisible = formPanel.style.display !== 'none' && !formPanel.classList.contains('hidden');
                        console.log('Fallback script: Form currently visible:', isVisible);
                        
                        if (!isVisible) {
                            console.log('Fallback script: Showing form');
                            formPanel.classList.remove('hidden');
                            formPanel.style.display = 'block';
                            // Scroll to form
                            setTimeout(() => {
                                formPanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                const firstInput = formPanel.querySelector('input[name="name"]');
                                if (firstInput) firstInput.focus();
                            }, 100);
                        } else {
                            console.log('Fallback script: Hiding form');
                            formPanel.classList.add('hidden');
                            formPanel.style.display = 'none';
                        }
                    });
                });
                
                // Ensure form is hidden initially
                console.log('Fallback script: Hiding form initially');
                formPanel.classList.add('hidden');
                formPanel.style.display = 'none';
            } else {
                console.error('Fallback script: Missing elements - buttons:', toggleButtons.length, 'panel:', !!formPanel);
            }
        });
    </script>
    
    <!-- Fallback script for copy functionality -->
    <script>
        // Fallback for copy spell functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Copy fallback: DOM loaded');
            
            // Handle copy button clicks
            document.addEventListener('click', function(event) {
                const button = event.target.closest('[data-copy-code]');
                if (!button) return;
                
                console.log('Copy fallback: Copy button clicked');
                const code = button.getAttribute('data-copy-code');
                console.log('Copy fallback: Code to copy:', code);
                
                if (!code) {
                    console.log('Copy fallback: No code found');
                    return;
                }
                
                const original = button.textContent;
                console.log('Copy fallback: Original text:', original);
                
                const notifyCopied = () => {
                    console.log('Copy fallback: Notifying copied');
                    button.textContent = 'Copied!';
                    button.classList.add('bg-[color:var(--brand-primary)]', 'text-white');
                    setTimeout(() => {
                        button.textContent = original || 'Copy Spell';
                        button.classList.remove('bg-[color:var(--brand-primary)]', 'text-white');
                    }, 1500);
                };
                
                // Try modern clipboard API first
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    console.log('Copy fallback: Using modern clipboard API');
                    navigator.clipboard.writeText(code).then(() => {
                        console.log('Copy fallback: Clipboard write successful');
                        notifyCopied();
                    }).catch((err) => {
                        console.log('Copy fallback: Clipboard write failed, using fallback:', err);
                        fallbackCopyTextToClipboard(code, notifyCopied);
                    });
                } else {
                    console.log('Copy fallback: Using fallback method');
                    fallbackCopyTextToClipboard(code, notifyCopied);
                }
            });
            
            // Fallback copy method
            function fallbackCopyTextToClipboard(text, callback) {
                console.log('Copy fallback: Executing fallback copy');
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                try {
                    const successful = document.execCommand('copy');
                    console.log('Copy fallback: Fallback copy successful:', successful);
                    if (successful) {
                        callback();
                    } else {
                        console.log('Copy fallback: Fallback copy failed');
                    }
                } catch (err) {
                    console.log('Copy fallback: Fallback copy error:', err);
                }
                
                document.body.removeChild(textArea);
            }
        });
    </script>
</body>
</html>

