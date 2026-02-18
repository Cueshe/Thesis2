<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PDF Reader · Quest2Learn</title>

    <x-theme-script />

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        :root {

            color-scheme: light;

            --brand-primary: #4f46e5;

            --brand-primary-dark: #4338ca;

            --page-bg: #f8fafc;

            --card-bg: #ffffff;

            --sidebar-bg: rgba(255, 255, 255, 0.94);

            --surface-border: rgba(148, 163, 184, 0.45);

            --text-primary: #1e293b;

            --text-muted: #64748b;

            --text-subtle: #7b8aa8;

        }



        .dark {

            color-scheme: dark;

            --brand-primary: #6366f1;

            --brand-primary-dark: #818cf8;

            --page-bg: #0f172a;

            --card-bg: rgba(15, 23, 42, 0.88);

            --sidebar-bg: rgba(15, 23, 42, 0.92);

            --surface-border: rgba(71, 85, 105, 0.55);

            --text-primary: #e2e8f0;

            --text-muted: #cbd5e5;

            --text-subtle: #94a3b8;

        }



        body {

            font-family: 'Inter', sans-serif;

            background: var(--page-bg);

            color: var(--text-primary);

            min-height: 100vh;

            margin: 0;

        }



        .layout-shell {

            padding: 0.5rem 0.75rem;

        }

        @media (min-width: 768px) {

            .layout-shell {

                padding: 2rem 1rem;

            }

        }

        @media (min-width: 1024px) {

            .layout-shell {

                padding: 2.5rem 1.5rem;

            }

        }

        .layout-grid {

            max-width: 1400px;

            margin: 0 auto;

            display: grid;

            gap: 1.5rem;

        }

        @media (min-width: 1024px) {

            .layout-grid {

                grid-template-columns: 260px 1fr;

            }

        }

        .dashboard-sidebar {

            background: var(--sidebar-bg);

            border-radius: 1.5rem;

            border: 1px solid var(--surface-border);

            transition: background-color 0.35s ease, border-color 0.35s ease, color 0.35s ease;

        }

        .main-content {

            padding-bottom: 6rem;

        }

        .dashboard-card {

            background: var(--card-bg);

            border-radius: 1.5rem;

            border: 1px solid var(--surface-border);

            transition: background-color 0.35s ease, border-color 0.35s ease, color 0.35s ease;

        }

        .nav-link {

            display: flex;

            align-items: center;

            gap: 0.75rem;

            padding: 0.625rem 0.875rem;

            border-radius: 0.75rem;

            font-size: 0.875rem;

            font-weight: 500;

            color: var(--text-subtle);

            transition: all 0.15s ease;

        }

        .nav-link:hover {

            color: var(--text-primary);

            background: rgba(79, 70, 229, 0.12);

        }

        .nav-link.active {

            color: var(--brand-primary);

            background: rgba(129, 140, 248, 0.24);

            font-weight: 600;

        }

        .pdf-item {

            background: var(--card-bg);

            border: 1px solid var(--surface-border);

            border-radius: 1rem;

            padding: 1.5rem;

            transition: all 200ms ease;

            cursor: pointer;

        }

        .pdf-item:hover {

            border-color: var(--brand-primary);

            box-shadow: 0 8px 25px -12px rgba(79, 70, 229, 0.25);

        }

        .pdf-viewer {

            background: var(--card-bg);

            border: 1px solid var(--surface-border);

            border-radius: 1rem;

            height: 70vh;

            position: relative;

            overflow: hidden;

        }

        .pdf-canvas {

            max-width: 100%;

            max-height: 100%;

            display: block;

            margin: 0 auto;

        }

        .recording-indicator {

            position: fixed;

            top: 20px;

            right: 20px;

            background: #ef4444;

            color: white;

            padding: 0.75rem 1rem;

            border-radius: 2rem;

            font-weight: 600;

            display: flex;

            align-items: center;

            gap: 0.5rem;

            z-index: 1000;

            animation: pulse 2s infinite;

        }

        @keyframes pulse {

            0%, 100% { opacity: 1; }

            50% { opacity: 0.7; }

        }

        .word-highlight {

            background-color: #fef3c7;

            padding: 2px 4px;

            border-radius: 4px;

            cursor: pointer;

            transition: all 200ms ease;

            color: #0f172a;

        }

        .dark .word-highlight {

            background-color: rgba(254, 243, 199, 0.92);

            color: #0b1220;

        }

        .word-highlight:hover {

            background-color: #fde68a;

        }

        .dark .word-highlight:hover {

            background-color: rgba(253, 230, 138, 0.95);

        }

        .word-highlight.difficult {

            background-color: #fecaca;

            color: #0f172a;

        }

        .dark .word-highlight.difficult {

            background-color: rgba(254, 202, 202, 0.92);

            color: #0b1220;

        }

        .word-highlight.difficult:hover {

            background-color: #fca5a5;

        }

        .dark .word-highlight.difficult:hover {

            background-color: rgba(252, 165, 165, 0.95);

        }

        .word-highlight.read {

            background-color: rgba(16, 185, 129, 0.18);

            color: var(--text-muted);

            cursor: default;

        }

        .dark .word-highlight.read {

            background-color: rgba(16, 185, 129, 0.22);

            color: rgba(226, 232, 240, 0.8);

        }

        .word-highlight.current {

            background-color: rgba(16, 185, 129, 0.35);

            outline: 2px solid rgba(16, 185, 129, 0.5);

            color: var(--text-primary);

            cursor: default;

        }

        .tutor-card {

            background: rgba(255, 255, 255, 0.85);

            border: 1px solid rgba(16, 185, 129, 0.25);

            border-radius: 0.85rem;

        }

        .dark .tutor-card {

            background: rgba(15, 23, 42, 0.65);

            border-color: rgba(16, 185, 129, 0.35);

        }

        .tutor-pill {

            display: inline-flex;

            align-items: center;

            gap: 0.35rem;

            padding: 0.25rem 0.6rem;

            border-radius: 999px;

            font-size: 0.75rem;

            font-weight: 600;

            border: 1px solid rgba(148, 163, 184, 0.45);

            color: var(--text-primary);

            background: rgba(255, 255, 255, 0.7);

        }

        .dark .tutor-pill {

            background: rgba(15, 23, 42, 0.7);

            border-color: rgba(71, 85, 105, 0.55);

        }



        .surface-muted {

            background: rgba(148, 163, 184, 0.14);

            border: 1px solid rgba(148, 163, 184, 0.35);

            color: var(--text-primary);

        }

        .dark .surface-muted {

            background: rgba(30, 41, 59, 0.6);

            border-color: rgba(71, 85, 105, 0.55);

        }



        .surface-base {

            background: var(--card-bg);

            border: 1px solid var(--surface-border);

            color: var(--text-primary);

        }



        .surface-contrast {

            background: rgba(255, 255, 255, 0.92);

            border: 1px solid rgba(148, 163, 184, 0.45);

            color: #0f172a;

        }

        .dark .surface-contrast {

            background: rgba(15, 23, 42, 0.9);

            border-color: rgba(71, 85, 105, 0.6);

            color: var(--text-primary);

        }



        .empty-icon-surface {

            background: rgba(148, 163, 184, 0.18);

        }

        .dark .empty-icon-surface {

            background: rgba(30, 41, 59, 0.75);

        }



        .feedback-toast {

            background: rgba(255, 255, 255, 0.96);

            border: 1px solid rgba(148, 163, 184, 0.55);

            color: #0f172a;

        }

        .dark .feedback-toast {

            background: rgba(15, 23, 42, 0.95);

            border-color: rgba(71, 85, 105, 0.65);

            color: var(--text-primary);

        }

        @media (max-width: 1023px) {

            .layout-grid {

                grid-template-columns: 1fr;

            }

            .dashboard-sidebar {

                display: none;

            }

        }

    </style>

</head>

<body class="min-h-screen">

    <div class="layout-shell">

        <div class="layout-grid">

            <!-- Sidebar -->

            <aside class="dashboard-sidebar">

                <div class="p-6">

                    <div class="flex items-center gap-3 mb-6">

                        <img src="{{ asset('assets/logo.png') }}" alt="Q2L Logo" class="h-8 w-auto rounded-lg">

                        <div>

                            <p class="text-xs font-semibold text-[color:var(--text-muted)] uppercase tracking-wider">Quest2Learn</p>

                            <h1 class="text-lg font-bold text-[color:var(--text-primary)]">PDF Reader</h1>

                        </div>

                    </div>

                    

                    <nav class="space-y-2">

                        <a href="{{ route('student.dashboard') }}" class="nav-link">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>

                            </svg>

                            Dashboard

                        </a>

                        <a href="{{ route('student.pdf.reader') }}" class="nav-link active">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>

                            </svg>

                            PDF Library

                        </a>

                    </nav>

                </div>

            </aside>



            <!-- Main Content -->

            <main class="main-content" x-data="pdfReader()">

                <!-- Header -->

                <div class="dashboard-card p-6 mb-6">

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">

                        <div>

                            <h2 class="text-2xl font-bold text-[color:var(--text-primary)] mb-2">Reading Materials</h2>

                            <p class="text-[color:var(--text-muted)]">Select a PDF to read with pronunciation tracking</p>

                        </div>

                        <div class="flex items-center gap-3">

                            <template x-if="isRecording">

                                <div class="recording-indicator">

                                    <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>

                                    Recording... <span x-text="formatRecordingTime()"></span>

                                </div>

                            </template>

                            <template x-if="selectedPdf && !isRecording && !showRepeatOptions">

                                <button @click="startRecording" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">

                                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">

                                        <circle cx="12" cy="12" r="8"></circle>

                                    </svg>

                                    Start Recording

                                </button>

                            </template>



                            <template x-if="isRecording">

                                <button @click="stopRecording" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">

                                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">

                                        <rect x="6" y="6" width="12" height="12"></rect>

                                    </svg>

                                    Stop Recording

                                </button>

                            </template>

                            <template x-if="showRepeatOptions">

                                <div class="flex items-center gap-3">

                                    <button @click="repeatReading" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">

                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>

                                        </svg>

                                        Repeat

                                    </button>

                                    <button @click="submitReading" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">

                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>

                                        </svg>

                                        Done

                                    </button>

                                </div>

                            </template>

                        </div>

                    </div>

                </div>



                <!-- PDF Selection Grid -->

                <div x-show="!selectedPdf" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <template x-for="pdf in pdfs" :key="pdf.id">

                        <div class="pdf-item" @click="selectPdf(pdf)">

                            <div class="flex items-start space-x-3 mb-3">

                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">

                                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">

                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>

                                    </svg>

                                </div>

                                <div class="flex-1 min-w-0">

                                    <h3 class="font-semibold text-[color:var(--text-primary)] truncate" x-text="pdf.title"></h3>

                                    <p class="text-sm text-[color:var(--text-muted)]" x-text="pdf.classroom?.name || 'General'"></p>

                                </div>

                            </div>

                            

                            <p class="text-sm text-[color:var(--text-muted)] mb-3 line-clamp-2" x-text="pdf.description || 'No description provided'"></p>

                            

                            <div class="flex items-center justify-between text-xs text-[color:var(--text-muted)]">

                                <span>Provided by <span x-text="pdf.teacher?.name"></span></span>

                                <span x-text="formatDate(pdf.created_at)"></span>

                            </div>

                        </div>

                    </template>

                </div>



                <!-- PDF Viewer -->

                <div x-show="selectedPdf" class="dashboard-card p-6">

                    <div class="flex items-center justify-between mb-4">

                        <div class="flex items-center space-x-4">

                            <button @click="cleanupMedia(); selectedPdf = null; difficultWords = []" class="text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)]">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>

                                </svg>

                                Back to Library

                            </button>

                            <h3 class="text-lg font-semibold text-[color:var(--text-primary)]" x-text="selectedPdf?.title"></h3>

                        </div>

                        <div class="flex items-center space-x-2">

                            <a :href="selectedPdf?.file_url" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>

                                </svg>

                                Open PDF

                            </a>

                        </div>

                    </div>

                    

                    <!-- PDF Info -->

                    <div class="mb-6 p-4 rounded-lg surface-muted">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                            <div>

                                <span class="font-medium text-[color:var(--text-primary)]">Teacher:</span>

                                <span class="text-[color:var(--text-muted)]" x-text="selectedPdf.teacher?.name"></span>

                            </div>

                            <div>

                                <span class="font-medium text-[color:var(--text-primary)]">Classroom:</span>

                                <span class="text-[color:var(--text-muted)] ml-2" x-text="selectedPdf?.classroom || 'General'"></span>

                            </div>

                            <div x-show="selectedPdf?.description">

                                <span class="font-medium text-[color:var(--text-primary)]">Description:</span>

                                <span class="text-[color:var(--text-muted)] ml-2" x-text="selectedPdf?.description"></span>

                            </div>

                        </div>

                    </div>

                    

                    <!-- Text Content for Reading -->

                    <div x-show="selectedPdf?.extracted_text" class="space-y-4 surface-contrast">

                        <div class="flex items-center justify-between">

                            <h4 class="font-semibold text-[color:var(--text-primary)]">Reading Text (Follow the green highlight):</h4>

                            <div class="text-sm text-[color:var(--text-muted)]">

                                <span x-text="currentIndex + 1"></span>/<span x-text="words.length"></span> words

                            </div>

                        </div>



                        <div class="p-4 rounded-lg surface-muted" x-show="selectedPdf?.extracted_text">

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                                <div class="text-sm">

                                    <span class="font-semibold" style="color: var(--text-primary)">Current word:</span>

                                    <span class="ml-2 font-bold" style="color: var(--text-primary)" x-text="currentWordDisplay()"></span>

                                </div>

                                <div class="text-sm" style="color: var(--text-primary)" x-show="isRecording">

                                    <span class="font-semibold">Heard:</span>

                                    <span class="ml-2" style="color: var(--text-muted)" x-text="lastHeardSnippet || '...'" ></span>

                                </div>

                            </div>



                            <div class="mt-4 tutor-card p-4">

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                                    <div>

                                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-900/80">Tutor</p>

                                        <p class="mt-1 text-sm font-semibold text-[color:var(--text-primary)]">

                                            <span x-text="tutorWordRaw || currentWordDisplay()"></span>

                                        </p>

                                    </div>

                                    <div class="flex items-center gap-2">

                                        <button type="button" @click="listenTutorWord()" class="tutor-pill">

                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5L6 9H2v6h4l5 4V5z" />

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.07 4.93a10 10 0 010 14.14" />

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.54 8.46a5 5 0 010 7.08" />

                                            </svg>

                                            Listen

                                        </button>

                                        <button type="button" @click="markTutorWordDifficult()" class="tutor-pill">

                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                                            </svg>

                                            Mark difficult & skip

                                        </button>

                                    </div>

                                </div>



                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">

                                    <div>

                                        <p class="text-xs font-semibold text-[color:var(--text-muted)]">Phonetic hint</p>

                                        <p class="mt-1 font-semibold text-[color:var(--text-primary)]" x-text="tutorPhonetic()"></p>

                                    </div>

                                    <div>

                                        <p class="text-xs font-semibold text-[color:var(--text-muted)]">Break into syllables</p>

                                        <p class="mt-1 font-semibold text-[color:var(--text-primary)]" x-text="tutorSyllables()"></p>

                                    </div>

                                </div>



                                <p class="mt-3 text-xs text-[color:var(--text-muted)]">Tip: Listen, then repeat the word. The green highlight will move only when the word is detected.</p>

                            </div>

                            <div class="mt-3 h-2 w-full bg-emerald-100 rounded-full overflow-hidden">

                                <div class="h-full bg-emerald-500 transition-all" :style="`width: ${progressPercent()}%`"></div>

                            </div>

                            <p class="mt-3 text-xs" style="color: var(--text-muted)" x-show="!supportsSpeech">

                                Your browser does not support Speech Recognition. Use Chrome/Edge on desktop for word-by-word tracking.

                            </p>

                        </div>

                        

                        <div class="p-6 rounded-lg max-h-96 overflow-y-auto surface-contrast">

                            <div class="text-[color:var(--text-primary)] leading-relaxed text-lg flex flex-wrap gap-x-1.5 gap-y-2">

                                <template x-for="(w, idx) in words" :key="idx">

                                    <span

                                        class="word-highlight"

                                        :class="{

                                            'read': idx < currentIndex,

                                            'current': idx === currentIndex,

                                            'difficult': difficultWords.includes(w.norm)

                                        }"

                                        x-text="w.raw"

                                    ></span>

                                </template>

                            </div>

                        </div>

                    </div>

                    

                    <!-- No Text Available -->

                    <div x-show="!selectedPdf?.extracted_text" class="text-center py-8 surface-muted">

                        <div class="w-16 h-16 mx-auto mb-4 rounded-full empty-icon-surface flex items-center justify-center">

                            <svg class="w-8 h-8" style="color: var(--text-subtle)" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>

                            </svg>

                        </div>

                        <h4 class="text-lg font-medium text-[color:var(--text-primary)] mb-2">No text content available</h4>

                        <p class="text-[color:var(--text-muted)] mb-4">This PDF doesn't have extractable text. Please open the PDF file directly.</p>

                        <a :href="selectedPdf?.file_url" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">

                            Open PDF File

                        </a>

                    </div>

                    

                    <!-- Difficult Words Summary -->

                    <div x-show="difficultWords.length > 0" class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">

                        <h4 class="font-semibold text-yellow-800 mb-2">Words marked difficult (auto-detected by timeouts):</h4>

                        <div class="flex flex-wrap gap-2 mb-4">

                            <template x-for="word in difficultWords" :key="word">

                                <button type="button" class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm hover:bg-yellow-300" @click="openTutorForWord(word)" x-text="word"></button>

                            </template>

                        </div>

                        <p class="text-xs text-yellow-900/80">Tip: You can click Repeat to try again. Click Done to submit for tracking.</p>

                    </div>



                    <!-- Optional practice after finishing -->

                    <div x-show="!isRecording && currentIndex >= words.length && difficultWords.length > 0" class="mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                            <div>

                                <h4 class="font-semibold text-emerald-900">Practice difficult words</h4>

                                <p class="text-sm text-emerald-900/80">Practice them one-by-one with the tutor. It will move to the next word only when you say it.</p>

                            </div>

                            <div class="flex items-center gap-2">

                                <button type="button" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold" @click="startPractice()" x-show="!practiceMode">

                                    Start Practice

                                </button>

                                <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-semibold" @click="stopPractice()" x-show="practiceMode">

                                    Stop Practice

                                </button>

                            </div>

                        </div>



                        <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4" x-show="practiceMode">

                            <div class="lg:col-span-2 tutor-card p-5">

                                <div class="flex items-start justify-between gap-3">

                                    <div>

                                        <p class="text-xs font-semibold uppercase tracking-wide text-[color:var(--text-muted)]">Now practicing</p>

                                        <p class="mt-1 text-3xl font-extrabold text-[color:var(--text-primary)]" x-text="practiceWordDisplay()"></p>

                                        <p class="mt-2 text-sm text-[color:var(--text-muted)]">

                                            <span class="font-semibold">Phonetic:</span>

                                            <span class="ml-2 font-semibold text-[color:var(--text-primary)]" x-text="practicePhonetic()"></span>

                                        </p>

                                        <p class="mt-1 text-sm text-[color:var(--text-muted)]">

                                            <span class="font-semibold">Syllables:</span>

                                            <span class="ml-2 font-semibold text-[color:var(--text-primary)]" x-text="practiceSyllables()"></span>

                                        </p>

                                    </div>

                                    <div class="flex flex-col gap-2">

                                        <button type="button" class="tutor-pill" @click="listenPracticeWord()">

                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5L6 9H2v6h4l5 4V5z" />

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.07 4.93a10 10 0 010 14.14" />

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.54 8.46a5 5 0 010 7.08" />

                                            </svg>

                                            Listen

                                        </button>

                                        <button type="button" class="tutor-pill" @click="skipPracticeWord()">

                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />

                                            </svg>

                                            Skip

                                        </button>

                                    </div>

                                </div>



                                <div class="mt-4 h-2 w-full bg-emerald-100 rounded-full overflow-hidden">

                                    <div class="h-full bg-emerald-500 transition-all" :style="`width: ${practicePercent()}%`"></div>

                                </div>

                                <p class="mt-3 text-sm text-[color:var(--text-muted)]">

                                    Step 1: Click <span class="font-semibold">Listen</span>.

                                    Step 2: Say the word clearly.

                                    Step 3: It will move forward once detected.

                                </p>

                                <p class="mt-2 text-xs text-[color:var(--text-muted)]" x-show="lastHeardSnippet">

                                    <span class="font-semibold">Heard:</span>

                                    <span class="ml-2" x-text="lastHeardSnippet"></span>

                                </p>

                                <p class="mt-2 text-xs text-[color:var(--text-muted)]" x-show="!supportsSpeech">

                                    Speech Recognition is not supported in this browser. Use Chrome/Edge on desktop.

                                </p>

                            </div>



                            <div class="bg-white/70 border border-emerald-200 rounded-lg p-4 surface-contrast">

                                <div class="flex items-center justify-between">

                                    <p class="text-sm font-semibold" style="color: var(--text-primary)">Practice list</p>

                                    <p class="text-xs" style="color: var(--text-muted)">

                                        <span x-text="practiceIndex + 1"></span>/<span x-text="practiceWords.length"></span>

                                    </p>

                                </div>

                                <div class="mt-3 space-y-2 max-h-56 overflow-y-auto">

                                    <template x-for="(w, idx) in practiceWords" :key="w + '-' + idx">

                                        <button

                                            type="button"

                                            class="w-full text-left px-3 py-2 rounded-lg border text-sm transition-colors"

                                            :class="idx === practiceIndex ? 'border-emerald-500 bg-emerald-50 text-emerald-900 font-semibold' : idx < practiceIndex ? 'border-gray-200 bg-gray-50 text-gray-500' : 'border-gray-200 bg-white text-[color:var(--text-primary)] hover:bg-gray-50 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800'"

                                            @click="jumpToPractice(idx)"

                                        >

                                            <span x-text="w"></span>

                                            <span class="ml-2 text-xs" x-show="idx === practiceIndex">(current)</span>

                                        </button>

                                    </template>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Empty State -->

                <div x-show="pdfs.length === 0 && !selectedPdf" class="text-center py-12">

                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">

                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>

                        </svg>

                    </div>

                    <h3 class="text-lg font-medium text-[color:var(--text-primary)] mb-2">No reading materials available</h3>

                    <p class="text-[color:var(--text-muted)]">Your teacher hasn't uploaded any PDF reading materials yet.</p>

                </div>

            </main>

        </div>

    </div>



    <script>

        function pdfReader() {

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;



            return {

                pdfs: @json($pdfs),

                selectedPdf: null,



                // Guided reading state

                words: [],

                currentIndex: 0,

                lastHeardSnippet: '',

                supportsSpeech: !!SpeechRecognition,



                // Tutor state

                tutorWordRaw: '',

                tutorWordNorm: '',



                // Practice mode (after finishing)

                practiceMode: false,

                practiceWords: [],

                practiceIndex: 0,



                // Difficulty tracking

                difficultWords: [],

                wordStartAt: null,

                wordTimeoutSeconds: 6,



                // Attempts

                readingAttempts: 0,

                maxAttempts: 3,

                showRepeatOptions: false,



                // Recording

                isRecording: false,

                recordingStartTime: null,

                recordingDuration: 0,

                recordingTimer: null,

                mediaStream: null,

                mediaRecorder: null,

                audioChunks: [],

                audioBlobUrl: null,

                recognition: null,



                // TTS audio

                ttsAudio: null,



                async selectPdf(pdf) {

                    this.cleanupMedia();

                    this.selectedPdf = pdf;

                    this.difficultWords = [];

                    this.readingAttempts = 0;

                    this.showRepeatOptions = false;

                    this.currentIndex = 0;

                    this.words = [];

                    this.lastHeardSnippet = '';

                    this.tutorWordRaw = '';

                    this.tutorWordNorm = '';

                    this.practiceMode = false;

                    this.practiceWords = [];

                    this.practiceIndex = 0;



                    try {

                        const response = await fetch(`{{ route('student.pdf.content', ":pdf") }}`.replace(':pdf', pdf.id));

                        const result = await response.json();



                        if (result.pdf) {

                            this.selectedPdf = result.pdf;

                            this.words = this.buildWords(result.pdf.extracted_text || '');

                            this.currentIndex = 0;

                            this.syncTutorToCurrent();

                        }

                    } catch (error) {

                        console.error('Failed to load PDF:', error);

                        this.showNotification('Failed to load PDF content', 'error');

                    }

                },



                buildWords(text) {

                    const rawTokens = (text || '').split(/\s+/).filter(Boolean);

                    const cleaned = rawTokens

                        .map(t => {

                            const norm = this.normalizeWord(t);

                            return {

                                raw: t,

                                norm,

                            };

                        })

                        .filter(w => w.norm.length > 0);



                    return cleaned;

                },



                normalizeWord(word) {

                    return (word || '')

                        .toLowerCase()

                        .replace(/^[^a-z0-9']+|[^a-z0-9']+$/g, '')

                        .replace(/\s+/g, '')

                        .trim();

                },



                currentWordDisplay() {

                    if (!this.words.length) return '-';

                    return this.words[this.currentIndex]?.raw || '-';

                },



                syncTutorToCurrent() {

                    if (!this.words.length) {

                        this.tutorWordRaw = '';

                        this.tutorWordNorm = '';

                        return;

                    }



                    const w = this.words[this.currentIndex];

                    this.tutorWordRaw = w?.raw || '';

                    this.tutorWordNorm = w?.norm || '';

                },



                openTutorForWord(normWord) {

                    if (!normWord) return;

                    const match = this.words.find(w => w.norm === normWord);

                    this.tutorWordNorm = normWord;

                    this.tutorWordRaw = match?.raw || normWord;

                },



                tutorPhonetic() {

                    const word = this.tutorWordNorm || this.normalizeWord(this.tutorWordRaw) || this.words[this.currentIndex]?.norm || '';

                    if (!word) return '-';

                    return this.simplePhoneticHint(word);

                },



                tutorSyllables() {

                    const word = this.tutorWordNorm || this.normalizeWord(this.tutorWordRaw) || this.words[this.currentIndex]?.norm || '';

                    if (!word) return '-';

                    return this.simpleSyllables(word);

                },



                listenTutorWord() {

                    const raw = this.tutorWordRaw || this.currentWordDisplay();

                    if (!raw || raw === '-') return;



                    this.playTts(raw);

                },



                playTts(text) {

                    const t = (text || '').toString().trim();

                    if (!t) return;



                    // Prefer server-side TTS for more natural pronunciation

                    this.playTtsFromServer(t);

                },



                async playTtsFromServer(text) {

                    const lang = 'en';

                    const baseUrl = `{{ route('tts.speak') }}`;

                    const url = `${baseUrl}?text=${encodeURIComponent(text)}&lang=${encodeURIComponent(lang)}`;



                    try {

                        const res = await fetch(url, {

                            method: 'GET',

                            headers: {

                                'Accept': 'audio/mpeg,audio/*;q=0.9,*/*;q=0.8'

                            }

                        });



                        if (!res.ok) {

                            throw new Error('TTS request failed');

                        }



                        const contentType = (res.headers.get('content-type') || '').toLowerCase();

                        if (!contentType.includes('audio')) {

                            // If the endpoint returned JSON error/HTML, fallback

                            throw new Error('TTS did not return audio');

                        }



                        const blob = await res.blob();

                        if (!blob || blob.size === 0) {

                            throw new Error('Empty TTS audio');

                        }



                        if (!this.ttsAudio) {

                            this.ttsAudio = new Audio();

                        }



                        // Create blob URL to avoid any streaming/CORS oddities

                        const blobUrl = URL.createObjectURL(blob);



                        this.ttsAudio.pause();

                        this.ttsAudio.currentTime = 0;

                        this.ttsAudio.src = blobUrl;



                        this.ttsAudio.onended = () => {

                            try { URL.revokeObjectURL(blobUrl); } catch (e) {}

                        };



                        this.ttsAudio.onerror = () => {

                            try { URL.revokeObjectURL(blobUrl); } catch (e) {}

                            this.playTtsFallback(text);

                        };



                        const p = this.ttsAudio.play();

                        if (p && typeof p.catch === 'function') {

                            p.catch(() => {

                                try { URL.revokeObjectURL(blobUrl); } catch (e) {}

                                this.playTtsFallback(text);

                            });

                        }

                    } catch (e) {

                        this.playTtsFallback(text);

                    }

                },



                playTtsFallback(text) {

                    if (!('speechSynthesis' in window)) {

                        this.showNotification('Text-to-speech is not supported in this browser.', 'warning');

                        return;

                    }



                    const speak = () => {

                        try {

                            const synth = window.speechSynthesis;

                            synth.cancel();



                            const utter = new SpeechSynthesisUtterance(text);

                            utter.lang = 'en-US';

                            utter.rate = 0.85;

                            utter.pitch = 1;



                            // Prefer higher quality voices when available

                            const voices = synth.getVoices ? synth.getVoices() : [];

                            const preferred = voices.find(v => /en\-us/i.test(v.lang) && /female|zira|samantha|aria|jenny|natural/i.test(v.name))

                                || voices.find(v => /en\-us/i.test(v.lang) && /google/i.test(v.name))

                                || voices.find(v => /en\-us/i.test(v.lang))

                                || voices.find(v => /en/i.test(v.lang));



                            if (preferred) {

                                utter.voice = preferred;

                            }



                            synth.speak(utter);

                        } catch (e) {

                            this.showNotification('Unable to play pronunciation audio.', 'error');

                        }

                    };



                    // Chrome sometimes loads voices asynchronously

                    const synth = window.speechSynthesis;

                    const voicesNow = synth.getVoices ? synth.getVoices() : [];

                    if (voicesNow && voicesNow.length > 0) {

                        speak();

                        return;

                    }



                    let spoke = false;

                    const handler = () => {

                        if (spoke) return;

                        spoke = true;

                        try { synth.onvoiceschanged = null; } catch (e) {}

                        speak();

                    };



                    try {

                        synth.onvoiceschanged = handler;

                    } catch (e) {

                        // ignore

                    }



                    setTimeout(() => {

                        if (!spoke) handler();

                    }, 400);

                },



                markTutorWordDifficult() {

                    const norm = this.tutorWordNorm || this.words[this.currentIndex]?.norm;

                    if (norm) {

                        this.addDifficult(norm);

                        // If the student is currently reading and marked the CURRENT word, skip it.

                        const currentNorm = this.words[this.currentIndex]?.norm;

                        if (this.isRecording && currentNorm && currentNorm === norm) {

                            this.showNotification('Marked difficult and skipped. You can practice it later.', 'success');

                            this.advanceWord();

                        } else {

                            this.showNotification('Marked as difficult.', 'success');

                        }

                    }

                },



                startPractice() {

                    if (!this.difficultWords.length) return;

                    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

                    if (!SpeechRecognition) {

                        this.showNotification('Speech Recognition not supported in this browser. Use Chrome/Edge desktop.', 'warning');

                        return;

                    }



                    this.practiceWords = [...this.difficultWords];

                    this.practiceIndex = 0;

                    this.practiceMode = true;

                    this.openTutorForWord(this.practiceWords[this.practiceIndex]);



                    // Start recognition if not already running

                    if (!this.isRecording) {

                        this.startSpeechRecognition();

                    }



                    this.showNotification('Practice started. Say the practice word to advance.', 'info');

                },



                stopPractice() {

                    this.practiceMode = false;

                    this.practiceWords = [];

                    this.practiceIndex = 0;



                    if (this.recognition && !this.isRecording) {

                        try { this.recognition.stop(); } catch (e) {}

                    }



                    this.showNotification('Practice stopped.', 'success');

                },



                practiceWordDisplay() {

                    if (!this.practiceWords.length) return '-';

                    return this.practiceWords[this.practiceIndex] || '-';

                },



                practicePhonetic() {

                    const w = this.practiceWords[this.practiceIndex];

                    if (!w) return '-';

                    return this.simplePhoneticHint(w);

                },



                practiceSyllables() {

                    const w = this.practiceWords[this.practiceIndex];

                    if (!w) return '-';

                    return this.simpleSyllables(w);

                },



                practicePercent() {

                    if (!this.practiceWords.length) return 0;

                    return Math.min(100, Math.round((this.practiceIndex / this.practiceWords.length) * 100));

                },



                listenPracticeWord() {

                    const raw = this.practiceWordDisplay();

                    if (!raw || raw === '-') return;

                    this.tutorWordRaw = raw;

                    this.tutorWordNorm = raw;

                    this.listenTutorWord();

                },



                skipPracticeWord() {

                    if (!this.practiceWords.length) return;

                    this.practiceIndex++;

                    if (this.practiceIndex >= this.practiceWords.length) {

                        this.practiceMode = false;

                        this.showNotification('Practice completed!', 'success');

                        return;

                    }

                    this.openTutorForWord(this.practiceWords[this.practiceIndex]);

                },



                jumpToPractice(idx) {

                    if (!this.practiceWords.length) return;

                    if (idx < 0 || idx >= this.practiceWords.length) return;

                    this.practiceIndex = idx;

                    this.openTutorForWord(this.practiceWords[this.practiceIndex]);

                },



                simplePhoneticHint(word) {

                    // Lightweight, offline hint (not true IPA). Designed for tutoring.

                    let w = word.toLowerCase();

                    w = w

                        .replace(/tion/g, 'shun')

                        .replace(/sion/g, 'zhun')

                        .replace(/ph/g, 'f')

                        .replace(/ght/g, 't')

                        .replace(/kn/g, 'n')

                        .replace(/wr/g, 'r')

                        .replace(/wh/g, 'w')

                        .replace(/ch/g, 'ch')

                        .replace(/sh/g, 'sh')

                        .replace(/th/g, 'th')

                        .replace(/ee/g, 'ee')

                        .replace(/oo/g, 'oo')

                        .replace(/ai/g, 'ay')

                        .replace(/ea/g, 'ee')

                        .replace(/ie/g, 'eye')

                        .replace(/igh/g, 'eye')

                        .replace(/qu/g, 'kw');



                    const syll = this.simpleSyllables(word);

                    return `/${w}/  (${syll})`;

                },



                simpleSyllables(word) {

                    // Very small heuristic syllabifier (English/Filipino friendly)

                    const v = 'aeiou';

                    const chars = (word || '').toLowerCase().split('');

                    if (!chars.length) return '-';



                    const out = [];

                    let buf = '';

                    for (let i = 0; i < chars.length; i++) {

                        const c = chars[i];

                        buf += c;

                        const next = chars[i + 1] || '';

                        const isVowel = v.includes(c);

                        const nextIsVowel = v.includes(next);



                        // Break after vowel when next is consonant, but keep common endings together

                        if (isVowel && !nextIsVowel && next) {

                            // don't split before a final 'e'

                            if (!(next === 'e' && i + 1 === chars.length - 1)) {

                                out.push(buf);

                                buf = '';

                            }

                        }

                    }

                    if (buf) out.push(buf);



                    // Cleanup (avoid too many tiny syllables)

                    return out.filter(Boolean).join('-');

                },



                progressPercent() {

                    if (!this.words.length) return 0;

                    return Math.min(100, Math.round(((this.currentIndex) / this.words.length) * 100));

                },



                async startRecording() {

                    if (!this.selectedPdf?.extracted_text) {

                        this.showNotification('No text available to read.', 'warning');

                        return;

                    }



                    this.cleanupMedia();

                    this.isRecording = true;

                    this.showRepeatOptions = false;

                    this.recordingStartTime = Date.now();

                    this.recordingDuration = 0;

                    this.audioChunks = [];

                    this.audioBlobUrl = null;

                    this.lastHeardSnippet = '';

                    this.wordStartAt = Date.now();

                    this.syncTutorToCurrent();



                    this.recordingTimer = setInterval(() => {

                        this.recordingDuration = Math.floor((Date.now() - this.recordingStartTime) / 1000);

                        this.checkWordTimeout();

                    }, 1000);



                    try {

                        this.mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });

                        this.mediaRecorder = new MediaRecorder(this.mediaStream);

                        this.mediaRecorder.ondataavailable = (e) => {

                            if (e.data && e.data.size > 0) this.audioChunks.push(e.data);

                        };

                        this.mediaRecorder.start();

                    } catch (e) {

                        this.isRecording = false;

                        this.stopTimers();

                        this.showNotification('Microphone permission denied or unavailable.', 'error');

                        return;

                    }



                    if (this.supportsSpeech) {

                        this.startSpeechRecognition();

                    } else {

                        this.showNotification('Speech Recognition not supported in this browser. Use Chrome/Edge desktop.', 'warning');

                    }



                    this.showNotification('Recording started. Read the green word to advance.', 'info');

                },



                stopRecording() {

                    if (!this.isRecording) return;

                    this.isRecording = false;

                    this.stopTimers();



                    if (this.recognition) {

                        try { this.recognition.stop(); } catch (e) {}

                    }



                    if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {

                        this.mediaRecorder.stop();

                    }



                    // Stop mic stream tracks

                    if (this.mediaStream) {

                        this.mediaStream.getTracks().forEach(t => t.stop());

                    }



                    // Create audio blob url (optional UI usage)

                    if (this.audioChunks.length) {

                        const blob = new Blob(this.audioChunks, { type: this.mediaRecorder?.mimeType || 'audio/webm' });

                        this.audioBlobUrl = URL.createObjectURL(blob);

                    }



                    this.showRepeatOptions = true;

                    this.showNotification('Recording stopped. Repeat or Done to submit.', 'success');

                },



                repeatReading() {

                    this.readingAttempts++;

                    this.showRepeatOptions = false;

                    this.difficultWords = [];

                    this.currentIndex = 0;

                    this.lastHeardSnippet = '';

                    this.wordStartAt = Date.now();



                    if (this.readingAttempts >= this.maxAttempts) {

                        this.showNotification(`Maximum attempts (${this.maxAttempts}) reached. Please click Done.`, 'warning');

                        this.showRepeatOptions = true;

                        return;

                    }



                    setTimeout(() => this.startRecording(), 500);

                },



                async submitReading() {

                    if (!this.selectedPdf) return;



                    // Prevent early submission before finishing/ending the session.

                    // showRepeatOptions is enabled once recording is stopped (or max attempts reached).

                    if (!this.showRepeatOptions && !(this.currentIndex >= this.words.length && this.words.length > 0)) {

                        this.showNotification('Please finish reading first, then click Stop Recording and Done.', 'warning');

                        return;

                    }



                    if (this.isRecording) {

                        this.stopRecording();

                    }



                    // If student didn't finish, consider remaining current word difficult

                    if (this.currentIndex < this.words.length && this.words[this.currentIndex]?.norm) {

                        this.addDifficult(this.words[this.currentIndex].norm);

                    }



                    try {

                        const formData = new FormData();

                        formData.append('pdf_id', this.selectedPdf.id);

                        // submit as JSON string; backend will decode

                        formData.append('difficult_words', JSON.stringify(this.difficultWords));

                        formData.append('recording_duration', this.recordingDuration || 0);

                        formData.append('attempts', this.readingAttempts + 1);



                        const response = await fetch('{{ route("student.pdf.recording.save") }}', {

                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN': '{{ csrf_token() }}',

                                'Accept': 'application/json',

                            },

                            body: formData

                        });



                        const result = await response.json();

                        if (!response.ok || !result.success) {

                            this.showNotification(result.message || 'Failed to submit reading.', 'error');

                            return;

                        }



                        const feedback = result.feedback;

                        if (feedback) {

                            this.showDetailedFeedback(this.formatFeedbackText(feedback));

                        } else {

                            this.showNotification('Reading submitted successfully!', 'success');

                        }



                        setTimeout(() => {

                            this.selectedPdf = null;

                            this.words = [];

                            this.currentIndex = 0;

                            this.difficultWords = [];

                            this.readingAttempts = 0;

                            this.showRepeatOptions = false;

                            this.cleanupMedia();

                        }, 1500);



                    } catch (e) {

                        this.showNotification('Failed to submit reading session.', 'error');

                    }

                },



                formatFeedbackText(feedback) {

                    const suggestions = Array.isArray(feedback.suggestions) ? feedback.suggestions : [];

                    let out = `Reading Analysis:\n`;

                    out += `• Performance: ${feedback.performance_level || '-'}\n`;

                    out += `• Accuracy: ${feedback.accuracy_score ?? '-'}%\n`;

                    out += `• Difficult words: ${feedback.difficult_words_count ?? 0}\n`;

                    out += `• Attempts: ${feedback.attempts ?? 1}\n`;

                    out += `• Duration: ${feedback.duration_seconds ?? 0}s\n\n`;

                    out += `Suggestions:\n`;

                    out += suggestions.map(s => `- ${s}`).join('\n');

                    if (this.difficultWords.length) {

                        out += `\n\nPractice: ${this.difficultWords.join(', ')}`;

                    }

                    return out;

                },



                startSpeechRecognition() {

                    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

                    if (!SpeechRecognition) return;



                    this.recognition = new SpeechRecognition();

                    this.recognition.continuous = true;

                    this.recognition.interimResults = true;

                    this.recognition.lang = 'en-US';



                    this.recognition.onresult = (event) => {

                        const results = event.results;

                        if (!results || results.length === 0) return;



                        const last = results[results.length - 1];

                        const transcript = (last[0]?.transcript || '').trim();

                        if (!transcript) return;



                        this.lastHeardSnippet = transcript.slice(-60);

                        this.consumeTranscript(transcript);

                    };



                    this.recognition.onerror = (e) => {

                        // Common: "no-speech" / "aborted" / "network"

                        console.warn('Speech recognition error:', e?.error || e);

                    };



                    try {

                        this.recognition.start();

                    } catch (e) {

                        // start can throw if called too quickly

                    }

                },



                consumeTranscript(transcript) {

                    // Practice mode (after finishing) uses the same speech recognition stream

                    if (this.practiceMode) {

                        const target = this.practiceWords[this.practiceIndex];

                        if (!target) return;



                        const tokens = (transcript || '')

                            .split(/\s+/)

                            .map(t => this.normalizeWord(t))

                            .filter(Boolean);



                        if (!tokens.length) return;

                        const tail = tokens.slice(-6);

                        if (tail.includes(target)) {

                            this.practiceIndex++;

                            if (this.practiceIndex >= this.practiceWords.length) {

                                this.practiceMode = false;

                                this.showNotification('Practice completed!', 'success');

                            } else {

                                this.openTutorForWord(this.practiceWords[this.practiceIndex]);

                            }

                        }

                        return;

                    }



                    if (!this.isRecording) return;

                    if (!this.words.length) return;

                    if (this.currentIndex >= this.words.length) return;



                    const tokens = (transcript || '')

                        .split(/\s+/)

                        .map(t => this.normalizeWord(t))

                        .filter(Boolean);



                    if (!tokens.length) return;



                    const target = this.words[this.currentIndex]?.norm;

                    if (!target) return;



                    // Check last few tokens for the target word

                    const tail = tokens.slice(-5);

                    const matched = tail.includes(target);



                    if (matched) {

                        this.advanceWord();

                    }

                },



                advanceWord() {

                    if (this.currentIndex >= this.words.length) return;

                    this.currentIndex++;

                    this.wordStartAt = Date.now();

                    this.syncTutorToCurrent();



                    if (this.currentIndex >= this.words.length) {

                        this.stopRecording();

                        this.showNotification('Finished reading! Click Done to submit.', 'success');

                    }

                },



                checkWordTimeout() {

                    if (!this.isRecording) return;

                    if (!this.wordStartAt) return;

                    if (!this.words.length) return;

                    if (this.currentIndex >= this.words.length) return;



                    const elapsed = Math.floor((Date.now() - this.wordStartAt) / 1000);

                    if (elapsed >= this.wordTimeoutSeconds) {

                        const w = this.words[this.currentIndex];

                        if (w?.norm) this.addDifficult(w.norm);

                        // reset timer but do not advance; student still must read it

                        this.wordStartAt = Date.now();

                    }

                },



                addDifficult(normWord) {

                    if (!normWord) return;

                    if (!this.difficultWords.includes(normWord)) {

                        this.difficultWords.push(normWord);

                    }

                },



                stopTimers() {

                    if (this.recordingTimer) {

                        clearInterval(this.recordingTimer);

                        this.recordingTimer = null;

                    }

                },



                cleanupMedia() {

                    this.stopTimers();



                    if (this.recognition) {

                        try { this.recognition.onresult = null; this.recognition.onerror = null; this.recognition.stop(); } catch (e) {}

                        this.recognition = null;

                    }



                    if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {

                        try { this.mediaRecorder.stop(); } catch (e) {}

                    }

                    this.mediaRecorder = null;



                    if (this.mediaStream) {

                        try { this.mediaStream.getTracks().forEach(t => t.stop()); } catch (e) {}

                    }

                    this.mediaStream = null;

                    this.audioChunks = [];



                    if (this.ttsAudio) {

                        try { this.ttsAudio.pause(); } catch (e) {}

                    }

                },



                formatRecordingTime() {

                    if (!this.recordingStartTime) return '0:00';

                    const elapsed = this.recordingDuration || Math.floor((Date.now() - this.recordingStartTime) / 1000);

                    const minutes = Math.floor(elapsed / 60);

                    const seconds = elapsed % 60;

                    return `${minutes}:${seconds.toString().padStart(2, '0')}`;

                },



                showDetailedFeedback(message) {

                    const feedbackDiv = document.createElement('div');

                    feedbackDiv.className = 'fixed top-4 right-4 left-4 md:left-auto md:right-4 max-w-md p-6 rounded-lg shadow-xl z-50 feedback-toast';

                    feedbackDiv.innerHTML = `

                        <div class="flex items-center justify-between mb-4">

                            <h3 class="text-lg font-semibold" style="color: var(--text-primary)">Reading Result</h3>

                            <button type="button" style="color: var(--text-subtle)" aria-label="Close">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>

                                </svg>

                            </button>

                        </div>

                        <pre class="text-sm whitespace-pre-wrap" style="color: var(--text-muted)">${message}</pre>

                        <div class="mt-4">

                            <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">OK</button>

                        </div>

                    `;



                    const closeBtns = feedbackDiv.querySelectorAll('button');

                    closeBtns.forEach(btn => btn.addEventListener('click', () => feedbackDiv.remove()));



                    document.body.appendChild(feedbackDiv);

                    setTimeout(() => {

                        if (feedbackDiv.parentElement) feedbackDiv.remove();

                    }, 12000);

                },



                showNotification(message, type) {

                    const notification = document.createElement('div');

                    notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg text-white z-50 ${

                        type === 'success' ? 'bg-green-500' :

                        type === 'error' ? 'bg-red-500' :

                        type === 'warning' ? 'bg-yellow-500' :

                        'bg-blue-500'

                    }`;

                    notification.textContent = message;

                    document.body.appendChild(notification);



                    setTimeout(() => {

                        notification.remove();

                    }, 4500);

                },

            };

        }

    </script>

</body>

</html>

