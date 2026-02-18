<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Tracking - {{ $classroom->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom styles for skill tracking */
        .skill-card {
            transition: all 0.3s ease;
        }
        .skill-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .progress-ring {
            transition: stroke-dashoffset 0.35s;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Skill Tracking</h1>
                <p class="text-gray-600 mt-1">Observe student performance by skill type and identify areas needing attention</p>
            </div>
            <a href="{{ route('teacher.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                ← Back to Dashboard
            </a>
        </div>
        
        <!-- Class Info -->
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold">{{ $classroom->name }}</h2>
            <p class="text-sm text-gray-600">{{ $classroom->students?->count() ?? 0 }} students • {{ $classroom->quests?->count() ?? 0 }} quests</p>
        </div>
    </div>

    <!-- Skill Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Responses</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalResponses">{{ $skillData['summary']['total_responses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Overall Accuracy</p>
                    <p class="text-2xl font-bold text-gray-900" id="overallAccuracy">{{ $skillData['summary']['overall_accuracy'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Most Challenging</p>
                    <p class="text-lg font-bold text-gray-900" id="challengingSkill">{{ $skillData['summary']['most_challenging_skill'] ? ucfirst($skillData['summary']['most_challenging_skill']) : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Most Successful</p>
                    <p class="text-lg font-bold text-gray-900" id="successfulSkill">{{ $skillData['summary']['most_successful_skill'] ? ucfirst($skillData['summary']['most_successful_skill']) : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Skill Type</label>
                <select id="skillTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Skills</option>
                    <option value="pronunciation">Pronunciation</option>
                    <option value="reading_comprehension">Reading Comprehension</option>
                    <option value="vocabulary">Vocabulary</option>
                    <option value="grammar">Grammar</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                <select id="studentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Students</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                <select id="difficultyFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Levels</option>
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <input type="date" id="dateFromFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        
        <div class="mt-4 flex justify-end space-x-3">
            <button onclick="clearFilters()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                Clear Filters
            </button>
            <button onclick="applyFilters()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Skill Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Skill Performance Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Skill Performance</h3>
            <div id="skillPerformanceChart" class="h-64"></div>
        </div>

        <!-- Difficulty Breakdown -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Performance by Difficulty</h3>
            <div id="difficultyChart" class="h-64"></div>
        </div>
    </div>

    <!-- Students Needing Attention -->
    @if ($skillData['summary']['students_needing_attention']->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4 text-red-600">Students Needing Attention</h3>
            <div class="space-y-3">
                @foreach ($skillData['summary']['students_needing_attention'] as $student)
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $student['student_name'] }}</p>
                            <p class="text-sm text-gray-600">{{ $student['total_responses'] }} responses • {{ number_format($student['recent_accuracy'], 1) }}% accuracy</p>
                        </div>
                        <button onclick="viewStudentDetails({{ $student['student_id'] }})" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                            View Details
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Responses Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Recent Student Responses</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skill</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Problem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accuracy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="responsesTable" class="bg-white divide-y divide-gray-200">
                    @foreach ($skillData['responses']->take(20) as $response)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $response->student->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $response->skill_type_display_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $response->problem_content }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $response->student_response }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($response->is_correct)
                                    <span class="text-green-600">✓ {{ $response->correct_answer }}</span>
                                @else
                                    <span class="text-red-600">✗ {{ $response->correct_answer }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($response->accuracy_score, 1) }}%</div>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $response->accuracy_score >= 80 ? 'green' : ($response->accuracy_score >= 60 ? 'yellow' : 'red') }}-500 h-2 rounded-full" style="width: {{ $response->accuracy_score }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $response->responded_at ? $response->responded_at->diffForHumans() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="viewResponseDetails({{ $response->id }})" class="text-indigo-600 hover:text-indigo-900">View</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Student Detail Modal -->
<div id="studentDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Student Skill Analysis</h3>
            <button onclick="closeStudentModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="studentDetailContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- Response Detail Modal -->
<div id="responseDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Response Details</h3>
            <button onclick="closeResponseModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="responseDetailContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
let currentSkillData = @json($skillData);
let classroomId = {{ $classroom->id }};

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    updateSkillPerformanceChart();
    updateDifficultyChart();
});

function updateSkillPerformanceChart() {
    const container = document.getElementById('skillPerformanceChart');
    const stats = currentSkillData.statistics;
    
    let html = '<div class="space-y-3">';
    for (const [skill, data] of Object.entries(stats)) {
        if (skill !== 'by_difficulty') {
            const trendColor = data.improvement_trend === 'improving' ? 'text-green-600' : 
                              data.improvement_trend === 'declining' ? 'text-red-600' : 'text-gray-600';
            const trendIcon = data.improvement_trend === 'improving' ? '↑' : 
                             data.improvement_trend === 'declining' ? '↓' : '→';
            
            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">${skill.charAt(0).toUpperCase() + skill.slice(1).replace('_', ' ')}</p>
                        <p class="text-sm text-gray-600">${data.total_responses} responses</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">${data.accuracy_rate}%</p>
                        <p class="text-sm ${trendColor}">${trendIcon} ${data.improvement_trend}</p>
                    </div>
                </div>
            `;
        }
    }
    html += '</div>';
    
    container.innerHTML = html;
}

function updateDifficultyChart() {
    const container = document.getElementById('difficultyChart');
    const difficultyStats = currentSkillData.statistics.by_difficulty || {};
    
    let html = '<div class="space-y-3">';
    const difficulties = ['easy', 'medium', 'hard'];
    const colors = ['green', 'yellow', 'red'];
    
    difficulties.forEach((difficulty, index) => {
        const data = difficultyStats[difficulty];
        if (data) {
            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">${difficulty.charAt(0).toUpperCase() + difficulty.slice(1)}</p>
                        <p class="text-sm text-gray-600">${data.total_responses} responses</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">${data.accuracy_rate}%</p>
                        <div class="w-16 bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-${colors[index]}-500 h-2 rounded-full" style="width: ${data.accuracy_rate}%"></div>
                        </div>
                    </div>
                </div>
            `;
        }
    });
    html += '</div>';
    
    container.innerHTML = html;
}

function applyFilters() {
    const filters = {
        skill_type: document.getElementById('skillTypeFilter').value,
        student_id: document.getElementById('studentFilter').value,
        difficulty_level: document.getElementById('difficultyFilter').value,
        date_from: document.getElementById('dateFromFilter').value
    };
    
    // Remove empty values
    Object.keys(filters).forEach(key => {
        if (!filters[key]) delete filters[key];
    });
    
    fetch(`/teacher/classes/${classroomId}/skill-tracking/data?${new URLSearchParams(filters)}`)
        .then(response => response.json())
        .then(data => {
            currentSkillData = data;
            updateDashboard();
        })
        .catch(error => {
            console.error('Error fetching skill data:', error);
        });
}

function clearFilters() {
    document.getElementById('skillTypeFilter').value = '';
    document.getElementById('studentFilter').value = '';
    document.getElementById('difficultyFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    
    applyFilters();
}

function updateDashboard() {
    // Update summary cards
    document.getElementById('totalResponses').textContent = currentSkillData.summary.total_responses;
    document.getElementById('overallAccuracy').textContent = currentSkillData.summary.overall_accuracy + '%';
    document.getElementById('challengingSkill').textContent = currentSkillData.summary.most_challenging_skill ? 
        currentSkillData.summary.most_challenging_skill.charAt(0).toUpperCase() + currentSkillData.summary.most_challenging_skill.slice(1) : 'N/A';
    document.getElementById('successfulSkill').textContent = currentSkillData.summary.most_successful_skill ? 
        currentSkillData.summary.most_successful_skill.charAt(0).toUpperCase() + currentSkillData.summary.most_successful_skill.slice(1) : 'N/A';
    
    // Update charts
    updateSkillPerformanceChart();
    updateDifficultyChart();
    
    // Update responses table
    updateResponsesTable();
}

function updateResponsesTable() {
    const tbody = document.getElementById('responsesTable');
    const responses = currentSkillData.responses.slice(0, 20);
    
    let html = '';
    responses.forEach(response => {
        const accuracyColor = response.accuracy_score >= 80 ? 'green' : (response.accuracy_score >= 60 ? 'yellow' : 'red');
        const correctIcon = response.is_correct ? '✓' : '✗';
        const correctColor = response.is_correct ? 'text-green-600' : 'text-red-600';
        
        html += `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${response.student.name}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                        ${response.skill_type.replace('_', ' ').charAt(0).toUpperCase() + response.skill_type.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 max-w-xs truncate">${response.problem_content}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 max-w-xs truncate">${response.student_response}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="${correctColor}">${correctIcon} ${response.correct_answer}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900">${response.accuracy_score.toFixed(1)}%</div>
                        <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                            <div class="bg-${accuracyColor}-500 h-2 rounded-full" style="width: ${response.accuracy_score}%"></div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${new Date(response.responded_at).toLocaleDateString()}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="viewResponseDetails(${response.id})" class="text-indigo-600 hover:text-indigo-900">View</button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function viewStudentDetails(studentId) {
    fetch(`/teacher/classes/${classroomId}/students/${studentId}/skill-analysis`)
        .then(response => response.json())
        .then(data => {
            displayStudentDetails(data);
            document.getElementById('studentDetailModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching student analysis:', error);
        });
}

function displayStudentDetails(data) {
    const content = document.getElementById('studentDetailContent');
    
    let html = `
        <div class="space-y-6">
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Overall Summary</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-600">Total Responses</p>
                        <p class="text-lg font-bold">${data.overall_summary.total_responses}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-600">Overall Accuracy</p>
                        <p class="text-lg font-bold">${data.overall_summary.overall_accuracy}%</p>
                    </div>
                </div>
            </div>
    `;
    
    // Add skill analysis
    for (const [skill, analysis] of Object.entries(data.skill_analysis)) {
        const trendColor = analysis.improvement_trend === 'improving' ? 'text-green-600' : 
                          analysis.improvement_trend === 'declining' ? 'text-red-600' : 'text-gray-600';
        const trendIcon = analysis.improvement_trend === 'improving' ? '↑' : 
                         analysis.improvement_trend === 'declining' ? '↓' : '→';
        
        html += `
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">${skill.charAt(0).toUpperCase() + skill.slice(1).replace('_', ' ')}</h4>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-600">Accuracy</p>
                        <p class="text-lg font-bold">${analysis.overall_accuracy}%</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-600">Recent Accuracy</p>
                        <p class="text-lg font-bold">${analysis.recent_accuracy}%</p>
                    </div>
                </div>
                <p class="text-sm ${trendColor}">${trendIcon} ${analysis.improvement_trend}</p>
            </div>
        `;
    }
    
    html += '</div>';
    content.innerHTML = html;
}

function closeStudentModal() {
    document.getElementById('studentDetailModal').classList.add('hidden');
}

function viewResponseDetails(responseId) {
    const response = currentSkillData.responses.find(r => r.id === responseId);
    if (!response) return;
    
    const content = document.getElementById('responseDetailContent');
    
    const detailsHtml = response.skill_details ? 
        Object.entries(response.skill_details).map(([key, value]) => 
            `<p><strong>${key}:</strong> ${value}</p>`
        ).join('') : '';
    
    content.innerHTML = `
        <div class="space-y-4">
            <div>
                <h4 class="font-semibold text-gray-900">Response Information</h4>
                <div class="mt-2 space-y-2">
                    <p><strong>Student:</strong> ${response.student.name}</p>
                    <p><strong>Skill:</strong> ${response.skill_type.replace('_', ' ').charAt(0).toUpperCase() + response.skill_type.slice(1)}</p>
                    <p><strong>Difficulty:</strong> ${response.difficulty_level}</p>
                    <p><strong>Attempts:</strong> ${response.attempts}</p>
                    <p><strong>Response Time:</strong> ${response.response_time_seconds ? response.response_time_seconds + 's' : 'N/A'}</p>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-900">Problem & Response</h4>
                <div class="mt-2 space-y-2">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-600">Problem:</p>
                        <p class="font-medium">${response.problem_content}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-600">Student Response:</p>
                        <p class="font-medium">${response.student_response}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-600">Correct Answer:</p>
                        <p class="font-medium">${response.correct_answer}</p>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-900">Result</h4>
                <div class="mt-2">
                    <p class="text-lg font-bold ${response.is_correct ? 'text-green-600' : 'text-red-600'}">
                        ${response.is_correct ? 'Correct' : 'Incorrect'}
                    </p>
                    <p class="text-sm text-gray-600">Accuracy Score: ${response.accuracy_score}%</p>
                </div>
            </div>
            
            ${detailsHtml ? `
            <div>
                <h4 class="font-semibold text-gray-900">Additional Details</h4>
                <div class="mt-2">
                    ${detailsHtml}
                </div>
            </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('responseDetailModal').classList.remove('hidden');
}

function closeResponseModal() {
    document.getElementById('responseDetailModal').classList.add('hidden');
}
</script>
</body>
</html>
