<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $classData['name'] }} · Class Dashboard</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>

    <script>
        let questToastTimer = null;

        function showQuestToast(quest = {}) {
            const toast = document.getElementById('questToast');
            if (!toast) return;

            const titleEl = document.getElementById('questToastTitle');
            const messageEl = document.getElementById('questToastMessage');
            const rewardEl = document.getElementById('questToastReward');

            titleEl.textContent = quest.title ? `${quest.title} unlocked!` : 'Quest Created';
            messageEl.textContent = quest.type ? `${quest.type} quest is live for your heroes.` : 'Students can now embark on this adventure.';
            rewardEl.textContent = quest.reward ? `+${quest.reward}` : `+${quest.reward_points ?? 50} XP`;

            toast.classList.add('active');

            if (questToastTimer) {
                clearTimeout(questToastTimer);
            }

            questToastTimer = setTimeout(() => {
                hideQuestToast();
            }, 6000);
        }

        function hideQuestToast() {
            const toast = document.getElementById('questToast');
            toast?.classList.remove('active');
            if (questToastTimer) {
                clearTimeout(questToastTimer);
                questToastTimer = null;
            }
        }
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
            border-radius: 1.5rem;
            box-shadow: var(--shadow-soft);
            transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease;
        }

        .card:hover {
            border-color: var(--surface-border-strong);
            transform: translateY(-2px);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .tone-emerald { background: rgba(16, 185, 129, 0.15); color: #065f46; }
        .tone-amber { background: rgba(245, 158, 11, 0.15); color: #92400e; }
        .tone-rose { background: rgba(244, 63, 94, 0.18); color: #be123c; }
        .tone-indigo { background: rgba(99, 102, 241, 0.18); color: #4338ca; }
        .tone-sky { background: rgba(14, 165, 233, 0.18); color: #0c4a6e; }

        .dark .tone-emerald { background: rgba(16, 185, 129, 0.25); color: #bbf7d0; }
        .dark .tone-amber { background: rgba(245, 158, 11, 0.28); color: #fcd34d; }
        .dark .tone-rose { background: rgba(244, 63, 94, 0.28); color: #fecdd3; }
        .dark .tone-indigo { background: rgba(99, 102, 241, 0.28); color: #c7d2fe; }
        .dark .tone-sky { background: rgba(14, 165, 233, 0.28); color: #bae6fd; }

        .progress-track {
            height: 0.5rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.3);
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #4f46e5 0%, #22d3ee 100%);
        }

        .avatar-chip {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        }

        .class-coach-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 0.85rem 1.5rem;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border: none;
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.4);
            cursor: pointer;
            z-index: 1050;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .class-coach-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 40px rgba(79, 70, 229, 0.5);
        }

        .class-coach-btn:active {
            transform: translateY(0);
        }

        .class-coach-btn .ai-icon {
            width: 2.4rem;
            height: 2.4rem;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .class-coach-chat {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 380px;
            max-height: 600px;
            background: var(--card-bg);
            border-radius: 1.5rem;
            border: 1px solid var(--surface-border);
            box-shadow: 0 25px 80px rgba(15, 23, 42, 0.25);
            display: flex;
            flex-direction: column;
            z-index: 1055;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .class-coach-chat.hidden {
            opacity: 0;
            pointer-events: none;
            transform: translateY(10px) scale(0.97);
        }

        /* Quest success toast */
        .quest-toast {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: min(24rem, calc(100% - 2rem));
            padding: 1.15rem 1.25rem 1.25rem;
            border-radius: 1.5rem;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(49, 46, 129, 0.95));
            border: 1px solid rgba(129, 140, 248, 0.5);
            box-shadow: 0 25px 70px rgba(56, 189, 248, 0.25);
            color: #e0e7ff;
            opacity: 0;
            pointer-events: none;
            transform: translateY(-10px) scale(0.98);
            transition: opacity 0.25s ease, transform 0.25s ease;
            z-index: 9999;
        }

        .quest-toast::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 1.5rem;
            background: radial-gradient(circle at top, rgba(236, 72, 153, 0.35), transparent 60%);
            opacity: 0.6;
            pointer-events: none;
        }

        .quest-toast.active {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        .quest-toast-content {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .quest-toast-icon {
            height: 3rem;
            width: 3rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #f472b6, #8b5cf6);
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .quest-toast-eyebrow {
            font-size: 0.65rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: #c7d2fe;
            margin-bottom: 0.35rem;
        }

        .quest-toast-title {
            font-size: 1.2rem;
            font-weight: 800;
            line-height: 1.2;
            color: #fff;
        }

        .quest-toast-message {
            font-size: 0.9rem;
            color: #cbd5f5;
            margin-top: 0.15rem;
        }

        .quest-toast-footer {
            margin-top: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .quest-toast-badge {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.35em;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            background: rgba(59, 7, 100, 0.6);
            border: 1px solid rgba(167, 139, 250, 0.6);
            color: #f5d0fe;
        }

        .quest-toast-xp {
            font-size: 1rem;
            font-weight: 700;
            color: #fef3c7;
            background: rgba(245, 158, 11, 0.15);
            border-radius: 999px;
            padding: 0.2rem 0.85rem;
            border: 1px solid rgba(245, 158, 11, 0.4);
        }

        .quest-toast-dismiss {
            height: 2rem;
            width: 2rem;
            border-radius: 999px;
            border: 1px solid rgba(203, 213, 225, 0.4);
            background: transparent;
            color: #cbd5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .quest-toast-dismiss:hover {
            background: rgba(203, 213, 225, 0.15);
            color: #fff;
        }

        .quest-toast-progress {
            position: absolute;
            inset: auto 1rem 0.75rem 1rem;
            height: 4px;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.3);
            overflow: hidden;
        }

        .quest-toast-progress::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, #f472b6, #c084fc, #60a5fa);
            width: 0%;
            border-radius: inherit;
        }

        .quest-toast.active .quest-toast-progress::after {
            animation: quest-toast-progress 4s linear forwards;
        }

        @keyframes quest-toast-progress {
            from { width: 0%; }
            to { width: 100%; }
        }

        .class-coach-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--surface-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .class-coach-header-left {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .class-coach-header-left div {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .class-coach-header-avatar {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #fff;
        }

        .class-coach-close {
            width: 2.3rem;
            height: 2.3rem;
            border-radius: 50%;
            border: none;
            background: rgba(99, 102, 241, 0.1);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .class-coach-close:hover {
            background: rgba(99, 102, 241, 0.2);
        }

        .class-coach-messages {
            flex: 1;
            padding: 1.25rem;
            overflow-y: auto;
            background: rgba(15, 23, 42, 0.02);
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .class-coach-message {
            display: flex;
            justify-content: flex-start;
        }

        .class-coach-message.user {
            justify-content: flex-end;
        }

        .class-coach-bubble {
            max-width: 80%;
            border-radius: 1.25rem;
            padding: 0.9rem 1rem;
            font-size: 0.9rem;
            line-height: 1.5;
            background: rgba(255, 255, 255, 0.85);
            color: var(--text-primary);
            border: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(6px);
        }

        .dark .class-coach-bubble {
            background: rgba(15, 23, 42, 0.8);
            border-color: rgba(99, 102, 241, 0.2);
            color: var(--text-primary);
        }

        .class-coach-message.user .class-coach-bubble {
            background: var(--brand-primary);
            color: #fff;
        }

        .class-coach-input {
            border-top: 1px solid var(--surface-border);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .class-coach-input textarea {
            border: 1px solid var(--surface-border);
            border-radius: 1rem;
            padding: 0.85rem 1rem;
            resize: none;
            background: transparent;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .class-coach-input textarea:focus {
            outline: none;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
        }

        .class-coach-send {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .class-coach-send button[type="submit"] {
            border: none;
            border-radius: 999px;
            background: var(--brand-primary);
            color: #fff;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
        }

        .class-coach-quick {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .class-coach-quick span {
            font-size: 0.75rem;
            border: 1px dashed var(--surface-border);
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            cursor: pointer;
            transition: border-color 0.2s ease;
        }

        .class-coach-quick span:hover {
            border-color: var(--brand-primary);
        }

        .class-coach-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1054;
            opacity: 1;
        }

        .class-coach-backdrop.hidden {
            opacity: 0;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .class-coach-btn {
                bottom: 1.5rem;
                right: 1.25rem;
                padding: 0.7rem 1.1rem;
            }

            .class-coach-chat {
                width: calc(100vw - 2rem);
                bottom: 1.25rem;
                right: 1rem;
            }
        }

        @media (max-width: 480px) {
            .class-coach-btn {
                border-radius: 50%;
                width: 3.25rem;
                height: 3.25rem;
                padding: 0;
                justify-content: center;
            }

            .class-coach-btn span {
                display: none;
            }

            .class-coach-chat {
                width: 100vw;
                height: calc(100vh - 3rem);
                bottom: 0;
                right: 0;
                border-radius: 1.5rem 1.5rem 0 0;
            }
        }
    </style>
</head>
<body class="min-h-screen pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-6 sm:py-8 space-y-6 sm:space-y-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div class="space-y-2">
                <p class="text-xs font-semibold tracking-[0.4em] text-[color:var(--text-soft)] uppercase">Class Dashboard</p>
                <h1 class="text-3xl sm:text-4xl font-black">{{ $classData['name'] }}</h1>
                <p class="text-sm text-[color:var(--text-muted)]">{{ $classData['schedule'] }}</p>
            </div>
            <div class="flex items-center gap-3 sm:gap-4">
                <x-translation-toggle class="hidden sm:flex" />
                <x-theme-toggle />
                <a href="{{ route('teacher.performance.analytics', $classroom->slug ?? $classroom->id) }}" class="pill tone-green text-xs">📊 Performance Analytics</a>
                <a href="{{ route('teacher.dashboard') }}" class="pill tone-indigo text-xs">Back to Teacher Hub</a>
            </div>
        </header>

        <!-- Research Focus Metrics -->
        <section class="space-y-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-[color:var(--text-soft)]">Research Focus</p>
                    <h2 class="text-2xl font-black">Learning Impact Signals</h2>
                    <p class="text-sm text-[color:var(--text-muted)]">Reading, pronunciation, vocabulary, and engagement insights based on quest data.</p>
                </div>
                <span class="pill tone-purple text-xs">Pilot Study</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach (($researchMetrics ?? []) as $metric)
                    <article class="rounded-3xl border border-gray-700 bg-gray-900/70 p-5 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-[1.02]">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold uppercase tracking-[0.3em] text-gray-200 bg-gray-800 px-2 py-1 rounded">{{ $metric['badge'] }}</span>
                            <span class="pill tone-indigo text-[0.6rem]">{{ $metric['change_label'] ?? '' }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-4">{{ $metric['title'] }}</h3>
                        <div class="flex items-end gap-2 mb-3">
                            <span class="text-4xl font-black text-indigo-400">{{ $metric['value'] ?? 0 }}</span>
                            <span class="text-base font-semibold text-gray-300">{{ $metric['unit'] ?? '' }}</span>
                        </div>
                        <p class="text-sm text-gray-200 mb-4">{{ $metric['subtext'] ?? '' }}</p>
                        <div class="flex items-center gap-2 pt-3 border-t border-gray-700">
                            <span class="text-xs font-semibold text-emerald-300 bg-emerald-900/30 px-2 py-1 rounded">{{ $metric['change'] > 0 ? '+' : '' }}{{ $metric['change'] ?? 0 }}{{ isset($metric['unit']) && $metric['unit'] === '%' ? '%' : '' }}</span>
                            <span class="text-xs text-gray-300">{{ $metric['description'] ?? '' }}</span>
                        </div>
                    </article>
                @endforeach
                @if(empty($researchMetrics))
                    <article class="rounded-3xl border border-dashed border-gray-700 bg-gray-900/40 p-6 text-center">
                        <p class="text-sm text-gray-300">Complete quests to unlock research metrics.</p>
                    </article>
                @endif
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <article class="card p-5 sm:p-7 lg:col-span-2 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-soft)]">Join Code</p>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="font-mono text-3xl font-bold tracking-[0.2em] text-[color:var(--brand-primary)]">{{ $classData['join_code'] }}</span>
                            <button type="button" class="pill tone-emerald text-xs" data-copy="{{ $classData['join_code'] }}">Copy</button>
                        </div>
                        <p class="text-xs text-[color:var(--text-muted)] mt-1">Share this with learners to unlock the class lobby.</p>
                    </div>
                    <div class="space-y-2 text-right">
                        <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-soft)]">Mentor</p>
                        <p class="text-lg font-semibold">{{ $classData['mentor'] }}</p>
                        <p class="text-sm text-[color:var(--text-muted)]">{{ $classData['streak_days'] }} day streak</p>
                    </div>
                </div>
            </article>

            <aside class="card p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold flex items-center gap-2">
                        <span class="h-6 w-6 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-xs font-bold">👑</span>
                        Top Players
                    </h3>
                    <span class="pill tone-rose text-xs">Leaderboard</span>
                </div>
                @forelse ($classData['leaderboard'] as $player)
                    <li class="flex items-center justify-between rounded-2xl border border-[color:var(--surface-border)] px-4 py-3 hover:border-[color:var(--surface-border-strong)] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <span class="avatar-chip text-sm font-bold">{{ $player['avatar'] }}</span>
                                @if ($player['rank'] === 1)
                                    <div class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-gradient-to-br from-yellow-400 to-amber-500 flex items-center justify-center text-white text-xs font-bold">🥇</div>
                                @elseif ($player['rank'] === 2)
                                    <div class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-white text-xs font-bold">🥈</div>
                                @elseif ($player['rank'] === 3)
                                    <div class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-gradient-to-br from-amber-600 to-orange-700 flex items-center justify-center text-white text-xs font-bold">🥉</div>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold">{{ $player['name'] }}</p>
                                <div class="flex items-center gap-2 text-xs text-[color:var(--text-muted)]">
                                    <span class="font-bold text-[color:var(--brand-primary)]">{{ number_format($player['xp']) }} XP</span>
                                    <span>•</span>
                                    <span>{{ $player['streak'] }} day streak</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($player['rank'] <= 3)
                                <span class="pill {{ $player['rank'] === 1 ? 'tone-amber' : ($player['rank'] === 2 ? 'tone-sky' : 'tone-rose') }} text-xs font-bold">
                                    #{{ $player['rank'] }}
                                </span>
                            @else
                                <span class="pill tone-indigo text-xs">#{{ $player['rank'] }}</span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-[color:var(--surface-border)] px-4 py-6 text-center">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white text-2xl mx-auto mb-3">🎮</div>
                        <p class="text-sm text-[color:var(--text-muted)] font-medium">No Heroes Yet</p>
                        <p class="text-xs text-[color:var(--text-muted)] mt-1">Share the class code to summon students to this quest!</p>
                    </li>
                @endforelse
            </aside>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
            <article id="questLog" class="card p-5 sm:p-6 xl:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Quest Log</h3>
                        <p class="text-sm text-[color:var(--text-muted)]">Manage the missions currently shaping this class story.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('teacher.classes.skill-tracking', $classroom->id) }}" class="pill tone-purple text-xs hover:bg-purple-600 hover:text-white transition-colors cursor-pointer">Skill Tracking</a>
                        <button type="button" class="pill tone-emerald text-xs hover:bg-green-600 hover:text-white transition-colors cursor-pointer" onclick="openQuestModal()">Add Quest</button>
                    </div>
                </div>
                <ul class="space-y-3 max-h-[500px] overflow-y-auto scrollbar-thin scrollbar-thumb-purple-500/40 scrollbar-track-purple-900/10 pr-1">
                    @foreach ($classData['quests'] as $quest)
                        <li class="rounded-2xl border border-[color:var(--surface-border)] px-4 py-4 flex flex-wrap items-center gap-3 justify-between hover:bg-[color:var(--surface-hover)] transition-colors">
                            <div class="flex-1 cursor-pointer" onclick="showQuestContent({{ $quest['id'] }})">
                                <p class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-soft)]">{{ $quest['type'] }}</p>
                                <h4 class="text-base font-semibold mt-1">{{ $quest['title'] }}</h4>
                                <p class="text-xs text-[color:var(--text-muted)] mt-1">Reward: {{ $quest['reward'] }} · {{ $quest['difficulty'] }} mode · {{ $quest['estimated_time'] }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="pill {{ $quest['status'] === 'Active' ? 'tone-rose' : ($quest['status'] === 'Queued' ? 'tone-amber' : 'tone-emerald') }}">
                                    {{ $quest['status'] }}
                                </span>
                                <button 
                                    onclick="deleteQuest({{ $quest['id'] }}, event)"
                                    data-quest-title="{{ e($quest['title']) }}"
                                    data-quest-reward="{{ e($quest['reward']) }}"
                                    data-quest-difficulty="{{ e($quest['difficulty']) }}"
                                    class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                    title="Delete quest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </article>
        </section>

                </div>
            </div>
            
        
    </div>

    <button type="button" id="classCoachToggle" class="class-coach-btn">
        <div class="ai-icon">🤖</div>
        <span>Class Coach AI</span>
    </button>

    <div id="classCoachBackdrop" class="class-coach-backdrop hidden"></div>

    <div id="classCoachChatbot" class="class-coach-chat hidden" x-data="classCoachChat(@js([
        'className' => $classData['name'] ?? 'your class',
        'mentor' => $classData['mentor'] ?? Auth::user()->name,
        'schedule' => $classData['schedule'] ?? '',
        'joinCode' => $classData['join_code'] ?? ''
    ]))">
        <div class="class-coach-header">
            <div class="class-coach-header-left">
                <div class="class-coach-header-avatar">AI</div>
                <div>
                    <h2 class="text-base font-semibold">Class Coach AI</h2>
                    <p class="text-xs text-[color:var(--text-muted)]">Lesson plans, quests, and motivation ideas.</p>
                </div>
            </div>
            <button type="button" id="closeClassCoach" class="class-coach-close" aria-label="Close Class Coach">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="class-coach-messages" x-ref="messageList">
            <div x-show="messages.length === 0 && !loading" class="flex-1 flex flex-col items-center justify-center text-center text-sm text-[color:var(--text-muted)] gap-1">
                <p>Ask your first question about the class to start chatting.</p>
                <p class="text-xs">The AI coach jumps in once you send a prompt.</p>
            </div>
            <template x-for="(message, index) in messages" :key="index">
                <div class="class-coach-message" :class="message.role === 'user' ? 'user' : ''">
                    <div class="class-coach-bubble">
                        <p class="text-[0.65rem] uppercase tracking-[0.25em] mb-1" :class="message.role === 'user' ? 'text-white/70' : 'text-[color:var(--text-soft)]'" x-text="message.role === 'user' ? 'You' : 'AI Coach'"></p>
                        <div class="space-y-2 text-sm leading-relaxed" x-html="formatMessage(message.content)"></div>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="class-coach-message">
                <div class="class-coach-bubble flex items-center gap-3 text-sm text-[color:var(--text-muted)]">
                    <div class="animate-spin rounded-full h-4 w-4 border-2 border-[color:var(--brand-primary)] border-t-transparent"></div>
                    Thinking...
                </div>
            </div>
        </div>

        <form @submit.prevent="sendMessage" class="class-coach-input">
            <textarea x-model="input" rows="2" @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()" placeholder="Ask about lesson plans, student motivation ideas, or quest tweaks..." :disabled="loading"></textarea>
            <div class="class-coach-send">
                <button type="submit" :disabled="!input.trim() || loading" :class="{'opacity-60 cursor-not-allowed': !input.trim() || loading}">
                    <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                    <div x-show="loading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                    <span x-show="!loading">Send</span>
                </button>
                <p x-show="error" class="text-sm text-red-500" x-text="error"></p>
            </div>
            <div class="class-coach-quick text-[color:var(--text-muted)]">
                <span @click="input = 'Give me a 10-minute pronunciation warm-up for today.'">Pronunciation warm-up</span>
                <span @click="input = 'Suggest an engaging reading quest based on our latest topic.'">Reading quest idea</span>
                <span @click="input = 'How can I motivate students with low streaks this week?'">Motivate streaks</span>
            </div>
            <p class="text-[0.7rem] text-[color:var(--text-muted)]">Responses rely on the configured OpenRouter or Gemini API key.</p>
        </form>
    </div>

    <script>
        document.addEventListener('click', (event) => {
            const target = event.target.closest('[data-copy]');
            if (!target) return;

            const code = target.getAttribute('data-copy');
            if (!code) return;

            const original = target.textContent;
            const showSuccess = () => {
                target.textContent = 'Copied!';
                setTimeout(() => {
                    target.textContent = original;
                }, 1200);
            };

            if (navigator.clipboard?.writeText) {
                navigator.clipboard.writeText(code).then(showSuccess).catch(showSuccess);
            } else {
                showSuccess();
            }
        });

        function classCoachChat(classContext = {}) {
            return {
                messages: [],
                input: '',
                loading: false,
                error: null,
                getLanguage() {
                    const stored = localStorage.getItem('selectedLanguage');
                    return ['en', 'fil', 'bis'].includes(stored) ? stored : 'en';
                },
                scrollToBottom() {
                    this.$nextTick(() => {
                        if (this.$refs.messageList) {
                            this.$refs.messageList.scrollTop = this.$refs.messageList.scrollHeight;
                        }
                    });
                },
                escapeHtml(value = '') {
                    return value
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                },
                formatMessage(content = '') {
                    const safe = this.escapeHtml(String(content ?? ''));
                    const paragraphs = safe
                        .split(/\n{2,}/)
                        .map((segment) => segment.trim())
                        .filter(Boolean)
                        .map((segment) => `<p>${segment.replace(/\n/g, '<br>')}</p>`);

                    return paragraphs.length ? paragraphs.join('') : '<p></p>';
                },
                async sendMessage() {
                    const trimmed = this.input.trim();
                    if (!trimmed || this.loading) {
                        return;
                    }

                    this.messages.push({ role: 'user', content: trimmed });
                    this.input = '';
                    this.loading = true;
                    this.error = null;
                    this.scrollToBottom();

                    const conversation = this.messages
                        .slice(0, -1)
                        .map((message) => ({
                            role: message.role === 'assistant' ? 'assistant' : 'user',
                            content: message.content
                        }));

                    try {
                        const response = await fetch('{{ route('assistant.chat') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                message: trimmed,
                                conversation,
                                language: this.getLanguage()
                            })
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok || !data?.reply) {
                            throw new Error(data?.reply || 'AI assistant is unavailable right now.');
                        }

                        this.messages.push({
                            role: 'assistant',
                            content: data.reply.trim() || 'I could not craft a response, please try again.'
                        });
                    } catch (error) {
                        this.error = error.message || 'Unable to contact the AI coach right now. Please try again shortly.';
                        this.messages.pop();
                    } finally {
                        this.loading = false;
                        this.scrollToBottom();
                    }
                }
            };
        }
    </script>

    <script>
        const classCoachToggle = document.getElementById('classCoachToggle');
        const classCoachChatbot = document.getElementById('classCoachChatbot');
        const classCoachBackdrop = document.getElementById('classCoachBackdrop');
        const closeClassCoach = document.getElementById('closeClassCoach');

        const openCoach = () => {
            classCoachChatbot.classList.remove('hidden');
            classCoachBackdrop.classList.remove('hidden');
            const textarea = classCoachChatbot.querySelector('textarea');
            if (textarea) {
                setTimeout(() => textarea.focus(), 50);
            }
        };

        const closeCoach = () => {
            classCoachChatbot.classList.add('hidden');
            classCoachBackdrop.classList.add('hidden');
        };

        classCoachToggle?.addEventListener('click', openCoach);
        closeClassCoach?.addEventListener('click', closeCoach);
        classCoachBackdrop?.addEventListener('click', closeCoach);
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !classCoachChatbot.classList.contains('hidden')) {
                closeCoach();
            }
        });
    </script>

    <!-- Quest Success Toast -->
    <div id="questToast" class="quest-toast" role="status" aria-live="assertive">
        <div class="quest-toast-content">
            <div class="quest-toast-icon">⚡</div>
            <div class="flex-1">
                <p class="quest-toast-eyebrow">Quest Forge</p>
                <p class="quest-toast-title" id="questToastTitle">Quest Created</p>
                <p class="quest-toast-message" id="questToastMessage">Students can now embark on this adventure.</p>
                <div class="quest-toast-footer">
                    <span class="quest-toast-badge">New Mission</span>
                    <span class="quest-toast-xp" id="questToastReward">+50 XP</span>
                </div>
            </div>
            <button type="button" class="quest-toast-dismiss" aria-label="Dismiss toast" onclick="hideQuestToast()">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="quest-toast-progress"></div>
    </div>

    <!-- Quest Upload Modal -->
    <div id="questModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-gray-900/95 border border-gray-700 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Create New Quest</h2>
                        <p class="text-sm text-gray-300 mt-1">Create a quest for your students to complete.</p>
                    </div>
                    <button onclick="closeQuestModal()" class="text-gray-400 hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="questUploadForm" class="p-6 space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Quest Title</label>
                    <input type="text" name="quest_title" required class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white placeholder-gray-400" placeholder="Enter quest title...">
                </div>

                <div id="questContentTextGroup">
                    <label class="block text-sm font-medium text-white mb-2">Quest Content</label>
                    <textarea name="quest_content" rows="6" required class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white placeholder-gray-400" placeholder="Enter the content for this quest (vocabulary words, reading passage, etc.)"></textarea>
                    <p class="text-xs text-gray-300 mt-1">Enter vocabulary words, reading passages, or other learning content.</p>
                </div>

                <div id="pronunciationContentGroup" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Pronunciation Items</label>
                        <p class="text-xs text-gray-300">Add the words and phonetic spellings you want students to practice.</p>
                    </div>
                    <div id="pronunciationItems" class="space-y-3"></div>
                    <div class="flex justify-end">
                        <button type="button" id="addPronunciationItem" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add Another Word</button>
                    </div>
                </div>

                <div id="readingContentGroup" class="hidden space-y-4">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-white">Reading Passage</label>
                        <p class="text-xs text-gray-300">Provide the paragraph students will read before answering.</p>
                        <textarea id="readingPassage" rows="4" class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white placeholder-gray-400" placeholder="Paste or write the passage here..."></textarea>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-medium text-white">Comprehension Questions</label>
                                <p class="text-xs text-gray-300">Add multiple-choice questions with answers tied to the passage.</p>
                            </div>
                            <button type="button" id="addReadingQuestion" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add Question</button>
                        </div>
                        <div id="readingQuestions" class="space-y-3"></div>
                    </div>
                </div>

                <div id="pdfContentGroup" class="hidden space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">PDF File</label>
                        <p class="text-xs text-gray-300">Upload a PDF. Students will be able to view the extracted text.</p>
                    </div>
                    <input type="file" name="quest_pdf" accept="application/pdf" class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-white mb-1">PDF Activity Type</label>
                        <p class="text-xs text-gray-300 mb-2">Choose how students will interact with this PDF content.</p>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="pdf_activity_type" value="read" checked class="mr-2 text-indigo-600 focus:ring-indigo-500 bg-gray-800 border-gray-600">
                                <span class="text-sm text-white">Read</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="pdf_activity_type" value="pronunciation" class="mr-2 text-indigo-600 focus:ring-indigo-500 bg-gray-800 border-gray-600">
                                <span class="text-sm text-white">Pronunciation</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-2">Quest Type</label>
                    <select name="quest_type" class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white">
                        <option value="pronunciation">Pronunciation Practice</option>
                        <option value="reading">Reading Comprehension</option>
                        <option value="mixed">Mixed Practice</option>
                        <option value="pdf">PDF Upload</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Difficulty</label>
                        <select name="difficulty" class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white">
                            <option value="easy">Easy</option>
                            <option value="medium" selected>Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Reward Points</label>
                        <input type="number" name="reward_points" value="50" min="10" max="200" class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-2">Estimated Time</label>
                    <input type="text" name="estimated_time" value="15 minutes" class="w-full px-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white placeholder-gray-400" placeholder="e.g., 15 minutes">
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                    <button type="button" onclick="closeQuestModal()" class="px-4 py-2 text-white bg-gray-600 hover:bg-gray-500 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="generateBtn" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Create Quest
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quest Delete Modal -->
    <div id="questDeleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>
        <div class="relative z-10 max-w-md w-full mx-auto px-4 py-8 flex items-center justify-center min-h-full">
            <div class="w-full rounded-3xl shadow-2xl border border-indigo-500/30 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-white overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-rose-500/80 via-purple-600 to-indigo-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs tracking-[0.35em] uppercase text-white/80">Danger Zone</p>
                            <h3 class="text-2xl font-bold mt-1">Delete Quest?</h3>
                        </div>
                        <div class="h-12 w-12 rounded-2xl bg-white/15 border border-white/30 flex items-center justify-center text-2xl">⚠️</div>
                    </div>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div>
                        <p class="text-xs uppercase text-indigo-200 tracking-[0.25em]">Quest Title</p>
                        <p id="questDeleteTitle" class="text-xl font-semibold mt-1">Quest Name</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-white/5 border border-white/10 p-3">
                            <p class="text-xs text-gray-300 uppercase tracking-wide">Reward</p>
                            <p id="questDeleteReward" class="text-lg font-bold text-emerald-300">50 XP</p>
                        </div>
                        <div class="rounded-2xl bg-white/5 border border-white/10 p-3">
                            <p class="text-xs text-gray-300 uppercase tracking-wide">Mode</p>
                            <p id="questDeleteDifficulty" class="text-lg font-bold text-amber-200">Medium</p>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-purple-500/40 bg-purple-900/20 p-4 text-purple-100 text-sm">
                        <p>All progress and rewards tied to this quest will be purged from the class log. This action can’t be undone.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button id="cancelQuestDelete" type="button" class="flex-1 px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white font-semibold hover:bg-white/20 transition-colors">Keep Quest</button>
                        <button id="confirmQuestDelete" type="button" class="flex-1 px-4 py-3 rounded-2xl bg-gradient-to-r from-rose-500 via-purple-500 to-indigo-500 text-white font-semibold shadow-lg shadow-rose-500/30 hover:brightness-110 transition-colors">Yes, delete quest</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-md w-full text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">AI is Working...</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Analyzing your PDF and generating lesson content. This may take a moment.</p>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quest Content Modal -->
    <div id="questContentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 id="questContentTitle" class="text-2xl font-bold text-gray-900 dark:text-white">Quest Content</h2>
                        <p id="questContentMeta" class="text-sm text-gray-600 dark:text-gray-400 mt-1">Loading...</p>
                    </div>
                    <button onclick="closeQuestContentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div id="questContentLoading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Loading quest content...</p>
                </div>
                <div id="questContentDetails" class="hidden space-y-6">
                    <!-- Quest Description -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Description</h3>
                        <p id="questContentDescription" class="text-gray-600 dark:text-gray-400"></p>
                    </div>

                    <!-- Pronunciation Exercises -->
                    <div id="pronunciationSection" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pronunciation Exercises</h3>
                        <div id="pronunciationExercises" class="space-y-3"></div>
                    </div>

                    <!-- Reading Exercises -->
                    <div id="readingSection" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reading Comprehension</h3>
                        <div id="readingExercises" class="space-y-4"></div>
                    </div>

                    <!-- Quest Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Difficulty</p>
                            <p id="questContentDifficulty" class="font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Estimated Time</p>
                            <p id="questContentTime" class="font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Reward Points</p>
                            <p id="questContentReward" class="font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quest Creation JavaScript -->
    <script>
        function openQuestModal() {
            document.getElementById('questModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeQuestModal() {
            document.getElementById('questModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            resetForm();
        }

        const questUploadForm = document.getElementById('questUploadForm');
        const questTypeSelect = questUploadForm.querySelector('select[name="quest_type"]');
        const questContentTextarea = questUploadForm.querySelector('textarea[name="quest_content"]');
        const questContentTextGroup = document.getElementById('questContentTextGroup');
        const pronunciationContentGroup = document.getElementById('pronunciationContentGroup');
        const pronunciationItemsContainer = document.getElementById('pronunciationItems');
        const addPronunciationItemBtn = document.getElementById('addPronunciationItem');
        const readingContentGroup = document.getElementById('readingContentGroup');
        const readingPassageInput = document.getElementById('readingPassage');
        const readingQuestionsContainer = document.getElementById('readingQuestions');
        const addReadingQuestionBtn = document.getElementById('addReadingQuestion');
        const difficultySelect = questUploadForm.querySelector('select[name="difficulty"]');
        const pdfContentGroup = document.getElementById('pdfContentGroup');
        const pdfFileInput = questUploadForm.querySelector('input[name="quest_pdf"]');

        function resetForm() {
            questUploadForm.reset();
            pronunciationItemsContainer.innerHTML = '';
            readingPassageInput.value = '';
            readingQuestionsContainer.innerHTML = '';
            toggleQuestContentFields();
        }

        function toggleQuestContentFields() {
            const questType = questTypeSelect.value;
            const isPronunciation = questType === 'pronunciation';
            const isReading = questType === 'reading';
            const isMixed = questType === 'mixed';
            const isPdf = questType === 'pdf';

            questContentTextGroup.classList.toggle('hidden', isPronunciation || isReading || isMixed || isPdf);
            pronunciationContentGroup.classList.toggle('hidden', !(isPronunciation || isMixed));
            readingContentGroup.classList.toggle('hidden', !(isReading || isMixed));
            pdfContentGroup.classList.toggle('hidden', !isPdf);
            questContentTextarea.required = false;
            readingPassageInput.required = isReading || isMixed;
            if (pdfFileInput) {
                pdfFileInput.required = isPdf;
            }

            if ((isPronunciation || isMixed) && pronunciationItemsContainer.children.length === 0) {
                addPronunciationItem();
            }

            if ((isReading || isMixed) && readingQuestionsContainer.children.length === 0) {
                addReadingQuestion();
            }

            if (isPronunciation || isReading || isMixed || isPdf) {
                questContentTextarea.value = '';
            }
        }

        function addPronunciationItem(word = '', phonetic = '', image = '') {
            const item = document.createElement('div');
            item.className = 'pronunciation-item border border-gray-600 rounded-xl p-4 space-y-4';
            item.innerHTML = `
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-white">Pronunciation Item</p>
                    <button type="button" class="text-xs text-red-400 hover:text-red-300" data-remove-item>Remove</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-white mb-1">Word</label>
                        <input type="text" name="pronunciation_word[]" class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white placeholder-gray-400" placeholder="e.g., Courage">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-white mb-1">Phonetic Spelling</label>
                        <input type="text" name="pronunciation_phonetic[]" class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white placeholder-gray-400" placeholder="e.g., /ˈkər-ij/">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-white mb-1">Image (Optional)</label>
                    <div class="flex items-center gap-3">
                        <input type="file" name="pronunciation_image[]" accept="image/*" class="flex-1 px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                        <div class="image-preview w-12 h-12 rounded-lg border-2 border-dashed border-gray-500 flex items-center justify-center bg-gray-800/50" data-image-preview>
                            ${image ? `<img src="${image}" alt="Preview" class="w-full h-full object-cover rounded-lg">` : '<span class="text-gray-400 text-xs">No image</span>'}
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Upload an image to help students recognize the word visually</p>
                </div>
            `;

            pronunciationItemsContainer.appendChild(item);
            item.querySelector('input[name="pronunciation_word[]"]').value = word;
            item.querySelector('input[name="pronunciation_phonetic[]"]').value = phonetic;
            
            // Handle image preview
            const fileInput = item.querySelector('input[name="pronunciation_image[]"]');
            const preview = item.querySelector('[data-image-preview]');
            
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover rounded-lg">`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '<span class="text-gray-400 text-xs">No image</span>';
                }
            });
        }

        function collectPronunciationItems() {
            const rows = pronunciationItemsContainer.querySelectorAll('.pronunciation-item');
            const items = [];

            rows.forEach(row => {
                const wordInput = row.querySelector('input[name="pronunciation_word[]"]');
                const phoneticInput = row.querySelector('input[name="pronunciation_phonetic[]"]');
                const imageInput = row.querySelector('input[name="pronunciation_image[]"]');
                const preview = row.querySelector('[data-image-preview] img');
                
                items.push({
                    word: wordInput?.value.trim() || '',
                    phonetic: phoneticInput?.value.trim() || '',
                    image: preview?.src || ''
                });
            });

            return items;
        }

        pronunciationItemsContainer.addEventListener('click', (event) => {
            if (event.target?.closest('[data-remove-item]')) {
                const item = event.target.closest('.pronunciation-item');
                item?.remove();
                if (pronunciationItemsContainer.children.length === 0 && questTypeSelect.value === 'pronunciation') {
                    addPronunciationItem();
                }
            }
        });

        addPronunciationItemBtn.addEventListener('click', () => {
            addPronunciationItem();
        });

        questTypeSelect.addEventListener('change', toggleQuestContentFields);
        addReadingQuestionBtn.addEventListener('click', () => addReadingQuestion());
        readingQuestionsContainer.addEventListener('click', (event) => {
            if (event.target?.closest('[data-remove-reading-question]')) {
                const questionBlock = event.target.closest('.reading-question');
                questionBlock?.remove();
                if (readingQuestionsContainer.children.length === 0 && questTypeSelect.value === 'reading') {
                    addReadingQuestion();
                }
            }
        });

        function addReadingQuestion(question = '', options = ['', '', '', ''], answer = 'A', questionDifficulty = difficultySelect.value || 'medium') {
            if (!Array.isArray(options) || options.length < 4) {
                options = ['', '', '', ''];
            }

            const item = document.createElement('div');
            item.className = 'reading-question border border-gray-600 rounded-xl p-4 space-y-4';
            item.innerHTML = `
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-white">Comprehension Question</p>
                    <button type="button" class="text-xs text-red-400 hover:text-red-300" data-remove-reading-question>Remove</button>
                </div>
                <div>
                    <label class="block text-xs font-medium text-white mb-1">Question Prompt</label>
                    <textarea rows="2" data-question-text class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white placeholder-gray-400" placeholder="e.g., What is the main idea of the passage?"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" data-question-options>
                    ${['A', 'B', 'C', 'D'].map(letter => `
                        <div>
                            <label class="block text-xs font-medium text-white mb-1">Option ${letter}</label>
                            <input type="text" data-question-option data-option-letter="${letter}" class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white placeholder-gray-400" placeholder="Answer choice ${letter}">
                        </div>
                    `).join('')}
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-white mb-1">Difficulty</label>
                        <select data-question-difficulty class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white">
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-white mb-1">Correct Answer</label>
                        <select data-question-answer class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-800 text-white">
                            <option value="A">Option A</option>
                            <option value="B">Option B</option>
                            <option value="C">Option C</option>
                            <option value="D">Option D</option>
                        </select>
                    </div>
                </div>
            `;

            readingQuestionsContainer.appendChild(item);
            item.querySelector('[data-question-text]').value = question;
            item.querySelector('[data-question-difficulty]').value = questionDifficulty;
            const optionInputs = item.querySelectorAll('[data-question-option]');
            optionInputs.forEach((input, idx) => {
                input.value = options[idx] ?? '';
            });
            item.querySelector('[data-question-answer]').value = answer;
        }

        function buildReadingContent() {
            const passage = readingPassageInput.value.trim();
            if (!passage) {
                alert('Please provide a reading passage.');
                return null;
            }

            const questionBlocks = readingQuestionsContainer.querySelectorAll('.reading-question');
            if (questionBlocks.length === 0) {
                alert('Add at least one comprehension question.');
                return null;
            }

            const readingQuestions = [];
            for (const block of questionBlocks) {
                const prompt = block.querySelector('[data-question-text]')?.value.trim() || '';
                if (!prompt) {
                    alert('Each question needs a prompt.');
                    return null;
                }

                const optionInputs = block.querySelectorAll('[data-question-option]');
                const options = Array.from(optionInputs).map(input => input.value.trim());
                if (options.some(option => !option)) {
                    alert('Please fill in all answer choices for every question.');
                    return null;
                }

                const answer = block.querySelector('[data-question-answer]')?.value || 'A';
                const answerIndex = answer.charCodeAt(0) - 65;
                if (!options[answerIndex]) {
                    alert('Select a correct answer that matches a provided option.');
                    return null;
                }

                const questionDifficulty = block.querySelector('[data-question-difficulty]')?.value || difficultySelect.value || 'medium';

                readingQuestions.push({
                    question: prompt,
                    options,
                    answer,
                    difficulty: questionDifficulty
                });
            }

            return {
                passage,
                questions: readingQuestions
            };
        }

        toggleQuestContentFields();

        // Handle form submission
        questUploadForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (questTypeSelect.value === 'pronunciation') {
                const pronunciationItems = collectPronunciationItems().filter(item => item.word || item.phonetic);

                if (pronunciationItems.length === 0) {
                    alert('Please add at least one word with its phonetic spelling for pronunciation practice.');
                    return;
                }

                const hasIncompleteItem = pronunciationItems.some(item => !item.word || !item.phonetic);
                if (hasIncompleteItem) {
                    alert('Each pronunciation item needs both a word and a phonetic spelling.');
                    return;
                }

                questContentTextarea.value = JSON.stringify(pronunciationItems);
            } else if (questTypeSelect.value === 'reading') {
                const readingContent = buildReadingContent();
                if (!readingContent) {
                    return;
                }

                questContentTextarea.value = JSON.stringify(readingContent);
            } else if (questTypeSelect.value === 'mixed') {
                const pronunciationItems = collectPronunciationItems().filter(item => item.word || item.phonetic);

                if (pronunciationItems.length === 0) {
                    alert('Please add at least one word with its phonetic spelling for pronunciation practice.');
                    return;
                }

                const hasIncompleteItem = pronunciationItems.some(item => !item.word || !item.phonetic);
                if (hasIncompleteItem) {
                    alert('Each pronunciation item needs both a word and a phonetic spelling.');
                    return;
                }

                const readingContent = buildReadingContent();
                if (!readingContent) {
                    return;
                }

                questContentTextarea.value = JSON.stringify({
                    pronunciation_items: pronunciationItems,
                    reading_passage: readingContent.passage,
                    reading_questions: readingContent.questions
                });
            } else if (questTypeSelect.value === 'pdf') {
                // PDF quests are provided via file upload; keep quest_content as a valid JSON string.
                // Backend will extract PDF text and override content.
                questContentTextarea.value = JSON.stringify({});
            }

            const formData = new FormData(this);
            
            // Collect all image files from pronunciation items
            const imageInputs = document.querySelectorAll('input[name="pronunciation_image[]"]');
            imageInputs.forEach((input, index) => {
                if (input.files && input.files[0]) {
                    formData.append(`pronunciation_images_${index}`, input.files[0]);
                }
            });
            
            const generateBtn = document.getElementById('generateBtn');
            
            // Show loading state
            generateBtn.disabled = true;
            generateBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Creating...';
            
            try {
                const response = await fetch(`/teacher/classes/{{ $classroom->id }}/generate-quest`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const raw = await response.text();
                let result;
                try {
                    result = JSON.parse(raw);
                } catch (e) {
                    throw new Error(`Unexpected response: ${raw.substring(0, 120)}`);
                }
                
                if (result.success) {
                    closeQuestModal();
                    showQuestToast(result.quest);
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    alert('Error creating quest: ' + result.message);
                }
            } catch (error) {
                alert('Error creating quest: ' + error.message);
            } finally {
                // Reset button state
                generateBtn.disabled = false;
                generateBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></svg> Create Quest';
            }
        });
    </script>

    <!-- Quest Content JavaScript -->
    <script>
        // Quest Content Modal Functions
        function showQuestContent(questId) {
            document.getElementById('questContentModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Show loading state
            document.getElementById('questContentLoading').classList.remove('hidden');
            document.getElementById('questContentDetails').classList.add('hidden');
            
            // Fetch quest content
            fetch(`/teacher/classes/{{ $classroom->id }}/quests/${questId}`)
                .then(response => response.json())
                .then(data => {
                    displayQuestContent(data.quest);
                })
                .catch(error => {
                    console.error('Error loading quest content:', error);
                    document.getElementById('questContentLoading').classList.add('hidden');
                    document.getElementById('questContentDetails').classList.remove('hidden');
                    document.getElementById('questContentTitle').textContent = 'Error Loading Quest';
                    document.getElementById('questContentDescription').textContent = 'Could not load quest content. Please try again.';
                });
        }

        function closeQuestContentModal() {
            document.getElementById('questContentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function displayQuestContent(quest) {
            // Update header
            document.getElementById('questContentTitle').textContent = quest.title;
            document.getElementById('questContentMeta').textContent = `${quest.type} Quest · Created ${quest.created_at}`;
            
            // Update description
            document.getElementById('questContentDescription').textContent = quest.description || 'No description available.';
            
            // Update quest info
            document.getElementById('questContentDifficulty').textContent = quest.difficulty;
            document.getElementById('questContentTime').textContent = quest.estimated_time;
            document.getElementById('questContentReward').textContent = quest.reward_points + ' XP';
            
            // Clear previous content
            document.getElementById('pronunciationExercises').innerHTML = '';
            document.getElementById('readingExercises').innerHTML = '';
            
            // Always hide sections first, then show relevant ones
            document.getElementById('pronunciationSection').classList.add('hidden');
            document.getElementById('readingSection').classList.add('hidden');
            
            // Show pronunciation exercises if available
            if (quest.content && quest.content.pronunciation_exercises && quest.content.pronunciation_exercises.length > 0) {
                document.getElementById('pronunciationSection').classList.remove('hidden');
                const pronContainer = document.getElementById('pronunciationExercises');
                
                quest.content.pronunciation_exercises.forEach((exercise, index) => {
                    const exerciseEl = document.createElement('div');
                    exerciseEl.className = 'bg-gray-50 dark:bg-gray-700 rounded-lg p-4';
                    exerciseEl.innerHTML = `
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Pronunciation Exercise ${index + 1}</h4>
                            <span class="text-xs px-2 py-1 rounded-full ${getDifficultyColor(exercise.difficulty)}">${exercise.difficulty}</span>
                        </div>
                        <p class="text-lg font-medium text-gray-900 dark:text-white mb-1">${exercise.word}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">${exercise.phonetic}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 italic">"${exercise.practice_sentence}"</p>
                    `;
                    pronContainer.appendChild(exerciseEl);
                });
            }
            
            // Show reading exercises if available
            if (quest.content && quest.content.reading_exercises && quest.content.reading_exercises.length > 0) {
                document.getElementById('readingSection').classList.remove('hidden');
                const readingContainer = document.getElementById('readingExercises');
                readingContainer.innerHTML = '';

                if (quest.content.reading_passage) {
                    const passageEl = document.createElement('div');
                    passageEl.className = 'bg-white dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mb-4';
                    passageEl.innerHTML = `
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/60 dark:text-green-200">Reading Passage</span>
                            <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Read before answering</span>
                        </div>
                        <p class="text-gray-800 dark:text-gray-100 leading-relaxed whitespace-pre-line">${quest.content.reading_passage}</p>
                    `;
                    readingContainer.appendChild(passageEl);
                }

                quest.content.reading_exercises.forEach((exercise, index) => {
                    const exerciseEl = document.createElement('div');
                    exerciseEl.className = 'bg-gray-50 dark:bg-gray-700 rounded-lg p-4';
                    exerciseEl.innerHTML = `
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reading Question ${index + 1}</h4>
                            <span class="text-xs px-2 py-1 rounded-full ${getDifficultyColor(exercise.difficulty)}">${exercise.difficulty}</span>
                        </div>
                        <p class="text-gray-900 dark:text-white mb-3">${exercise.question}</p>
                        <div class="space-y-2">
                            ${exercise.options.map((option, i) => `
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="question_${index}" value="${String.fromCharCode(65 + i)}" class="text-indigo-600">
                                    <span class="text-gray-700 dark:text-gray-300">${String.fromCharCode(65 + i)}. ${option}</span>
                                </label>
                            `).join('')}
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <strong>Answer:</strong> ${exercise.answer}
                            </p>
                        </div>
                    `;
                    readingContainer.appendChild(exerciseEl);
                });
            }
            
            // Hide loading and show content
            document.getElementById('questContentLoading').classList.add('hidden');
            document.getElementById('questContentDetails').classList.remove('hidden');
        }

        function getDifficultyColor(difficulty) {
            switch(difficulty?.toLowerCase()) {
                case 'easy': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                case 'medium': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                case 'hard': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                default: return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
            }
        }

        // Delete quest function
        const questDeleteModal = document.getElementById('questDeleteModal');
        const questDeleteTitle = document.getElementById('questDeleteTitle');
        const questDeleteReward = document.getElementById('questDeleteReward');
        const questDeleteDifficulty = document.getElementById('questDeleteDifficulty');
        const cancelQuestDeleteBtn = document.getElementById('cancelQuestDelete');
        const confirmQuestDeleteBtn = document.getElementById('confirmQuestDelete');
        let questDeleteState = { id: null, button: null, element: null, title: '', reward: '', difficulty: '' };

        function deleteQuest(questId, event) {
            event.stopPropagation();

            const button = event.currentTarget;
            const questElement = button.closest('li');

            questDeleteState = {
                id: questId,
                button,
                element: questElement,
                title: button.dataset.questTitle || questElement?.querySelector('h4')?.textContent?.trim() || 'this quest',
                reward: button.dataset.questReward || '',
                difficulty: button.dataset.questDifficulty || ''
            };

            questDeleteTitle.textContent = questDeleteState.title;
            questDeleteReward.textContent = questDeleteState.reward || '—';
            questDeleteDifficulty.textContent = questDeleteState.difficulty ? `${questDeleteState.difficulty} mode` : '—';

            questDeleteModal.classList.remove('hidden');
            questDeleteModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeQuestDeleteModal() {
            questDeleteModal.classList.add('hidden');
            questDeleteModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
            confirmQuestDeleteBtn.disabled = false;
            confirmQuestDeleteBtn.innerHTML = 'Yes, delete quest';
        }

        function confirmQuestDeletion() {
            if (!questDeleteState.id) return;

            confirmQuestDeleteBtn.disabled = true;
            confirmQuestDeleteBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin inline-block mr-2"></div> Purging...';

            fetch(`/teacher/classes/{{ $classroom->id }}/quests/${questDeleteState.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to delete quest');
                }
                return response.json();
            })
            .then(data => {
                const questElement = questDeleteState.element;
                if (questElement) {
                    questElement.style.transition = 'opacity 0.3s, transform 0.3s';
                    questElement.style.opacity = '0';
                    questElement.style.transform = 'translateX(20px)';

                    setTimeout(() => {
                        questElement.remove();
                        showNotification('Quest deleted successfully', 'success');
                        updateQuestCount();
                    }, 300);
                } else {
                    showNotification('Quest deleted successfully', 'success');
                }
                closeQuestDeleteModal();
            })
            .catch(error => {
                console.error('Error deleting quest:', error);
                showNotification('Failed to delete quest. Please try again.', 'error');
                confirmQuestDeleteBtn.disabled = false;
                confirmQuestDeleteBtn.innerHTML = 'Yes, delete quest';
            });
        }

        cancelQuestDeleteBtn.addEventListener('click', closeQuestDeleteModal);
        confirmQuestDeleteBtn.addEventListener('click', confirmQuestDeletion);
        questDeleteModal.addEventListener('click', (event) => {
            if (event.target === questDeleteModal) {
                closeQuestDeleteModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !questDeleteModal.classList.contains('hidden')) {
                closeQuestDeleteModal();
            }
        });

        // Show notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Update quest count
        function updateQuestCount() {
            const questCount = document.querySelectorAll('#questLog li').length;
            const questHeader = document.querySelector('#questLog h3');
            if (questHeader && questCount === 0) {
                questHeader.innerHTML = 'Quest Log <span class="text-sm text-gray-500">(No quests yet)</span>';
            }
        }
    </script>
</body>
</html>

