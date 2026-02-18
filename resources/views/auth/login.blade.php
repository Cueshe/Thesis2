<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In / Sign Up - Q2L</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --page-gradient: #f0f4f8;
            --text-primary: #1e293b;
            --text-muted: #475569;
            --text-subtle: #64748b;
            --icon-muted: #94a3b8;
            --surface-bg: rgba(255, 255, 255, 0.95);
            --surface-border: rgba(59, 130, 246, 0.15);
            --surface-hover-border: rgba(59, 130, 246, 0.3);
            --surface-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
            --surface-hover-shadow: 0 15px 40px -10px rgba(59, 130, 246, 0.15);
            --surface-muted: rgba(59, 130, 246, 0.1);
            --surface-muted-border: rgba(59, 130, 246, 0.2);
            --field-bg: #ffffff;
            --field-bg-focus: #ffffff;
            --field-border: rgba(148, 163, 184, 0.4);
            --field-border-focus: rgba(59, 130, 246, 0.6);
            --field-ring: rgba(59, 130, 246, 0.15);
            --portal-banner-bg: rgba(255, 255, 255, 0.9);
            --portal-banner-text: #1e293b;
            --portal-banner-subtle: #475569;
            --primary-blue: #3b82f6;
            --primary-blue-hover: #2563eb;
        }

        .dark {
            color-scheme: dark;
            --page-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0b1120 100%);
            --text-primary: #f1f5f9;
            --text-muted: #cbd5e1;
            --text-subtle: #94a3b8;
            --icon-muted: #64748b;
            --surface-bg: rgba(15, 23, 42, 0.8);
            --surface-border: rgba(71, 85, 105, 0.4);
            --surface-hover-border: rgba(129, 140, 248, 0.5);
            --surface-shadow: 0 25px 70px -25px rgba(0, 0, 0, 0.6);
            --surface-hover-shadow: 0 30px 80px -25px rgba(99, 102, 241, 0.4);
            --surface-muted: rgba(99, 102, 241, 0.15);
            --surface-muted-border: rgba(129, 140, 248, 0.3);
            --field-bg: rgba(30, 41, 59, 0.6);
            --field-bg-focus: rgba(30, 41, 59, 0.8);
            --field-border: rgba(71, 85, 105, 0.5);
            --field-border-focus: rgba(129, 140, 248, 0.6);
            --field-ring: rgba(99, 102, 241, 0.2);
            --portal-banner-bg: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 50%, rgba(15, 23, 42, 0.95) 100%);
            --portal-banner-text: #f1f5f9;
            --portal-banner-subtle: #cbd5e1;
        }

        * {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-gradient);
            background-attachment: fixed;
            color: var(--text-primary);
            min-height: 100vh;
            margin: 0;
            transition: background 0.5s ease, color 0.5s ease;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.08), transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.06), transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        body > * {
            position: relative;
            z-index: 1;
        }

        .theme-body {
            color: var(--text-primary);
            transition: background 0.45s ease, color 0.45s ease;
        }

        .theme-surface {
            background-color: var(--surface-bg) !important;
            border-color: var(--surface-border) !important;
            box-shadow: var(--surface-shadow) !important;
            backdrop-filter: blur(20px) saturate(180%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .theme-surface:hover,
        .theme-surface:focus-within {
            border-color: var(--surface-hover-border) !important;
            box-shadow: var(--surface-hover-shadow) !important;
            transform: translateY(-2px);
        }

        .theme-tile {
            background-color: var(--surface-bg) !important;
            border: 1px solid var(--surface-border) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .theme-tile:hover,
        .theme-tile:focus-visible {
            border-color: var(--surface-hover-border) !important;
            box-shadow: 0 8px 24px -8px rgba(99, 102, 241, 0.4) !important;
            background-color: var(--surface-bg) !important;
            transform: translateY(-2px);
        }

        .theme-text-primary {
            color: var(--text-primary) !important;
            transition: color 0.35s ease;
        }

        .theme-text-muted {
            color: var(--text-muted) !important;
            transition: color 0.35s ease;
        }

        .theme-text-subtle {
            color: var(--text-subtle) !important;
            transition: color 0.35s ease;
        }

        .theme-icon-muted {
            color: var(--icon-muted) !important;
            transition: color 0.35s ease;
        }

        .theme-divider {
            color: var(--text-subtle) !important;
            transition: color 0.35s ease;
        }

        .theme-divider span {
            background-color: var(--surface-bg);
            transition: background-color 0.35s ease;
        }

        .portal-banner {
            background: var(--portal-banner-bg);
            color: var(--portal-banner-text);
            transition: background 0.4s ease, color 0.4s ease;
        }

        .portal-banner .portal-banner-subtitle {
            color: var(--portal-banner-subtle) !important;
        }

        #authForms,
        #authForms .theme-section,
        #teacherRequestModal .theme-section {
            color: var(--text-primary);
        }

        #tabHeader {
            background: var(--surface-muted) !important;
            border: 1px solid var(--surface-muted-border) !important;
        }

        #tabButtons .tab-button {
            color: var(--text-muted);
        }

        #tabButtons .tab-button.tab-active {
            color: var(--primary-blue);
            background: rgba(59, 130, 246, 0.15);
            box-shadow: 0 2px 8px -4px rgba(59, 130, 246, 0.4);
            font-weight: 700;
        }

        .dark #tabButtons .tab-button {
            color: #cbd5e1;
        }

        .dark #tabButtons .tab-button.tab-active {
            color: #e0e7ff;
            background: rgba(59, 130, 246, 0.25);
            box-shadow: 0 2px 8px -4px rgba(59, 130, 246, 0.5);
        }

        #authForms input[type="text"],
        #authForms input[type="email"],
        #authForms input[type="password"],
        #authForms input[type="number"],
        #authForms select,
        #teacherRequestModal input[type="text"],
        #teacherRequestModal input[type="email"],
        #teacherRequestModal select,
        #teacherRequestModal textarea {
            background-color: var(--field-bg);
            border: 1.5px solid var(--field-border);
            color: var(--text-primary);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0.75rem;
        }

        #authForms input[type="text"]:focus,
        #authForms input[type="email"]:focus,
        #authForms input[type="password"]:focus,
        #authForms input[type="number"]:focus,
        #authForms select:focus,
        #teacherRequestModal input[type="text"]:focus,
        #teacherRequestModal input[type="email"]:focus,
        #teacherRequestModal select:focus,
        #teacherRequestModal textarea:focus {
            background-color: var(--field-bg-focus);
            border-color: var(--field-border-focus);
            box-shadow: 0 0 0 4px var(--field-ring), 0 4px 12px -4px rgba(59, 130, 246, 0.2);
            outline: none;
            transform: translateY(-1px);
        }

        #authForms input::placeholder,
        #teacherRequestModal input::placeholder,
        #teacherRequestModal textarea::placeholder {
            color: var(--text-subtle);
            opacity: 0.75;
        }

        #authForms input[type="checkbox"] {
            background-color: var(--field-bg);
            border: 1px solid var(--field-border);
            transition: border-color 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        }

        #authForms input[type="checkbox"]:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .tab-button {
            flex: 1;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.65rem 1rem;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        @media (min-width: 640px) {
            .tab-button {
                font-size: 0.95rem;
                padding: 0.75rem 1.5rem;
            }
        }

        .tab-button:focus-visible {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
        }

        .tab-button:hover {
            color: var(--text-primary);
        }

        #tabButtons.single-tab {
            display: flex;
            width: 100%;
            gap: 0;
        }

        #tabButtons.single-tab .tab-button {
            flex: 1;
            justify-content: center;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        /* Floating Label Styles */
        .floating-label-input {
            position: relative;
        }

        .floating-label {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-subtle);
            font-size: 0.875rem;
            pointer-events: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--field-bg);
            padding: 0 0.5rem;
            border-radius: 0.25rem;
        }

        .floating-label-input input:focus ~ .floating-label,
        .floating-label-input input:not(:placeholder-shown) ~ .floating-label,
        .floating-label-input input.has-value ~ .floating-label {
            top: 0;
            font-size: 0.75rem;
            color: var(--primary-blue);
            font-weight: 600;
            background: var(--field-bg);
        }

        .dark .floating-label-input input:focus ~ .floating-label,
        .dark .floating-label-input input:not(:placeholder-shown) ~ .floating-label,
        .dark .floating-label-input input.has-value ~ .floating-label {
            color: #a5b4fc;
        }
    </style>
</head>
    <body class="theme-body flex items-center justify-center min-h-screen px-3 sm:px-4 py-6 sm:py-8 md:py-12 relative">

    @php
        $requestedRole = old('role', request('role'));
        $initialRole = in_array($requestedRole, ['student', 'teacher', 'admin']) ? $requestedRole : null;
        $requestedTab = old('tab', request('tab'));
        $selectedTab = in_array($requestedTab, ['signin', 'signup']) ? $requestedTab : 'signin';
        $hasAlerts = session('success') || session('error') || $errors->any();
    @endphp
    <div class="w-full max-w-2xl mx-auto space-y-4 sm:space-y-6 md:space-y-8">
        <div class="flex justify-end items-center gap-2 sm:gap-3">
            <x-translation-toggle />
            <x-theme-toggle />
        </div>
        <!-- Logo and Header -->
        <div id="roleSelection" class="{{ $initialRole ? 'hidden' : '' }} space-y-8">
            <div class="text-center space-y-3 sm:space-y-4">
                <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-20 sm:h-24 md:h-28 w-auto mx-auto">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold theme-text-primary px-2" data-translate="login-role-title">Choose the portal that fits how you use Q2L.</h1>
                <p class="text-sm sm:text-base theme-text-subtle px-2" data-translate="login-role-subtitle">We'll tailor the experience just for you.</p>
            </div>
            
            <!-- Role Cards Container -->
            <div class="theme-surface rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 space-y-3 sm:space-y-4 border">
                <a href="{{ route('login', ['role' => 'student', 'tab' => 'signin']) }}" data-select-role="student" data-default-tab="signin"
                    class="group block rounded-xl sm:rounded-2xl theme-tile px-5 sm:px-7 md:px-8 py-5 sm:py-6 md:py-7 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white" style="--tw-ring-color: rgba(59, 130, 246, 0.4);" role="button">
                    <div class="flex items-center justify-between gap-4 sm:gap-5">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg group-hover:scale-110 transition-transform duration-300" style="background: var(--primary-blue);">S</div>
                            <div class="flex-1">
                                <h2 class="text-lg sm:text-xl font-bold theme-text-primary mb-1.5" data-translate="login-student-title">Student</h2>
                                <p class="text-xs sm:text-sm theme-text-subtle leading-relaxed" data-translate="login-student-desc">Access classes, assignments, and announcements.</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 theme-icon-muted flex-shrink-0 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                
                <a href="{{ route('login', ['role' => 'teacher', 'tab' => 'signin']) }}" data-select-role="teacher" data-default-tab="signin"
                    class="group block rounded-xl sm:rounded-2xl theme-tile px-5 sm:px-7 md:px-8 py-5 sm:py-6 md:py-7 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white" style="--tw-ring-color: rgba(59, 130, 246, 0.4);" role="button">
                    <div class="flex items-center justify-between gap-4 sm:gap-5">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg group-hover:scale-110 transition-transform duration-300" style="background: #10b981;">T</div>
                            <div class="flex-1">
                                <h2 class="text-lg sm:text-xl font-bold theme-text-primary mb-1.5" data-translate="login-teacher-title">Teacher</h2>
                                <p class="text-xs sm:text-sm theme-text-subtle leading-relaxed" data-translate="login-teacher-desc">Manage your classes and connect with students.</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 theme-icon-muted flex-shrink-0 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                
                <div class="pt-4 text-center">
                    <p class="text-sm theme-text-subtle">Need administrative access? <a href="#" id="adminLoginLink" class="font-semibold hover:opacity-70 transition" style="color: var(--primary-blue);">Sign in as admin</a></p>
                </div>
            </div>
        </div>

        <div id="authForms" class="{{ $initialRole ? '' : 'hidden' }} theme-surface rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-6 md:space-y-8 border">
            <div class="rounded-xl sm:rounded-2xl border-2 portal-banner px-4 sm:px-6 py-4 sm:py-6 shadow-sm" style="border-color: rgba(59, 130, 246, 0.2);">
                <div class="space-y-3 sm:space-y-4 md:space-y-5 text-center">
                    <div class="space-y-1">
                        <span class="text-[0.6rem] sm:text-[0.65rem] font-semibold uppercase tracking-[0.45em]" style="color: var(--primary-blue);" data-translate="login-current-portal">Current Portal</span>
                        <p id="roleHeading" class="text-xl sm:text-2xl font-semibold theme-text-primary" data-translate="login-portal-title">Student Portal</p>
                        <p id="roleSubtitle" class="text-xs sm:text-sm theme-text-subtle portal-banner-subtitle" data-translate="login-portal-subtitle">Sign in or create your account to continue.</p>
                    </div>
                    <button type="button" id="changeRoleButton" class="inline-flex items-center justify-center gap-2 rounded-full px-4 sm:px-5 py-2 sm:py-2.5 text-xs font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white" style="background: var(--primary-blue); box-shadow: 0 4px 15px -5px rgba(59, 130, 246, 0.4);" onmouseover="this.style.background='var(--primary-blue-hover)'; this.style.boxShadow='0 6px 20px -5px rgba(59, 130, 246, 0.5)';" onmouseout="this.style.background='var(--primary-blue)'; this.style.boxShadow='0 4px 15px -5px rgba(59, 130, 246, 0.4)';">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" class="h-3.5 w-3.5 sm:h-4 sm:w-4">
                            <path d="M8 5l4 4-4 4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4 9h8" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        <span class="hidden sm:inline" data-translate="login-change-portal">Change portal</span>
                        <span class="sm:hidden" data-translate="login-change">Change</span>
                    </button>
                </div>
            </div>
            <p id="adminNotice" class="hidden rounded-xl border border-amber-200 dark:border-amber-500/40 bg-amber-50 dark:bg-amber-500/10 px-4 py-3 text-xs text-amber-700 dark:text-amber-300">Administrator sign-in only. Accounts are provisioned by the IT team.</p>

            <!-- Tab Headers -->
            <div class="rounded-xl sm:rounded-2xl p-1" id="tabHeader" style="background: rgba(59, 130, 246, 0.1);">
                <div id="tabButtons" class="grid grid-cols-2 gap-1">
                    <button id="signInTab" class="tab-button {{ $selectedTab === 'signin' ? 'tab-active' : '' }}" type="button" data-translate="login-signin">
                        Sign In
                    </button>
                    <button id="signUpTab" class="tab-button {{ $selectedTab === 'signup' ? 'tab-active' : '' }}" type="button" data-translate="login-signup">
                        Sign Up
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <div id="alertStack" class="space-y-3 {{ $hasAlerts ? '' : 'hidden' }}" data-has-alerts="{{ $hasAlerts ? 'true' : 'false' }}" data-target-tab="{{ $selectedTab }}">
                @if(session('success'))
                    <div class="rounded-xl border border-emerald-200 dark:border-emerald-500/40 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-200 shadow-sm" role="alert">
                        <span class="font-semibold">Success:</span> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="rounded-xl border border-amber-200 dark:border-amber-500/40 bg-amber-50 dark:bg-amber-500/10 px-4 py-3 text-sm text-amber-700 dark:text-amber-300 shadow-sm" role="alert">
                        <span class="font-semibold">Notice:</span> {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="rounded-xl border border-rose-200 dark:border-rose-500/40 bg-rose-50 dark:bg-rose-500/10 px-4 py-3 text-sm text-rose-600 dark:text-rose-200 shadow-sm" role="alert">
                        <div class="space-y-1">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sign In Form -->
            <form id="signInForm" action="{{ route('login') }}" method="POST" class="{{ $selectedTab === 'signin' ? '' : 'hidden' }} space-y-4 sm:space-y-5 md:space-y-7">
                @csrf
                <input type="hidden" name="tab" value="signin">
                <input type="hidden" name="role" id="signInRoleInput" value="{{ $initialRole }}">
                <div class="space-y-1 sm:space-y-2 text-center">
                    <h2 class="text-xl sm:text-2xl font-semibold theme-text-primary" data-translate="login-welcome">Welcome</h2>
                    <p class="text-xs sm:text-sm theme-text-subtle px-2" data-role-copy="signin" data-translate="login-signin-desc">Enter your credentials to access your student dashboard.</p>
                </div>
                <div class="space-y-3">
                    <div class="floating-label-input">
                        <input type="text" id="signInEmail" name="email" value="{{ old('tab') === 'signin' ? old('email') : '' }}" placeholder=" " required
                            class="peer block w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-4 pt-6 pb-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2" style="--tw-border-color-focus: var(--primary-blue); --tw-ring-color: rgba(59, 130, 246, 0.2);"">
                        <label for="signInEmail" class="floating-label" data-translate="login-email-username">Email or Username</label>
                    </div>
                    <div class="floating-label-input">
                        <div class="relative">
                            <input type="password" id="signInPassword" name="password" placeholder=" " required
                                class="peer block w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-4 pt-6 pb-2 pr-12 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2" style="--tw-border-color-focus: var(--primary-blue); --tw-ring-color: rgba(59, 130, 246, 0.2);"">
                            <label for="signInPassword" class="floating-label" data-translate="login-password">Password</label>
                            <button type="button" id="toggleSignInPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                <svg id="eyeIconClosed" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                                <svg id="eyeIconOpen" class="w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 text-xs sm:text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input id="remember-me" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 dark:border-slate-700 cursor-pointer bg-white dark:bg-slate-900/70" style="--tw-text-opacity: 1; color: var(--primary-blue);">
                        <span style="color: var(--text-dark);" data-translate="login-remember">Remember me</span>
                    </label>
                    <a href="#" class="font-medium underline-offset-4 transition hover:opacity-70 hover:underline" style="color: var(--primary-blue);" data-translate="login-forgot">Forgot password?</a>
                </div>
                <div class="space-y-2 sm:space-y-3">
                    <button type="submit"
                        class="w-full flex justify-center rounded-xl px-4 py-3 sm:py-3.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white" style="background: var(--primary-blue); box-shadow: 0 4px 15px -5px rgba(59, 130, 246, 0.4);" onmouseover="this.style.background='var(--primary-blue-hover)'; this.style.boxShadow='0 6px 20px -5px rgba(59, 130, 246, 0.5)';" onmouseout="this.style.background='var(--primary-blue)'; this.style.boxShadow='0 4px 15px -5px rgba(59, 130, 246, 0.4)';">
                        <span data-translate="login-signin">Sign In</span>
                    </button>
                    <button id="teacherRequestBtn" type="button" class="hidden w-full rounded-xl border border-transparent px-4 py-2 text-xs font-medium transition focus:outline-none focus:ring-2 hover:opacity-90">
                        Request admin to make an account
                    </button>
                </div>
                <div class="relative text-center text-xs theme-divider" id="googleDivider">
                    <span class="relative inline-block px-3" data-translate="login-or-continue">or continue with</span>
                </div>
                <a href="{{ route('auth.google.redirect') }}" id="googleSignInButton"
                    class="flex items-center justify-center gap-2 rounded-xl border border-[#dadce0] dark:border-[#dadce0] bg-white dark:bg-white px-4 py-3 text-sm font-medium text-[#3c4043] dark:text-black transition-colors duration-200 hover:border-[#bdc1c6] dark:hover:border-[#bdc1c6] hover:bg-[#f8f9fa] dark:hover:bg-[#f8f9fa] focus:outline-none focus:ring-2 focus:ring-[#4285f4]/20 dark:focus:ring-[#4285f4]/20 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                        <path fill="#4285F4" d="M23.49 12.27c0-.82-.07-1.64-.21-2.44H12v4.62h6.48c-.28 1.5-1.13 2.86-2.4 3.74v3.1h3.87c2.27-2.09 3.54-5.17 3.54-9.02Z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.96-1.07 7.94-2.91l-3.87-3.1c-1.08.73-2.46 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.95H1.3v3.12C3.33 21.3 7.36 24 12 24Z"/>
                        <path fill="#FBBC05" d="M5.27 14.2c-.25-.73-.39-1.51-.39-2.31s.14-1.58.39-2.31V6.46H1.3A11.99 11.99 0 0 0 0 11.89c0 1.92.46 3.73 1.3 5.43l3.97-3.12Z"/>
                        <path fill="#EA4335" d="M12 4.74c1.76 0 3.34.6 4.58 1.78l3.43-3.43C17.94 1.19 15.22 0 12 0 7.36 0 3.33 2.7 1.3 6.46L5.27 9.2C6.22 6.36 8.87 4.24 12 4.24Z"/>
                    </svg>
                    <span data-translate="login-google-signin">Sign in with Google</span>
                </a>
            </form>

            <!-- Sign Up Form -->
            <form id="signUpForm" action="{{ route('register') }}" method="POST" class="{{ $selectedTab === 'signup' ? '' : 'hidden' }} space-y-4 sm:space-y-5 md:space-y-7">
                @csrf
                <input type="hidden" name="tab" value="signup">
                <input type="hidden" name="role" id="signUpRoleInput" value="{{ $initialRole }}">
                <div class="space-y-1 sm:space-y-2 text-center">
                    <h2 class="text-xl sm:text-2xl font-semibold theme-text-primary" data-translate="login-create-account">Create your account</h2>
                    <p class="text-xs sm:text-sm theme-text-subtle px-2" data-role-copy="signup" data-translate="login-signup-desc">Join Q2L to start learning with your community.</p>
                    <p class="text-xs theme-text-subtle" data-role-register-label data-translate="login-registering-as">Registering as a student</p>
                </div>
                <div class="space-y-3">
                    <div class="floating-label-input">
                        <input type="text" id="signUpName" name="name" value="{{ old('tab') === 'signup' ? old('name') : '' }}" placeholder=" " required
                            class="peer block w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-4 pt-6 pb-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2" style="--tw-border-color-focus: var(--primary-blue); --tw-ring-color: rgba(59, 130, 246, 0.2);"">
                        <label for="signUpName" class="floating-label" data-translate="login-name">Full Name</label>
                    </div>
                    <div class="floating-label-input">
                        <input type="email" id="signUpEmail" name="email" value="{{ old('tab') === 'signup' ? old('email') : '' }}" placeholder=" " required
                            class="peer block w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-4 pt-6 pb-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2" style="--tw-border-color-focus: var(--primary-blue); --tw-ring-color: rgba(59, 130, 246, 0.2);"">
                        <label for="signUpEmail" class="floating-label" data-translate="login-email">Email Address</label>
                    </div>
                    <div class="space-y-2">
                        <label for="signUpGradeLevel" class="block text-sm font-medium text-gray-700 dark:text-gray-300" data-translate="login-grade-level">Grade Level</label>
                        <select id="signUpGradeLevel" name="grade_level" required
                            class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-4 py-3 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2" style="--tw-border-color-focus: var(--primary-blue); --tw-ring-color: rgba(59, 130, 246, 0.2);"">
                            <option value="" disabled {{ (old('tab') === 'signup' && old('grade_level')) ? '' : 'selected' }} data-translate="login-select-grade">Select grade level</option>
                            @foreach(['7' => 'Grade 7', '8' => 'Grade 8', '9' => 'Grade 9', '10' => 'Grade 10'] as $value => $label)
                                <option value="{{ $value }}" {{ (old('tab') === 'signup' && old('grade_level') == $value) ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('grade_level')
                            <p class="text-xs text-rose-500 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="floating-label-input">
                        <div class="relative">
                            <input type="password" id="signUpPassword" name="password" placeholder=" " required
                                class="peer block w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-4 pt-6 pb-2 pr-12 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2" style="--tw-border-color-focus: var(--primary-blue); --tw-ring-color: rgba(59, 130, 246, 0.2);"">
                            <label for="signUpPassword" class="floating-label" data-translate="login-password">Password</label>
                            <button type="button" id="toggleSignUpPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                <svg class="eye-closed w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                                <svg class="eye-open w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="floating-label-input">
                        <div class="relative">
                            <input type="password" id="confirmPassword" name="password_confirmation" placeholder=" " required
                                class="peer block w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-4 pt-6 pb-2 pr-12 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2" style="--tw-border-color-focus: var(--primary-blue); --tw-ring-color: rgba(59, 130, 246, 0.2);"">
                            <label for="confirmPassword" class="floating-label" data-translate="login-confirm-password">Confirm Password</label>
                            <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                <svg class="eye-closed w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                                <svg class="eye-open w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="submit"
                    class="w-full flex justify-center rounded-xl px-4 py-3 sm:py-3.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900" style="background: var(--primary-blue); box-shadow: 0 4px 15px -5px rgba(59, 130, 246, 0.4);" onmouseover="this.style.background='var(--primary-blue-hover)'; this.style.boxShadow='0 6px 20px -5px rgba(59, 130, 246, 0.5)';" onmouseout="this.style.background='var(--primary-blue)'; this.style.boxShadow='0 4px 15px -5px rgba(59, 130, 246, 0.4)';">
                    <span data-translate="login-create-account-btn">Create Account</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Teacher Request Modal -->
    <div id="teacherRequestModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 dark:bg-black/70 px-3 sm:px-4 py-4 sm:py-6">
        <div class="w-full max-w-lg rounded-xl sm:rounded-2xl bg-white dark:bg-slate-900 shadow-xl border border-transparent dark:border-slate-800/80 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-700 px-4 sm:px-6 py-3 sm:py-4 sticky top-0 bg-white dark:bg-slate-900">
                <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100">Request Teacher Account</h3>
                <button id="teacherRequestClose" class="text-gray-400 dark:text-gray-500 transition hover:text-gray-600 dark:hover:text-gray-300 text-xl sm:text-2xl leading-none" type="button" aria-label="Close">✕</button>
            </div>
            <form action="{{ route('teacher.request') }}" method="POST" class="space-y-3 sm:space-y-4 px-4 sm:px-6 py-4 sm:py-5">
                @csrf
                <p class="text-sm text-gray-600 dark:text-gray-300">Fill out the details below. An administrator will approve your account before you can sign in.</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Fields marked with <span class="text-red-500">*</span> are required.</p>
                @if($errors->any())
                    <div class="rounded-lg bg-red-50 dark:bg-rose-500/10 border border-transparent dark:border-rose-500/40 p-3 text-sm text-red-600 dark:text-rose-200">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500/20 dark:focus:ring-blue-500/30">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500/20 dark:focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Subject <span class="text-red-500">*</span></label>
                        <select name="subject" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500/20 dark:focus:ring-blue-500/30">
                            <option value="">Select</option>
                            <option value="english">English</option>
                            <option value="filipino">Filipino</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Grade Level <span class="text-red-500">*</span></label>
                        <select name="grade_level" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500/20 dark:focus:ring-blue-500/30">
                            <option value="">Select</option>
                            <option value="7">Grade 7</option>
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                            <option value="10">Grade 10</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Notes for Admin (optional)</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/70 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500/20 dark:focus:ring-blue-500/30" placeholder="Provide context for your request"></textarea>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-3 border-t border-gray-200 dark:border-slate-700 pt-3 sm:pt-4">
                    <button type="button" id="teacherRequestCancel" class="w-full sm:w-auto rounded-lg border border-gray-200 dark:border-slate-700 px-4 py-2.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300 transition hover:bg-gray-50 dark:hover:bg-slate-900/70">Cancel</button>
                    <button type="submit" class="w-full sm:w-auto rounded-lg px-4 py-2.5 sm:py-2 text-xs sm:text-sm font-semibold text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white" style="background: var(--primary-blue);" onmouseover="this.style.background='var(--primary-blue-hover)';" onmouseout="this.style.background='var(--primary-blue)';"">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const roleSelection = document.getElementById('roleSelection');
        const authForms = document.getElementById('authForms');
        const roleHeading = document.getElementById('roleHeading');
        const roleSubtitle = document.getElementById('roleSubtitle');
        const changeRoleButton = document.getElementById('changeRoleButton');
        const roleChoiceButtons = document.querySelectorAll('[data-select-role]');
        const signInTab = document.getElementById('signInTab');
        const signUpTab = document.getElementById('signUpTab');
        const tabHeader = document.getElementById('tabHeader');
        const signInForm = document.getElementById('signInForm');
        const signUpForm = document.getElementById('signUpForm');
        const signInRoleInput = document.getElementById('signInRoleInput');
        const signUpRoleInput = document.getElementById('signUpRoleInput');
        const roleCopyElements = document.querySelectorAll('[data-role-copy]');
        const roleRegisterLabel = document.querySelector('[data-role-register-label]');
        const googleDivider = document.getElementById('googleDivider');
        const googleButton = document.getElementById('googleSignInButton');
        const adminLoginLink = document.getElementById('adminLoginLink');
        const adminNotice = document.getElementById('adminNotice');
        const teacherRequestBtn = document.getElementById('teacherRequestBtn');
        const teacherRequestModal = document.getElementById('teacherRequestModal');
        const teacherRequestClose = document.getElementById('teacherRequestClose');
        const teacherRequestCancel = document.getElementById('teacherRequestCancel');
        const tabButtons = document.getElementById('tabButtons');
        const alertStack = document.getElementById('alertStack');
        const alertHasMessages = () => alertStack && alertStack.dataset.hasAlerts === 'true';
        const alertTargetTab = () => (alertStack ? alertStack.dataset.targetTab || null : null);
        const setAlertPresence = (value) => {
            if (!alertStack) return;
            alertStack.classList.toggle('hidden', !value);
        };

        const initialRole = "{{ $initialRole }}" || null;
        const initialTab = "{{ $selectedTab }}";

        let currentRole = initialRole;
        let currentTab = initialTab || 'signin';
        let canSignUp = currentRole === 'student';
        window.currentRole = currentRole; // Make it globally accessible for translations

        function setUrl(role, tab) {
            const params = new URLSearchParams();
            if (role) {
                params.set('role', role);
            }
            if (tab) {
                params.set('tab', tab);
            }
            const query = params.toString();
            const newUrl = query ? `${window.location.pathname}?${query}` : window.location.pathname;
            window.history.replaceState({}, '', newUrl);
        }

        function humanizeRole(role) {
            switch (role) {
                case 'teacher':
                    return {
                        label: 'teacher',
                        portalTitle: 'Teacher Portal',
                        subtitle: 'Sign in or create your account to manage classes.',
                        copySignin: 'Enter your credentials to access your teacher dashboard.',
                        copySignup: 'Provide details to begin teaching with Q2L.',
                    };
                case 'admin':
                    return {
                        label: 'administrator',
                        portalTitle: 'Admin Console',
                        subtitle: 'Sign in to manage the platform and its users.',
                        copySignin: 'Enter your credentials to manage the platform.',
                        copySignup: 'Administrator accounts must be provisioned by IT.',
                    };
                default:
                    return {
                        label: 'student',
                        portalTitle: 'Student Portal',
                        subtitle: 'Sign in or create your account to continue.',
                        copySignin: 'Enter your credentials to access your student dashboard.',
                        copySignup: 'Join Q2L to start learning with your community.',
                    };
            }
        }

        function updateRole(role) {
            const roleInfo = humanizeRole(role);
            signInRoleInput.value = role;
            if (signUpRoleInput) {
                signUpRoleInput.value = role;
            }

            currentRole = role;
            window.currentRole = role; // Update global for translations
            canSignUp = role === 'student';

            // Apply translations if language is set
            const savedLang = localStorage.getItem('selectedLanguage') || 'en';
            if (savedLang !== 'en' && window.changeLanguage) {
                window.changeLanguage(savedLang);
            } else {
                // Fallback to English if no translation
                roleHeading.textContent = roleInfo.portalTitle;
                roleSubtitle.textContent = roleInfo.subtitle;

                roleCopyElements.forEach((el) => {
                    const context = el.dataset.roleCopy;
                    if (context === 'signin') {
                        el.textContent = roleInfo.copySignin;
                    } else if (context === 'signup') {
                        el.textContent = roleInfo.copySignup;
                    }
                });

                if (roleRegisterLabel) {
                    if (!canSignUp) {
                        roleRegisterLabel.classList.add('hidden');
                    } else {
                        roleRegisterLabel.classList.remove('hidden');
                        roleRegisterLabel.textContent = `Registering as a ${roleInfo.label}`;
                    }
                }
            }

            if (googleDivider && googleButton) {
                const shouldShowGoogle = role === 'student';
                googleDivider.classList.toggle('hidden', !shouldShowGoogle);
                googleButton.classList.toggle('hidden', !shouldShowGoogle);
            }

            const isAdmin = role === 'admin';
            adminNotice.classList.toggle('hidden', !isAdmin);

            const isTeacher = role === 'teacher';
            if (teacherRequestBtn) {
                teacherRequestBtn.classList.toggle('hidden', !isTeacher);
            }

            if (!canSignUp) {
                showSignIn();
                if (tabButtons) {
                    tabButtons.classList.add('single-tab');
                }
                signUpTab.classList.add('hidden');
                signUpForm.classList.add('hidden');
            } else {
                if (tabButtons) {
                    tabButtons.classList.remove('single-tab');
                }
                signUpTab.classList.remove('hidden');
            }
        }

        function revealForms(role, initialTab = 'signin') {
            updateRole(role);
            roleSelection.classList.add('hidden');
            authForms.classList.remove('hidden');
            if (initialTab === 'signup' && canSignUp) {
                showSignUp();
            } else {
                showSignIn();
            }
        }

        function showSignIn() {
            signInTab.classList.add('tab-active');
            signUpTab.classList.remove('tab-active');

            signInForm.classList.remove('hidden');
            signUpForm.classList.add('hidden');
            currentTab = 'signin';
            setUrl(currentRole, currentTab);
            if (alertStack) {
                const desiredTab = alertTargetTab();
                const shouldShow = alertHasMessages() && (!desiredTab || desiredTab === 'signin');
                setAlertPresence(shouldShow);
            }
        }

        function showSignUp() {
            if (!canSignUp) {
                showSignIn();
                return;
            }
            signUpTab.classList.add('tab-active');
            signInTab.classList.remove('tab-active');

            signUpForm.classList.remove('hidden');
            signInForm.classList.add('hidden');
            currentTab = 'signup';
            setUrl(currentRole, currentTab);
            if (alertStack) {
                const desiredTab = alertTargetTab();
                const shouldShow = alertHasMessages() && desiredTab === 'signup';
                setAlertPresence(shouldShow);
            }
        }

        signInTab.addEventListener('click', showSignIn);
        signUpTab.addEventListener('click', showSignUp);

        roleChoiceButtons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                const role = btn.dataset.selectRole;
                const defaultTab = btn.dataset.defaultTab || 'signin';
                revealForms(role, defaultTab);
                const targetUrl = btn.getAttribute('href');
                if (targetUrl) {
                    window.history.replaceState({}, '', targetUrl);
                }
            });
        });

        if (changeRoleButton) {
            changeRoleButton.addEventListener('click', () => {
                authForms.classList.add('hidden');
                roleSelection.classList.remove('hidden');

                signInRoleInput.value = '';
                if (signUpRoleInput) {
                    signUpRoleInput.value = '';
                }

                adminNotice.classList.add('hidden');
                if (tabButtons) {
                    tabButtons.classList.remove('single-tab');
                }
                signUpTab.classList.remove('hidden');
                showSignIn();

                if (googleDivider && googleButton) {
                    googleDivider.classList.add('hidden');
                    googleButton.classList.add('hidden');
                }

                currentRole = null;
                currentTab = 'signin';
                canSignUp = false;
                setUrl(currentRole, currentTab);
                setAlertPresence(false);
            });
        }

        adminLoginLink.addEventListener('click', (event) => {
            event.preventDefault();
            revealForms('admin');
        });

        if (teacherRequestBtn && teacherRequestModal) {
            teacherRequestBtn.addEventListener('click', () => {
                teacherRequestModal.classList.remove('hidden');
                // Update modal colors when opened to ensure theme is applied
                setTimeout(updateModalColors, 10);
            });
        }

        const hideTeacherModal = () => {
            if (teacherRequestModal) {
                teacherRequestModal.classList.add('hidden');
            }
        };

        if (teacherRequestClose && teacherRequestModal) {
            teacherRequestClose.addEventListener('click', hideTeacherModal);
        }

        if (teacherRequestCancel && teacherRequestModal) {
            teacherRequestCancel.addEventListener('click', hideTeacherModal);
        }

        document.addEventListener('click', (event) => {
            if (teacherRequestModal && !teacherRequestModal.classList.contains('hidden')) {
                if (event.target === teacherRequestModal) {
                    hideTeacherModal();
                }
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                hideTeacherModal();
            }
        });

        // Check if there are validation errors for signup
        @if($errors->has('name') || $errors->has('password_confirmation'))
            showSignUp();
        @endif

        if (initialRole) {
            revealForms(initialRole, initialTab || 'signin');
        }

        // Password visibility toggle for Sign In
        const toggleSignInPassword = document.getElementById('toggleSignInPassword');
        const signInPasswordInput = document.getElementById('signInPassword');
        const eyeIconClosed = document.getElementById('eyeIconClosed');
        const eyeIconOpen = document.getElementById('eyeIconOpen');

        toggleSignInPassword?.addEventListener('click', () => {
            const isPassword = signInPasswordInput.type === 'password';
            signInPasswordInput.type = isPassword ? 'text' : 'password';
            eyeIconClosed.classList.toggle('hidden');
            eyeIconOpen.classList.toggle('hidden');
        });

        // Password visibility toggle for Sign Up
        const toggleSignUpPassword = document.getElementById('toggleSignUpPassword');
        const signUpPasswordInput = document.getElementById('signUpPassword');

        toggleSignUpPassword?.addEventListener('click', () => {
            const isPassword = signUpPasswordInput.type === 'password';
            signUpPasswordInput.type = isPassword ? 'text' : 'password';
            const closedIcon = toggleSignUpPassword.querySelector('.eye-closed');
            const openIcon = toggleSignUpPassword.querySelector('.eye-open');
            closedIcon.classList.toggle('hidden');
            openIcon.classList.toggle('hidden');
        });

        // Password visibility toggle for Confirm Password
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');

        toggleConfirmPassword?.addEventListener('click', () => {
            const isPassword = confirmPasswordInput.type === 'password';
            confirmPasswordInput.type = isPassword ? 'text' : 'password';
            const closedIcon = toggleConfirmPassword.querySelector('.eye-closed');
            const openIcon = toggleConfirmPassword.querySelector('.eye-open');
            closedIcon.classList.toggle('hidden');
            openIcon.classList.toggle('hidden');
        });

        // Floating label handling for inputs with values
        function handleFloatingLabels() {
            const inputs = document.querySelectorAll('.floating-label-input input');
            inputs.forEach(input => {
                // Check if input has value on page load
                if (input.value) {
                    input.classList.add('has-value');
                }
                
                // Add class when input has value
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.classList.add('has-value');
                    } else {
                        this.classList.remove('has-value');
                    }
                });
            });
        }

        // Call on page load
        handleFloatingLabels();

        // Re-check when forms are revealed
        const originalRevealForms = window.revealForms;
        window.revealForms = function(role, tab) {
            originalRevealForms(role, tab);
            setTimeout(handleFloatingLabels, 0);
        };

        // Translation functionality
        window.changeLanguage = function(lang) {
                localStorage.setItem('selectedLanguage', lang);
                const langText = lang === 'fil' ? 'Filipino' : lang === 'bis' ? 'Bisaya' : 'English';
                document.querySelectorAll('.translation-current-lang').forEach(el => {
                    el.textContent = langText;
                });
                
                const translations = {
                    'en': {
                        'login-role-title': 'Choose the portal that fits how you use Q2L.',
                        'login-role-subtitle': "We'll tailor the experience just for you.",
                        'login-student-title': 'Student',
                        'login-student-desc': 'Access classes, assignments, and announcements.',
                        'login-teacher-title': 'Teacher',
                        'login-teacher-desc': 'Manage classes, students, and course content.',
                        'login-current-portal': 'Current Portal',
                        'login-portal-title': 'Student Portal',
                        'login-portal-subtitle': 'Sign in or create your account to continue.',
                        'login-change-portal': 'Change portal',
                        'login-change': 'Change',
                        'login-signin': 'Sign In',
                        'login-signup': 'Sign Up',
                        'login-welcome': 'Welcome',
                        'login-signin-desc': 'Enter your credentials to access your student dashboard.',
                        'login-signup-desc': 'Join Q2L to start learning with your community.',
                        'login-email-username': 'Email or Username',
                        'login-password': 'Password',
                        'login-remember': 'Remember me',
                        'login-forgot': 'Forgot password?',
                        'login-or-continue': 'or continue with',
                        'login-google-signin': 'Sign in with Google',
                        'login-create-account': 'Create your account',
                        'login-registering-as': 'Registering as a student',
                        'login-name': 'Full Name',
                        'login-email': 'Email Address',
                        'login-grade-level': 'Grade Level',
                        'login-select-grade': 'Select grade level',
                        'login-confirm-password': 'Confirm Password',
                        'login-create-account-btn': 'Create Account'
                    },
                    'fil': {
                        'login-role-title': 'Piliin ang portal na akma sa kung paano mo ginagamit ang Q2L.',
                        'login-role-subtitle': 'I-aangkop namin ang karanasan para sa iyo.',
                        'login-student-title': 'Mag-aaral',
                        'login-student-desc': 'I-access ang mga klase, takdang-aralin, at anunsyo.',
                        'login-teacher-title': 'Guro',
                        'login-teacher-desc': 'Pamahalaan ang mga klase, mag-aaral, at nilalaman ng kurso.',
                        'login-current-portal': 'Kasalukuyang Portal',
                        'login-portal-title': 'Portal ng Mag-aaral',
                        'login-portal-subtitle': 'Mag-sign in o gumawa ng account upang magpatuloy.',
                        'login-change-portal': 'Palitan ang portal',
                        'login-change': 'Palitan',
                        'login-signin': 'Mag-sign In',
                        'login-signup': 'Mag-sign Up',
                        'login-welcome': 'Maligayang Pagdating',
                        'login-signin-desc': 'Ilagay ang iyong mga kredensyal upang ma-access ang iyong dashboard ng mag-aaral.',
                        'login-signup-desc': 'Sumali sa Q2L upang magsimulang matuto kasama ang iyong komunidad.',
                        'login-email-username': 'Email o Username',
                        'login-password': 'Password',
                        'login-remember': 'Tandaan ako',
                        'login-forgot': 'Nakalimutan ang password?',
                        'login-or-continue': 'o magpatuloy gamit ang',
                        'login-google-signin': 'Mag-sign in gamit ang Google',
                        'login-create-account': 'Gumawa ng iyong account',
                        'login-registering-as': 'Nagre-rehistro bilang mag-aaral',
                        'login-name': 'Buong Pangalan',
                        'login-email': 'Email Address',
                        'login-grade-level': 'Antas ng Baitang',
                        'login-select-grade': 'Pumili ng antas ng baitang',
                        'login-confirm-password': 'Kumpirmahin ang Password',
                        'login-create-account-btn': 'Gumawa ng Account'
                    },
                    'bis': {
                        'login-role-title': 'Pilia ang portal nga haom sa kung giunsa nimo gamiton ang Q2L.',
                        'login-role-subtitle': 'I-adjust namo ang karanasan alang kanimo.',
                        'login-student-title': 'Estudyante',
                        'login-student-desc': 'I-access ang mga klase, takdang-aralin, ug mga anunsyo.',
                        'login-teacher-title': 'Magtutudlo',
                        'login-teacher-desc': 'Pangulohan ang mga klase, estudyante, ug sulod sa kurso.',
                        'login-current-portal': 'Karon nga Portal',
                        'login-portal-title': 'Portal sa Estudyante',
                        'login-portal-subtitle': 'Mag-sign in o paghimo og account aron magpadayon.',
                        'login-change-portal': 'Usba ang portal',
                        'login-change': 'Usba',
                        'login-signin': 'Mag-sign In',
                        'login-signup': 'Mag-sign Up',
                        'login-welcome': 'Maayong Pag-abot',
                        'login-signin-desc': 'Ibutang ang imong mga kredensyal aron ma-access ang imong dashboard sa estudyante.',
                        'login-signup-desc': 'Apil sa Q2L aron magsugod sa pagkat-on uban sa imong komunidad.',
                        'login-email-username': 'Email o Username',
                        'login-password': 'Password',
                        'login-remember': 'Hinumdumi ako',
                        'login-forgot': 'Nakalimtan ang password?',
                        'login-or-continue': 'o magpadayon gamit ang',
                        'login-google-signin': 'Mag-sign in gamit ang Google',
                        'login-create-account': 'Paghimo sa imong account',
                        'login-registering-as': 'Nagparehistro isip estudyante',
                        'login-name': 'Tibuok Ngalan',
                        'login-email': 'Email Address',
                        'login-grade-level': 'Antas sa Baitang',
                        'login-select-grade': 'Pilia ang antas sa baitang',
                        'login-confirm-password': 'Kumpirmaha ang Password',
                        'login-create-account-btn': 'Paghimo og Account'
                    }
                };
                
                const langData = translations[lang] || translations['en'];
                document.querySelectorAll('[data-translate]').forEach(el => {
                    const key = el.getAttribute('data-translate');
                    if (langData[key]) {
                        el.textContent = langData[key];
                    }
                });
                
                // Update dynamic role-based content
                if (window.currentRole) {
                    const roleTranslations = {
                        'en': {
                            'student': {
                                'portal-title': 'Student Portal',
                                'portal-subtitle': 'Sign in or create your account to continue.',
                                'signin-desc': 'Enter your credentials to access your student dashboard.',
                                'signup-desc': 'Join Q2L to start learning with your community.',
                                'registering-as': 'Registering as a student'
                            },
                            'teacher': {
                                'portal-title': 'Teacher Portal',
                                'portal-subtitle': 'Sign in or create your account to manage classes.',
                                'signin-desc': 'Enter your credentials to access your teacher dashboard.',
                                'signup-desc': 'Provide details to begin teaching with Q2L.',
                                'registering-as': 'Registering as a teacher'
                            },
                            'admin': {
                                'portal-title': 'Admin Console',
                                'portal-subtitle': 'Sign in to manage the platform and its users.',
                                'signin-desc': 'Enter your credentials to manage the platform.',
                                'signup-desc': 'Administrator accounts must be provisioned by IT.',
                                'registering-as': 'Registering as an administrator'
                            }
                        },
                        'fil': {
                            'student': {
                                'portal-title': 'Portal ng Mag-aaral',
                                'portal-subtitle': 'Mag-sign in o gumawa ng account upang magpatuloy.',
                                'signin-desc': 'Ilagay ang iyong mga kredensyal upang ma-access ang iyong dashboard ng mag-aaral.',
                                'signup-desc': 'Sumali sa Q2L upang magsimulang matuto kasama ang iyong komunidad.',
                                'registering-as': 'Nagre-rehistro bilang mag-aaral'
                            },
                            'teacher': {
                                'portal-title': 'Portal ng Guro',
                                'portal-subtitle': 'Mag-sign in o gumawa ng account upang pamahalaan ang mga klase.',
                                'signin-desc': 'Ilagay ang iyong mga kredensyal upang ma-access ang iyong dashboard ng guro.',
                                'signup-desc': 'Magbigay ng detalye upang magsimulang magturo sa Q2L.',
                                'registering-as': 'Nagre-rehistro bilang guro'
                            },
                            'admin': {
                                'portal-title': 'Admin Console',
                                'portal-subtitle': 'Mag-sign in upang pamahalaan ang platform at mga user nito.',
                                'signin-desc': 'Ilagay ang iyong mga kredensyal upang pamahalaan ang platform.',
                                'signup-desc': 'Ang mga account ng administrator ay dapat na i-provision ng IT.',
                                'registering-as': 'Nagre-rehistro bilang administrator'
                            }
                        },
                        'bis': {
                            'student': {
                                'portal-title': 'Portal sa Estudyante',
                                'portal-subtitle': 'Mag-sign in o paghimo og account aron magpadayon.',
                                'signin-desc': 'Ibutang ang imong mga kredensyal aron ma-access ang imong dashboard sa estudyante.',
                                'signup-desc': 'Apil sa Q2L aron magsugod sa pagkat-on uban sa imong komunidad.',
                                'registering-as': 'Nagparehistro isip estudyante'
                            },
                            'teacher': {
                                'portal-title': 'Portal sa Magtutudlo',
                                'portal-subtitle': 'Mag-sign in o paghimo og account aron pangulohan ang mga klase.',
                                'signin-desc': 'Ibutang ang imong mga kredensyal aron ma-access ang imong dashboard sa magtutudlo.',
                                'signup-desc': 'Hatagi og detalye aron magsugod sa pagtudlo sa Q2L.',
                                'registering-as': 'Nagparehistro isip magtutudlo'
                            },
                            'admin': {
                                'portal-title': 'Admin Console',
                                'portal-subtitle': 'Mag-sign in aron pangulohan ang platform ug mga user niini.',
                                'signin-desc': 'Ibutang ang imong mga kredensyal aron pangulohan ang platform.',
                                'signup-desc': 'Ang mga account sa administrator kinahanglan i-provision sa IT.',
                                'registering-as': 'Nagparehistro isip administrator'
                            }
                        }
                    };
                    
                    const roleLangData = roleTranslations[lang] || roleTranslations['en'];
                    const roleData = roleLangData[window.currentRole] || roleLangData['student'];
                    
                    const roleHeading = document.getElementById('roleHeading');
                    const roleSubtitle = document.getElementById('roleSubtitle');
                    const roleCopyElements = document.querySelectorAll('[data-role-copy]');
                    const roleRegisterLabel = document.querySelector('[data-role-register-label]');
                    
                    if (roleHeading && roleData['portal-title']) {
                        roleHeading.textContent = roleData['portal-title'];
                    }
                    if (roleSubtitle && roleData['portal-subtitle']) {
                        roleSubtitle.textContent = roleData['portal-subtitle'];
                    }
                    
                    roleCopyElements.forEach((el) => {
                        const context = el.dataset.roleCopy;
                        if (context === 'signin' && roleData['signin-desc']) {
                            el.textContent = roleData['signin-desc'];
                        } else if (context === 'signup' && roleData['signup-desc']) {
                            el.textContent = roleData['signup-desc'];
                        }
                    });
                    
                    if (roleRegisterLabel && roleData['registering-as']) {
                        roleRegisterLabel.textContent = roleData['registering-as'];
                    }
                }
        }

        // Function to update teacher request button colors based on theme
        function updateTeacherRequestButton() {
            const teacherRequestBtn = document.getElementById('teacherRequestBtn');
            if (teacherRequestBtn) {
                const isDarkMode = document.documentElement.classList.contains('dark');
                if (isDarkMode) {
                    // Dark mode: white background, black text
                    teacherRequestBtn.style.backgroundColor = '#ffffff';
                    teacherRequestBtn.style.color = '#000000';
                } else {
                    // Light mode: black background, white text
                    teacherRequestBtn.style.backgroundColor = '#000000';
                    teacherRequestBtn.style.color = '#ffffff';
                }
            }
        }

        // Function to force update modal colors (ensures Tailwind classes apply)
        function updateModalColors() {
            const modal = document.getElementById('teacherRequestModal');
            if (modal && !modal.classList.contains('hidden')) {
                // If modal is visible, trigger a small reflow to ensure classes update
                const wasVisible = modal.style.display !== 'none';
                if (wasVisible) {
                    modal.style.visibility = 'hidden';
                    modal.offsetHeight; // Trigger reflow
                    modal.style.visibility = '';
                }
            }
        }

        // Update button colors on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTeacherRequestButton();
            
            // Watch for theme changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        updateTeacherRequestButton();
                        updateModalColors();
                    }
                });
            });
            
            // Observe the html element for class changes
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });

            // Also listen to themechange events from the theme manager
            window.addEventListener('themechange', function(event) {
                updateTeacherRequestButton();
                updateModalColors();
            });
            
            const savedLang = localStorage.getItem('selectedLanguage') || 'en';
            const langText = savedLang === 'fil' ? 'Filipino' : savedLang === 'bis' ? 'Bisaya' : 'English';
            document.querySelectorAll('.translation-current-lang').forEach(el => {
                el.textContent = langText;
            });
            if (savedLang !== 'en') {
                window.changeLanguage(savedLang);
            }
        });

    </script>

</body>
</html>
