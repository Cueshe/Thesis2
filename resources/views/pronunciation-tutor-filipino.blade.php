<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Filipino Pronunciation Tutor - Q2L</title>
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
            --brand-primary: #dc2626;
            --brand-secondary: #ef4444;
            --brand-accent: #f87171;
            --page-bg: #0a0f25;
            --card-bg: rgba(13, 18, 37, 0.95);
            --surface-border: rgba(220, 38, 38, 0.25);
            --text-primary: #f1f5ff;
            --text-muted: rgba(255,255,255,0.6);
            --input-bg: rgba(23, 31, 60, 0.8);
            --input-border: rgba(220, 38, 38, 0.3);
            --input-ring: rgba(220, 38, 38, 0.4);
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
            background: #dc2626;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateY(-2px) scale(1.01);
        }
        .practice-item-container {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.4);
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
            background: #dc2626;
            color: white;
        }
        .record-btn {
            background: #059669;
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
            background: rgba(220, 38, 38, 0.12);
            border: 1px solid rgba(220, 38, 38, 0.4);
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
            content: 'Nakikinig...';
            color: var(--text-muted);
            font-style: italic;
        }
        .feedback-section {
            background: rgba(8, 12, 28, 0.85);
            border: 1px solid rgba(220, 38, 38, 0.25);
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
            background: rgba(220, 38, 38, 0.25);
            border: 2px solid rgba(220, 38, 38, 0.45);
            border-radius: 1.5rem;
            padding: 1.75rem;
            box-shadow: 0 25px 55px -35px rgba(220, 38, 38, 0.8);
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
            background: #dc2626;
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
            background: rgba(255, 255, 255, 0.1);
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
            background: rgba(255, 255, 255, 0.1);
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
            border-color: rgba(220, 38, 38, 0.4);
            background: rgba(220, 38, 38, 0.1);
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
            Bumalik sa Dashboard
        </a>

        <div class="language-nav">
            <a href="{{ route('pronunciation.tutor.filipino') }}" class="language-nav-item active">
                🇵🇭 Filipino
            </a>
            <a href="{{ route('pronunciation.tutor.english') }}" class="language-nav-item">
                🇺🇸 English
            </a>
        </div>

        <div class="dashboard-card">
            <div class="pronunciation-header">
                <div>
                    <h2 class="pronunciation-title">Filipino Pronunciation Tutor</h2>
                    <p class="pronunciation-subtitle">Pagsasanay sa bigkas sa Filipino gamit ang AI-powered coaching. Master ang mga salita, parirala, at pangungusap para sa mas maayos na bigkas at pag-unawa sa wikang Filipino.</p>
                </div>
            </div>

            <div class="pronunciation-content">
                <div class="practice-column practice-stack">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)] mb-1">Antas ng pagsasanay</p>
                            <div class="practice-modes">
                                <button class="practice-mode-btn active" data-mode="word">Salita</button>
                                <button class="practice-mode-btn" data-mode="phrase">Parirala</button>
                                <button class="practice-mode-btn" data-mode="sentence">Pangungusap</button>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)]">Kasalukuyang streak</p>
                            <p class="text-2xl font-black text-[color:var(--brand-primary)]" id="streakCount">0</p>
                        </div>
                    </div>

                    <div class="practice-item-container">
                        <div class="practice-item-display">
                            <div class="practice-item-text" id="practiceText">Naglo-load...</div>
                            <div class="practice-item-phonetic" id="practicePhonetic"></div>
                        </div>
                        <div class="flex gap-4 justify-center mt-6">
                            <div class="glass-pill">
                                <span class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Wika</span>
                                <span class="font-semibold" id="languageBadge">TL</span>
                            </div>
                            <div class="glass-pill">
                                <span class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Mode</span>
                                <span class="font-semibold" id="modeBadge">Salita</span>
                            </div>
                        </div>
                        <div class="practice-actions mt-8">
                            <button id="playReferenceBtn" class="action-btn play-btn">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z" />
                                </svg>
                                <span>Pakinggan</span>
                            </button>
                            <button id="startRecordingBtn" class="action-btn record-btn">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                                </svg>
                                <span>Rekord</span>
                            </button>
                            <button id="stopRecordingBtn" class="action-btn stop-btn hidden">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                                </svg>
                                <span>Itigil</span>
                            </button>
                        </div>
                    </div>

                    <div id="recordingStatus" class="recording-status hidden">
                        <div class="recording-indicator">
                            <span class="recording-dot"></span>
                            <span>Nagre-rekord...</span>
                        </div>
                    </div>

                    <div id="liveTranscription" class="live-transcription hidden">
                        <div class="live-transcription-label">Sinasabi mo:</div>
                        <div class="live-transcription-text" id="liveTranscriptionText">Nakikinig...</div>
                    </div>

                    <div id="feedbackSection" class="feedback-section hidden">
                        <div class="feedback-header">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)]">Feedback sa bigkas</p>
                                <h3 class="text-xl font-semibold">Mga Turo ng Coach</h3>
                            </div>
                            <div class="accuracy-score" id="accuracyScore">0%</div>
                        </div>
                        <div class="feedback-content">
                            <div class="feedback-item">
                                <div class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Ang iyong bigkas</div>
                                <div class="text-base font-medium" id="userPronunciation">-</div>
                            </div>
                            <div class="feedback-item">
                                <div class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)]">Tamang bigkas</div>
                                <div class="text-base font-medium" id="correctPronunciation">-</div>
                            </div>
                            <div class="feedback-tips mt-4">
                                <div class="text-xs uppercase tracking-[0.3em] text-[color:var(--text-muted)] mb-2">Mga Tip</div>
                                <ul id="tipsList" class="list-disc list-inside space-y-1"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="practice-list-container">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-[color:var(--text-muted)]">Listahan ng pagsasanay</p>
                                <h3 class="text-lg font-bold">Random na Pagsasanay</h3>
                            </div>
                            <button id="randomWordBtn" class="action-btn play-btn" style="padding: 0.5rem 1rem;">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                <span>Bagong Salita</span>
                            </button>
                        </div>
                        <p class="text-sm text-[color:var(--text-muted)]">Kumuha ng bagong Filipino word na pagsasanayan mula sa aming AI-powered selection.</p>
                    </div>
                </div>

                <aside class="coach-column space-y-6">
                    <div class="coach-card">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.4em] text-[color:var(--text-muted)]">Mga Tip</p>
                                <h3 class="text-lg font-semibold">Filipino Coach</h3>
                            </div>
                            <div class="glass-pill text-sm">Araw-araw na practice</div>
                        </div>
                        <ul class="space-y-3 text-sm text-[color:var(--text-muted)]">
                            <li class="flex gap-3">
                                <span class="text-lg">🎧</span>
                                <div>
                                    <p class="font-semibold text-[color:var(--text-primary)]">Makinig nang mabuti muna.</p>
                                    <p>Pansinin ang stress patterns at intonation sa Filipino.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-lg">📝</span>
                                <div>
                                    <p class="font-semibold text-[color:var(--text-primary)]">Hatiin ang mga mahirap na salita.</p>
                                    <p>Practice ang bawat pantig para sa mas maayos na bigkas.</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-lg">⚡</span>
                                <div>
                                    <p class="font-semibold text-[color:var(--text-primary)]">Focus sa mga karaniwang tunog.</p>
                                    <p>Master ang mga tricky Filipino sounds tulad ng 'ng', 'ñ', at 'r'.</p>
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
                                    <div class="stat-label">Mga Salitang In-practice</div>
                                </div>
                            </div>
                            <div class="stat-card stat-card-success">
                                <div class="stat-icon">📊</div>
                                <div class="stat-content">
                                    <div class="stat-value" id="averageAccuracy">0%</div>
                                    <div class="stat-label">Average na Accuracy</div>
                                </div>
                            </div>
                            <div class="stat-card stat-card-warning">
                                <div class="stat-icon">🔥</div>
                                <div class="stat-content">
                                    <div class="stat-value" id="streakCount">0</div>
                                    <div class="stat-label">Araw na Streak</div>
                                </div>
                            </div>
                            <div class="stat-card stat-card-info">
                                <div class="stat-icon">🏆</div>
                                <div class="stat-content">
                                    <div class="stat-value" id="perfectCount">0</div>
                                    <div class="stat-label">Mga Perfect Score</div>
                                </div>
                            </div>
                        </div>

                        <div class="achievements-section">
                            <div class="achievements-header">
                                <span class="achievements-icon">🏅</span>
                                <span>Mga Kamakailang Achievements</span>
                            </div>
                            <div class="achievements-list" id="achievementsList">
                                <div class="achievement-placeholder">Magpatuloy sa pagsasanay para ma-unlock ang mga achievements!</div>
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
            let currentLanguage = 'tl-PH';
            let currentIndex = 0;
            let practiceData = { 'tl-PH': { word: [], phrase: [], sentence: [] } };
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
            
            // Filipino word pools
            const filipinoWords = {
                word: [
                    { text: 'Kumusta', phonetic: '/kuːˈmuːstɑː/', tips: ['Ang "ku" ay parang "coo"', 'I-stress ang second syllable'] },
                    { text: 'Magandang', phonetic: '/mɑːɡɑːnˈdɑːŋ/', tips: ['Ang "ng" ay isang tunog lang', 'I-stress ang last syllable'] },
                    { text: 'Araw', phonetic: '/ˈɑːrɑːw/', tips: ['Ang "a" ay parang "ah"', 'Ang "w" ay malambot'] },
                    { text: 'Salamat', phonetic: '/sɑːˈlɑːmɑːt/', tips: ['Lahat ng "a" ay mahaba', 'I-stress ang second syllable'] },
                    { text: 'Paalam', phonetic: '/pɑːˈɑːlɑːm/', tips: ['Ang "aa" ay mahaba', 'I-stress ang second syllable'] },
                    { text: 'Mahal', phonetic: '/mɑːˈhɑːl/', tips: ['Ang "h" ay binibigkas', 'I-stress ang second syllable'] },
                    { text: 'Kaibigan', phonetic: '/kɑːɪˈbiːɡɑːn/', tips: ['Hatihin sa mga pantig: ka-i-bi-gan', 'I-stress ang third syllable'] },
                    { text: 'Pag-aaral', phonetic: '/pɑːɡ ɑːˈɑːrɑːl/', tips: ['May glottal stop sa pagitan ng "pag" at "aaral"', 'I-stress ang "aaral"'] },
                    { text: 'Pamilya', phonetic: '/pɑːˈmiːljɑː/', tips: ['I-stress ang second syllable', 'Ang "y" ay malambot'] },
                    { text: 'Maraming', phonetic: '/mɑːˈrɑːmɪŋ/', tips: ['Ang "ng" ay malinaw', 'I-stress ang second syllable'] },
                    { text: 'Guro', phonetic: '/ˈɡuːroː/', tips: ['I-stress ang first syllable', 'Ang "u" ay mahaba'] },
                    { text: 'Estudyante', phonetic: '/esˈtuːdjɑːnte/', tips: ['I-stress ang second syllable', 'Ang "e" sa simula ay malinaw'] },
                    { text: 'Aklatan', phonetic: '/ɑːkˈlɑːtɑːn/', tips: ['I-stress ang second syllable', 'Ang "k" ay malinaw'] },
                    { text: 'Paaralan', phonetic: '/pɑːˈɑːrɑːlɑːn/', tips: ['I-stress ang second syllable', 'Ang "aa" ay mahaba'] },
                    { text: 'Libro', phonetic: '/ˈliːbroː/', tips: ['I-stress ang first syllable', 'Ang "i" ay mahaba'] },
                    { text: 'Lapis', phonetic: '/ˈlɑːpɪs/', tips: ['I-stress ang first syllable', 'Ang "a" ay mahaba'] },
                    { text: 'Bahay', phonetic: '/ˈbɑːhɑːj/', tips: ['I-stress ang first syllable', 'Ang "h" ay binibigkas'] },
                    { text: 'Puso', phonetic: '/ˈpuːsoː/', tips: ['I-stress ang first syllable', 'Ang "u" ay mahaba'] },
                    { text: 'Isip', phonetic: '/ˈɪsɪp/', tips: ['I-stress ang first syllable', 'Lahat ng vowels ay maikli'] },
                    { text: 'Kamay', phonetic: '/ˈkɑːmɑːj/', tips: ['I-stress ang first syllable', 'Lahat ng "a" ay mahaba'] }
                ],
                phrase: [
                    { text: 'Kumusta ka?', phonetic: '/kuːˈmuːstɑː kɑː/', tips: ['Ikonek ang "kumusta" at "ka" nang maayos', 'Ang "ka" ay maikli'] },
                    { text: 'Magandang umaga', phonetic: '/mɑːɡɑːnˈdɑːŋ uːˈmɑːɡɑː/', tips: ['Ang "ng" ay kumokonek sa "umaga"', 'I-stress ang parehong words'] },
                    { text: 'Maraming salamat', phonetic: '/mɑːˈrɑːmɪŋ sɑːˈlɑːmɑːt/', tips: ['Ang "ng" sa "maraming" ay malinaw', 'Parehong words ay stressed'] },
                    { text: 'Ingat ka', phonetic: '/ɪˈŋɑːt kɑː/', tips: ['Ang "ng" ay binibigkas', 'Maikli at malinaw'] },
                    { text: 'Mahal kita', phonetic: '/mɑːˈhɑːl ˈkiːtɑː/', tips: ['Parehong words ay stressed', 'Ang "h" sa "mahal" ay malinaw'] },
                    { text: 'Salamat po', phonetic: '/sɑːˈlɑːmɑːt poː/', tips: ['Idagdag ang "po" para sa paggalang', 'Parehong words ay malinaw'] },
                    { text: 'Ano ang pangalan mo?', phonetic: '/ˈɑːnoː ɑːŋ pɑːˈŋɑːlɑːn moː/', tips: ['I-stress ang "pangalan"', 'Ikonek ang words nang maayos'] },
                    { text: 'Saan ka nakatira?', phonetic: '/sɑːˈɑːn kɑː nɑːkɑːˈtiːrɑː/', tips: ['I-stress ang "saan" at "nakatira"', 'Ang "ng" sa "nakatira" ay malinaw'] },
                    { text: 'Magandang hapon', phonetic: '/mɑːɡɑːnˈdɑːŋ ˈhɑːpoːn/', tips: ['Ang "ng" ay kumokonek nang maayos', 'I-stress ang parehong words'] },
                    { text: 'Paalam na', phonetic: '/pɑːˈɑːlɑːm nɑː/', tips: ['I-stress ang "paalam"', 'Ang "na" ay maikli'] },
                    { text: 'Tulong po', phonetic: '/ˈtuːloːŋ poː/', tips: ['I-stress ang "tulong"', 'Ang "ng" ay malinaw'] },
                    { text: 'Walang anuman', phonetic: '/ˈwɑːlɑːŋ ɑːˈnuːmɑːn/', tips: ['I-stress ang "anuman"', 'Ang "ng" sa "walang" ay malinaw'] }
                ],
                sentence: [
                    { text: 'Ako ay nag-aaral ng Filipino.', phonetic: '/ˈɑːkoː ɑːj nɑːɡ ɑːˈɑːrɑːl nɑːŋ fiːˈliːpiːnoː/', tips: ['Magsalita nang mabagal at malinaw', 'Mag-pause sa pagitan ng phrases kung kailangan'] },
                    { text: 'Mahal ko ang aking pamilya.', phonetic: '/mɑːˈhɑːl koː ɑːŋ ˈɑːkɪŋ pɑːˈmiːljɑː/', tips: ['I-emphasize ang "mahal" at "pamilya"', 'Gumamit ng natural rhythm'] },
                    { text: 'Ang pag-aaral ay mahalaga.', phonetic: '/ɑːŋ pɑːɡ ɑːˈɑːrɑːl ɑːj mɑːhɑːˈlɑːɡɑː/', tips: ['Gumamit ng rising intonation', 'Ikonek ang words nang maayos'] },
                    { text: 'Saan ang aklatan?', phonetic: '/sɑːˈɑːn ɑːŋ ɑːkˈlɑːtɑːn/', tips: ['I-stress ang "saan" at "aklatan"', 'Mag-pause pagkatapos ng "saan"'] },
                    { text: 'Gusto ko mag-aral ng Ingles.', phonetic: '/ˈɡuːstoː koː mɑːɡ ɑːˈɑːrɑːl nɑːŋ ˈɪŋɡles/', tips: ['I-stress ang "gusto" at "mag-aral"', 'Ang "ng" ay malinaw'] },
                    { text: 'Maraming salamat po sa inyo.', phonetic: '/mɑːˈrɑːmɪŋ sɑːˈlɑːmɑːt poː sɑː ˈɪnjoː/', tips: ['I-stress ang "maraming" at "salamat"', 'Ang "po" ay nagpapakita ng paggalang'] },
                    { text: 'Ako ay estudyante sa paaralan.', phonetic: '/ˈɑːkoː ɑːj esˈtuːdjɑːnte sɑː pɑːˈɑːrɑːlɑːn/', tips: ['I-stress ang "estudyante" at "paaralan"', 'Magsalita nang malinaw'] }
                ]
            };
            
            // Initialize with Filipino words
            practiceData['tl-PH'] = filipinoWords;
            
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
            
            // Mode labels in Filipino
            const modeLabels = {
                word: 'Salita',
                phrase: 'Parirala',
                sentence: 'Pangungusap'
            };
            
            // Initialize practice data
            function loadPracticeItem() {
                const items = practiceData[currentLanguage][currentMode];
                if (items && items.length > 0) {
                    currentIndex = Math.floor(Math.random() * items.length);
                    const item = items[currentIndex];
                    practiceText.textContent = item.text;
                    practicePhonetic.textContent = item.phonetic;
                    languageBadge.textContent = 'TL';
                    modeBadge.textContent = modeLabels[currentMode] || currentMode.charAt(0).toUpperCase() + currentMode.slice(1);
                }
            }
            
            // Speech synthesis with voice options
            let selectedVoice = null;
            let availableVoices = [];
            let selectedVoiceType = 'boy'; // Default to boy voice
            
            function loadVoices() {
                availableVoices = synthesis.getVoices();
                updateVoiceSelection();
            }
            
            function updateVoiceSelection() {
                const filipinoVoices = availableVoices.filter(voice => 
                    voice.lang.includes('tl') || 
                    voice.lang.includes('fil') || 
                    voice.lang.includes('ph') ||
                    voice.name.toLowerCase().includes('filipino') ||
                    voice.name.toLowerCase().includes('tagalog')
                );
                
                if (filipinoVoices.length > 0) {
                    // Try to find a voice based on selected voice type
                    const targetVoice = filipinoVoices.find(voice => {
                        const voiceName = voice.name.toLowerCase();
                        if (selectedVoiceType === 'boy') {
                            return voiceName.includes('boy') ||
                                   voiceName.includes('child') ||
                                   voiceName.includes('kid') ||
                                   voiceName.includes('bata') ||
                                   voiceName.includes('young');
                        } else {
                            return voiceName.includes('female') ||
                                   voiceName.includes('woman') ||
                                   voiceName.includes('babae');
                        }
                    });
                    
                    selectedVoice = targetVoice || filipinoVoices[0];
                    console.log('Selected Filipino voice:', selectedVoice.name, 'Type:', selectedVoiceType);
                } else {
                    console.warn('No Filipino voices found, using default voice');
                    selectedVoice = availableVoices[0] || null;
                }
            }
            
            function playReference() {
                if (synthesis.speaking) {
                    synthesis.cancel();
                    return;
                }
                
                const text = practiceText.textContent;
                if (text) {
                    // Try multiple methods for Filipino TTS
                    
                    // Method 1: Try Web Speech API with Filipino voice
                    if (selectedVoice && selectedVoice.lang.includes('tl')) {
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.voice = selectedVoice;
                        utterance.lang = 'tl-PH';
                        utterance.rate = 0.8;
                        // Boy voice: higher pitch than adult male but lower than female
                        utterance.pitch = selectedVoiceType === 'boy' ? 1.2 : 1.1;
                        utterance.volume = 1.0;
                        
                        utterance.onend = () => {
                            console.log('Web Speech API TTS completed');
                        };
                        
                        utterance.onerror = (error) => {
                            console.error('Web Speech API TTS error:', error);
                            fallbackToGoogleTTS(text);
                        };
                        
                        synthesis.speak(utterance);
                        return;
                    }
                    
                    // Method 2: Try backend TTS API
                    tryBackendTTS(text);
                }
            }
            
            function tryBackendTTS(text) {
                if (currentAudio) {
                    currentAudio.pause();
                    currentAudio = null;
                }
                
                // Try selected voice type
                const ttsUrl = `/api/tts/speak?text=${encodeURIComponent(text)}&lang=tl&voice=${selectedVoiceType}`;
                const audio = new Audio(ttsUrl);
                currentAudio = audio;
                
                audio.addEventListener('ended', () => {
                    currentAudio = null;
                    console.log(`Backend TTS (${selectedVoiceType}) completed`);
                });
                
                audio.addEventListener('error', () => {
                    console.error(`Backend TTS (${selectedVoiceType}) failed, trying fallback...`);
                    // Try other voice type as fallback
                    const fallbackVoice = selectedVoiceType === 'boy' ? 'female' : 'boy';
                    const fallbackTtsUrl = `/api/tts/speak?text=${encodeURIComponent(text)}&lang=tl&voice=${fallbackVoice}`;
                    const fallbackAudio = new Audio(fallbackTtsUrl);
                    currentAudio = fallbackAudio;
                    
                    fallbackAudio.addEventListener('ended', () => {
                        currentAudio = null;
                        console.log(`Backend TTS (${fallbackVoice}) completed`);
                    });
                    
                    fallbackAudio.addEventListener('error', () => {
                        console.error('Backend TTS fallback failed, trying Google TTS...');
                        fallbackToGoogleTTS(text);
                    });
                    
                    fallbackAudio.play().catch(() => {
                        fallbackToGoogleTTS(text);
                    });
                });
                
                audio.play().catch(() => {
                    fallbackToGoogleTTS(text);
                });
            }
            
            function fallbackToGoogleTTS(text) {
                // Fallback to Google Translate TTS
                const encodedText = encodeURIComponent(text);
                const ttsUrl = `https://translate.google.com/translate_tts?ie=UTF-8&tl=tl&client=gtx&q=${encodedText}`;
                const audio = new Audio(ttsUrl);
                currentAudio = audio;
                
                audio.addEventListener('ended', () => {
                    currentAudio = null;
                    console.log('Google TTS completed');
                });
                
                audio.addEventListener('error', () => {
                    console.error('All TTS methods failed');
                    currentAudio = null;
                });
                
                audio.play().catch(() => {
                    console.error('Google TTS failed');
                    currentAudio = null;
                });
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

                rec.onstart = () => {
                    console.log('Recording started');
                    isRecording = true;
                    startRecordingBtn.classList.add('hidden');
                    stopRecordingBtn.classList.remove('hidden');
                    recordingStatus.classList.remove('hidden');
                    liveTranscription.classList.remove('hidden');
                    liveTranscriptionText.textContent = 'Nakikinig...';
                };

                rec.onresult = (event) => {
                    console.log('Speech recognition result received');
                    
                    let fullTranscript = '';
                    let hasFinal = false;
                    
                    for (let i = 0; i < event.results.length; i++) {
                        const transcript = event.results[i][0].transcript;
                        if (event.results[i].isFinal) {
                            fullTranscript += transcript + ' ';
                            hasFinal = true;
                        } else if (i === event.results.length - 1) {
                            fullTranscript += transcript;
                        }
                    }
                    
                    const displayText = fullTranscript.trim() || 'Nakikinig...';
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
                            setTimeout(() => {
                                analyzePronunciation(finalTranscript);
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
            
            function analyzePronunciation(userText) {
                const referenceText = practiceText.textContent.toLowerCase().trim();
                const userTextClean = userText.toLowerCase().trim();
                
                // Simple accuracy calculation
                const accuracy = calculateAccuracy(referenceText, userTextClean);
                
                // Update feedback
                userPronunciation.textContent = userText;
                correctPronunciation.textContent = practiceText.textContent;
                accuracyScore.textContent = accuracy + '%';
                
                // Update tips
                const currentItem = practiceData[currentLanguage][currentMode][currentIndex];
                tipsList.innerHTML = '';
                if (currentItem && currentItem.tips) {
                    currentItem.tips.forEach(tip => {
                        const li = document.createElement('li');
                        li.textContent = tip;
                        tipsList.appendChild(li);
                    });
                }
                
                // Show feedback section
                feedbackSection.classList.remove('hidden');
                liveTranscription.classList.add('hidden');
                
                // Update stats
                updateStats(accuracy);
            }
            
            function calculateAccuracy(reference, user) {
                // Normalize both texts for comparison
                const normalizeText = (text) => {
                    return text.toLowerCase()
                        .replace(/[.,!?;:'"()\[\]{}]/g, '') // Remove punctuation
                        .replace(/\s+/g, ' ') // Normalize whitespace
                        .trim();
                };
                
                const normalizedRef = normalizeText(reference);
                const normalizedUser = normalizeText(user);
                
                console.log('Calculating accuracy:');
                console.log('Reference:', reference, '-> Normalized:', normalizedRef);
                console.log('User:', user, '-> Normalized:', normalizedUser);
                
                // Word-based accuracy calculation
                const refWords = normalizedRef.split(' ').filter(w => w.length > 0);
                const userWords = normalizedUser.split(' ').filter(w => w.length > 0);
                
                console.log('Reference words:', refWords);
                console.log('User words:', userWords);
                
                let matches = 0;
                let totalWords = Math.max(refWords.length, userWords.length);
                
                for (let i = 0; i < Math.min(refWords.length, userWords.length); i++) {
                    if (refWords[i] === userWords[i]) {
                        matches++;
                        console.log(`Match: "${refWords[i]}" === "${userWords[i]}"`);
                    } else {
                        console.log(`Mismatch: "${refWords[i]}" !== "${userWords[i]}"`);
                    }
                }
                
                // Apply penalties for word count differences
                let accuracy = (matches / refWords.length) * 100;
                
                // Penalty for missing words
                if (userWords.length < refWords.length) {
                    const missingWords = refWords.length - userWords.length;
                    accuracy -= (missingWords / refWords.length) * 100;
                }
                
                // Penalty for extra words
                if (userWords.length > refWords.length) {
                    const extraWords = userWords.length - refWords.length;
                    accuracy -= (extraWords / refWords.length) * 50; // Less penalty for extra words
                }
                
                accuracy = Math.round(Math.max(0, Math.min(100, accuracy)));
                console.log('Final accuracy:', accuracy + '%');
                
                return accuracy;
            }
            
            function updateStats(accuracy) {
                practiceStats.totalPracticed++;
                practiceStats.totalAccuracy += accuracy;
                practiceStats.attempts++;
                
                if (accuracy >= 90) {
                    practiceStats.perfectCount++;
                    practiceStats.xp += XP_PER_PERFECT;
                } else if (accuracy >= 70) {
                    practiceStats.xp += XP_PER_GOOD;
                } else {
                    practiceStats.xp += XP_PER_PRACTICE;
                }
                
                // Check for level up
                const xpNeeded = practiceStats.level * XP_LEVEL_MULTIPLIER;
                if (practiceStats.xp >= xpNeeded) {
                    practiceStats.level++;
                    practiceStats.xp = practiceStats.xp - xpNeeded;
                }
                
                // Update UI
                updateStatsDisplay();
            }
            
            function updateStatsDisplay() {
                totalPracticedEl.textContent = practiceStats.totalPracticed;
                const avgAccuracy = practiceStats.attempts > 0 ? Math.round(practiceStats.totalAccuracy / practiceStats.attempts) : 0;
                averageAccuracyEl.textContent = avgAccuracy + '%';
                streakCountEl.textContent = practiceStats.streak;
                userLevelEl.textContent = practiceStats.level;
                userXPEl.textContent = practiceStats.xp;
                perfectCountEl.textContent = practiceStats.perfectCount;
                
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
            
            // Load voices when available
            if (synthesis) {
                // Load voices immediately if available
                if (synthesis.getVoices().length > 0) {
                    loadVoices();
                } else {
                    // Wait for voices to be loaded
                    synthesis.onvoiceschanged = () => {
                        loadVoices();
                    };
                }
            }
        });
    </script>
</body>
</html>
