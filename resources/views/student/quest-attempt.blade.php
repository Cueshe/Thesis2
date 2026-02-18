<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $quest->title }} - {{ $classroom->name }} · Quest Portal</title>
    <x-theme-script />
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/student/quest-attempt.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.classes.show', $classroom->id) }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $quest->title }}</h1>
                        <p class="text-sm text-gray-600">{{ $classroom->name }} • {{ ucfirst($quest->type) }} Quest</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Reward</p>
                        <p class="font-bold text-indigo-600">+{{ $quest->reward_points }} XP</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Time</p>
                        <p class="font-bold text-gray-900">{{ $quest->estimated_time }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Container -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Quiz Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $quest->title }}</h2>
                        <p class="text-indigo-100 mt-1">{{ $quest->description ?? 'Complete all exercises to finish the quest!' }}</p>
                    </div>
                    <div class="text-center">
                        <div id="timer" class="text-3xl font-bold">00:00</div>
                        <p class="text-sm text-indigo-100">Time Elapsed</p>
                    </div>
                </div>
            </div>

            <!-- Quiz Content -->
            <form id="quizForm" class="p-8 space-y-8">
                @csrf
                <input type="hidden" name="time_spent_minutes" id="timeSpent" value="0">
                
                @php
                    $content = $quest->content;
                    // Handle different content structures
                    if (!is_array($content)) {
                        $content = json_decode($content, true) ?: [];
                    }
                    $questionNumber = 0;
                    $isMixedQuest = $quest->type === 'mixed';
                @endphp

                @if (($quest->type === 'pdf') || (($content['type'] ?? null) === 'pdf'))
                    @php $pdfActivityType = $content['pdf_activity_type'] ?? 'read'; @endphp
                    <section class="space-y-4">
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    PDF Content
                                    @if ($pdfActivityType === 'pronunciation')
                                        <span class="ml-2 text-sm font-medium text-blue-600">(Pronunciation Practice)</span>
                                    @else
                                        <span class="ml-2 text-sm font-medium text-green-600">(Reading)</span>
                                    @endif
                                </h3>
                                @if (!empty($content['pdf_url']))
                                    <a href="{{ $content['pdf_url'] }}" target="_blank" rel="noopener" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Open PDF</a>
                                @endif
                            </div>
                            @if (!empty($content['pdf_text']))
                                <div class="mt-4 whitespace-pre-wrap text-sm text-gray-700 leading-relaxed">{{ $content['pdf_text'] }}</div>
                            @else
                                <div class="mt-4 text-sm text-gray-600">No readable text found in this PDF.</div>
                            @endif
                        </div>

                        @if ($pdfActivityType === 'pronunciation')
                            @php
                                $pdfPronunciationExercises = $result['pdf_pronunciation_exercises'] ?? [];
                                if (empty($pdfPronunciationExercises)) {
                                    // Generate exercises from PDF text if not already generated
                                    $words = preg_split('/[\s,;:.\-]+/', $content['pdf_text'] ?? '');
                                    $pdfPronunciationExercises = [];
                                    foreach ($words as $word) {
                                        $word = trim($word);
                                        if (strlen($word) >= 3 && strlen($word) <= 15 && preg_match('/^[a-zA-Z]+$/', $word)) {
                                            $pdfPronunciationExercises[] = [
                                                'word' => $word,
                                                'phonetic' => '[' . strtoupper($word) . ']',
                                                'practice_sentence' => "Please practice saying the word: {$word}.",
                                                'difficulty' => 'medium'
                                            ];
                                            if (count($pdfPronunciationExercises) >= 10) break;
                                        }
                                    }
                                }
                            @endphp
                            @if (!empty($pdfPronunciationExercises))
                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Pronunciation Practice from PDF</h4>
                                    <div class="space-y-4">
                                        @foreach ($pdfPronunciationExercises as $index => $exercise)
                                            @php $questionNumber++; @endphp
                                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-600 text-white font-bold text-xs">
                                                        {{ $questionNumber }}
                                                    </span>
                                                    <span class="text-xs font-medium text-gray-600">Word from PDF</span>
                                                </div>
                                                <div class="text-center">
                                                    <p class="text-xl font-bold text-gray-900 mb-1">{{ $exercise['word'] }}</p>
                                                    <p class="text-sm text-gray-600 mb-2">{{ $exercise['phonetic'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $exercise['practice_sentence'] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </section>
                @endif

                <!-- Simple Q&A Format (for basic quests) -->
                @if (!isset($content['pronunciation_exercises']) && !isset($content['reading_exercises']))
                    @if (isset($content['question']) || isset($content['word']))
                        <section class="space-y-6">
                            <div class="flex items-center space-x-3">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Quest Challenge</h3>
                            </div>

                            @php $questionNumber++; @endphp
                            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-600 text-white font-bold text-sm">
                                            {{ $questionNumber }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-600">
                                            {{ $quest->type === 'pronunciation' ? 'Pronunciation' : 'Question' }}
                                        </span>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $quest->difficulty }}
                                    </span>
                                </div>
                                
                                <div class="space-y-4">
                                    @if (isset($content['word']))
                                        <!-- Pronunciation Format -->
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-gray-900 mb-2">{{ $content['word'] }}</p>
                                            @if (isset($content['phonetic']))
                                                <p class="text-lg text-gray-600 mb-4">[{{ $content['phonetic'] }}]</p>
                                            @endif
                                            @if (isset($content['tips']))
                                                <div class="bg-blue-50 rounded-lg p-3 text-sm text-blue-800">
                                                    💡 {{ $content['tips'] }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700">Pronounce the word below:</label>
<div class="flex items-center space-x-2 mt-2">
    <button type="button" id="startRecordingBtn" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18v4m0 0h4m-4 0H8m8-8a4 4 0 11-8 0V5a4 4 0 018 0v9z" />
        </svg>
        Start Recording
    </button>
    <span id="recordingStatus" class="text-red-600 font-semibold hidden">● Recording...</span>
</div>
<div class="mt-2">
    <label class="block text-xs text-gray-500 mb-1">Recognized text:</label>
    <div id="recognizedText" class="p-2 border border-gray-200 rounded bg-gray-50 text-lg min-h-[2.5rem]"></div>
    <input type="hidden" name="{{ $isMixedQuest ? 'answers[pronunciation][0]' : 'answers[0]' }}" id="recognizedInput" required>
</div>
                                        </div>
                                    @else
                                        <!-- Q&A Format -->
                                        <div class="space-y-4">
                                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                                <p class="text-gray-900 font-medium">{{ $content['question'] ?? 'Answer the question below:' }}</p>
                                            </div>
                                            
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Your answer:</label>
                                                <input type="text" 
                                                       name="answers[0]" 
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                                                       placeholder="Enter your answer..."
                                                       required>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </section>
                    @endif
                @endif

                <!-- Pronunciation Exercises -->
                @if ($quest->type === 'pronunciation' || $quest->type === 'mixed')
                    @if (isset($content['pronunciation_exercises']) && count($content['pronunciation_exercises']) > 0)
                        <section class="space-y-6">
                            <div class="flex items-center space-x-3">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Pronunciation Practice</h3>
                            </div>

                            @foreach ($content['pronunciation_exercises'] as $index => $exercise)
                                @php $questionNumber++; @endphp
                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <span class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-600 text-white font-bold text-sm">
                                                {{ $questionNumber }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-600">Pronunciation Exercise</span>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            {{ $exercise['difficulty'] ?? 'Medium' }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div class="text-center">
                                            @if (isset($exercise['image']) && !empty($exercise['image']))
                                                <div class="mb-4 flex justify-center">
                                                    <img src="{{ $exercise['image'] }}" alt="{{ $exercise['word'] ?? 'Pronunciation image' }}" class="max-w-xs max-h-48 rounded-lg shadow-md border border-gray-200 object-contain">
                                                </div>
                                            @endif
                                            <p class="text-2xl font-bold text-gray-900 mb-2">{{ $exercise['word'] ?? '' }}</p>
                                            @if (isset($exercise['phonetic']))
                                                <p class="text-lg text-gray-600 mb-4">[{{ $exercise['phonetic'] }}]</p>
                                            @endif
                                            @if (isset($exercise['tips']))
                                                <div class="bg-blue-50 rounded-lg p-3 text-sm text-blue-800">
                                                    💡 {{ $exercise['tips'] }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700">Pronounce the word below:</label>
<div class="flex items-center space-x-2 mt-2">
    <button type="button" id="startRecordingBtn-{{ $index }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18v4m0 0h4m-4 0H8m8-8a4 4 0 11-8 0V5a4 4 0 018 0v9z" />
        </svg>
        Start Recording
    </button>
    <span id="recordingStatus-{{ $index }}" class="text-red-600 font-semibold hidden">● Recording...</span>
</div>
<div class="mt-2">
    <label class="block text-xs text-gray-500 mb-1">Recognized text:</label>
    <div id="recognizedText-{{ $index }}" class="p-2 border-2 border-indigo-500 rounded bg-indigo-50 text-2xl font-bold text-indigo-700 min-h-[2.5rem] transition-all duration-200"></div>
    @php
        $pronunciationInputName = $isMixedQuest
            ? "answers[pronunciation][$index]"
            : "answers[$index]";
    @endphp
    <input type="hidden" name="{{ $pronunciationInputName }}" id="recognizedInput-{{ $index }}" required>
</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </section>
                    @endif
                @endif

                <!-- Reading Comprehension Exercises -->
                @if ($quest->type === 'reading' || $quest->type === 'mixed')
                    @if (isset($content['reading_exercises']) && count($content['reading_exercises']) > 0)
                        <section class="space-y-6">
                            <div class="flex items-center space-x-3">
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Reading Comprehension</h3>
                            </div>

                            @if (!empty($content['reading_passage']))
                                <div class="bg-white rounded-xl p-6 border border-gray-200">
                                    <div class="flex items-center gap-3 mb-3">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700">Reading Passage</span>
                                        <span class="text-xs uppercase tracking-wide text-gray-500">Read before answering</span>
                                    </div>
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $content['reading_passage'] }}</p>
                                </div>
                            @endif

                            @foreach ($content['reading_exercises'] as $index => $exercise)
                                @php $questionNumber++; @endphp
                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <span class="flex items-center justify-center h-8 w-8 rounded-full bg-green-600 text-white font-bold text-sm">
                                                {{ $questionNumber }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-600">Comprehension Question</span>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            {{ $exercise['difficulty'] ?? 'Medium' }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        @if (isset($exercise['passage']))
                                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                                <p class="text-gray-700 leading-relaxed">{{ $exercise['passage'] }}</p>
                                            </div>
                                        @endif
                                        
                                        <div>
                                            <p class="font-medium text-gray-900 mb-3">{{ $exercise['question'] ?? '' }}</p>
                                            
                                            @if (isset($exercise['options']) && is_array($exercise['options']))
                                                <div class="space-y-3">
                                                    @foreach ($exercise['options'] as $optionIndex => $option)
                                                        @php
                                                            $readingInputName = $isMixedQuest
                                                                ? "answers[reading][$index]"
                                                                : "answers[$index]";
                                                        @endphp
                                                        <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                                                            <input type="radio" 
                                                                   name="{{ $readingInputName }}" 
                                                                   value="{{ chr(65 + $optionIndex) }}" 
                                                                   class="mr-3 text-green-600 focus:ring-green-500"
                                                                   required>
                                                            <span class="flex-1">
                                                                <span class="font-medium text-gray-700">{{ chr(65 + $optionIndex) }}.</span> 
                                                                {{ $option }}
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="space-y-3">
                                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                                                        <input type="radio" name="{{ $readingInputName }}" value="A" class="mr-3 text-green-600 focus:ring-green-500" required>
                                                        <span class="flex-1"><span class="font-medium text-gray-700">A.</span> Option A</span>
                                                    </label>
                                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                                                        <input type="radio" name="{{ $readingInputName }}" value="B" class="mr-3 text-green-600 focus:ring-green-500" required>
                                                        <span class="flex-1"><span class="font-medium text-gray-700">B.</span> Option B</span>
                                                    </label>
                                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                                                        <input type="radio" name="{{ $readingInputName }}" value="C" class="mr-3 text-green-600 focus:ring-green-500" required>
                                                        <span class="flex-1"><span class="font-medium text-gray-700">C.</span> Option C</span>
                                                    </label>
                                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                                                        <input type="radio" name="{{ $readingInputName }}" value="D" class="mr-3 text-green-600 focus:ring-green-500" required>
                                                        <span class="flex-1"><span class="font-medium text-gray-700">D.</span> Option D</span>
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </section>
                    @endif
                @endif

                <!-- Submit Button -->
                <div class="flex justify-center pt-8 border-t border-gray-200">
                    <button type="submit" 
                            id="submitBtn"
                            class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg transform transition hover:scale-105 flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Submit Quest</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Modal -->
    <div id="resultsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-8 text-center">
            <div id="resultsContent">
                <!-- Results will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div id="confirmSubmitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-8 space-y-6 text-center shadow-2xl">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl mx-auto">
                ⚔️
            </div>
            <div class="space-y-2">
                <h3 class="text-2xl font-bold text-gray-900">Submit Quest?</h3>
                <p class="text-gray-600 text-sm">Make sure you're happy with your answers—there's no turning back once you confirm.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button id="cancelSubmitBtn" type="button" class="w-full px-5 py-3 rounded-xl border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50">
                    Review Answers
                </button>
                <button id="confirmSubmitBtn" type="button" class="w-full px-5 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold hover:from-indigo-700 hover:to-purple-700 shadow-lg">
                    Yes, Submit Quest
                </button>
            </div>
        </div>
    </div>

    <script>
    // Pass Laravel variables to JavaScript
    window.classroomId = {{ $classroom->id }};
    window.questId = {{ $quest->id }};
</script>
<script src="{{ asset('js/student/quest-attempt.js') }}" defer></script>
</body>
</html>
