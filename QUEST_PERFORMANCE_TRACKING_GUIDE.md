# Quest Performance Tracking Guide

## Overview

This guide explains how to integrate performance tracking with the quest system. When students complete quests, their performance data is automatically recorded and analyzed.

## What to Put in Quest Performance Data

### Required Fields (All Quest Types)
```json
{
    "total_score": 8,           // Points earned by student
    "max_score": 10,            // Maximum possible points
    "time_spent_minutes": 15,   // Time spent on the quest
    "attempts_count": 1         // Number of attempts (optional, defaults to 1)
}
```

### Pronunciation Quests
```json
{
    "total_score": 8,
    "max_score": 10,
    "time_spent_minutes": 12,
    "pronunciation_scores": [
        {
            "word": "example",
            "correct": true,
            "attempts": 2
        },
        {
            "word": "pronunciation", 
            "correct": false,
            "attempts": 3
        }
    ]
}
```

### Reading Quests
```json
{
    "total_score": 7,
    "max_score": 10,
    "time_spent_minutes": 18,
    "reading_scores": [
        {
            "question": "What is the main idea?",
            "correct": true,
            "response_time": 45
        },
        {
            "question": "Who is the main character?",
            "correct": false,
            "response_time": 30
        }
    ]
}
```

### Mixed Quests (Pronunciation + Reading)
```json
{
    "total_score": 15,
    "max_score": 20,
    "time_spent_minutes": 25,
    "pronunciation_scores": [
        {
            "word": "vocabulary",
            "correct": true,
            "attempts": 1
        }
    ],
    "reading_scores": [
        {
            "question": "What does this word mean?",
            "correct": true,
            "response_time": 20
        }
    ]
}
```

## How to Use the Performance Tracking

### 1. Student Submits Quest Completion

**API Endpoint:** `POST /student/quests/{questId}/complete`

**Example Request:**
```javascript
fetch('/student/quests/123/complete', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    },
    body: JSON.stringify({
        total_score: 8,
        max_score: 10,
        time_spent_minutes: 15,
        pronunciation_scores: [
            { word: "hello", correct: true, attempts: 1 },
            { word: "world", correct: false, attempts: 2 }
        ]
    })
})
```

**Response:**
```json
{
    "success": true,
    "performance": {
        "accuracy": 80.0,
        "improvement_rate": 5.2,
        "points_earned": 50,
        "current_level": 3,
        "current_points": 350
    }
}
```

### 2. Teacher Views Quest Performance

**API Endpoint:** `GET /teacher/classes/{classroom}/quests/{quest}/performance`

**Response:**
```json
{
    "quest": {
        "id": 123,
        "title": "Pronunciation Practice",
        "type": "pronunciation",
        "difficulty": "medium",
        "reward_points": 50
    },
    "performances": [
        {
            "student_name": "John Doe",
            "student_id": 456,
            "accuracy_percentage": 85.0,
            "time_spent_minutes": 12,
            "attempts_count": 1,
            "improvement_rate": 3.5,
            "completed_at": "Nov 21, 2025 15:30",
            "pronunciation_accuracy": 85.0
        }
    ],
    "statistics": {
        "total_completions": 25,
        "average_accuracy": 78.5,
        "average_time_spent": 14.2,
        "average_improvement": 2.1
    }
}
```

## Quest Type Mapping

The system automatically maps quest types to activity types:

| Quest Type | Activity Type | Skills Tracked |
|------------|---------------|----------------|
| pronunciation | pronunciation | Pronunciation accuracy |
| reading | reading | Reading comprehension |
| mixed | mixed | Both skills |
| vocabulary | general | Overall performance |
| grammar | general | Overall performance |
| comprehension | reading | Reading comprehension |
| listening | general | Overall performance |

## Automatic Calculations

### Accuracy Percentage
```
accuracy = (total_score / max_score) * 100
```

### Pronunciation Accuracy
```
pronunciation_accuracy = (correct_words / total_words) * 100
```

### Reading Comprehension
```
reading_comprehension = (correct_answers / total_questions) * 100
```

### Improvement Rate
```
improvement_rate = ((current_accuracy - previous_average) / previous_average) * 100
```

## Integration Steps

### 1. Frontend Integration

Add this JavaScript to your quest completion pages:

```javascript
class QuestCompletionTracker {
    constructor(questId) {
        this.questId = questId;
        this.startTime = Date.now();
        this.attempts = 1;
        this.pronunciationScores = [];
        this.readingScores = [];
    }
    
    recordPronunciation(word, correct, attempts = 1) {
        this.pronunciationScores.push({
            word: word,
            correct: correct,
            attempts: attempts
        });
    }
    
    recordReading(question, correct, responseTime = null) {
        this.readingScores.push({
            question: question,
            correct: correct,
            response_time: responseTime
        });
    }
    
    async submit(totalScore, maxScore) {
        const timeSpentMinutes = Math.floor((Date.now() - this.startTime) / 60000);
        
        const data = {
            total_score: totalScore,
            max_score: maxScore,
            time_spent_minutes: timeSpentMinutes,
            attempts_count: this.attempts
        };
        
        if (this.pronunciationScores.length > 0) {
            data.pronunciation_scores = this.pronunciationScores;
        }
        
        if (this.readingScores.length > 0) {
            data.reading_scores = this.readingScores;
        }
        
        const response = await fetch(`/student/quests/${this.questId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        return await response.json();
    }
}

// Usage example:
const tracker = new QuestCompletionTracker(123);

// During quest:
tracker.recordPronunciation("hello", true, 1);
tracker.recordReading("What is this?", true, 30);

// At completion:
const result = await tracker.submit(8, 10);
console.log('Performance recorded:', result);
```

### 2. Backend Integration

Add the QuestPerformanceTracking trait to your StudentController:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\QuestPerformanceTracking;

class StudentController extends Controller
{
    use QuestPerformanceTracking;
    
    // ... other methods
    
    public function submitQuestCompletion(Request $request, $questId)
    {
        return $this->submitQuestCompletion($request, $questId);
    }
}
```

## Best Practices

### 1. Data Quality
- Record all attempts and scores accurately
- Include timing data for better analytics
- Track individual word/question performance

### 2. User Experience
- Show immediate feedback on performance
- Display improvement trends to students
- Celebrate achievements and milestones

### 3. Teacher Insights
- Use performance data to identify struggling students
- Adjust quest difficulty based on class performance
- Provide targeted recommendations

### 4. Performance Considerations
- Batch score updates for efficiency
- Cache performance summaries
- Use background processing for complex calculations

## Example Implementation

### Pronunciation Quest Page
```html
<div id="quest-container">
    <h2>Pronunciation Practice</h2>
    <div id="words-container">
        <!-- Words will be loaded here -->
    </div>
    <button onclick="completeQuest()">Complete Quest</button>
</div>

<script>
const questTracker = new QuestCompletionTracker(123);

function recordWord(word, correct, attempts) {
    questTracker.recordPronunciation(word, correct, attempts);
}

async function completeQuest() {
    const totalScore = calculateScore();
    const maxScore = getTotalWords();
    
    const result = await questTracker.submit(totalScore, maxScore);
    
    if (result.success) {
        showSuccessMessage(result.performance);
        redirectToDashboard();
    }
}
</script>
```

### Teacher Quest Performance View
```javascript
async function loadQuestPerformance(questId) {
    const response = await fetch(`/teacher/classes/${classroomId}/quests/${questId}/performance`);
    const data = await response.json();
    
    displayQuestStats(data.statistics);
    displayStudentPerformances(data.performances);
}

function displayQuestStats(stats) {
    document.getElementById('total-completions').textContent = stats.total_completions;
    document.getElementById('average-accuracy').textContent = stats.average_accuracy + '%';
    document.getElementById('average-time').textContent = stats.average_time_spent + ' min';
}
```

This system provides comprehensive performance tracking for all quest types while maintaining data quality and providing valuable insights for both students and teachers.
