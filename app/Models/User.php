<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'status',
        'must_change_password',
        'points',
        'level',
        'experience',
        'streak_days',
        'last_activity_date',
        'achievements',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'last_activity_date' => 'date',
            'achievements' => 'array',
        ];
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    /**
     * Calculate experience needed for next level
     */
    public function getExperienceForNextLevel(): int
    {
        $level = $this->level ?? 1;
        return $level * 100;
    }

    /**
     * Calculate progress percentage to next level
     */
    public function getLevelProgress(): float
    {
        $level = $this->level ?? 1;
        $experience = $this->experience ?? 0;
        
        $currentLevelExp = ($level - 1) * 100;
        $nextLevelExp = $level * 100;
        $progress = $nextLevelExp - $currentLevelExp;
        $currentProgress = $experience - $currentLevelExp;
        
        return $progress > 0 ? min(100, max(0, ($currentProgress / $progress) * 100)) : 0;
    }

    /**
     * Add points and experience
     */
    public function addPoints(int $points): void
    {
        $this->increment('points', $points);
        $this->addExperience($points);
    }

    /**
     * Add experience and level up if needed
     */
    public function addExperience(int $exp): void
    {
        $this->increment('experience', $exp);
        $this->refresh();
        
        while ($this->experience >= $this->getExperienceForNextLevel()) {
            $this->increment('level');
            $this->refresh();
        }
        
        $this->save();
    }

    /**
     * Update daily streak
     */
    public function updateStreak(): void
    {
        try {
            $today = now()->toDateString();
            $lastActivity = $this->last_activity_date?->toDateString();
            
            if ($lastActivity === $today) {
                return; // Already updated today
            }
            
            if ($lastActivity && $lastActivity === now()->subDay()->toDateString()) {
                // Consecutive day
                $this->increment('streak_days');
            } else {
                // Streak broken or first time
                $this->streak_days = 1;
            }
            
            $this->last_activity_date = $today;
            $this->save();
        } catch (\Exception $e) {
            // Silently fail if columns don't exist yet
            Log::warning('Failed to update streak: ' . $e->getMessage());
        }
    }

    /**
     * Add achievement
     */
    public function addAchievement(string $achievement): void
    {
        $achievements = $this->achievements ?? [];
        if (!in_array($achievement, $achievements)) {
            $achievements[] = $achievement;
            $this->achievements = $achievements;
            $this->save();
        }
    }

    /**
     * Get leaderboard position
     */
    public static function getLeaderboard(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('role', 'student')
            ->orderBy('points', 'desc')
            ->orderBy('level', 'desc')
            ->orderBy('experience', 'desc')
            ->limit($limit)
            ->get();
    }
}
