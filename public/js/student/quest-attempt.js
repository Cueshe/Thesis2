// Student Quest Attempt JavaScript
let startTime = Date.now();
let timerInterval;

// Start timer
function startTimer() {
    timerInterval = setInterval(() => {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        document.getElementById('timer').textContent = 
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Update time spent in minutes
        document.getElementById('timeSpent').value = Math.ceil(elapsed / 60);
    }, 1000);
}

// Stop timer
function stopTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
}

// Show results modal
function showResults(results) {
    const modal = document.getElementById('resultsModal');
    const content = document.getElementById('resultsContent');
    
    const successClass = results.accuracy >= 70 ? 'text-green-600' : 'text-yellow-600';
    const message = results.accuracy >= 70 ? 'Excellent work!' : 'Good effort! Keep practicing!';
    
    content.innerHTML = `
        <div class="space-y-6">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-3xl font-bold mx-auto">
                ${results.correct}/${results.total}
            </div>
            
            <div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Quest Complete!</h3>
                <p class="text-gray-600">${message}</p>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Accuracy</span>
                    <span class="${successClass} font-bold text-lg">${results.accuracy}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Points Earned</span>
                    <span class="text-indigo-600 font-bold text-lg">+${results.points_earned} XP</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">New Level</span>
                    <span class="text-purple-600 font-bold text-lg">Level ${results.new_level}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Points</span>
                    <span class="text-gray-900 font-bold text-lg">${results.new_points}</span>
                </div>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-1000" 
                     style="width: ${results.accuracy}%"></div>
            </div>
            
            <button onclick="location.href='/student/classes/' + window.classroomId" 
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transform transition hover:scale-105">
                Back to Classroom
            </button>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

// Handle form submission
const quizForm = document.getElementById('quizForm');
const submitBtn = document.getElementById('submitBtn');
const confirmModal = document.getElementById('confirmSubmitModal');
const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
const cancelSubmitBtn = document.getElementById('cancelSubmitBtn');
const submitBtnOriginalHtml = submitBtn.innerHTML;
let isSubmitting = false;

quizForm.addEventListener('submit', function(e) {
    e.preventDefault();
    if (isSubmitting) {
        return;
    }
    confirmModal.classList.remove('hidden');
});

cancelSubmitBtn.addEventListener('click', function() {
    confirmModal.classList.add('hidden');
});

confirmSubmitBtn.addEventListener('click', function() {
    confirmModal.classList.add('hidden');
    handleQuestSubmission();
});

async function handleQuestSubmission() {
    if (isSubmitting) {
        return;
    }

    isSubmitting = true;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Submitting...</span>
    `;

    try {
        const formData = new FormData(quizForm);
        const response = await fetch('/student/classes/' + window.classroomId + '/quests/' + window.questId + '/submit', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        stopTimer();

        if (result.success) {
            showResults(result.results);
        } else {
            alert(result.message || 'Failed to submit quest. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = submitBtnOriginalHtml;
            isSubmitting = false;
        }
    } catch (error) {
        console.error('Submission error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = submitBtnOriginalHtml;
        isSubmitting = false;
    }
}

// Start timer when page loads
document.addEventListener('DOMContentLoaded', function() {
    startTimer();
});

// Stop timer when leaving page
window.addEventListener('beforeunload', function() {
    stopTimer();
});

// Speech Recognition for pronunciation exercises
document.addEventListener('DOMContentLoaded', function() {
    const count = document.querySelectorAll('[id^="startRecordingBtn-"]').length;
    for (let i = 0; i < count; i++) {
        const startBtn = document.getElementById(`startRecordingBtn-${i}`);
        const status = document.getElementById(`recordingStatus-${i}`);
        const recognizedText = document.getElementById(`recognizedText-${i}`);
        const recognizedInput = document.getElementById(`recognizedInput-${i}`);
        let recognition;
        if (startBtn) {
            startBtn.addEventListener('click', function() {
                if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                    startBtn.disabled = true;
                    startBtn.title = 'Speech recognition not supported in your browser.';
                    recognizedText.textContent = 'Speech recognition not supported.';
                    return;
                }
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                recognition = new SpeechRecognition();
                recognition.lang = 'en-US';
                recognition.continuous = false;
                recognition.interimResults = false;
                status.classList.remove('hidden');
                recognizedText.textContent = '';
                recognizedInput.value = '';
                recognition.start();
                recognition.onresult = function(event) {
                    if (event.results && event.results[0] && event.results[0][0]) {
                        const transcript = event.results[0][0].transcript;
                        recognizedText.textContent = transcript;
                        recognizedInput.value = transcript;
                        startBtn.disabled = true;
                        startBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        recognizedText.textContent = 'No speech detected.';
                    }
                    status.classList.add('hidden');
                };
                recognition.onerror = function(event) {
                    status.classList.add('hidden');
                    recognizedText.textContent = 'Error: ' + (event.error || 'Unknown error');
                };
                recognition.onend = function() {
                    status.classList.add('hidden');
                };
            });
        }
    }
});
