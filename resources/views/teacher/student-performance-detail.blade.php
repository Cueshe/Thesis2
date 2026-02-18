<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $student->name }} · Performance Details</title>
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
            <div class="flex items-center space-x-4">
                <a href="{{ route('teacher.performance.analytics', $classroom->slug) }}" 
                   class="inline-flex items-center text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)]">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Analytics
                </a>
                <div class="h-6 w-px bg-[color:var(--surface-border)]"></div>
                <div>
                    <p class="text-xs font-semibold tracking-[0.4em] text-[color:var(--text-soft)] uppercase">Student Performance</p>
                    <h1 class="text-3xl sm:text-4xl font-black">{{ $student->name }}</h1>
                    <p class="text-sm text-[color:var(--text-muted)]">{{ $classroom->name }} - Performance Details</p>
                </div>
            </div>
            <div class="flex items-center gap-3 sm:gap-4">
                <x-translation-toggle class="hidden sm:flex" />
                <x-theme-toggle />
            </div>
        </header>

        <!-- Overview Cards -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <span class="text-white text-xl">📊</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[color:var(--text-soft)] uppercase">Average Accuracy</p>
                        <p class="text-2xl font-black">{{ number_format($overallPerformance['average_accuracy'], 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                        <span class="text-white text-xl">⚡</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[color:var(--text-soft)] uppercase">Total Activities</p>
                        <p class="text-2xl font-black">{{ $overallPerformance['total_activities'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center">
                        <span class="text-white text-xl">📈</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[color:var(--text-soft)] uppercase">Improvement Rate</p>
                        <p class="text-2xl font-black">{{ number_format($overallPerformance['improvement_rate'], 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                        <span class="text-white text-xl">⏱️</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[color:var(--text-soft)] uppercase">Time Spent</p>
                        <p class="text-2xl font-black">{{ $overallPerformance['total_time_spent'] }}m</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Performance History -->
            <div class="lg:col-span-2">
                <section class="card p-6">
                    <h3 class="text-xl font-black mb-4">📜 Performance History</h3>
                    
                    @if($performanceHistory->count() > 0)
                        <div class="space-y-4">
                            @foreach($performanceHistory as $performance)
                                <div class="card p-4 border border-[color:var(--surface-border)]">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h4 class="font-semibold">{{ $performance['quest_title'] }}</h4>
                                            <p class="text-xs text-[color:var(--text-soft)]">{{ $performance['completed_at'] }} • {{ $performance['time_spent_minutes'] }} min</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="pill {{ $performance['accuracy_percentage'] >= 80 ? 'tone-green' : 
                                                           ($performance['accuracy_percentage'] >= 60 ? 'tone-yellow' : 'tone-red') }} text-xs">
                                                {{ number_format($performance['accuracy_percentage'], 1) }}%
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-[color:var(--text-soft)]">Score</p>
                                            <p class="font-bold">{{ $performance['total_score'] }}/{{ $performance['max_score'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[color:var(--text-soft)]">Type</p>
                                            <p class="font-bold">{{ ucfirst($performance['activity_type']) }}</p>
                                        </div>
                                        @if($performance['pronunciation_accuracy'])
                                            <div>
                                                <p class="text-[color:var(--text-soft)]">Pronunciation</p>
                                                <p class="font-bold">{{ number_format($performance['pronunciation_accuracy'], 1) }}%</p>
                                            </div>
                                        @endif
                                        @if($performance['reading_comprehension'])
                                            <div>
                                                <p class="text-[color:var(--text-soft)]">Reading</p>
                                                <p class="font-bold">{{ number_format($performance['reading_comprehension'], 1) }}%</p>
                                            </div>
                                        @endif
                                        @if($performance['improvement_rate'] != 0)
                                            <div>
                                                <p class="text-[color:var(--text-soft)]">Improvement</p>
                                                <p class="font-bold {{ $performance['improvement_rate'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $performance['improvement_rate'] > 0 ? '+' : '' }}{{ number_format($performance['improvement_rate'], 1) }}%
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-[color:var(--text-muted)]">No performance data available yet.</p>
                        </div>
                    @endif
                </section>
            </div>

            <!-- Side Panels -->
            <div class="space-y-6">
                <!-- Skill Analytics -->
                <section class="card p-6">
                    <h3 class="text-xl font-black mb-4">🎯 Skill Analytics</h3>
                    
                    <div class="space-y-4">
                        @foreach($skillAnalytics as $skill => $data)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold">{{ ucfirst($skill) }}</span>
                                    <div class="flex items-center space-x-2">
                                        @switch($data['trend'])
                                            @case('improving')
                                                <span class="pill tone-green text-xs">↑</span>
                                                @break
                                            @case('declining')
                                                <span class="pill tone-red text-xs">↓</span>
                                                @break
                                            @default
                                                <span class="pill tone-gray text-xs">→</span>
                                        @endswitch
                                        <span class="text-xs text-[color:var(--text-soft)]">{{ $data['total_activities'] }} activities</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $data['average'] }}%"></div>
                                </div>
                                <p class="text-xs text-[color:var(--text-soft)] mt-1">{{ number_format($data['average'], 1) }}% average</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- Performance Summary -->
                <section class="card p-6">
                    <h3 class="text-xl font-black mb-4">📊 Summary</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-[color:var(--text-soft)]">Strongest Area</span>
                            <span class="text-sm font-bold">{{ $overallPerformance['strongest_area'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-[color:var(--text-soft)]">Area for Improvement</span>
                            <span class="text-sm font-bold">{{ $overallPerformance['weakest_area'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-[color:var(--text-soft)]">Recent Trend</span>
                            <span class="text-sm font-bold">
                                @switch($overallPerformance['recent_trend'])
                                    @case('improving')
                                        <span class="text-green-600">Improving</span>
                                        @break
                                    @case('declining')
                                        <span class="text-red-600">Declining</span>
                                        @break
                                    @default
                                        <span class="text-gray-600">Stable</span>
                                @endswitch
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-[color:var(--text-soft)]">Last Activity</span>
                            <span class="text-sm font-bold">
                                {{ $student->last_activity_date ? $student->last_activity_date->format('M d, Y') : 'No activity' }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Recommendations -->
                <div class="card p-6 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Recommendations</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                @if($overallPerformance['recent_trend'] === 'declining')
                                    <p>Student performance has been declining. Consider providing additional support and practice exercises.</p>
                                @elseif($overallPerformance['recent_trend'] === 'improving')
                                    <p>Great progress! Keep encouraging the student with challenging activities.</p>
                                @else
                                    <p>Student performance is stable. Consider introducing varied activities to maintain engagement.</p>
                                @endif
                                
                                @if($overallPerformance['weakest_area'] !== 'N/A')
                                    <p class="mt-2">Focus on <strong>{{ $overallPerformance['weakest_area'] }}</strong> exercises to improve overall performance.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
