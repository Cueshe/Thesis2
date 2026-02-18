/**
 * Student Performance Tracker
 * Helper class for recording student performance data
 */

class PerformanceTracker {
    constructor() {
        this.apiEndpoint = '/teacher/performance/record';
    }

    /**
     * Record student performance for an activity
     * @param {Object} data - Performance data
     * @param {number} data.studentId - Student ID
     * @param {number} data.classroomId - Classroom ID
     * @param {string} data.activityType - Type of activity (pronunciation, reading, mixed, general)
     * @param {number} data.totalScore - Score achieved
     * @param {number} data.maxScore - Maximum possible score
     * @param {number} data.timeSpentMinutes - Time spent in minutes
     * @param {number} [data.pronunciationAccuracy] - Pronunciation accuracy percentage
     * @param {number} [data.readingComprehension] - Reading comprehension percentage
     * @param {Array} [data.pronunciationScores] - Individual pronunciation scores
     * @param {Array} [data.readingScores] - Individual reading scores
     * @param {number} [data.questId] - Quest ID if applicable
     */
    async recordPerformance(data) {
        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                console.log('Performance recorded successfully:', result);
                return {
                    success: true,
                    performanceId: result.performance_id,
                    accuracy: result.accuracy_percentage,
                    improvement: result.improvement_rate
                };
            } else {
                console.error('Failed to record performance:', result.error || 'Unknown error');
                return { success: false, error: result.error || 'Unknown error' };
            }
        } catch (error) {
            console.error('Error recording performance:', error);
            return { success: false, error: error.message };
        }
    }

    /**
     * Record pronunciation practice performance
     */
    async recordPronunciationPractice(studentId, classroomId, scores, timeSpentMinutes, questId = null) {
        const totalScore = scores.reduce((sum, score) => sum + (score.correct ? 1 : 0), 0);
        const maxScore = scores.length;
        const accuracy = (totalScore / maxScore) * 100;

        return this.recordPerformance({
            studentId,
            classroomId,
            questId,
            activityType: 'pronunciation',
            totalScore,
            maxScore,
            timeSpentMinutes,
            pronunciationAccuracy: accuracy,
            pronunciationScores: scores
        });
    }

    /**
     * Record reading comprehension performance
     */
    async recordReadingComprehension(studentId, classroomId, scores, timeSpentMinutes, questId = null) {
        const totalScore = scores.reduce((sum, score) => sum + (score.correct ? 1 : 0), 0);
        const maxScore = scores.length;
        const accuracy = (totalScore / maxScore) * 100;

        return this.recordPerformance({
            studentId,
            classroomId,
            questId,
            activityType: 'reading',
            totalScore,
            maxScore,
            timeSpentMinutes,
            readingComprehension: accuracy,
            readingScores: scores
        });
    }

    /**
     * Record mixed activity performance
     */
    async recordMixedActivity(studentId, classroomId, pronunciationScores, readingScores, timeSpentMinutes, questId = null) {
        const pronunciationTotal = pronunciationScores.reduce((sum, score) => sum + (score.correct ? 1 : 0), 0);
        const readingTotal = readingScores.reduce((sum, score) => sum + (score.correct ? 1 : 0), 0);
        const totalScore = pronunciationTotal + readingTotal;
        const maxScore = pronunciationScores.length + readingScores.length;
        const accuracy = (totalScore / maxScore) * 100;

        const pronunciationAccuracy = (pronunciationTotal / pronunciationScores.length) * 100;
        const readingComprehension = (readingTotal / readingScores.length) * 100;

        return this.recordPerformance({
            studentId,
            classroomId,
            questId,
            activityType: 'mixed',
            totalScore,
            maxScore,
            timeSpentMinutes,
            pronunciationAccuracy,
            readingComprehension,
            pronunciationScores,
            readingScores
        });
    }

    /**
     * Get performance summary for a student (helper for frontend display)
     */
    formatPerformanceSummary(performanceData) {
        return {
            accuracy: `${performanceData.accuracy.toFixed(1)}%`,
            improvement: performanceData.improvement > 0 ? `+${performanceData.improvement.toFixed(1)}%` : 
                        performanceData.improvement < 0 ? `${performanceData.improvement.toFixed(1)}%` : '0%',
            trend: performanceData.improvement > 5 ? 'improving' : 
                   performanceData.improvement < -5 ? 'declining' : 'stable',
            status: performanceData.accuracy >= 80 ? 'excellent' : 
                   performanceData.accuracy >= 60 ? 'good' : 'needs_improvement'
        };
    }
}

// Make it available globally
window.PerformanceTracker = PerformanceTracker;

// Example usage:
/*
const tracker = new PerformanceTracker();

// Record pronunciation practice
tracker.recordPronunciationPractice(
    123, // studentId
    456, // classroomId
    [
        { word: 'example', correct: true, attempts: 2 },
        { word: 'pronunciation', correct: false, attempts: 3 }
    ],
    15, // timeSpentMinutes
    789 // questId (optional)
).then(result => {
    if (result.success) {
        console.log('Performance recorded:', result);
    }
});
*/
