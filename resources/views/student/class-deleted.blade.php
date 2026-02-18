<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Class Deleted - Quest2Learn</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/student/class-deleted.css') }}" rel="stylesheet">
</head>
<body class="min-h-screen relative">
    <nav class="relative bg-[color:var(--card-bg)] border-b border-[color:var(--surface-border)] shadow-[0_20px_40px_-34px_rgba(15,23,42,0.35)]">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
            <div class="h-14 sm:h-16 flex items-center justify-between">
                <div class="flex items-center gap-2 sm:gap-3">
                    <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-9 sm:h-11 w-auto rounded-xl sm:rounded-2xl shadow-lg shadow-indigo-500/40">
                    <div>
                        <p class="text-[0.65rem] sm:text-xs uppercase tracking-widest text-[color:var(--text-soft)]">Quest2Learn</p>
                        <h1 class="text-sm sm:text-base md:text-lg font-semibold text-[color:var(--text-primary)]">Student Portal</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <x-translation-toggle class="hidden sm:flex" />
                    <x-theme-toggle class="hidden sm:flex" />
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs sm:text-sm font-medium text-[color:var(--text-primary)]">{{ \Illuminate\Support\Str::title(auth()->user()->name ?? 'Student') }}</span>
                        <span class="text-[0.65rem] sm:text-xs text-[color:var(--text-muted)]">Level {{ auth()->user()->level ?? 1 }} Hero</span>
                    </div>
                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button type="button" @click="open = !open" @click.outside="open = false"
                                class="flex items-center justify-center rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                                aria-haspopup="true" :aria-expanded="open ? 'true' : 'false'">
                            <span class="sr-only">Open user menu</span>
                            <div class="h-9 w-9 sm:h-11 sm:w-11 rounded-full bg-gradient-to-br from-emerald-200 to-teal-200 text-emerald-600 flex items-center justify-center font-semibold shadow-inner text-sm sm:text-base">
                                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
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
                                <p class="text-[0.65rem] font-semibold uppercase tracking-widest text-[color:var(--text-soft)]">Account</p>
                                <p class="mt-1 text-sm font-semibold text-[color:var(--text-primary)]">{{ \Illuminate\Support\Str::title(auth()->user()->name ?? 'Student') }}</p>
                                <p class="mt-1 text-xs text-[color:var(--text-muted)]">
                                    <span>Signed in as</span>
                                    <span class="ml-1 font-medium text-[color:var(--text-primary)]">{{ auth()->user()->email ?? 'student@example.com' }}</span>
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
                                        <span>Log out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="relative max-w-4xl mx-auto px-3 sm:px-4 lg:px-8 py-6 sm:py-8 md:py-12">
        <div class="min-h-[60vh] flex items-center justify-center">
            <div class="text-center space-y-8 max-w-2xl mx-auto">
                <!-- Warning Icon -->
                <div class="relative">
                    <div class="h-32 w-32 rounded-full bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 flex items-center justify-center mx-auto floating-animation">
                        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white text-4xl shadow-lg shadow-orange-500/40">
                            ⚠️
                        </div>
                    </div>
                    <div class="absolute -top-2 -right-2 h-8 w-8 rounded-full bg-red-500 flex items-center justify-center text-white text-sm font-bold pulse-animation">
                        !
                    </div>
                </div>

                <!-- Warning Message -->
                <div class="space-y-4">
                    <h1 class="text-3xl sm:text-4xl font-black text-[color:var(--text-primary)]">
                        Class Not Available
                    </h1>
                    
                    <div class="space-y-2">
                        <p class="text-lg font-semibold text-[color:var(--text-muted)]">
                            "{{ $className }}"
                        </p>
                        <p class="text-[color:var(--text-soft)] leading-relaxed">
                            {{ $message }}
                        </p>
                    </div>

                    <!-- Additional Info -->
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-2xl p-6 border border-amber-200 dark:border-amber-800/30">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-full bg-amber-500 flex items-center justify-center text-white">
                                📚
                            </div>
                            <h3 class="text-lg font-semibold text-[color:var(--text-primary)]">What happened?</h3>
                        </div>
                        <p class="text-[color:var(--text-muted)] text-sm leading-relaxed">
                            This class has been deleted by the teacher or is no longer available. Don't worry - your progress and achievements are safe! You can join other classes or explore new learning adventures.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('student.dashboard') }}" class="glass-button px-8 py-4 text-base font-semibold shadow-lg shadow-indigo-500/30 transform transition hover:scale-105 flex items-center gap-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Back to Dashboard
                    </a>
                    
                    <button onclick="history.back()" class="glass-button secondary px-8 py-4 text-base font-semibold transform transition hover:scale-105 flex items-center gap-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Go Back
                    </button>
                </div>

                <!-- Helpful Tips -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">
                    <div class="text-center p-4 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800/30">
                        <div class="text-2xl mb-2">🎯</div>
                        <h4 class="font-semibold text-[color:var(--text-primary)] text-sm mb-1">Join New Classes</h4>
                        <p class="text-xs text-[color:var(--text-muted)]">Ask your teacher for new class codes</p>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800/30">
                        <div class="text-2xl mb-2">🏆</div>
                        <h4 class="font-semibold text-[color:var(--text-primary)] text-sm mb-1">Check Progress</h4>
                        <p class="text-xs text-[color:var(--text-muted)]">View your achievements on the dashboard</p>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border border-purple-200 dark:border-purple-800/30">
                        <div class="text-2xl mb-2">🌟</div>
                        <h4 class="font-semibold text-[color:var(--text-primary)] text-sm mb-1">Keep Learning</h4>
                        <p class="text-xs text-[color:var(--text-muted)]">Explore other available activities</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
