<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Select Grade Level - Q2L</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="{{ asset('css/student/grade-selection.css') }}" rel="stylesheet">
</head>
<body class="py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-14 w-auto">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-[var(--text-muted)]" data-translate="grade-portal-label">Student Portal</p>
                    <h1 class="text-2xl font-semibold" data-translate="grade-hero-heading" data-name="{{ $user->name }}">Almost there, {{ $user->name }}!</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <x-translation-toggle />
                <x-theme-toggle />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold uppercase tracking-wide text-[var(--text-muted)] hover:text-[var(--text-primary)]" data-translate="grade-sign-out">Sign out</button>
                </form>
            </div>
        </div>

        <div class="rounded-3xl border" style="background: var(--surface-bg); border-color: var(--surface-border); box-shadow: var(--surface-shadow);">
            <div class="p-6 sm:p-10 space-y-8">
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-[var(--accent)] tracking-[0.35em] uppercase" data-translate="grade-step-label">Step 2 of 2</p>
                    <h2 class="text-3xl font-semibold" data-translate="grade-heading">Choose your grade level</h2>
                    <p class="text-[var(--text-muted)] text-sm sm:text-base max-w-2xl" data-translate="grade-description">
                        We use your grade level to tailor lessons, leaderboards, and assignments just for you. Pick the grade that matches your current class so we can set everything up correctly.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="rounded-2xl border px-4 py-3 text-sm shadow-sm"
                         style="background: #ffe3e3; border-color: #fb7185; color: #7f1d1d;"
                         data-theme-light-error>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('student.grade.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach (['7' => 'Grade 7', '8' => 'Grade 8', '9' => 'Grade 9', '10' => 'Grade 10'] as $value => $label)
                            <label class="grade-option">
                                <input type="radio" name="grade_level" value="{{ $value }}" class="sr-only" {{ $selectedGrade == $value ? 'checked' : '' }}>
                                <div class="h-full rounded-2xl border border-slate-200/60 dark:border-slate-700/70 px-6 py-5 flex flex-col gap-1 transition hover:-translate-y-1 hover:border-[var(--accent)] hover:bg-[var(--surface-hover)] cursor-pointer">
                                    <span class="text-sm font-semibold text-[var(--text-muted)]" data-translate="grade-level-label" data-grade="{{ $value }}">Level {{ $value }}</span>
                                    <span class="text-xl font-semibold text-[var(--text-primary)]" data-translate="grade-card-title" data-grade="{{ $value }}">{{ $label }}</span>
                                    <p class="text-xs text-[var(--text-muted)]" data-translate="grade-card-description" data-grade="{{ $value }}">Recommended content for Grade {{ $value }} students.</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="space-y-3">
                        <button type="submit" class="w-full rounded-2xl bg-[var(--accent)] text-white font-semibold py-4 text-sm tracking-wide uppercase shadow-lg hover:bg-[var(--accent-hover)] transition" data-translate="grade-save-button">
                            Save my grade level
                        </button>
                        <p class="text-xs text-[var(--text-muted)] text-center" data-translate="grade-helper-text">
                            Need a different grade later? You can update this anytime from your profile settings.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="{{ asset('js/student/grade-selection.js') }}" defer></script>
</body>
</html>

