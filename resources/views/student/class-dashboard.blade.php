<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $classroom->name }} · Quest Portal</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/student/class-dashboard.css') }}" rel="stylesheet">
</head>
<body class="pb-12">
    @php
        $classProgressData = $classProgress ?? [
            'level' => 1,
            'xp_into_level' => 0,
            'xp_for_next_level' => 100,
            'progress_percent' => 0,
            'xp_total' => 0,
            'completed_quests' => 0,
        ];
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <!-- Gamified Quest Header -->
        <header class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-900/20 via-indigo-900/20 to-blue-900/20 rounded-3xl"></div>
            <div class="relative gamified-card p-6 sm:p-8 md:p-12 flex flex-col lg:flex-row gap-8 lg:items-center lg:justify-between">
                <!-- Quest Info Section -->
                <div class="flex-1 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-purple-500/40 border-4 border-white/20">
                                ⚔️
                            </div>
                            <div class="absolute -bottom-1 -right-1 h-6 w-6 rounded-full bg-amber-500 border-2 border-white flex items-center justify-center">
                                <span class="text-white text-xs font-bold">Q</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold uppercase tracking-wider text-purple-400 bg-purple-900/30 px-2 py-1 rounded-full">Active Quest</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 bg-indigo-900/30 px-2 py-1 rounded-full">In Progress</span>
                            </div>
                            <h1 class="text-3xl sm:text-4xl font-black text-white">
                                <span class="bg-gradient-to-r from-purple-400 via-indigo-400 to-blue-400 bg-clip-text text-transparent">{{ $classroom->name }}</span>
                            </h1>
                            <p class="text-gray-300 text-sm mt-1">Epic Learning Adventure • Quest Portal</p>
                        </div>
                    </div>

                    <!-- Quest Stats Bar -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-purple-900/30 to-purple-800/30 rounded-xl p-3 border border-purple-700/30">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-purple-400 text-xs font-semibold">Difficulty</p>
                                    <p class="text-white font-bold">Epic</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-900/30 to-indigo-800/30 rounded-xl p-4 border border-indigo-700/30 space-y-3">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-full bg-indigo-500 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l2.09 6.26H20l-5 3.64 1.91 6.09L12 15.77l-4.91 2.22L9 11.9 4 8.26h5.91z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-indigo-400 text-xs font-semibold uppercase tracking-wide">Class Level</p>
                                    <p class="text-white font-black text-xl">Level {{ $classProgressData['level'] }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-indigo-200 font-semibold mb-1">Progress to next level</p>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ min(100, max(0, $classProgressData['progress_percent'])) }}%"></div>
                                </div>
                                <p class="text-xs text-gray-300 mt-1">{{ $classProgressData['xp_into_level'] }} / {{ $classProgressData['xp_for_next_level'] }} XP</p>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-900/30 to-blue-800/30 rounded-xl p-3 border border-blue-700/30">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM7 17a3 3 0 006 0H7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-blue-400 text-xs font-semibold">Quest XP Earned</p>
                                    <p class="text-white font-bold">{{ number_format($classProgressData['xp_total']) }} XP</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-900/30 to-blue-800/30 rounded-xl p-3 border border-blue-700/30">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-blue-400 text-xs font-semibold">Rewards</p>
                                    <p class="text-white font-bold">{{ auth()->user()->points ?? 0 }} Coins</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-900/30 to-emerald-800/30 rounded-xl p-3 border border-emerald-700/30">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-emerald-500 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2 10a1 1 0 011-1h3.382l1.447-4.342A1 1 0 018.79 4h2.42a1 1 0 01.96.658L13.618 9H17a1 1 0 010 2h-3.382l-1.447 4.342A1 1 0 0111.21 16H8.79a1 1 0 01-.96-.658L6.382 11H3a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-emerald-400 text-xs font-semibold">Quests Completed</p>
                                    <p class="text-white font-bold">{{ $classProgressData['completed_quests'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('student.dashboard') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transform transition hover:scale-105 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Quest Hub
                        </a>
                        <button onclick="openTeacherMessagesModal()" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-xl font-semibold transform transition hover:scale-105 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                            View Announcements
                        </button>
                        <form action="{{ route('student.classes.leave', $classroom) }}" method="POST" onsubmit="return confirm('Are you sure you want to abandon this quest?')">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="openAbandonQuestModal()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-semibold transform transition hover:scale-105 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Abandon Quest
                        </button>
                        </form>
                    </div>
                </div>

                <!-- Quest Progress & Code -->
                <div class="w-full lg:w-96">
                    <div class="bg-gradient-to-br from-purple-900/40 to-indigo-900/40 rounded-2xl p-6 border border-purple-700/30 backdrop-blur-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-purple-300 text-xs font-bold uppercase tracking-wider">Quest Code</p>
                                <p class="text-white font-black text-2xl">{{ strtoupper($classroom->join_code) }}</p>
                            </div>
                            <div class="h-14 w-14 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold shadow-lg shadow-orange-500/40 animate-pulse">
                                🗝️
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-purple-300">Quest Progress</span>
                                    <span class="text-white font-bold">65% Complete</span>
                                </div>
                                <div class="h-3 bg-purple-900/50 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-400 rounded-full transition-all duration-1000 ease-out" style="width: 65%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-purple-300">Mastery Level</span>
                                    <span class="text-white font-bold">Apprentice</span>
                                </div>
                                <div class="h-2 bg-purple-900/50 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-400 rounded-full transition-all duration-1000 ease-out" style="width: 40%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-purple-700/30">
                            <p class="text-xs text-purple-300 leading-relaxed">
                                🎯 Share this quest code with fellow heroes to expand your adventure party!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Quest Adventures & Challenges -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quest Adventures -->
            <section class="relative">
                <div class="absolute inset-0 bg-gray-900/20 rounded-2xl"></div>
                <div class="relative gamified-card p-6 space-y-4 border border-purple-700/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold">
                                ⚔️
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Quest Adventures</h3>
                                <p class="text-xs text-purple-300">{{ count($quests) }} Challenge{{ count($quests) != 1 ? 's' : '' }} Await</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            @for ($i = 0; $i < min(3, count($quests)); $i++)
                                <div class="h-2 w-2 rounded-full bg-purple-400 animate-pulse" style="animation-delay: {{ $i * 0.2 }}s"></div>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="space-y-3 max-h-[500px] overflow-y-auto scrollbar-thin scrollbar-thumb-purple-700/40 scrollbar-track-purple-900/10 pr-1">
                        @forelse ($quests as $quest)
                            <div class="bg-purple-900/20 rounded-xl p-4 border border-purple-700/30 hover:bg-purple-900/30 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/20 hover:scale-[1.02]">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="h-10 w-10 rounded-full @if($quest['type'] == 'pronunciation') bg-gradient-to-br from-blue-500 to-cyan-500 @elseif($quest['type'] == 'reading') bg-gradient-to-br from-green-500 to-emerald-500 @else bg-gradient-to-br from-orange-500 to-red-500 @endif flex items-center justify-center text-white font-bold shadow-lg">
                                            @if($quest['type'] == 'pronunciation') 🗣️ @elseif($quest['type'] == 'reading') 📖 @else ⚡ @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-white font-semibold truncate max-w-[140px]">{{ $quest['title'] }}</h4>
                                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                <span class="text-xs px-2 py-1 rounded-full bg-purple-600/30 text-purple-300 border border-purple-600/50 truncate max-w-[100px]">
                                                    @if($quest['type'] == 'pronunciation') Pronunciation Master @elseif($quest['type'] == 'reading') Reading Quest @else Lightning Challenge @endif
                                                </span>
                                                <span class="text-xs text-gray-400 truncate">{{ $quest['difficulty'] ?? 'Medium' }}</span>
                                                <span class="text-xs text-gray-400">•</span>
                                                <span class="text-xs text-gray-400 truncate">{{ $quest['estimated_time'] ?? '5 min' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right min-w-0">
                                        <div class="bg-gradient-to-r from-yellow-400 to-orange-400 text-transparent bg-clip-text text-sm font-bold truncate">+{{ $quest['reward_points'] }} XP</div>
                                        @if ($quest['is_completed'])
                                            <div class="text-xs text-green-400 mt-1 flex items-center gap-1 truncate">
                                                <span>✨</span> Conquered
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                @if ($quest['is_completed'])
                                    <div class="mt-3 pt-3 border-t border-purple-700/30">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-400">🏆 Best Score: {{ $quest['best_accuracy'] }}%</span>
                                            <span class="text-gray-400">⚔️ Attempts: {{ $quest['attempts'] }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-3 flex justify-end">
                                        <a href="{{ route('student.classes.quests.show', [$classroom->id, $quest['id']]) }}" 
                                           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold text-sm flex items-center gap-2">
                                            @if($quest['type'] == 'pronunciation') 
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                                </svg>
                                                Begin Voice Quest
                                            @elseif($quest['type'] == 'reading')
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                                Start Reading Quest
                                            @else
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                                Accept Challenge
                                            @endif
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center text-white text-2xl mx-auto mb-4 animate-bounce">
                                    🗡️
                                </div>
                                <h4 class="text-lg font-semibold text-white mb-2">No Adventures Yet</h4>
                                <p class="text-sm text-gray-400">Your Quest Master is preparing new challenges. Return soon, brave hero!</p>
                                <div class="mt-4 flex justify-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-purple-400 animate-pulse"></div>
                                    <div class="h-2 w-2 rounded-full bg-purple-400 animate-pulse" style="animation-delay: 0.2s"></div>
                                    <div class="h-2 w-2 rounded-full bg-purple-400 animate-pulse" style="animation-delay: 0.4s"></div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- Hero Progress -->
            <section class="relative">
                <div class="absolute inset-0 bg-gray-900/20 rounded-2xl"></div>
                <div class="relative gamified-card p-6 space-y-4 border border-amber-700/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-amber-600 flex items-center justify-center text-white font-bold">
                                ⚡
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Hero Progress</h3>
                                <p class="text-xs text-amber-300">Level Up Your Skills</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white font-bold">
                                    {{ auth()->user()->level ?? 1 }}
                                </div>
                                <div>
                                    <p class="text-white font-black text-2xl">{{ number_format(auth()->user()->points ?? 0) }}</p>
                                    <p class="text-gray-300 text-xs">Total Points</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-purple-300">Level Progress</span>
                                <span class="text-white font-bold">{{ auth()->user()->experience ?? 0 }} / 1000 XP</span>
                            </div>
                            <div class="h-3 bg-purple-900/50 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-400 rounded-full transition-all duration-1000 ease-out" style="width: {{ min(100, (auth()->user()->experience ?? 0) / 10) }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <div class="h-6 w-6 rounded-full bg-red-500 flex items-center justify-center">
                                <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-bold text-sm">{{ auth()->user()->streak_days ?? 0 }} days</p>
                                <p class="text-gray-300 text-xs">Daily Streak</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Treasure Vault -->
            <section class="relative">
                <div class="absolute inset-0 bg-gray-900/20 rounded-2xl"></div>
                <div class="relative gamified-card p-6 space-y-4 border border-rose-700/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-rose-600 flex items-center justify-center text-white font-bold">
                                💎
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Treasure Vault</h3>
                                <p class="text-xs text-rose-300">Unlock Rewards</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="bg-rose-900/20 rounded-xl p-3 border border-rose-700/30 hover:bg-rose-900/30 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-rose-300 text-xs font-bold">⚡ Lightning Boost</p>
                                <span class="text-rose-400 text-xs">100 Coins</span>
                            </div>
                            <p class="text-white font-semibold text-sm">2x XP for 1 hour</p>
                            <button class="mt-2 w-full bg-rose-600 hover:bg-rose-700 text-white px-3 py-2 rounded-lg font-semibold transform transition hover:scale-105 text-xs">
                                Unlock Power
                            </button>
                        </div>
                        
                        <div class="bg-rose-900/20 rounded-xl p-3 border border-rose-700/30 hover:bg-rose-900/30 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-rose-300 text-xs font-bold">🎁 Mystery Chest</p>
                                <span class="text-rose-400 text-xs">250 Coins</span>
                            </div>
                            <p class="text-white font-semibold text-sm">Random legendary reward</p>
                            <button class="mt-2 w-full bg-rose-600 hover:bg-rose-700 text-white px-3 py-2 rounded-lg font-semibold transform transition hover:scale-105 text-xs">
                                Open Chest
                            </button>
                        </div>
                        
                        <div class="bg-rose-900/20 rounded-xl p-3 border border-rose-700/30 opacity-50">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-rose-300 text-xs font-bold">👑 Crown of Legends</p>
                                <span class="text-rose-400 text-xs">1000 Coins</span>
                            </div>
                            <p class="text-white font-semibold text-sm">Exclusive items</p>
                            <button class="mt-2 w-full bg-gray-600 text-gray-400 px-3 py-2 rounded-lg font-semibold text-xs cursor-not-allowed" disabled>
                                Locked
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Announcements Dashboard Modal -->
    <div id="teacherMessagesModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);">
        <div class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-2xl border border-orange-500/30 shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-orange-500/20 bg-gradient-to-r from-orange-900/30 to-amber-900/30">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white font-bold shadow-lg">
                        �
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Announcements Dashboard</h2>
                        <p class="text-xs text-orange-300">All messages from your Quest Master</p>
                    </div>
                </div>
                <button onclick="closeTeacherMessagesModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                @if($announcements->isEmpty())
                    <div class="text-center py-12">
                        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center text-white text-3xl mx-auto mb-4">
                            📜
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">No Announcements</h3>
                        <p class="text-gray-300 text-sm">The Quest Master hasn't sent any messages yet.</p>
                    </div>
                @else
                    @foreach($announcements as $announcement)
                        <div class="bg-gray-800/50 border border-orange-500/20 rounded-xl p-5 hover:bg-gray-800/70 transition-colors">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white font-bold text-sm shadow-lg flex-shrink-0 mt-0.5">
                                        👑
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold uppercase tracking-wider text-orange-300 mb-1 leading-tight">Quest Master</p>
                                        <h3 class="text-lg font-bold text-white break-words leading-tight">{{ $announcement->title }}</h3>
                                    </div>
                                </div>
                                <span class="pill tone-orange text-xs whitespace-nowrap flex-shrink-0 ml-2 mt-1">
                                    {{ $announcement->sent_at?->diffForHumans() ?? $announcement->created_at->diffForHumans() }}
                                </span>
                            </div>
                            @if ($announcement->message ?? $announcement->body)
                                <div class="mb-4 pl-0">
                                    <div class="bg-orange-900/20 rounded-xl p-4 border border-orange-500/30">
                                        <p class="text-orange-100 text-sm leading-relaxed break-words text-left whitespace-pre-wrap word-wrap overflow-wrap break-words" style="text-align: left; word-break: break-word;">{{ $announcement->message ?? $announcement->body }}</p>
                                    </div>
                                </div>
                            @endif
                            <div class="pt-4 border-t border-orange-500/10">
                                <p class="text-xs text-gray-400 text-left leading-normal">
                                    <span class="text-orange-400 font-semibold">Posted:</span> 
                                    <span class="ml-1">{{ $announcement->created_at->format('F j, Y \a\t g:i A') }}</span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    
    <!-- Abandon Quest Modal -->
    <div id="abandonQuestModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);">
        <div class="relative bg-gray-900 rounded-2xl border border-red-500/30 shadow-2xl max-w-md w-full overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-red-900/30 to-orange-900/30 p-6 border-b border-red-500/20">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-full bg-red-600 flex items-center justify-center text-white font-bold shadow-lg shadow-red-500/40">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Abandon Quest</h3>
                        <p class="text-red-300 text-sm">This action cannot be undone</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <div class="bg-red-900/20 rounded-xl p-4 border border-red-500/30">
                    <div class="flex items-start gap-3">
                        <div class="h-6 w-6 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-white font-semibold">Warning: Quest Abandonment</h4>
                            <p class="text-gray-300 text-sm leading-relaxed">
                                Are you sure you want to abandon this quest? All progress will be lost and you'll need to start over if you rejoin.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-gray-800/50 rounded-lg border border-gray-700/50">
                        <div class="h-8 w-8 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold">
                            ⚔️
                        </div>
                        <div class="flex-1">
                            <p class="text-white font-semibold">{{ $classroom->name }}</p>
                            <p class="text-gray-400 text-xs">Current Quest</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="bg-gray-800/50 rounded-lg p-3 border border-gray-700/50">
                            <p class="text-amber-400 font-bold text-lg">{{ number_format($classProgressData['xp_total']) }}</p>
                            <p class="text-gray-400 text-xs">XP Earned</p>
                        </div>
                        <div class="bg-gray-800/50 rounded-lg p-3 border border-gray-700/50">
                            <p class="text-green-400 font-bold text-lg">{{ $classProgressData['completed_quests'] }}</p>
                            <p class="text-gray-400 text-xs">Quests Done</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-700/50 bg-gray-800/50">
                <div class="flex gap-3">
                    <button onclick="closeAbandonQuestModal()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white px-4 py-3 rounded-xl font-semibold transition-colors">
                        Cancel
                    </button>
                    <form action="{{ route('student.classes.leave', $classroom) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Abandon Quest
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Pass Laravel variables to JavaScript
    window.classroomId = {{ $classroom->id }};
</script>
<script src="{{ asset('js/student/class-dashboard.js') }}" defer></script>
</body>
</html>

