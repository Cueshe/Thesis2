<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>English Pronunciation Tutor - Q2L</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --brand-primary: #2563eb;
            --brand-secondary: #3b82f6;
            --brand-accent: #60a5fa;
            --page-bg: #0a0f25;
            --card-bg: rgba(13, 18, 37, 0.95);
            --surface-border: rgba(37, 99, 235, 0.25);
            --text-primary: #f1f5ff;
            --text-muted: rgba(255,255,255,0.6);
            --input-bg: rgba(23, 31, 60, 0.8);
            --input-border: rgba(37, 99, 235, 0.3);
            --input-ring: rgba(37, 99, 235, 0.4);
            --surface-muted: rgba(18, 24, 48, 0.7);
            --surface-muted-strong: rgba(12, 16, 35, 0.9);
        }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: var(--text-primary);
            background: var(--page-bg);
        }
        .dashboard-card {
            background: var(--card-bg);
            border: 1px solid var(--surface-border);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 35px 80px -45px rgba(8, 10, 25, 0.85);
        }
        .pronunciation-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            gap: 1.5rem;
        }
        .pronunciation-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .pronunciation-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .pronunciation-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .practice-column,
        .coach-column {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .practice-stack,
        .practice-list-container,
        .coach-card,
        .gamified-stats-container > div {
            background: var(--surface-muted-strong);
            border: 1px solid var(--surface-border);
            border-radius: 1.5rem;
            padding: 1.5rem;
            box-shadow: 0 20px 45px -30px rgba(15, 23, 42, 0.6);
        }
        @media (min-width: 1024px) {
            .pronunciation-content {
                display: grid;
                grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr);
                gap: 2rem;
                align-items: start;
            }
        }
        .practice-modes {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .practice-mode-btn {
            padding: 0.75rem 1.5rem;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 0.9rem;
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .practice-mode-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .practice-mode-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        .practice-mode-btn.active {
            background: #2563eb;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateY(-2px) scale(1.01);
        }
        .practice-item-container {
            background: rgba(37, 99, 235, 0.15);
            border: 1px solid rgba(37, 99, 235, 0.4);
            border-radius: 1.35rem;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 25px 65px -40px rgba(19, 17, 58, 0.9);
        }
        .glass-pill {
            display: inline-flex;
            flex-direction: column;
            gap: 0.2rem;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);
        }
        .practice-item-text {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .practice-item-phonetic {
            font-size: 1.125rem;
            color: var(--text-muted);
            font-style: italic;
        }
        .practice-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 1.75rem;
            border: none;
            border-radius: 0.85rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 30px -15px rgba(8, 9, 20, 0.8);
        }
        .action-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .action-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        .action-btn:active {
            transform: translateY(0);
        }
        .play-btn {
            background: #2563eb;
            color: white;
        }
        .record-btn {
            background: #dc2626;
            color: white;
        }
        .stop-btn {
            background: #f59e0b;
            color: #1b122f;
        }
        .recording-status {
            text-align: center;
            padding: 1rem;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 0.75rem;
        }
        .recording-dot {
            width: 12px;
            height: 12px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
            display: inline-block;
            margin-right: 0.5rem;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }
        .live-transcription {
            margin-top: 1.25rem;
            padding: 1.75rem;
            background: rgba(37, 99, 235, 0.12);
            border: 1px solid rgba(37, 99, 235, 0.4);
            border-radius: 0.85rem;
            text-align: center;
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .live-transcription-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        .live-transcription-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--brand-primary);
            min-height: 2rem;
            word-wrap: break-word;
            transition: all 0.2s ease;
        }
        .live-transcription-text:empty::before {
            content: 'Listening...';
            color: var(--text-muted);
            font-style: italic;
        }
        .feedback-section {
            background: rgba(8, 12, 28, 0.85);
            border: 1px solid rgba(37, 99, 235, 0.25);
            border-radius: 1.25rem;
            padding: 1.75rem;
            box-shadow: 0 25px 50px -35px rgba(9, 10, 32, 0.9);
        }
        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .accuracy-score {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brand-primary);
        }
        .practice-list-container {
            background: var(--surface-muted);
            border: 1px solid var(--surface-border);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        .practice-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.75rem;
        }
        .practice-item {
            padding: 0.75rem;
            background: var(--card-bg);
            border: 1px solid var(--surface-border);
            border-radius: 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .practice-item.active {
            background: var(--brand-primary);
            color: white;
        }
        /* Gamified Stats */
        .gamified-stats-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .level-progress-section {
            background: rgba(37, 99, 235, 0.25);
            border: 2px solid rgba(37, 99, 235, 0.45);
            border-radius: 1.5rem;
            padding: 1.75rem;
            box-shadow: 0 25px 55px -35px rgba(37, 99, 235, 0.8);
            position: relative;
            overflow: hidden;
        }
        .level-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .level-badge {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .level-icon {
            font-size: 3rem;
            animation: rotate 3s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .level-info {
            display: flex;
            flex-direction: column;
        }
        .level-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }
        .level-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--brand-primary);
            line-height: 1;
            transition: transform 0.5s ease;
        }
        .xp-display {
            text-align: right;
        }
        .xp-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
        }
        .xp-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #f59e0b;
        }
        .progress-bar-container {
            position: relative;
        }
        .progress-bar-bg {
            width: 100%;
            height: 1rem;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            overflow: hidden;
            position: relative;
        }
        .progress-bar-fill {
            height: 100%;
            background: #2563eb;
            border-radius: 0.5rem;
            transition: width 0.5s ease;
            position: relative;
            overflow: hidden;
        }
        .progress-bar-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .progress-text {
            text-align: center;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }
        .stat-card {
            background: rgba(15, 23, 42, 0.1);
            border: 2px solid rgba(148, 163, 184, 0.2);
            border-radius: 1.25rem;
            padding: 1.35rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        .stat-card:hover::before {
            left: 100%;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        .stat-card-primary {
            border-color: rgba(37, 99, 235, 0.4);
            background: rgba(37, 99, 235, 0.1);
        }
        .stat-card-success {
            border-color: rgba(34, 197, 94, 0.4);
            background: rgba(34, 197, 94, 0.1);
        }
        .stat-card-warning {
            border-color: rgba(245, 158, 11, 0.4);
            background: rgba(245, 158, 11, 0.1);
        }
        .stat-card-info {
            border-color: rgba(59, 130, 246, 0.4);
            background: rgba(59, 130, 246, 0.1);
        }
        .stat-icon {
            font-size: 2rem;
            line-height: 1;
        }
        .stat-content {
            flex: 1;
        }
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }
        .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.25rem;
        }
        .achievements-section {
            background: var(--surface-muted);
            border: 2px solid var(--surface-border);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        .achievements-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        .achievements-icon {
            font-size: 1.5rem;
        }
        .achievements-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .achievement-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f59e0b;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: white;
            animation: achievementPop 0.5s ease;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }
        @keyframes achievementPop {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .achievement-placeholder {
            color: var(--text-muted);
            font-style: italic;
            font-size: 0.875rem;
        }
        .hidden {
            display: none !important;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--brand-primary);
            text-decoration: none;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .language-nav {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 0.5rem;
            background: var(--surface-muted);
            border-radius: 1rem;
        }
        .language-nav-item {
            flex: 1;
            padding: 1rem;
            text-align: center;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .language-nav-item.active {
            background: var(--brand-primary);
            color: white;
        }
        .language-nav-item:not(.active) {
            color: var(--text-muted);
        }
        .language-nav-item:not(.active):hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <a href="{{ route('student.dashboard') }}" class="back-link">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Dashboard
        </a>

        <div class="language-nav">
            <a href="{{ route('pronunciation.tutor.filipino') }}" class="language-nav-item">
                🇵🇭 Filipino
            </a>
            <a href="{{ route('pronunciation.tutor.english') }}" class="language-nav-item active">
                🇺🇸 English
            </a>
        </div>

        <div class="dashboard-card">
            <div class="pronunciation-header">
                <div>
                    <h2 class="pronunciation-title">English Pronunciation Tutor</h2>
                    <p class="pronunciation-subtitle">Master English pronunciation with AI-powered coaching. Practice words, phrases, and sentences with instant feedback to improve your accent and clarity.</p>
                </div>
            </div>

            <div class="pronunciation-content">
                <div class="practice-column practice-stack">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)] mb-1">Practice difficulty</p>
                            <div class="practice-modes">
                                <button class="practice-mode-btn active" data-mode="word">Word Practice</button>
                                <button class="practice-mode-btn" data-mode="phrase">Phrase Practice</button>
                                <button class="practice-mode-btn" data-mode="sentence">Sentence Practice</button>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)]">Current streak</p>
                            <p class="text-2xl font-black text-[color:var(--brand-primary)]" id="streakCount">0</p>
                        </div>
                    </div>

                    <div class="practice-item-container">
                        <div class="practice-item-display">
                            <div class="practice-item-text" id="practiceText">Loading...</div>
                            <div class="practice-item-phonetic" id="practicePhonetic"></div>
                        </div>
                        <div class="flex gap-4 justify-center mt-6">
                            <div class="glass-pill">
                                <span class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Language</span>
                                <span class="font-semibold" id="languageBadge">EN</span>
                            </div>
                            <div class="glass-pill">
                                <span class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Mode</span>
                                <span class="font-semibold" id="modeBadge">Word</span>
                            </div>
                        </div>
                        <div class="practice-actions mt-8">
                            <button id="playReferenceBtn" class="action-btn play-btn">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z" />
                                </svg>
                                <span>Listen</span>
                            </button>
                            <button id="startRecordingBtn" class="action-btn record-btn">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                                </svg>
                                <span>Record</span>
                            </button>
                            <button id="stopRecordingBtn" class="action-btn stop-btn hidden">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                                </svg>
                                <span>Stop</span>
                            </button>
                        </div>
                    </div>

                    <div id="recordingStatus" class="recording-status hidden">
                        <div class="recording-indicator">
                            <span class="recording-dot"></span>
                            <span>Recording...</span>
                        </div>
                    </div>

                    <div id="liveTranscription" class="live-transcription hidden">
                        <div class="live-transcription-label">You're saying:</div>
                        <div class="live-transcription-text" id="liveTranscriptionText">Listening...</div>
                    </div>

                    <div id="feedbackSection" class="feedback-section hidden">
                        <div class="feedback-header">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)]">Pronunciation feedback</p>
                                <h3 class="text-xl font-semibold">Coach Notes</h3>
                            </div>
                            <div class="accuracy-score" id="accuracyScore">0%</div>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-item">
                                <div class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Your attempt</div>
                                <div class="text-base font-medium" id="userPronunciation">-</div>
                            </div>
                            <div class="feedback-item">
                                <div class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Reference</div>
                                <div class="text-base font-medium" id="correctPronunciation">-</div>
                            </div>
                            <div class="feedback-tips mt-4">
                                <div class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)] mb-2">Tips</div>
                                <ul id="tipsList" class="list-disc list-inside space-y-1"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="practice-list-container">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)]">Practice list</p>
                                <h3 class="text-lg font-bold">Random Practice</h3>
                            </div>
                            <button id="randomWordBtn" class="action-btn play-btn" style="padding: 0.5rem 1rem;">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                <span>New Word</span>
                            </button>
                        </div>
                        <p class="text-sm text-[color:var(--text-muted)]">Get a new English word to practice from our AI-powered selection.</p>
                    </div>
                </div>

                <aside class="coach-column space-y-6">
                    <div class="coach-card">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.4em] text-[color:var(--text-muted)]">Tips</p>
                                <h3 class="text-lg font-semibold">English Coach</h3>
                            </div>
                            <div class="glass-pill text-sm">Daily practice</div>
                        </div>
                        <ul class="space-y-3 text-sm text-[color:var(--text-muted)]">
                            <li class="flex gap-3">
                                <span class="text-lg">🎧</span>
                                <div>
                                    <p class="font-semibold text-[color:var(--text-primary)]">Listen carefully first.</p>
                                    <p>Pay attention to stress patterns and intonation in English.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-lg">📝</span>
                                <div>
                                    <p class="font-semibold text-[color:var(--text-primary)]">Break down difficult words.</p>
                                    <p>Practice syllable by syllable for better pronunciation.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-lg">⚡</span>
                                <div>
                                    <p class="font-semibold text-[color:var(--text-primary)]">Focus on common sounds.</p>
                                    <p>Master tricky English sounds like 'th', 'r', and 'v'.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="gamified-stats-container">
                        <div class="level-progress-section">
                            <div class="level-header">
                                <div class="level-badge">
                                    <div class="level-icon">⭐</div>
                                    <div class="level-info">
                                        <div class="level-label">Level</div>
                                        <div class="level-number" id="userLevel">1</div>
                                    </div>
                                </div>
                                <div class="xp-display">
                                    <div class="xp-label">XP</div>
                                    <div class="xp-value" id="userXP">0</div>
                                </div>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" id="levelProgressBar" style="width: 0%"></div>
                                </div>
                                <div class="progress-text">
                                    <span id="currentXP">0</span> / <span id="nextLevelXP">100</span> XP
                                </div>
                            </div>
                        </div>

                        <div class="stats-grid">
                            <div class="stat-card stat-card-primary">
                                <div class="stat-icon">🎯</div>
                                <div class="stat-content">
                                    <div class="stat-value" id="totalPracticed">0</div>
                                    <div class="stat-label">Words Practiced</div>
                                </div>
                            </div>
                            <div class="stat-card stat-card-success">
                                <div class="stat-icon">📊</div>
                                <div class="stat-content">
                                    <div class="stat-value" id="averageAccuracy">0%</div>
                                    <div class="stat-label">Avg Accuracy</div>
                                </div>
                            </div>
                            <div class="stat-card stat-card-warning">
                                <div class="stat-icon">🔥</div>
                                <div class="stat-content">
                                    <div class="stat-value" id="streakCount">0</div>
                                    <div class="stat-label">Day Streak</div>
                                </div>
                            </div>
                            <div class="stat-card stat-card-info">
                                <div class="stat-icon">🏆</div>
                                <div class="stat-content">
                                    <div class="stat-value" id="perfectCount">0</div>
                                    <div class="stat-label">Perfect Scores</div>
                                </div>
                            </div>
                        </div>

                        <div class="achievements-section">
                            <div class="achievements-header">
                                <span class="achievements-icon">🏅</span>
                                <span>Recent Achievements</span>
                            </div>
                            <div class="achievements-list" id="achievementsList">
                                <div class="achievement-placeholder">Keep practicing to unlock achievements!</div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let currentMode = 'word';
            let currentLanguage = 'en-US';
            let currentIndex = 0;
            let practiceData = { 'en-US': { word: [], phrase: [], sentence: [] } };
            let synthesis = window.speechSynthesis;
            let currentAudio = null;
            let recognition = null;
            let isRecording = false;
            let practiceStats = {
                totalPracticed: 0,
                totalAccuracy: 0,
                attempts: 0,
                streak: 0,
                xp: 0,
                level: 1,
                perfectCount: 0
            };
            
            // XP and Level system
            const XP_PER_PRACTICE = 10;
            const XP_PER_PERFECT = 50;
            const XP_PER_GOOD = 30;
            const XP_LEVEL_MULTIPLIER = 100;
            
            // English word pools
            const englishWords = {
                word: [
                    { text: 'Hello', phonetic: '/həˈloʊ/', tips: ['Focus on the "h" sound at the beginning', 'The "o" should be long and clear'] },
                    { text: 'World', phonetic: '/wɜːrld/', tips: ['Pronounce the "r" clearly', 'The "ld" should be soft'] },
                    { text: 'Beautiful', phonetic: '/ˈbjuːtɪfəl/', tips: ['Emphasize the first syllable', 'The "t" is soft, almost like "d"'] },
                    { text: 'Pronunciation', phonetic: '/prəˌnʌnsiˈeɪʃən/', tips: ['Break it into syllables: pro-nun-ci-a-tion', 'Stress the third syllable'] },
                    { text: 'Education', phonetic: '/ˌedʒuˈkeɪʃən/', tips: ['The "e" sounds like "eh"', 'Stress the second syllable'] },
                    { text: 'Computer', phonetic: '/kəmˈpjuːtər/', tips: ['The "u" sounds like "you"', 'Stress the second syllable'] },
                    { text: 'Language', phonetic: '/ˈlæŋɡwɪdʒ/', tips: ['The "ng" is one sound', 'The "g" is soft'] },
                    { text: 'Practice', phonetic: '/ˈpræktɪs/', tips: ['The "a" sounds like "ah"', 'Stress the first syllable'] },
                    { text: 'Excellent', phonetic: '/ˈeksələnt/', tips: ['Stress the first syllable', 'The "x" sounds like "ks"'] },
                    { text: 'Wonderful', phonetic: '/ˈwʌndərfəl/', tips: ['Stress the first syllable', 'The "o" sounds like "u"'] },
                    { text: 'Important', phonetic: '/ɪmˈpɔːrtənt/', tips: ['Stress the second syllable', 'The "t" at the end is clear'] },
                    { text: 'Different', phonetic: '/ˈdɪfərənt/', tips: ['Stress the first syllable', 'The "e" in the middle is soft'] },
                    { text: 'Together', phonetic: '/təˈɡeðər/', tips: ['Stress the second syllable', 'The "th" is soft'] },
                    { text: 'Remember', phonetic: '/rɪˈmembər/', tips: ['Stress the second syllable', 'The "er" at the end is clear'] },
                    { text: 'Understand', phonetic: '/ˌʌndərˈstænd/', tips: ['Stress the last syllable', 'Break into: un-der-stand'] },
                    { text: 'Question', phonetic: '/ˈkwestʃən/', tips: ['Stress the first syllable', 'The "tion" sounds like "shun"'] },
                    { text: 'Answer', phonetic: '/ˈænsər/', tips: ['Stress the first syllable', 'The "w" is silent'] },
                    { text: 'Student', phonetic: '/ˈstuːdənt/', tips: ['Stress the first syllable', 'The "u" is long'] },
                    { text: 'Teacher', phonetic: '/ˈtiːtʃər/', tips: ['Stress the first syllable', 'The "ch" is clear'] },
                    { text: 'Library', phonetic: '/ˈlaɪbreri/', tips: ['Stress the first syllable', 'The "r" is pronounced'] }
                ],
                phrase: [
                    { text: 'How are you?', phonetic: '/haʊ ɑːr juː/', tips: ['Connect "how" and "are" smoothly', 'The "you" should be clear'] },
                    { text: 'Thank you very much', phonetic: '/θæŋk juː ˈveri mʌtʃ/', tips: ['The "th" in "thank" is soft', 'Emphasize "very"'] },
                    { text: 'Nice to meet you', phonetic: '/naɪs tuː miːt juː/', tips: ['The "t" in "to" is soft', 'Connect words smoothly'] },
                    { text: 'I love learning', phonetic: '/aɪ lʌv ˈlɜːrnɪŋ/', tips: ['The "I" sounds like "eye"', 'Stress "learning"'] },
                    { text: 'Good morning', phonetic: '/ɡʊd ˈmɔːrnɪŋ/', tips: ['The "oo" in "good" is short', 'Stress "morning"'] },
                    { text: 'Have a nice day', phonetic: '/hæv ə naɪs deɪ/', tips: ['Connect words naturally', 'Stress "nice" and "day"'] },
                    { text: 'See you later', phonetic: '/siː juː ˈleɪtər/', tips: ['The "see" is long', 'Stress "later"'] },
                    { text: 'What is your name?', phonetic: '/wʌt ɪz jʊr neɪm/', tips: ['Connect "what" and "is"', 'Stress "name"'] },
                    { text: 'Where are you from?', phonetic: '/wer ɑːr juː frʌm/', tips: ['The "wh" sounds like "w"', 'Stress "from"'] },
                    { text: 'How do you do?', phonetic: '/haʊ duː juː duː/', tips: ['Both "do" sounds are the same', 'Keep it formal'] },
                    { text: 'I am fine', phonetic: '/aɪ æm faɪn/', tips: ['Connect "I" and "am"', 'The "fine" is clear'] },
                    { text: 'You are welcome', phonetic: '/juː ɑːr ˈwelkəm/', tips: ['Stress "welcome"', 'Connect words smoothly'] }
                ],
                sentence: [
                    { text: 'I am learning English pronunciation.', phonetic: '/aɪ æm ˈlɜːrnɪŋ ˈɪŋɡlɪʃ prəˌnʌnsiˈeɪʃən/', tips: ['Speak slowly and clearly', 'Pause between words if needed'] },
                    { text: 'Practice makes perfect.', phonetic: '/ˈpræktɪs meɪks ˈpɜːrfɪkt/', tips: ['Emphasize "practice" and "perfect"', 'Keep a steady rhythm'] },
                    { text: 'The more you practice, the better you become.', phonetic: '/ðə mɔːr juː ˈpræktɪs ðə ˈbetər juː bɪˈkʌm/', tips: ['Use rising and falling intonation', 'Connect words naturally'] },
                    { text: 'Learning a new language is exciting.', phonetic: '/ˈlɜːrnɪŋ ə nuː ˈlæŋɡwɪdʒ ɪz ɪkˈsaɪtɪŋ/', tips: ['Stress "learning" and "exciting"', 'Pause after "language"'] },
                    { text: 'Can you help me please?', phonetic: '/kæn juː help miː pliːz/', tips: ['Stress "help" and "please"', 'The "can" is clear'] },
                    { text: 'I would like to learn more.', phonetic: '/aɪ wʊd laɪk tuː lɜːrn mɔːr/', tips: ['Connect "would" and "like"', 'Stress "learn" and "more"'] },
                    { text: 'What time is it now?', phonetic: '/wʌt taɪm ɪz ɪt naʊ/', tips: ['Stress "time" and "now"', 'Connect words smoothly'] },
                    { text: 'Where is the library?', phonetic: '/wer ɪz ðə ˈlaɪbreri/', tips: ['Stress "library"', 'The "the" is soft'] }
                ]
            };
            
            // Initialize with English words
            practiceData['en-US'] = englishWords;
            
            // DOM elements
            const startRecordingBtn = document.getElementById('startRecordingBtn');
            const stopRecordingBtn = document.getElementById('stopRecordingBtn');
            const recordingStatus = document.getElementById('recordingStatus');
            const liveTranscription = document.getElementById('liveTranscription');
            const liveTranscriptionText = document.getElementById('liveTranscriptionText');
            const feedbackSection = document.getElementById('feedbackSection');
            const userPronunciation = document.getElementById('userPronunciation');
            const correctPronunciation = document.getElementById('correctPronunciation');
            const accuracyScore = document.getElementById('accuracyScore');
            const tipsList = document.getElementById('tipsList');
            const totalPracticedEl = document.getElementById('totalPracticed');
            const averageAccuracyEl = document.getElementById('averageAccuracy');
            const streakCountEl = document.getElementById('streakCount');
            const userLevelEl = document.getElementById('userLevel');
            const userXPEl = document.getElementById('userXP');
            const currentXPEl = document.getElementById('currentXP');
            const nextLevelXPEl = document.getElementById('nextLevelXP');
            const levelProgressBar = document.getElementById('levelProgressBar');
            const perfectCountEl = document.getElementById('perfectCount');
            const achievementsList = document.getElementById('achievementsList');
            const playReferenceBtn = document.getElementById('playReferenceBtn');
            const randomWordBtn = document.getElementById('randomWordBtn');
            const practiceText = document.getElementById('practiceText');
            const practicePhonetic = document.getElementById('practicePhonetic');
            const languageBadge = document.getElementById('languageBadge');
            const modeBadge = document.getElementById('modeBadge');
            
            // Initialize practice data
            function loadPracticeItem() {
                const items = practiceData[currentLanguage][currentMode];
                if (items && items.length > 0) {
                    currentIndex = Math.floor(Math.random() * items.length);
                    const item = items[currentIndex];
                    practiceText.textContent = item.text;
                    practicePhonetic.textContent = item.phonetic;
                    languageBadge.textContent = 'EN';
                    modeBadge.textContent = currentMode.charAt(0).toUpperCase() + currentMode.slice(1);
                }
            }
            
            // Speech synthesis
            function playReference() {
                if (synthesis.speaking) {
                    synthesis.cancel();
                    return;
                }
                
                const text = practiceText.textContent;
                if (text) {
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = currentLanguage;
                    utterance.rate = 0.8;
                    synthesis.speak(utterance);
                }
            }
            
            // Speech recognition
            function initSpeechRecognition() {
                if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                    console.warn('Speech recognition not supported');
                    startRecordingBtn.disabled = true;
                    startRecordingBtn.title = 'Speech recognition not supported in your browser';
                    return null;
                }

                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                const rec = new SpeechRecognition();
                rec.continuous = false;
                rec.interimResults = true;
                rec.lang = currentLanguage;
                // Enhanced settings for better vowel recognition
                rec.maxAlternatives = 10; // Get more alternatives for better vowel analysis
                rec.serviceURI = 'wss://www.google.com/speech-api/v2';

                rec.onstart = () => {
                    console.log('Recording started');
                    isRecording = true;
                    startRecordingBtn.classList.add('hidden');
                    stopRecordingBtn.classList.remove('hidden');
                    recordingStatus.classList.remove('hidden');
                    liveTranscription.classList.remove('hidden');
                    liveTranscriptionText.textContent = 'Listening...';
                };

                rec.onresult = (event) => {
                    console.log('Speech recognition result received');
                    
                    let fullTranscript = '';
                    let hasFinal = false;
                    let allTranscripts = [];
                    let allAlternatives = [];
                    
                    for (let i = 0; i < event.results.length; i++) {
                        const result = event.results[i];
                        const transcript = result[0].transcript;
                        const confidence = result[0].confidence || 0;
                        
                        allTranscripts.push({
                            transcript: transcript,
                            isFinal: result.isFinal,
                            confidence: confidence
                        });
                        
                        // Collect alternatives for vowel analysis
                        if (result[0].alternatives && result[0].alternatives.length > 0) {
                            result[0].alternatives.forEach(alt => {
                                allAlternatives.push({
                                    transcript: alt.transcript,
                                    confidence: alt.confidence || 0
                                });
                            });
                        }
                        
                        if (result.isFinal) {
                            fullTranscript += transcript + ' ';
                            hasFinal = true;
                        } else if (i === event.results.length - 1) {
                            fullTranscript += transcript;
                        }
                    }
                    
                    const displayText = fullTranscript.trim() || 'Listening...';
                    liveTranscriptionText.textContent = displayText;
                    
                    if (hasFinal) {
                        let finalText = '';
                        for (let i = 0; i < event.results.length; i++) {
                            if (event.results[i].isFinal) {
                                finalText += event.results[i][0].transcript + ' ';
                            }
                        }
                        const finalTranscript = finalText.trim();
                        if (finalTranscript) {
                            console.log('Final transcript:', finalTranscript);
                            console.log('All transcripts:', allTranscripts);
                            console.log('All alternatives:', allAlternatives);
                            
                            // Enhanced vowel and pronunciation analysis
                            setTimeout(() => {
                                analyzePronunciationWithVowelDetection(finalTranscript, allTranscripts, allAlternatives);
                            }, 100);
                        }
                    }
                };

                rec.onerror = (event) => {
                    console.error('Speech recognition error:', event.error);
                    stopRecording();
                };

                rec.onend = () => {
                    console.log('Recording ended');
                    stopRecording();
                };

                return rec;
            }
            
            function startRecording() {
                if (!recognition) {
                    recognition = initSpeechRecognition();
                }
                if (recognition && !isRecording) {
                    recognition.start();
                }
            }
            
            function stopRecording() {
                if (recognition && isRecording) {
                    recognition.stop();
                }
                isRecording = false;
                startRecordingBtn.classList.remove('hidden');
                stopRecordingBtn.classList.add('hidden');
                recordingStatus.classList.add('hidden');
            }
            
            function analyzePronunciationWithVowelDetection(finalTranscript, allTranscripts, allAlternatives) {
                const expectedWord = practiceText.textContent.toLowerCase().trim();
                const detectedWord = finalTranscript.toLowerCase().trim();
                
                console.log('Vowel analysis for:', detectedWord, 'Expected:', expectedWord);
                
                // Collect all possible transcriptions for analysis
                const intermediateResults = allTranscripts.filter(t => !t.isFinal);
                const finalResult = allTranscripts.find(t => t.isFinal);
                const allPossibleTranscriptions = [
                    finalTranscript,
                    ...intermediateResults.map(t => t.transcript),
                    ...allAlternatives.map(a => a.transcript)
                ].filter(t => t && t.trim().length > 0);
                
                console.log('All possible transcriptions:', allPossibleTranscriptions);
                
                let bestMatch = finalTranscript;
                let bestAccuracy = calculateWordSimilarity(detectedWord, expectedWord);
                let detectedVowelIssues = [];
                
                // Specific vowel substitution patterns for common words
                const specificVowelPatterns = {
                    'practice': [
                        { mispronounced: 'praektis', issues: ['"a" should be short "æ" not long "e"', 'Missing "c" sound'] },
                        { mispronounced: 'praktis', issues: ['Missing "c" sound', 'Vowel "a" is unclear'] },
                        { mispronounced: 'prectis', issues: ['Missing "a" sound', 'Vowel clarity issue'] },
                        { mispronounced: 'practis', issues: ['Missing "e" sound', 'Second vowel unclear'] }
                    ],
                    'beautiful': [
                        { mispronounced: 'beutiful', issues: ['"a" vowel missing', 'First vowel unclear'] },
                        { mispronounced: 'butiful', issues: ['"ea" should be "be" not "bu"', 'First vowel wrong'] }
                    ],
                    'education': [
                        { mispronounced: 'edukation', issues: ['"c" should be soft "c" not "k"', 'Vowel clarity issue'] }
                    ]
                };
                
                // Check for specific mispronunciation patterns
                if (specificVowelPatterns[expectedWord]) {
                    for (const pattern of specificVowelPatterns[expectedWord]) {
                        if (detectedWord.includes(pattern.mispronounced) || 
                            allPossibleTranscriptions.some(t => t.toLowerCase().includes(pattern.mispronounced))) {
                            console.log('Specific vowel pattern detected:', pattern.mispronounced);
                            detectedVowelIssues.push(...pattern.issues);
                            bestAccuracy = Math.max(60, bestAccuracy - 25); // Significant penalty for specific vowel errors
                            break;
                        }
                    }
                }
                
                // General vowel analysis
                if (detectedVowelIssues.length === 0) {
                    // Analyze each alternative for vowel mispronunciations
                    for (const transcription of allPossibleTranscriptions) {
                        const cleanTranscription = transcription.toLowerCase().trim();
                        const accuracy = calculateWordSimilarity(cleanTranscription, expectedWord);
                        
                        // Check for vowel-specific issues
                        const vowelIssues = detectVowelIssues(cleanTranscription, expectedWord);
                        
                        if (vowelIssues.length > 0) {
                            console.log('Vowel issues detected in:', cleanTranscription, vowelIssues);
                            detectedVowelIssues.push(...vowelIssues);
                            
                            // If this transcription has vowel issues but still matches closely,
                            // it might indicate auto-correction masking vowel problems
                            if (accuracy > 85 && accuracy < 98) {
                                console.log('Possible auto-correction masking vowel issues');
                                bestAccuracy = accuracy - 15; // Apply penalty for masked vowel issues
                                bestMatch = transcription;
                            }
                        }
                        
                        // Keep track of the best match
                        if (accuracy > bestAccuracy) {
                            bestAccuracy = accuracy;
                            bestMatch = transcription;
                        }
                    }
                    
                    // Additional check: If we got an exact match with high confidence,
                    // check if it might be hiding vowel pronunciation issues
                    if (detectedWord === expectedWord && finalResult && finalResult.confidence > 0.85) {
                        const hasVowelComplexity = expectedWord.match(/[aeiou]/gi);
                        if (hasVowelComplexity && hasVowelComplexity.length >= 2) {
                            console.log('High confidence exact match on vowel-complex word - checking for hidden issues');
                            
                            // Look for vowel substitutions in intermediate results
                            const hiddenVowelIssues = [];
                            for (const intermediate of intermediateResults) {
                                const issues = detectVowelIssues(intermediate.transcript.toLowerCase().trim(), expectedWord);
                                if (issues.length > 0) {
                                    hiddenVowelIssues.push(...issues);
                                }
                            }
                            
                            if (hiddenVowelIssues.length > 0) {
                                console.log('Hidden vowel issues detected:', hiddenVowelIssues);
                                bestAccuracy = Math.max(70, bestAccuracy - 20); // Apply penalty
                                detectedVowelIssues.push(...hiddenVowelIssues);
                            }
                        }
                    }
                }
                
                // Update feedback with detected accuracy and vowel issues
                userPronunciation.textContent = bestMatch;
                correctPronunciation.textContent = practiceText.textContent;
                accuracyScore.textContent = bestAccuracy + '%';
                
                // Color code accuracy
                if (bestAccuracy >= 95) {
                    accuracyScore.style.color = '#10b981'; // Green - Excellent
                } else if (bestAccuracy >= 85) {
                    accuracyScore.style.color = '#22c55e'; // Light green - Very Good
                } else if (bestAccuracy >= 75) {
                    accuracyScore.style.color = '#84cc16'; // Lime - Good
                } else if (bestAccuracy >= 60) {
                    accuracyScore.style.color = '#f59e0b'; // Orange - Fair
                } else if (bestAccuracy >= 40) {
                    accuracyScore.style.color = '#f97316'; // Dark orange - Needs Improvement
                } else {
                    accuracyScore.style.color = '#ef4444'; // Red - Poor
                }
                
                // Generate specific vowel feedback
                const currentItem = practiceData[currentLanguage][currentMode][currentIndex];
                tipsList.innerHTML = '';
                
                // Add vowel-specific tips
                if (detectedVowelIssues.length > 0) {
                    addTip('Vowel pronunciation issues detected:');
                    detectedVowelIssues.forEach(issue => {
                        addTip(`• ${issue}`);
                    });
                    addTip('Practice each vowel sound separately: A-E-I-O-U');
                    addTip('Listen carefully to the reference pronunciation');
                } else if (bestAccuracy < 100) {
                    addTip('Good attempt! Focus on clear vowel sounds');
                    addTip('Try to match the reference pronunciation exactly');
                } else {
                    addTip('Excellent vowel pronunciation!');
                }
                
                // Add existing tips if available
                if (currentItem && currentItem.tips) {
                    currentItem.tips.forEach(tip => {
                        addTip(tip);
                    });
                }
                
                // Show feedback section
                feedbackSection.classList.remove('hidden');
                liveTranscription.classList.add('hidden');
                
                // Update stats
                practiceStats.totalPracticed++;
                practiceStats.attempts++;
                practiceStats.totalAccuracy += bestAccuracy;
                updateStatsDisplay();
            }
            
            function detectVowelIssues(userWord, expectedWord) {
                const issues = [];
                const vowels = ['a', 'e', 'i', 'o', 'u'];
                
                console.log('Detecting vowel issues between:', userWord, 'and', expectedWord);
                
                // Find vowel positions in expected word
                const expectedVowels = [];
                for (let i = 0; i < expectedWord.length; i++) {
                    if (vowels.includes(expectedWord[i].toLowerCase())) {
                        expectedVowels.push({ char: expectedWord[i], index: i });
                    }
                }
                
                // Find vowel positions in user word
                const userVowels = [];
                for (let i = 0; i < userWord.length; i++) {
                    if (vowels.includes(userWord[i].toLowerCase())) {
                        userVowels.push({ char: userWord[i], index: i });
                    }
                }
                
                console.log('Expected vowels:', expectedVowels);
                console.log('User vowels:', userVowels);
                
                // Compare vowel patterns with position awareness
                for (let i = 0; i < Math.min(expectedVowels.length, userVowels.length); i++) {
                    const expectedVowel = expectedVowels[i].char.toLowerCase();
                    const userVowel = userVowels[i].char.toLowerCase();
                    
                    if (expectedVowel !== userVowel) {
                        issues.push(`Vowel "${expectedVowel}" sounds like "${userVowel}"`);
                    }
                }
                
                // Check for missing or extra vowels
                if (expectedVowels.length !== userVowels.length) {
                    if (userVowels.length < expectedVowels.length) {
                        issues.push('Some vowel sounds are missing or unclear');
                    } else {
                        issues.push('Extra vowel sounds detected');
                    }
                }
                
                // Specific vowel quality checks for common issues
                if (expectedWord === 'practice') {
                    // Check if "a" sound is wrong (should be short 'æ' like in 'cat')
                    if (userWord.includes('prect') || userWord.includes('prekt')) {
                        issues.push('"a" should be short "æ" sound like in "cat"');
                    }
                    // Check if "i" sound is wrong (should be short 'ɪ' like in 'sit')
                    if (userWord.includes('practeese') || userWord.includes('practees')) {
                        issues.push('"i" should be short "ɪ" sound like in "sit"');
                    }
                    // Check for missing consonants that affect vowel perception
                    if (!userWord.includes('c') && userWord.includes('pra')) {
                        issues.push('Missing "c" sound affects vowel clarity');
                    }
                }
                
                // Additional vowel pattern analysis
                const userVowelSequence = userVowels.map(v => v.char).join('').toLowerCase();
                const expectedVowelSequence = expectedVowels.map(v => v.char).join('').toLowerCase();
                
                if (userVowelSequence !== expectedVowelSequence) {
                    console.log('Vowel sequence mismatch:', userVowelSequence, 'vs', expectedVowelSequence);
                    
                    // Check for specific vowel substitutions
                    if (expectedVowelSequence.includes('ae') && userVowelSequence.includes('ee')) {
                        issues.push('"ae" sounds like "ee" - focus on short "æ" sound');
                    }
                    if (expectedVowelSequence.includes('a') && userVowelSequence.includes('e')) {
                        issues.push('"a" sounds like "e" - open your mouth more');
                    }
                    if (expectedVowelSequence.includes('i') && userVowelSequence.includes('e')) {
                        issues.push('"i" sounds like "e" - make the sound shorter');
                    }
                }
                
                console.log('Final vowel issues detected:', issues);
                return issues;
            }
            
            function addTip(tipText) {
                const li = document.createElement('li');
                li.textContent = tipText;
                tipsList.appendChild(li);
            }
            
            function analyzePronunciation(userText) {
                const referenceText = practiceText.textContent.toLowerCase().trim();
                const userTextClean = userText.toLowerCase().trim();
                
                // Normalize text for comparison
                const normalizedReference = normalizeText(referenceText);
                const normalizedUser = normalizeText(userTextClean);
                
                let accuracy = 0;
                
                if (currentMode === 'word') {
                    accuracy = calculateWordSimilarity(normalizedUser, normalizedReference);
                } else {
                    accuracy = calculatePhraseSimilarity(normalizedUser, normalizedReference);
                }
                
                // Update feedback
                userPronunciation.textContent = userText;
                correctPronunciation.textContent = practiceText.textContent;
                accuracyScore.textContent = accuracy + '%';
                
                // Color code accuracy
                if (accuracy >= 95) {
                    accuracyScore.style.color = '#10b981'; // Green - Excellent
                } else if (accuracy >= 85) {
                    accuracyScore.style.color = '#22c55e'; // Light green - Very Good
                } else if (accuracy >= 75) {
                    accuracyScore.style.color = '#84cc16'; // Lime - Good
                } else if (accuracy >= 60) {
                    accuracyScore.style.color = '#f59e0b'; // Orange - Fair
                } else if (accuracy >= 40) {
                    accuracyScore.style.color = '#f97316'; // Dark orange - Needs Improvement
                } else {
                    accuracyScore.style.color = '#ef4444'; // Red - Poor
                }
                
                // Update tips
                const currentItem = practiceData[currentLanguage][currentMode][currentIndex];
                tipsList.innerHTML = '';
                if (currentItem && currentItem.tips) {
                    currentItem.tips.forEach(tip => {
                        const li = document.createElement('li');
                        li.textContent = tip;
                        tipsList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'Keep practicing! Try to match the reference pronunciation.';
                    tipsList.appendChild(li);
                }
                
                // Show feedback section
                feedbackSection.classList.remove('hidden');
                liveTranscription.classList.add('hidden');
                
                // Update stats
                practiceStats.totalPracticed++;
                practiceStats.attempts++;
                practiceStats.totalAccuracy += accuracy;
                updateStatsDisplay();
            }
            
            // Normalize text for comparison (handles common variations but maintains accuracy)
            function normalizeText(text) {
                return text.toLowerCase()
                    .replace(/[.,!?;:'"()\[\]{}]/g, '') // Remove punctuation
                    .replace(/\s+/g, ' ') // Normalize whitespace
                    .trim()
                    // Don't normalize common words too much - we want to detect pronunciation errors
                    // Only handle obvious speech recognition artifacts
                    .replace(/\b(uh|um|er|ah)\b/g, '') // Remove filler words
                    .replace(/\s+/g, ' ') // Re-normalize whitespace after removal
                    .trim();
            }
            
            // Calculate similarity for single words (strict but fair)
            function calculateWordSimilarity(userWord, expectedWord) {
                // Perfect match = 100%
                if (userWord === expectedWord) return 100;
                
                if (expectedWord.length === 0) return 0;
                if (userWord.length === 0) return 0;
                
                // Calculate Levenshtein distance
                const distance = levenshteinDistance(userWord, expectedWord);
                const maxLength = Math.max(userWord.length, expectedWord.length);
                const minLength = Math.min(userWord.length, expectedWord.length);
                
                // Base similarity calculation
                const baseSimilarity = ((maxLength - distance) / maxLength) * 100;
                
                // Length difference penalty (moderate)
                const lengthDiff = Math.abs(userWord.length - expectedWord.length);
                const lengthPenalty = (lengthDiff / maxLength) * 25;
                
                // Additional penalty for significant length differences
                let extraPenalty = 0;
                if (lengthDiff > 0) {
                    const lengthRatio = minLength / maxLength;
                    if (lengthRatio < 0.7) { // More than 30% length difference
                        extraPenalty = 20;
                    } else if (lengthRatio < 0.85) { // More than 15% length difference
                        extraPenalty = 10;
                    }
                }
                
                // Penalty for high edit distance relative to word length
                const distanceRatio = distance / maxLength;
                if (distanceRatio > 0.3) { // More than 30% of characters are different
                    extraPenalty += 20;
                } else if (distanceRatio > 0.2) { // More than 20% different
                    extraPenalty += 10;
                }
                
                // Calculate final score with all penalties
                const finalScore = Math.max(0, baseSimilarity - lengthPenalty - extraPenalty);
                
                // For very short words, be more lenient
                if (expectedWord.length <= 4 && distance <= 1) {
                    return Math.max(70, finalScore);
                }
                
                return Math.round(finalScore);
            }
            
            // Calculate similarity for phrases and sentences
            function calculatePhraseSimilarity(userPhrase, expectedPhrase) {
                // Perfect match = 100%
                if (userPhrase === expectedPhrase) return 100;
                
                const userWords = userPhrase.split(/\s+/).filter(w => w.length > 0);
                const expectedWords = expectedPhrase.split(/\s+/).filter(w => w.length > 0);
                
                if (expectedWords.length === 0) return 0;
                if (userWords.length === 0) return 0;
                
                let totalSimilarity = 0;
                const expectedLength = expectedWords.length;
                const userLength = userWords.length;
                
                // Word-by-word comparison
                for (let i = 0; i < expectedLength; i++) {
                    const userWord = userWords[i] || '';
                    const expectedWord = expectedWords[i] || '';
                    
                    if (userWord && expectedWord) {
                        if (userWord === expectedWord) {
                            // Perfect word match
                            totalSimilarity += 100;
                        } else {
                            // Calculate word similarity
                            const wordSim = calculateWordSimilarity(userWord, expectedWord);
                            totalSimilarity += wordSim;
                        }
                    } else {
                        // Missing or extra word - penalty
                        totalSimilarity += 0;
                    }
                }
                
                // Calculate base score based on expected length
                const baseScore = totalSimilarity / expectedLength;
                
                // Word count penalty (moderate)
                let wordCountPenalty = 0;
                if (userLength !== expectedLength) {
                    const wordDiff = Math.abs(userLength - expectedLength);
                    // 15% per missing/extra word
                    wordCountPenalty = (wordDiff / expectedLength) * 15;
                    
                    // Additional penalty for significant word count differences
                    if (wordDiff >= expectedLength * 0.3) { // 30% or more words different
                        wordCountPenalty += 15;
                    }
                }
                
                // Penalty for words that are completely wrong
                let wrongWordPenalty = 0;
                for (let i = 0; i < Math.min(userLength, expectedLength); i++) {
                    const userWord = userWords[i] || '';
                    const expectedWord = expectedWords[i] || '';
                    
                    if (userWord && expectedWord) {
                        const wordSim = calculateWordSimilarity(userWord, expectedWord);
                        if (wordSim < 50) { // Word is less than 50% similar
                            wrongWordPenalty += 8;
                        } else if (wordSim < 70) { // Word is less than 70% similar
                            wrongWordPenalty += 4;
                        }
                    }
                }
                
                // Calculate final score with all penalties
                const finalScore = Math.max(0, Math.min(100, baseScore - wordCountPenalty - wrongWordPenalty));
                
                return Math.round(finalScore);
            }
            
            // Levenshtein distance algorithm
            function levenshteinDistance(str1, str2) {
                const matrix = [];
                
                for (let i = 0; i <= str2.length; i++) {
                    matrix[i] = [i];
                }
                
                for (let j = 0; j <= str1.length; j++) {
                    matrix[0][j] = j;
                }
                
                for (let i = 1; i <= str2.length; i++) {
                    for (let j = 1; j <= str1.length; j++) {
                        if (str2.charAt(i - 1) === str1.charAt(j - 1)) {
                            matrix[i][j] = matrix[i - 1][j - 1];
                        } else {
                            matrix[i][j] = Math.min(
                                matrix[i - 1][j - 1] + 1,
                                matrix[i][j - 1] + 1,
                                matrix[i - 1][j] + 1
                            );
                        }
                    }
                }
                
                return matrix[str2.length][str1.length];
            }
            
            function updateStatsDisplay() {
                totalPracticedEl.textContent = practiceStats.totalPracticed;
                const avgAccuracy = practiceStats.attempts > 0 ? Math.round(practiceStats.totalAccuracy / practiceStats.attempts) : 0;
                averageAccuracyEl.textContent = avgAccuracy + '%';
                streakCountEl.textContent = practiceStats.streak;
                perfectCountEl.textContent = practiceStats.perfectCount;
                userLevelEl.textContent = practiceStats.level;
                userXPEl.textContent = practiceStats.xp;
                
                // Update progress bar
                const xpNeeded = practiceStats.level * XP_LEVEL_MULTIPLIER;
                const progress = (practiceStats.xp / xpNeeded) * 100;
                levelProgressBar.style.width = progress + '%';
                currentXPEl.textContent = practiceStats.xp;
                nextLevelXPEl.textContent = xpNeeded;
            }
            
            // Event listeners
            playReferenceBtn.addEventListener('click', playReference);
            randomWordBtn.addEventListener('click', loadPracticeItem);
            startRecordingBtn.addEventListener('click', startRecording);
            stopRecordingBtn.addEventListener('click', stopRecording);
            
            // Practice mode buttons
            document.querySelectorAll('.practice-mode-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.practice-mode-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentMode = this.dataset.mode;
                    loadPracticeItem();
                });
            });
            
            // Initialize
            loadPracticeItem();
            updateStatsDisplay();
        });
    </script>
</body>
</html>
