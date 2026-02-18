<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q2L - Quest to Learn</title>
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>
<body class="antialiased overflow-x-hidden">
    <!-- Navigation -->
    <nav class="nav-surface fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-10 w-auto">
                    <span class="text-xl font-semibold" style="color: #000000;">Q2L</span>
                </div>
                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="#home" class="nav-link" data-translate="nav-home">Home</a>
                    <a href="#quest" class="nav-link" data-translate="nav-quest">Quest Path</a>
                    <a href="#missions" class="nav-link" data-translate="nav-missions">Live Missions</a>
                    <a href="#rewards" class="nav-link" data-translate="nav-rewards">Rewards</a>
                    <a href="#community" class="nav-link" data-translate="nav-community">Community</a>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 bg-white/80 text-sm font-medium transition hover:bg-white/90" style="color: var(--text-dark);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                            <span id="currentLang">English</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-lg"
                             style="display: none;">
                            <div class="py-1">
                                <button @click="document.getElementById('currentLang').textContent = 'English'; open = false; changeLanguage('en')"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-sm transition hover:bg-gray-50" style="color: var(--text-dark);">
                                    English
                                </button>
                                <button @click="document.getElementById('currentLang').textContent = 'Filipino'; open = false; changeLanguage('fil')"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-sm transition hover:bg-gray-50" style="color: var(--text-dark);">
                                    Filipino
                                </button>
                                <button @click="document.getElementById('currentLang').textContent = 'Bisaya'; open = false; changeLanguage('bis')"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-sm transition hover:bg-gray-50" style="color: var(--text-dark);">
                                    Bisaya
                                </button>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold transition text-white btn-primary" data-translate="btn-login">Login</a>
                    <button id="mobileMenuBtn" class="md:hidden" style="color: var(--text-dark);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
            <div id="mobileMenu" class="hidden md:hidden pb-6 space-y-3">
                <a href="#home" class="block px-4 py-2 rounded-lg text-sm font-medium transition bg-white/80 text-gray-700 hover:bg-white/90" data-translate="nav-home">Home</a>
                <a href="#quest" class="block px-4 py-2 rounded-lg text-sm font-medium transition bg-white/80 text-gray-700 hover:bg-white/90" data-translate="nav-quest">Quest Path</a>
                <a href="#missions" class="block px-4 py-2 rounded-lg text-sm font-medium transition bg-white/80 text-gray-700 hover:bg-white/90" data-translate="nav-missions">Live Missions</a>
                <a href="#rewards" class="block px-4 py-2 rounded-lg text-sm font-medium transition bg-white/80 text-gray-700 hover:bg-white/90" data-translate="nav-rewards">Rewards</a>
                <a href="#community" class="block px-4 py-2 rounded-lg text-sm font-medium transition bg-white/80 text-gray-700 hover:bg-white/90" data-translate="nav-community">Community</a>
                <div class="pt-3 border-t" style="border-color: rgba(0, 0, 0, 0.1);">
                    <div class="relative mt-3" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2 text-sm font-medium rounded-lg transition bg-white/80 text-gray-700 hover:bg-white/90">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                </svg>
                                <span id="currentLangMobile">English</span>
                            </div>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="mt-2 space-y-1"
                             style="display: none;">
                            <button @click="document.getElementById('currentLangMobile').textContent = 'English'; document.getElementById('currentLang').textContent = 'English'; open = false; changeLanguage('en')"
                                    class="block w-full text-left px-4 py-2 text-sm transition hover:bg-gray-50" style="color: var(--text-dark);">English</button>
                            <button @click="document.getElementById('currentLangMobile').textContent = 'Filipino'; document.getElementById('currentLang').textContent = 'Filipino'; open = false; changeLanguage('fil')"
                                    class="block w-full text-left px-4 py-2 text-sm transition hover:bg-gray-50" style="color: var(--text-dark);">Filipino</button>
                            <button @click="document.getElementById('currentLangMobile').textContent = 'Bisaya'; document.getElementById('currentLang').textContent = 'Bisaya'; open = false; changeLanguage('bis')"
                                    class="block w-full text-left px-4 py-2 text-sm transition hover:bg-gray-50" style="color: var(--text-dark);">Bisaya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="section-padding relative z-10">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-8 relative fade-in-up">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold tracking-wide uppercase border" style="border-color: var(--primary-blue); background: rgba(59, 130, 246, 0.1); color: var(--primary-blue);" data-translate="hero-badge">AI-Powered Learning</span>
                <h1 class="text-5xl sm:text-6xl xl:text-7xl font-extrabold leading-tight">
                    <span class="block" style="color: var(--text-dark);" data-translate="hero-title-1">Quest2Learn</span>
                    <span class="block gradient-text" data-translate="hero-title-2">AI Gamified Tutoring</span>
                    <span class="block" style="color: var(--text-dark);" data-translate="hero-title-3">for Indigenous People</span>
                </h1>
                <p class="text-lg sm:text-xl max-w-xl leading-relaxed" style="color: var(--text-medium);" data-translate="hero-subtitle">
                    Experience personalized AI tutoring through gamified learning adventures. Master reading, writing, and language skills with intelligent feedback designed specifically for indigenous learners.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('login', ['role' => 'student', 'tab' => 'signup']) }}" class="px-8 py-4 rounded-xl font-semibold text-base text-white btn-primary" data-translate="hero-cta-primary">Start Learning Now</a>
                    <a href="#quest" class="px-8 py-4 rounded-xl font-semibold text-base btn-secondary" data-translate="hero-cta-secondary">Explore Features</a>
                </div>
                <div class="grid grid-cols-3 gap-4 pt-4">
                    <div class="glass-card stat-card p-5 text-center">
                        <p class="text-3xl sm:text-4xl font-bold mb-1" style="color: var(--primary-blue);">{{ $activeStudents ?? 0 }}</p>
                        <p class="text-xs" style="color: var(--text-light);" data-translate="hero-stat-quests-label">Active Students</p>
                    </div>
                    <div class="glass-card stat-card p-5 text-center">
                        <p class="text-3xl sm:text-4xl font-bold mb-1" style="color: var(--primary-blue);">AI</p>
                        <p class="text-xs" style="color: var(--text-light);" data-translate="hero-stat-xp-label">Powered Tutoring</p>
                    </div>
                    <div class="glass-card stat-card p-5 text-center">
                        <p class="text-3xl sm:text-4xl font-bold mb-1" style="color: var(--primary-blue);">100%</p>
                        <p class="text-xs" style="color: var(--text-light);" data-translate="hero-stat-speed-label">Gamified Learning</p>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-6 relative z-10">
                <div class="space-y-6 fade-in-up" style="animation-delay: 0.2s;">
                    <div class="glass-card p-8 relative overflow-hidden">
                        <div class="absolute inset-0 pointer-events-none" style="background: rgba(59, 130, 246, 0.05);"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.3em] mb-1" style="color: var(--text-light);">Learning Dashboard</p>
                                    <h3 class="text-2xl font-bold" style="color: var(--text-dark);">AI Tutoring Session</h3>
                                </div>
                                <span class="px-4 py-1.5 rounded-full text-xs font-semibold border" style="background: rgba(59, 130, 246, 0.1); border-color: var(--primary-blue); color: var(--primary-blue);">Level 5</span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-sm mb-6">
                                <div>
                                    <p class="mb-1" style="color: var(--text-light);">Current Lesson</p>
                                    <p class="font-semibold" style="color: var(--text-dark);">Reading Comprehension</p>
                                </div>
                                <div>
                                    <p class="mb-1" style="color: var(--text-light);">Study Streak</p>
                                    <p class="font-semibold" style="color: var(--text-dark);">7 days 🔥</p>
                                </div>
                                <div>
                                    <p class="mb-1" style="color: var(--text-light);">AI Assistant</p>
                                    <p class="font-semibold" style="color: var(--text-dark);">Online ✨</p>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs uppercase tracking-widest" style="color: var(--text-light);">Learning Progress</p>
                                    <p class="text-xs font-semibold" style="color: var(--primary-blue);">68%</p>
                                </div>
                                <div class="h-2.5 w-full rounded-full overflow-hidden" style="background: #e2e8f0;">
                                    <div class="h-full rounded-full transition-all duration-500" style="width: 68%; background: var(--primary-blue);"></div>
                                </div>
                                <p class="mt-2 text-xs" style="color: var(--text-light);">Module 3 of 5</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="glass-card p-6 hover:scale-[1.02]">
                            <p class="text-xs uppercase tracking-widest mb-2" style="color: var(--text-light);">Daily Challenge</p>
                            <h4 class="text-lg font-semibold mb-2" style="color: var(--text-dark);">Vocabulary Builder</h4>
                            <p class="text-sm mb-4" style="color: var(--text-light);">Master 10 new words today with AI-powered pronunciation practice.</p>
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl text-white flex items-center justify-center font-bold text-lg shadow-lg" style="background: var(--primary-blue);">+250</div>
                                <p class="text-sm font-medium" style="color: var(--text-medium);">Points Reward</p>
                            </div>
                        </div>
                        <div class="glass-card p-6 hover:scale-[1.02]">
                            <p class="text-xs uppercase tracking-widest mb-2" style="color: var(--text-light);">AI Tutor</p>
                            <h4 class="text-lg font-semibold mb-2" style="color: var(--text-dark);">Live Session</h4>
                            <p class="text-sm mb-4" style="color: var(--text-light);">Your AI tutor is available now. Get instant feedback on your progress.</p>
                            <div class="flex -space-x-2">
                                <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center text-white text-xs font-semibold shadow-lg" style="background: var(--primary-blue); border-color: white;">AK</div>
                                <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center text-white text-xs font-semibold shadow-lg" style="background: var(--primary-blue); border-color: white;">JR</div>
                                <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center text-white text-xs font-semibold shadow-lg" style="background: var(--primary-blue); border-color: white;">LM</div>
                                <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center text-white text-xs font-semibold shadow-lg" style="background: var(--primary-blue); border-color: white;">+8</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quest Path -->
    <section id="quest" class="section-padding relative z-10">
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="text-4xl sm:text-5xl font-bold mb-4" style="color: var(--text-dark);" data-translate="quest-title">How Quest2Learn Works</h2>
            <p class="text-lg max-w-3xl mx-auto leading-relaxed" style="color: var(--text-medium);" data-translate="quest-subtitle">An intelligent tutoring system that adapts to each indigenous learner's unique needs through gamified experiences.</p>
        </div>
            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-card p-8 relative overflow-hidden group">
                <div class="absolute top-6 right-6 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(59, 130, 246, 0.1);">
                    <span class="text-lg font-bold" style="color: var(--primary-blue);">1</span>
                </div>
                <h3 class="text-xl font-bold mb-3 pr-12" style="color: var(--text-dark);" data-translate="quest-step-1-title">Assessment</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-medium);" data-translate="quest-step-1-desc">AI evaluates your current skills and creates a personalized learning path.</p>
            </div>
            <div class="glass-card p-8 relative overflow-hidden group">
                <div class="absolute top-6 right-6 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(59, 130, 246, 0.1);">
                    <span class="text-lg font-bold" style="color: var(--primary-blue);">2</span>
                </div>
                <h3 class="text-xl font-bold mb-3 pr-12" style="color: var(--text-dark);" data-translate="quest-step-2-title">Interactive Tutoring</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-medium);" data-translate="quest-step-2-desc">Learn through gamified lessons with real-time AI feedback and guidance.</p>
            </div>
            <div class="glass-card p-8 relative overflow-hidden group">
                <div class="absolute top-6 right-6 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(59, 130, 246, 0.1);">
                    <span class="text-lg font-bold" style="color: var(--primary-blue);">3</span>
                </div>
                <h3 class="text-xl font-bold mb-3 pr-12" style="color: var(--text-dark);" data-translate="quest-step-3-title">Practice & Mastery</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-medium);" data-translate="quest-step-3-desc">Engage in fun challenges that reinforce learning with instant corrections.</p>
            </div>
            <div class="glass-card p-8 relative overflow-hidden group">
                <div class="absolute top-6 right-6 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(59, 130, 246, 0.1);">
                    <span class="text-lg font-bold" style="color: var(--primary-blue);">4</span>
                </div>
                <h3 class="text-xl font-bold mb-3 pr-12" style="color: var(--text-dark);" data-translate="quest-step-4-title">Progress Tracking</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-medium);" data-translate="quest-step-4-desc">Monitor your growth with detailed analytics and achievement milestones.</p>
            </div>
        </div>
    </section>

    <!-- Live Missions -->
    <section id="missions" class="section-padding relative z-10">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-16">
                <div>
                    <h2 class="text-4xl sm:text-5xl font-bold mb-4" style="color: var(--text-dark);" data-translate="missions-title">AI Tutoring Features</h2>
                    <p class="text-lg max-w-2xl leading-relaxed" style="color: var(--text-medium);" data-translate="missions-subtitle">Experience cutting-edge AI technology designed to support indigenous learners in their educational journey.</p>
                </div>
                <a href="{{ route('login', ['role' => 'student', 'tab' => 'signup']) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold transition border" style="color: var(--primary-blue); border-color: var(--primary-blue);" onmouseover="this.style.background='var(--primary-blue)'; this.style.color='white';" onmouseout="this.style.background='transparent'; this.style.color='var(--primary-blue)';">Get Started →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-card p-8 flex flex-col gap-5 hover:scale-[1.02]">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl" style="background: rgba(59, 130, 246, 0.1);">🤖</div>
                        <h3 class="text-xl font-bold" style="color: var(--text-dark);" data-translate="mission-card-1-title">AI-Powered Feedback</h3>
                    </div>
                    <p class="text-sm leading-relaxed flex-grow" style="color: var(--text-medium);" data-translate="mission-card-1-desc">Receive instant, personalized feedback on pronunciation, grammar, and comprehension from our intelligent AI tutor.</p>
                    <div class="flex items-center justify-between pt-4 border-t" style="border-color: rgba(0, 0, 0, 0.1);">
                        <span class="text-xs font-medium" style="color: var(--primary-blue);">Real-time</span>
                        <span class="text-xs font-medium" style="color: var(--text-light);">24/7 Available</span>
                    </div>
                </div>
                <div class="glass-card p-8 flex flex-col gap-5 hover:scale-[1.02]">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl" style="background: rgba(59, 130, 246, 0.1);">🎮</div>
                        <h3 class="text-xl font-bold" style="color: var(--text-dark);" data-translate="mission-card-2-title">Gamified Learning</h3>
                    </div>
                    <p class="text-sm leading-relaxed flex-grow" style="color: var(--text-medium);" data-translate="mission-card-2-desc">Learn through engaging games, quests, and challenges that make education fun and motivating for indigenous students.</p>
                    <div class="flex items-center justify-between pt-4 border-t" style="border-color: rgba(0, 0, 0, 0.1);">
                        <span class="text-xs font-medium" style="color: var(--primary-blue);">Interactive</span>
                        <span class="text-xs font-medium" style="color: var(--text-light);">Reward System</span>
                    </div>
                </div>
                <div class="glass-card p-8 flex flex-col gap-5 hover:scale-[1.02]">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl" style="background: rgba(59, 130, 246, 0.1);">🌍</div>
                        <h3 class="text-xl font-bold" style="color: var(--text-dark);" data-translate="mission-card-3-title">Cultural Adaptation</h3>
                    </div>
                    <p class="text-sm leading-relaxed flex-grow" style="color: var(--text-medium);" data-translate="mission-card-3-desc">Content and teaching methods specifically tailored to respect and incorporate indigenous cultural contexts and languages.</p>
                    <div class="flex items-center justify-between pt-4 border-t" style="border-color: rgba(0, 0, 0, 0.1);">
                        <span class="text-xs font-medium" style="color: var(--primary-blue);">Inclusive</span>
                        <span class="text-xs font-medium" style="color: var(--text-light);">Culturally Aware</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rewards Vault -->
    <section id="rewards" class="section-padding relative z-10">
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="text-4xl sm:text-5xl font-bold mb-4" style="color: var(--text-dark);" data-translate="rewards-title">Key Features & Benefits</h2>
            <p class="text-lg max-w-3xl mx-auto leading-relaxed" style="color: var(--text-medium);" data-translate="rewards-subtitle">Discover what makes Quest2Learn the perfect tutoring system for indigenous learners.</p>
        </div>
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card p-8 text-left hover:scale-[1.02]">
                <div class="w-16 h-16 rounded-2xl text-white flex items-center justify-center text-2xl font-bold mb-6 shadow-lg" style="background: var(--primary-blue);">AI</div>
                <h3 class="text-xl font-bold mb-3" style="color: var(--text-dark);" data-translate="reward-1-title">Intelligent Tutoring</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-medium);" data-translate="reward-1-desc">Advanced AI algorithms provide personalized instruction that adapts to each learner's unique pace and learning style.</p>
            </div>
            <div class="glass-card p-8 text-left hover:scale-[1.02]">
                <div class="w-16 h-16 rounded-2xl text-white flex items-center justify-center text-2xl mb-6 shadow-lg" style="background: var(--primary-blue);">🎯</div>
                <h3 class="text-xl font-bold mb-3" style="color: var(--text-dark);" data-translate="reward-2-title">Gamification</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-medium);" data-translate="reward-2-desc">Points, levels, achievements, and leaderboards transform learning into an engaging, motivating experience.</p>
            </div>
            <div class="glass-card p-8 text-left hover:scale-[1.02]">
                <div class="w-16 h-16 rounded-2xl text-white flex items-center justify-center text-2xl mb-6 shadow-lg" style="background: var(--primary-blue);">🌿</div>
                <h3 class="text-xl font-bold mb-3" style="color: var(--text-dark);" data-translate="reward-3-title">Indigenous Focus</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-medium);" data-translate="reward-3-desc">Culturally-responsive content and multilingual support for indigenous languages including Filipino and Bisaya.</p>
            </div>
        </div>
    </section>

    <!-- Community -->
    <section id="community" class="section-padding relative z-10">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-5 space-y-6">
                <h2 class="text-4xl sm:text-5xl font-bold mb-4" style="color: var(--text-dark);" data-translate="community-title">Why Quest2Learn?</h2>
                <p class="text-lg leading-relaxed" style="color: var(--text-medium);" data-translate="community-subtitle">Empowering indigenous communities through accessible, culturally-aware AI-powered education.</p>
                <blockquote class="glass-card p-8 text-left">
                    <p class="text-base italic leading-relaxed mb-4" style="color: var(--text-medium);" data-translate="community-quote">"Quest2Learn has revolutionized how we teach indigenous students. The AI tutor provides personalized support that respects our cultural values while making learning engaging and fun."</p>
                    <div class="flex items-center gap-3 pt-4 border-t" style="border-color: rgba(0, 0, 0, 0.1);">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background: var(--primary-blue);">MS</div>
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--text-dark);" data-translate="community-quote-author">Maria Santos</p>
                            <p class="text-xs" style="color: var(--text-light);" data-translate="community-quote-role">Indigenous Education Coordinator</p>
                        </div>
                    </div>
                </blockquote>
            </div>
            <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass-card p-8 hover:scale-[1.02]">
                    <p class="text-xs uppercase tracking-[0.3em] mb-6" style="color: var(--text-light);">System Highlights</p>
                    <div class="space-y-4 text-sm">
                        <div class="flex items-center justify-between pb-3 border-b" style="border-color: rgba(0, 0, 0, 0.1);">
                            <span class="font-semibold" style="color: var(--text-dark);">AI Tutoring</span>
                            <span class="font-medium" style="color: var(--primary-blue);">24/7 Available</span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b" style="border-color: rgba(0, 0, 0, 0.1);">
                            <span style="color: var(--text-medium);">Gamification</span>
                            <span style="color: var(--text-light);">100% Engaged</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span style="color: var(--text-medium);">Indigenous Support</span>
                            <span style="color: var(--text-light);">Multi-language</span>
                        </div>
                    </div>
                </div>
                <div class="glass-card p-8 hover:scale-[1.02]">
                    <p class="text-xs uppercase tracking-[0.3em] mb-6" style="color: var(--text-light);">Learning Impact</p>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <p class="text-4xl font-bold mb-2" style="color: var(--primary-blue);">95%</p>
                            <p class="text-xs" style="color: var(--text-light);" data-translate="community-stat-1-label">Student Engagement Rate</p>
                        </div>
                        <div>
                            <p class="text-4xl font-bold mb-2" style="color: var(--primary-blue);">2.5x</p>
                            <p class="text-xs" style="color: var(--text-light);" data-translate="community-stat-2-label">Faster Learning Progress</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section id="call-to-action" class="section-padding relative z-10">
        <div class="max-w-5xl mx-auto glass-card p-12 text-center relative overflow-hidden">
            <div class="absolute inset-0" style="background: rgba(59, 130, 246, 0.05);"></div>
            <div class="relative">
                <h2 class="text-4xl sm:text-5xl font-bold mb-4" style="color: var(--text-dark);" data-translate="cta-title">Ready to Start Your Learning Journey?</h2>
                <p class="text-lg max-w-2xl mx-auto leading-relaxed" style="color: var(--text-medium);" data-translate="cta-subtitle">Join Quest2Learn today and experience AI-powered gamified tutoring designed specifically for indigenous learners.</p>
                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('login', ['role' => 'student', 'tab' => 'signup']) }}" class="px-8 py-4 rounded-xl font-semibold text-base text-white btn-primary" data-translate="btn-get-started">Start Learning Now</a>
                    <a href="{{ route('login', ['role' => 'teacher', 'tab' => 'signin']) }}" class="px-8 py-4 rounded-xl font-semibold text-base btn-secondary" data-translate="btn-teacher">For Educators</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-14 px-4 sm:px-6 lg:px-8 border-t" style="background: rgba(255, 255, 255, 0.8); border-color: rgba(0, 0, 0, 0.1);">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 text-sm">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-8 w-auto">
                    <span class="text-lg font-semibold" style="color: #000000;">Q2L</span>
                </div>
                <p style="color: var(--text-light);">Quest2Learn - An AI Gamified Tutoring System for Indigenous People. Empowering education through technology.</p>
            </div>
            <div>
                <h4 class="text-base font-semibold mb-3" style="color: var(--text-dark);">Explore</h4>
                <ul class="space-y-2" style="color: var(--text-light);">
                    <li><a href="#home" class="hover:opacity-70 transition" style="color: var(--primary-blue);">Home</a></li>
                    <li><a href="#missions" class="hover:opacity-70 transition" style="color: var(--primary-blue);">Live Missions</a></li>
                    <li><a href="#rewards" class="hover:opacity-70 transition" style="color: var(--primary-blue);">Rewards</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-base font-semibold mb-3" style="color: var(--text-dark);">Support</h4>
                <ul class="space-y-2" style="color: var(--text-light);">
                    <li><a href="#" class="hover:opacity-70 transition" style="color: var(--primary-blue);">Help Center</a></li>
                    <li><a href="#" class="hover:opacity-70 transition" style="color: var(--primary-blue);">Terms of Service</a></li>
                    <li><a href="#" class="hover:opacity-70 transition" style="color: var(--primary-blue);">Privacy Policy</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-base font-semibold mb-3" style="color: var(--text-dark);">Stay Connected</h4>
                <p style="color: var(--text-light);">Follow community drops, mission resets, and upcoming seasons.</p>
            </div>
        </div>
        <div class="max-w-6xl mx-auto mt-10 text-center text-xs" style="color: var(--text-light);">
            <p>&copy; 2024 Q2L - Quest to Learn. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('js/welcome.js') }}"></script>
</body>
</html>

