<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Performance Analytics · {{ $classroom->name }}</title>
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
            --brand-secondary: #22d3ee;
            --page-bg: linear-gradient(160deg, #e0e7ff 0%, #eef2ff 45%, #e0f2fe 100%);
            --card-bg: rgba(255, 255, 255, 0.98);
            --surface-border: rgba(99, 102, 241, 0.2);
            --surface-border-strong: rgba(99, 102, 241, 0.4);
            --shadow-soft: 0 35px 80px -45px rgba(30, 58, 138, 0.55);
            --text-primary: #111827;
            --text-muted: #6b7280;
            --text-soft: #94a3b8;
        }

        .dark {
            color-scheme: dark;
            --brand-primary: #818cf8;
            --brand-secondary: #67e8f9;
            --page-bg: linear-gradient(180deg, #0f172a 0%, #111827 50%, #020617 100%);
            --card-bg: rgba(15, 23, 42, 0.92);
            --surface-border: rgba(129, 140, 248, 0.35);
            --surface-border-strong: rgba(129, 140, 248, 0.5);
            --shadow-soft: 0 35px 80px -45px rgba(2, 6, 23, 0.9);
            --text-primary: #e2e8f0;
            --text-muted: #cbd5f5;
            --text-soft: #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: var(--page-bg);
            color: var(--text-primary);
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--surface-border);
            border-radius: 1rem;
            box-shadow: var(--shadow-soft);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .pill.tone-indigo {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
        }

        .pill.tone-indigo:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .pill.tone-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .pill.tone-green:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .pill.tone-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .pill.tone-yellow {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .pill.tone-gray {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }
    </style>
</head>
<body class="min-h-screen pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-6 sm:py-8 space-y-6 sm:space-y-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div class="space-y-2">
                <p class="text-xs font-semibold tracking-[0.4em] text-[color:var(--text-soft)] uppercase">Performance Analytics</p>
                <h1 class="text-3xl sm:text-4xl font-black">{{ $classroom->name }}</h1>
                <p class="text-sm text-[color:var(--text-muted)]">Student Performance Overview</p>
            </div>
            <div class="flex items-center gap-3 sm:gap-4">
                <x-translation-toggle class="hidden sm:flex" />
                <x-theme-toggle />
                <a href="{{ route('teacher.classes.show', $classroom->slug) }}" class="pill tone-green text-xs">📊 Back to Class</a>
            </div>
        </header>

        <!-- Summary Cards -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <span class="text-white text-xl">👥</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[color:var(--text-soft)] uppercase">Total Students</p>
                        <p class="text-2xl font-black">{{ $performanceSummary['total_students'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                        <span class="text-white text-xl">⚡</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[color:var(--text-soft)] uppercase">Active Students</p>
                        <p class="text-2xl font-black">{{ $performanceSummary['active_students'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center">
                        <span class="text-white text-xl">📊</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[color:var(--text-soft)] uppercase">Class Average</p>
                        <p class="text-2xl font-black">{{ number_format($performanceSummary['class_average'], 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                        <span class="text-white text-xl">📈</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[color:var(--text-soft)] uppercase">Trend</p>
                        <p class="text-2xl font-black">
                            @switch($performanceTrends['overall_trend'])
                                @case('improving')
                                    <span class="text-green-600">↑</span>
                                    @break
                                @case('declining')
                                    <span class="text-red-600">↓</span>
                                    @break
                                @default
                                    <span class="text-gray-600">→</span>
                            @endswitch
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Export Button -->
        <div class="flex justify-end mb-6">
            <form action="{{ route('teacher.performance.export', $classroom->slug) }}" method="GET">
                <button type="submit" class="pill tone-indigo text-xs">
                    📤 Export Data
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Student Performance List -->
            <div class="lg:col-span-2">
                <section class="card p-6">
                    <h3 class="text-xl font-black mb-4">Student Performance</h3>
                    
                    @forelse($students as $student)
                        <div class="card p-4 mb-4 border border-[color:var(--surface-border)]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                        {{ $student['avatar'] }}
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $student['name'] }}</p>
                                        <p class="text-xs text-[color:var(--text-soft)]">Level {{ $student['current_level'] }} • {{ $student['points'] }} pts</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <p class="font-bold">{{ number_format($student['performance']['average_accuracy'], 1) }}%</p>
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $student['performance']['average_accuracy']) }}%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        @switch($student['performance']['recent_trend'])
                                            @case('improving')
                                                <span class="pill tone-green text-xs">↑</span>
                                                @break
                                            @case('declining')
                                                <span class="pill tone-red text-xs">↓</span>
                                                @break
                                            @default
                                                <span class="pill tone-gray text-xs">→</span>
                                        @endswitch
                                    </div>
                                    <a href="{{ route('teacher.student.performance', [$classroom->slug, $student['student_id']]) }}" 
                                       class="pill tone-indigo text-xs">View</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-[color:var(--text-muted)]">No student performance data available yet.</p>
                        </div>
                    @endforelse
                </section>
            </div>

            <!-- Side Panels -->
            <div class="space-y-6">
                <!-- Top Performers -->
                <section class="card p-6">
                    <h3 class="text-xl font-black mb-4">🏆 Top Performers</h3>
                    <div class="space-y-3">
                        @forelse($performanceSummary['top_performers'] as $performer)
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg border border-yellow-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($performer['student_name'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm">{{ $performer['student_name'] }}</p>
                                        <p class="text-xs text-[color:var(--text-soft)]">{{ $performer['activities_count'] }} activities</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-sm">{{ number_format($performer['average'], 1) }}%</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-[color:var(--text-muted)]">No performance data available</p>
                        @endforelse
                    </div>
                </section>

                <!-- Students Needing Help -->
                <section class="card p-6">
                    <h3 class="text-xl font-black mb-4">⚠️ Needs Support</h3>
                    <div class="space-y-3">
                        @forelse($performanceSummary['students_needing_help'] as $student)
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-400 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($student['student_name'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm">{{ $student['student_name'] }}</p>
                                        <p class="text-xs text-[color:var(--text-soft)]">{{ $student['activities_count'] }} activities</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-sm">{{ number_format($student['average'], 1) }}%</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-[color:var(--text-muted)]">All students are performing well!</p>
                        @endforelse
                    </div>
                </section>

                <!-- Activity Breakdown -->
                <section class="card p-6">
                    <h3 class="text-xl font-black mb-4">📊 Activity Types</h3>
                    <div class="space-y-3">
                        @foreach($performanceSummary['activity_breakdown'] as $type => $data)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-semibold">{{ ucfirst($type) }}</span>
                                    <span class="text-[color:var(--text-soft)]">{{ $data['count'] }} activities</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $data['average_accuracy']) }}%"></div>
                                </div>
                                <p class="text-xs text-[color:var(--text-soft)] mt-1">{{ number_format($data['average_accuracy'], 1) }}% avg accuracy</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
