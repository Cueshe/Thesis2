<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'slug',
        'join_code',
        'schedule',
        'live_buff',
        'coin_bonus',
    ];

    protected static function booted(): void
    {
        static::creating(function (Classroom $classroom) {
            if (empty($classroom->slug)) {
                $classroom->slug = static::generateUniqueSlug($classroom->name ?? Str::random(6));
            }

            if (empty($classroom->join_code)) {
                $classroom->join_code = static::generateJoinCode();
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(ClassAnnouncement::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(StudentProfile::class, 'classroom_id');
    }

    public function quests(): HasMany
    {
        return $this->hasMany(Quest::class);
    }

    public static function generateJoinCode(): string
    {
        do {
            $code = strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(3));
        } while (static::where('join_code', $code)->exists());

        return $code;
    }

    protected static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: Str::random(6);
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}

