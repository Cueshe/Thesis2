<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Quest;

class StudentPerformance extends Model
{
    protected $fillable = [
        'student_id',
        'classroom_id',
        'quest_id',
        'activity_type',
        'total_score',
        'max_score',
        'accuracy_percentage',
        'time_spent_minutes',
        'attempts_count',
        'pronunciation_scores',
        'reading_scores',
        'pronunciation_accuracy',
        'reading_comprehension',
        'improvement_rate',
        'streak_bonus',
        'difficulty_levels',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'pronunciation_scores' => 'array',
            'reading_scores' => 'array',
            'difficulty_levels' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }

    public function getEarnedXpAttribute(): int
    {
        $reward = $this->quest?->reward_points ?? 0;
        if ($reward <= 0) {
            return 0;
        }

        $totalQuestions = (int) ($this->max_score ?? 0);
        $correctCount = (int) ($this->total_score ?? 0);

        if ($totalQuestions > 0) {
            $ratio = max(0, min(1, $correctCount / $totalQuestions));
            return (int) round($reward * $ratio);
        }

        $accuracy = $this->accuracy_percentage;
        if ($accuracy === null) {
            return $reward;
        }

        $ratio = max(0, min(1, $accuracy / 100));
        return (int) round($reward * $ratio);
    }

    /**
     * Get student's overall performance in a classroom
     */
    public static function getStudentOverallPerformance(int $studentId, int $classroomId): array
    {
        $performances = self::where('student_id', $studentId)
            ->where('classroom_id', $classroomId)
            ->whereNotNull('completed_at')
            ->get();

        if ($performances->isEmpty()) {
            return [
                'total_activities' => 0,
                'average_accuracy' => 0,
                'total_time_spent' => 0,
                'improvement_rate' => 0,
                'strongest_area' => 'N/A',
                'weakest_area' => 'N/A',
                'recent_trend' => 'stable'
            ];
        }

        $totalActivities = $performances->count();
        $averageAccuracy = $performances->avg('accuracy_percentage');
        $totalTimeSpent = $performances->sum('time_spent_minutes');
        $improvementRate = $performances->avg('improvement_rate');

        // Analyze skill areas
        $pronunciationAvg = $performances->whereNotNull('pronunciation_accuracy')->avg('pronunciation_accuracy') ?? 0;
        $readingAvg = $performances->whereNotNull('reading_comprehension')->avg('reading_comprehension') ?? 0;

        $strongestArea = $pronunciationAvg > $readingAvg ? 'Pronunciation' : ($readingAvg > 0 ? 'Reading' : 'N/A');
        $weakestArea = $pronunciationAvg < $readingAvg ? 'Pronunciation' : ($readingAvg > 0 ? 'Reading' : 'N/A');

        // Calculate recent trend (last 5 activities vs previous 5)
        $recentPerformances = $performances->sortByDesc('completed_at')->take(5);
        $previousPerformances = $performances->sortByDesc('completed_at')->skip(5)->take(5);
        
        $recentAvg = $recentPerformances->avg('accuracy_percentage');
        $previousAvg = $previousPerformances->avg('accuracy_percentage') ?? $recentAvg;

        $trend = 'stable';
        if ($recentAvg > $previousAvg + 5) {
            $trend = 'improving';
        } elseif ($recentAvg < $previousAvg - 5) {
            $trend = 'declining';
        }

        return [
            'total_activities' => $totalActivities,
            'average_accuracy' => round($averageAccuracy, 2),
            'total_time_spent' => $totalTimeSpent,
            'improvement_rate' => round($improvementRate, 2),
            'strongest_area' => $strongestArea,
            'weakest_area' => $weakestArea,
            'recent_trend' => $trend,
            'pronunciation_average' => round($pronunciationAvg, 2),
            'reading_average' => round($readingAvg, 2),
        ];
    }

    /**
     * Get classroom performance summary
     */
    public static function getClassroomPerformanceSummary(int $classroomId): array
    {
        $performances = self::where('classroom_id', $classroomId)
            ->whereNotNull('completed_at')
            ->with('student')
            ->get();

        if ($performances->isEmpty()) {
            return [
                'total_students' => 0,
                'active_students' => 0,
                'class_average' => 0,
                'top_performers' => [],
                'students_needing_help' => [],
                'activity_breakdown' => [],
                'difficulty_distribution' => []
            ];
        }

        // Group by student
        $studentPerformances = $performances->groupBy('student_id');
        $totalStudents = $studentPerformances->count();
        $activeStudents = $studentPerformances->filter(function ($studentPerf) {
            return $studentPerf->last()->completed_at && $studentPerf->last()->completed_at->gt(now()->subDays(7));
        })->count();

        // Calculate class average
        $classAverage = $performances->avg('accuracy_percentage');

        // Find top performers and students needing help
        $studentAverages = $studentPerformances->map(function ($studentPerf) {
            return [
                'student_id' => $studentPerf->first()->student_id,
                'student_name' => $studentPerf->first()->student->name,
                'average' => $studentPerf->avg('accuracy_percentage'),
                'activities_count' => $studentPerf->count(),
                'last_activity' => $studentPerf->max('completed_at')
            ];
        });

        $topPerformers = $studentAverages->sortByDesc('average')->take(5)->values();
        $studentsNeedingHelp = $studentAverages->sortBy('average')->take(5)->values();

        // Activity breakdown
        $activityBreakdown = $performances->groupBy('activity_type')->map(function ($activities) {
            return [
                'count' => $activities->count(),
                'average_accuracy' => round($activities->avg('accuracy_percentage'), 2),
                'total_time' => $activities->sum('time_spent_minutes')
            ];
        });

        return [
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'class_average' => round($classAverage, 2),
            'top_performers' => $topPerformers->toArray(),
            'students_needing_help' => $studentsNeedingHelp->toArray(),
            'activity_breakdown' => $activityBreakdown->toArray(),
            'difficulty_distribution' => self::getDifficultyDistribution($performances)
        ];
    }

    /**
     * Get performance trends over time
     */
    public static function getPerformanceTrends(int $classroomId, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $dailyPerformance = self::where('classroom_id', $classroomId)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $startDate)
            ->selectRaw('DATE(completed_at) as date, AVG(accuracy_percentage) as avg_accuracy, COUNT(*) as activity_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'daily_performance' => $dailyPerformance->map(function ($day) {
                return [
                    'date' => $day->date,
                    'average_accuracy' => round($day->avg_accuracy, 2),
                    'activity_count' => $day->activity_count
                ];
            })->toArray(),
            'overall_trend' => self::calculateTrendDirection($dailyPerformance->pluck('avg_accuracy'))
        ];
    }

    /**
     * Record student performance
     */
    public static function recordPerformance(array $data): self
    {
        // Calculate improvement rate based on previous performances
        $previousPerformance = self::where('student_id', $data['student_id'])
            ->where('classroom_id', $data['classroom_id'])
            ->where('activity_type', $data['activity_type'])
            ->orderBy('completed_at', 'desc')
            ->first();

        $improvementRate = 0;
        if ($previousPerformance && isset($data['accuracy_percentage'])) {
            $previousAccuracy = $previousPerformance->accuracy_percentage;
            $currentAccuracy = $data['accuracy_percentage'];
            $improvementRate = $previousAccuracy > 0 ? (($currentAccuracy - $previousAccuracy) / $previousAccuracy) * 100 : 0;
        }

        $performanceData = array_merge($data, [
            'improvement_rate' => round($improvementRate, 2),
            'completed_at' => now(),
        ]);

        return self::create($performanceData);
    }

    /**
     * Get difficulty distribution
     */
    private static function getDifficultyDistribution($performances): array
    {
        $distribution = ['easy' => 0, 'medium' => 0, 'hard' => 0];
        
        foreach ($performances as $performance) {
            $accuracy = $performance->accuracy_percentage;
            if ($accuracy >= 80) {
                $distribution['easy']++;
            } elseif ($accuracy >= 60) {
                $distribution['medium']++;
            } else {
                $distribution['hard']++;
            }
        }

        return $distribution;
    }

    /**
     * Calculate trend direction
     */
    private static function calculateTrendDirection($accuracies): string
    {
        if ($accuracies->count() < 2) {
            return 'insufficient_data';
        }

        $firstHalf = $accuracies->take(floor($accuracies->count() / 2));
        $secondHalf = $accuracies->skip(floor($accuracies->count() / 2));

        $firstAvg = $firstHalf->avg();
        $secondAvg = $secondHalf->avg();

        if ($secondAvg > $firstAvg + 5) {
            return 'improving';
        } elseif ($secondAvg < $firstAvg - 5) {
            return 'declining';
        } else {
            return 'stable';
        }
    }
}
