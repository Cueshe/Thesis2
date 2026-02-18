<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Pronunciation Tutor - Q2L</title>
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
            --brand-primary: #7f5bff;
            --brand-secondary: #26d8ff;
            --brand-accent: #ff5f8f;
            --page-bg: #0a0f25;
            --card-bg: rgba(13, 18, 37, 0.95);
            --surface-border: rgba(119, 141, 253, 0.25);
            --text-primary: #f1f5ff;
            --text-muted: rgba(255,255,255,0.6);
            --input-bg: rgba(23, 31, 60, 0.8);
            --input-border: rgba(119, 141, 253, 0.3);
            --input-ring: rgba(119, 141, 253, 0.4);
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
            text-align: center;
            margin-bottom: 3rem;
        }
        .pronunciation-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        .pronunciation-subtitle {
            font-size: 1.125rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }
        .language-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        .language-card {
            background: var(--surface-muted-strong);
            border: 1px solid var(--surface-border);
            border-radius: 1.5rem;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .language-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }
        .language-card:hover::before {
            left: 100%;
        }
        .language-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        .language-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        .language-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        .language-description {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .language-features {
            text-align: left;
            margin-bottom: 2rem;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .feature-icon {
            font-size: 1rem;
        }
        .start-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            color: white;
        }
        .english-btn {
            background: #2563eb;
        }
        .english-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }
        .filipino-btn {
            background: #dc2626;
        }
        .filipino-btn:hover {
            background: #b91c1c;
            transform: translateY(-2px);
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--brand-primary);
            text-decoration: none;
            margin-bottom: 2rem;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .stats-preview {
            display: flex;
            justify-content: space-around;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--surface-border);
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brand-primary);
        }
        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <a href="{{ route('student.dashboard') }}" class="back-link">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Dashboard
        </a>

        <div class="dashboard-card">
            <div class="pronunciation-header">
                <h1 class="pronunciation-title">AI Pronunciation Tutor</h1>
                <p class="pronunciation-subtitle">Choose your language of practice and master pronunciation with AI-powered coaching. Get instant feedback and improve your speaking skills with interactive exercises.</p>
            </div>

            <div class="language-cards">
                <!-- English Language Card -->
                <div class="language-card" onclick="window.location.href='{{ route('pronunciation.tutor.english') }}'">
                    <div class="language-icon">🇺🇸</div>
                    <h2 class="language-title">English</h2>
                    <p class="language-description">Master English pronunciation with comprehensive word, phrase, and sentence practice. Perfect for improving your accent and clarity.</p>
                    
                    <div class="language-features">
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>20+ practice words with phonetics</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>12 common phrases</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>8 sentence patterns</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>AI-powered feedback</span>
                        </div>
                    </div>

                    <a href="{{ route('pronunciation.tutor.english') }}" class="start-btn english-btn">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z" />
                        </svg>
                        Start English Practice
                    </a>
                </div>

                <!-- Filipino Language Card -->
                <div class="language-card" onclick="window.location.href='{{ route('pronunciation.tutor.filipino') }}'">
                    <div class="language-icon">🇵🇭</div>
                    <h2 class="language-title">Filipino</h2>
                    <p class="language-description">Pagsasanay sa bigkas sa Filipino gamit ang AI-powered coaching. Master ang mga salita, parirala, at pangungusap.</p>
                    
                    <div class="language-features">
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>20+ salitang pagsasanayan</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>12 karaniwang parirala</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>8 pattern ng pangungusap</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>AI-powered feedback</span>
                        </div>
                    </div>

                    <a href="{{ route('pronunciation.tutor.filipino') }}" class="start-btn filipino-btn">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z" />
                        </svg>
                        Simulang Mag-practice
                    </a>
                </div>
            </div>

            <div class="stats-preview">
                <div class="stat-item">
                    <div class="stat-value">40+</div>
                    <div class="stat-label">Practice Items</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">AI</div>
                    <div class="stat-label">Powered Feedback</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">2</div>
                    <div class="stat-label">Languages</div>
                </div>
            </div>
        </div>
    </body>
</html>
